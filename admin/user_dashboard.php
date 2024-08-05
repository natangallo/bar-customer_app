<?php
// user_dashboard.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$userId = $_GET['id'] ?? null;

if (!$userId) {
    header('Location: manage_users.php');
    exit();
}

// Recupera i prodotti ordinati alfabeticamente e le informazioni dell'utente
$products = getAllProductsOrdered();
$user = getUserById($userId);

// Recupera le transazioni dell'utente
$transactions = getUserTransactions($userId);

$cartTotal = 0;
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestisce l'aggiunta dei prodotti al carrello
    $productIds = $_POST['product_ids'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    if ($productIds) {
        try {
            $db->beginTransaction();

            $cartTotal = 0;
            
            foreach ($productIds as $productId) {
                // Recupera il prezzo del prodotto
                $stmt = $db->prepare('SELECT price FROM products WHERE id = :product_id');
                $stmt->execute([':product_id' => $productId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    $productPrice = $product['price'];
                    $quantity = (int) ($quantities[$productId] ?? 1);
                    $cartTotal += $productPrice * $quantity;

                    // Aggiungi il prodotto al carrello
                    $stmt = $db->prepare('INSERT INTO cart (user_id, product_id, quantity, added_date) VALUES (:user_id, :product_id, :quantity, NOW())');
                    $stmt->execute([
                        ':user_id' => $userId,
                        ':product_id' => $productId,
                        ':quantity' => $quantity
                    ]);

                    // Creare una transazione per ogni unità del prodotto aggiunta al carrello
                    for ($i = 0; $i < $quantity; $i++) {
                        $stmt = $db->prepare('INSERT INTO transactions (product_id, amount, transaction_date, type, description, user_id) VALUES (:product_id, :amount, NOW(), "add_to_cart", "Aggiunta al carrello", :user_id)');
                        $stmt->execute([
                            ':product_id' => $productId,
                            ':amount' => -$productPrice, // Sottrarre il prezzo di una singola unità
                            ':user_id' => $userId
                        ]);
                    }
                }
            }

            // Aggiorna il credito disponibile
            $stmt = $db->prepare('UPDATE users SET balance = balance - :amount WHERE id = :user_id');
            $stmt->execute([
                ':amount' => $cartTotal,
                ':user_id' => $userId
            ]);

            $db->commit();
            $successMessage = 'Prodotti aggiunti al carrello con successo. Totale carrello: €' . number_format($cartTotal, 2);
            
            // Ricarica le informazioni aggiornate dell'utente e delle transazioni
            $user = getUserById($userId);
            $transactions = getUserTransactions($userId);
        } catch (Exception $e) {
            $db->rollBack();
            $errorMessage = 'Errore durante l\'aggiunta dei prodotti: ' . $e->getMessage();
        }
    }
}

// Dividi i prodotti per tipo
$productsByType = [];
foreach ($products as $product) {
    $productsByType[$product['type']][] = $product;
}

include 'views/header.php';
?>

<main>
    <h2>Dashboard Profilo Utente: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>

    <div class="user-info">
        <p><strong>Credito Disponibile:</strong> €<?php echo number_format($user['balance'], 2); ?></p>
    </div>

    <?php if ($successMessage): ?>
        <p class="alert success"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <p class="alert"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <!-- Pulsanti per cambiare vista -->
    <div class="view-toggle">
        <button id="list-view-btn" class="toggle-btn active">Vista Elenco</button>
        <button id="grid-view-btn" class="toggle-btn">Vista Griglia</button>
    </div>

    <!-- Vista elenco -->
    <form id="list-view" action="user_dashboard.php?id=<?php echo urlencode($userId); ?>" method="post" style="display: block;">
        <h3>Elenco Prodotti</h3>
        <input type="text" id="product-filter" placeholder="Cerca prodotto..." onkeyup="filterProducts()">
        <table id="product-table">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Nome Prodotto &#x21C5;</th>
                    <th onclick="sortTable(1)">Prezzo &#x21C5;</th>
                    <th>Quantità</th>
                    <th>Aggiungi al Carrello</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4"><strong>Gelato</strong></td>
                </tr>
                <?php foreach ($productsByType['gelato'] as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?> €</td>
                        <td><input type="number" name="quantities[<?php echo $product['id']; ?>]" value="1" min="1" style="width: 50px;"></td>
                        <td><input type="checkbox" name="product_ids[]" value="<?php echo $product['id']; ?>"></td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <td colspan="4"><strong>Bibite</strong></td>
                </tr>
                <?php foreach ($productsByType['bibita'] as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?> €</td>
                        <td><input type="number" name="quantities[<?php echo $product['id']; ?>]" value="1" min="1" style="width: 50px;"></td>
                        <td><input type="checkbox" name="product_ids[]" value="<?php echo $product['id']; ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Conferma Aggiunta al Carrello</button>
    </form>

    <!-- Sezione della griglia dei prodotti -->
    <div id="grid-view" style="display: none;">
        <h3>Griglia Prodotti</h3>
        <div class="grid-container">
            <div class="grid-section">
                <h3>Gelati</h3>
                <?php foreach ($productsByType['gelato'] as $product): ?>
                    <div class="grid-item">
                        <span><?php echo htmlspecialchars($product['name']); ?></span>
                        <span><?php echo htmlspecialchars($product['price']); ?> €</span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="grid-section">
                <h3>Bibite</h3>
                <?php foreach ($productsByType['bibita'] as $product): ?>
                    <div class="grid-item">
                        <span><?php echo htmlspecialchars($product['name']); ?></span>
                        <span><?php echo htmlspecialchars($product['price']); ?> €</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <h3>Transazioni Recenti</h3>
    <table>
        <thead>
            <tr>
                <th>Data e Ora</th>
                <th>Tipo</th>
                <th>Descrizione</th>
                <th>Importo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['amount']); ?> €</td>
                    <td>
                        <?php if ($transaction['type'] === 'add_to_cart'): ?>
                            <form action="edit_cart.php" method="post" style="display:inline;">
                                <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction['id']); ?>">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
                                <button type="submit">Modifica</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script>
    // Script per alternare le viste
    document.getElementById('list-view-btn').addEventListener('click', function() {
        document.getElementById('list-view').style.display = 'block';
        document.getElementById('grid-view').style.display = 'none';
        this.classList.add('active');
        document.getElementById('grid-view-btn').classList.remove('active');
    });

    document.getElementById('grid-view-btn').addEventListener('click', function() {
        document.getElementById('list-view').style.display = 'none';
        document.getElementById('grid-view').style.display = 'block';
        this.classList.add('active');
        document.getElementById('list-view-btn').classList.remove('active');
    });

    // Funzione per filtrare i prodotti nella tabella
    function filterProducts() {
        const filter = document.getElementById('product-filter').value.toLowerCase();
        const rows = document.querySelectorAll('#product-table tbody tr');

        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            row.style.display = name.includes(filter) ? '' : 'none';
        });
    }

    // Funzione per ordinare la tabella
    function sortTable(columnIndex) {
        const table = document.getElementById('product-table');
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);

        const sortedRows = rows.sort((a, b) => {
            const aText = a.cells[columnIndex].textContent.trim().toLowerCase();
            const bText = b.cells[columnIndex].textContent.trim().toLowerCase();
            return aText.localeCompare(bText);
        });

        sortedRows.forEach(row => tbody.appendChild(row));
    }
</script>

<?php include 'views/footer.php'; ?>
