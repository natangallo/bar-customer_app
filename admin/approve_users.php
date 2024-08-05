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
    $data = json_decode(file_get_contents("php://input"), true);
    $userIds = $data['user_ids'] ?? [];

    $success = true;

    foreach ($userIds as $userId) {
        // Approvazione utente
        $query = "UPDATE users SET status = 'active' WHERE id = :id AND status != 'active'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $success = $success && $stmt->execute();

        // Registra la transazione
        $transactionQuery = "INSERT INTO transactions (user_id, type, description, transaction_date) VALUES (:user_id, 'approval', 'Utente approvato', NOW())";
        $transactionStmt = $db->prepare($transactionQuery);
        $transactionStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $transactionStmt->execute();
    }

    echo json_encode(['success' => $success]);
}
?>
