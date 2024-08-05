<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
require_once '../includes/db.php';

// session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Recupero degli utenti
$stmt = $db->prepare("SELECT id, first_name, last_name FROM users WHERE status = 'pending' LIMIT 10");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pendingUsers = $db->query("SELECT * FROM users WHERE status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);

// Recupero dei prodotti
$stmt = $db->prepare("SELECT type, name, price FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recupero delle transazioni
$stmt = $db->prepare("SELECT id, user_id, type, amount, description, transaction_date FROM transactions ORDER BY transaction_date DESC LIMIT 5");
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'views/header.php';
?>

<main>
        <h1>Dashboard Amministrativa</h1>
        
        <h2>Elenco Utenti da Attivare</h2>
        <table>
        	<?php if (!empty($pendingUsers)): ?>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php else: ?>
            	<thead>Tutti gli utenti sono stati approvati.</thead>
	    	<?php endif; ?>
        </table>

        <h2>Elenco Prodotti</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Prezzo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['type']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Ultime Transazioni</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Utente</th>
                    <th>Tipo</th>
                    <th>Importo</th>
                    <th>Descrizione</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['amount']); ?> €</td>
                        <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

<?php include 'views/footer.php'; ?>
