<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'] ?? null;
    $amountToAdd = $_POST['balance'] ?? 0.0;

    if ($userId !== null && is_numeric($amountToAdd)) {
        $amountToAdd = floatval($amountToAdd);

        // Aggiorna il saldo dell'utente
        $query = "UPDATE users SET balance = balance + :amount WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':amount', $amountToAdd, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Registra la transazione
            $transactionQuery = "INSERT INTO transactions (user_id, amount, type, description, transaction_date) VALUES (:user_id, :amount, 'credit', 'Credito aggiunto', NOW())";
            $transactionStmt = $db->prepare($transactionQuery);
            $transactionStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $transactionStmt->bindParam(':amount', $amountToAdd, PDO::PARAM_STR);
            $transactionStmt->execute();

            header('Location: manage_users.php');
            exit();
        }
    }
}
?>
