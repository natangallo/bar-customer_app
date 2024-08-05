<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href='assets/css/admin.css'>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href='index.php'>Home</a></li>
            <li><a href="manage_users.php">Gestione Utenti</a></li>
            <li><a href="manage_products.php">Gestione Prodotti</a></li>
            <li><a href="view_transactions.php">Storico Transazioni</a></li>
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
            	<li><a href="../logout.php">Logout</a></li>
            	<?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
