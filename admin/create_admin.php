<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
require_once '../includes/db.php';

// Controlla se una sessione è già avviata prima di avviarne una nuova
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Controlla se l'utente amministratore esiste già
$stmt = $db->prepare("SELECT * FROM users WHERE first_name = ?");
$stmt->execute([ADMIN_USER]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    echo "L'utente amministratore esiste già.";
} else {
    // Crea un nuovo utente amministratore
    $hashedPassword = password_hash(ADMIN_PASS, PASSWORD_BCRYPT);
    // Aggiorna la query per l'inserimento dell'utente amministratore
    $stmt = $db->prepare("INSERT INTO users (first_name, pin, role) VALUES (?, ?, ?)");

    if ($stmt->execute([ADMIN_USER, $hashedPassword, 'admin'])) {
        echo "Utente amministratore creato con successo.";
    } else {
        echo "Errore durante la creazione dell'utente amministratore.";
    }
}

?>
