<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'views/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'];
    $pin = $_POST['pin'];
    $confirmPin = $_POST['confirm_pin'];

    if ($pin !== $confirmPin) {
        die('I PIN inseriti non corrispondono.');
    }

    // Verifica se il PIN è già impostato
    $stmt = $db->prepare("SELECT pin FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && !empty($user['pin'])) {
        die('Il PIN è già stato impostato per questo utente.');
    }

    $hashedPin = password_hash($pin, PASSWORD_BCRYPT);

    // Aggiorna il PIN e lo stato dell'utente
    $stmt = $db->prepare("UPDATE users SET pin = ?, status = 'pending' WHERE id = ?");
    $stmt->execute([$hashedPin, $userId]);

    header('Location: login.php');
    exit();
}
?>

<?php include 'views/footer.php';  ?>