<?php

require_once 'includes/config.php';
require_once 'includes/db.php';
include 'views/header.php';

session_start();

// Controlla se la sessione è attiva
if (isset($_SESSION['user_id'])) {
    // Se la sessione è attiva, reindirizza alla pagina dashboard.php
    header('Location: dashboard.php');
    exit();
} else {
    // Se la sessione non è attiva, mostra il contenuto HTML esistente
    echo '<h2>Benvenuto al Bar</h2>';
    echo '<p>Accedi per visualizzare il tuo saldo e gli acquisti.</p>';
}

include 'views/footer.php';

?>
