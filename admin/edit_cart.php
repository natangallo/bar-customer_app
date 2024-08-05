<?php
// edit_cart.php

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

$transactionId = $_GET['transaction_id'] ?? null;

if (!$transactionId) {
    header('Location: user_dashboard.php');
    exit();
}

// Recupera l'ID utente dalla transazione
$stmt = $db->prepare('SELECT user_id FROM transactions WHERE id = :transaction_id');
$stmt->execute([':transaction_id' => $transactionId]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    header('Location: user_dashboard.php');
    exit();
}

$userId = $transaction['user_id'];

// Recupera i dettagli del carrello per l'utente
$stmt = $db->prepare('SELECT * FROM cart WHERE user_id = :user_id');
$stmt->execute([':user_id' => $userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recupera tutti i prodotti
$products = getAllProducts();

// Variabili di messaggio
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestisce la modifica del carrello
    $updatedProductIds = $_POST['product_ids'] ?? [];
    
    try {
        $db->beginTransaction();

        // Pulisce il carrello esistente per l'utente
        $stmt = $db->prepare('DELETE FROM cart WHERE user_id = :user_id');
        $stmt->execute([':user_id' => $userId]);

        $cartTotal = 0;
        
        foreach ($updatedProductIds as $productId) {
            // Recupera il prezzo del prodotto
            $stmt = $db->prepare('SELECT price FROM products WHERE id = :product_id');
            $stmt->execute([':product_id' => $productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $productPrice = $product['price'];
                $cartTotal += $productPrice;

                // Aggiungi il prodotto al carrello
                $stmt = $db->prepare('INSERT INTO cart (user_id, product_id) VALUES (:user_id, :product_id)');
                $stmt->execute([
                    ':user_id' => $userId,
                    ':product_id' => $productId
                ]);
            }
        }

        // Aggiorna la transazione con il nuovo totale
        $stmt = $db->prepare('UPDATE transactions SET amount = :amount WHERE id = :transaction_id');
        $stmt->execute([
            ':amount' => -$cartTotal, // Totale negativo per il credito speso
            ':transaction_id' => $transactionId
        ]);

        // Aggiorna il credito disponibile
        $stmt = $db->prepare('UPDATE users SET balance = balance - :amount WHERE id = :user_id');
        $stmt->execute([
            ':amount' => $cartTotal,
            ':user_id' => $userId
        ]);

        $db->commit();
        $successMessage = 'Carrello aggiornato con successo. Totale carrello: €' . number_format($cartTotal, 2);
    } catch (Exception $e) {
        $db->rollBack();
        $errorMessage = 'Errore durante l\'aggiornamento del carrello: ' . $e->getMessage();
    }
}

include 'views/header.php';
?>

<main>
    <h2>Modifica Carrello</h2>

    <?php if ($successMessage): ?>
        <p class="alert success"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <p class="alert"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form action="edit_cart.php?transaction_id=<?php echo urlencode($transactionId); ?>" method="post">
        <h3>Elenco Prodotti</h3>
        <input type="text" id="product-filter" placeholder="Cerca prodotto..." onkeyup="filterProducts()">
        <table id="product-table">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Nome Prodotto &#x21C5;</th>
                    <th onclick="sortTable(1)">Prezzo &#x21C5;</th>
                    <th>Aggiungi al Carrello</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?> €</td>
                        <td><input type="checkbox" name="product_ids[]" value="<?php echo $product['id']; ?>" <?php echo in_array($product['id'], array_column($cartItems, 'product_id')) ? 'checked' : ''; ?>></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Conferma Modifica Carrello</button>
    </form>

    <a href="user_dashboard.php?id=<?php echo urlencode($userId); ?>" class="button">Torna al Profilo Utente</a>
</main>

<?php include 'views/footer.php'; ?>

<script>
    function filterProducts() {
        const filter = document.getElementById('product-filter').value.toLowerCase();
        const rows = document.querySelectorAll('#product-table tbody tr');
        
        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            row.style.display = name.includes(filter) ? '' : 'none';
        });
    }

    function sortTable(columnIndex) {
        const table = document.getElementById('product-table');
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const isAscending = table.querySelectorAll('th')[columnIndex].classLis
