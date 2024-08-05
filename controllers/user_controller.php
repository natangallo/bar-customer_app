<?php
include '../includes/config.php';
include '../includes/db.php';
include '../includes/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'register') {
        // Codice per registrare un nuovo utente
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $roomNumber = $_POST['room_number'];
        $group = $_POST['group'];
        $week = $_POST['week'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, room_number, group_name, week, password, balance) VALUES (?, ?, ?, ?, ?, ?, 0)");
        if ($stmt->execute([$firstName, $lastName, $roomNumber, $group, $week, $password])) {
            $_SESSION['success'] = 'Registrazione completata con successo. Attendi l\'approvazione dell\'amministratore.';
            header('Location: ../public/login.php');
        } else {
            $_SESSION['error'] = 'Registrazione fallita. Riprova.';
            header('Location: ../public/registration.php');
        }
    } elseif ($action == 'login') {
        // Codice per autenticare un utente
        $roomNumber = $_POST['room_number'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE room_number = ?");
        $stmt->execute([$roomNumber]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            header('Location: ../public/index.php');
        } else {
            $_SESSION['error'] = 'Credenziali non valide. Riprova.';
            header('Location: ../public/login.php');
        }
    } elseif ($action == 'logout') {
        // Codice per il logout
        session_unset();
        session_destroy();
        header('Location: ../public/index.php');
    }
}
?>
