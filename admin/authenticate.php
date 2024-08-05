<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        session_start();
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
    } else {
        echo 'Credenziali non valide.';
    }
}
?>
