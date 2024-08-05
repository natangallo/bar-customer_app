<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include 'views/header.php';
?>

<main>
    <h2>Dashboard Amministrativa</h2>
    <ul>
        <li><a href="admin/manage_users.php">Gestione Utenti</a></li>
        <li><a href="admin/manage_products.php">Gestione Prodotti</a></li>
        <li><a href="admin/view_transactions.php">Storico Transazioni</a></li>
    </ul>
</main>

<?php include 'views/footer.php'; ?>
