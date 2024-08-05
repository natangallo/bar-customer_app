<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = trim($_POST['user_id']);
    $pin = trim($_POST['pin']);
    $userType = $_POST['user_type'];

    // Check user credentials from the database
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pin, $user['pin'])) {
        if ($userType === 'admin' && $user['role'] === 'admin') {
            // If the user is an admin, set the admin session and redirect
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin/index.php');
            exit();
        } elseif ($userType === 'customer') {
            // If the user is a normal user, set the user session and redirect
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            // Invalid user type
            echo 'Tipo di utente non valido.';
        }
    } else {
        // Invalid credentials
        echo 'Credenziali non valide.';
    }
}
?>