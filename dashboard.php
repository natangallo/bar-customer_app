<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Ottenere le informazioni dell'utente
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ottenere le transazioni dell'utente, ordinate dalla più recente
$stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'views/header.php'; ?>

<main>
    <h2>Benvenuto, <?php echo htmlspecialchars($user['first_name']); ?> <?php echo htmlspecialchars($user['last_name']); ?></h2>
    <p>Saldo Attuale: <?php echo htmlspecialchars(number_format($user['balance'], 2)); ?> €</p>
    
    <?php if ($user['balance'] < ($user['initial_balance'] * 0.8)): ?>
        <div class="alert alert-warning">
            Attenzione: Il tuo saldo è esaurito o sta per esaurirsi. <br>
            Ricordati di ricaricare al Bar!
        </div>
    <?php endif; ?>
    
    <h3>Storico Acquisti</h3>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrizione</th>
                <th>Ammontare (€)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars(date("d-m-Y H:i", strtotime($transaction['transaction_date']))); ?></td>
                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($transaction['amount'], 2)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include 'views/footer.php'; ?>

<script>
    document.querySelectorAll('th').forEach(header => {
        header.addEventListener('click', () => {
            const table = header.closest('table');
            const tbody = table.querySelector('tbody');
            const index = Array.from(header.parentNode.children).indexOf(header);
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isAscending = header.classList.toggle('asc');
            
            rows.sort((a, b) => {
                const aText = a.children[index].textContent.trim();
                const bText = b.children[index].textContent.trim();
                
                if (!isNaN(parseFloat(aText)) && !isNaN(parseFloat(bText))) {
                    return isAscending ? aText - bText : bText - aText;
                }
                
                return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
            });
            
            rows.forEach(row => tbody.appendChild(row));
        });
    });
</script>
