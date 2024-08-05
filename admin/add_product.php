<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type = $_POST['type'];

    $stmt = $db->prepare("INSERT INTO products (name, price, type) VALUES (?, ?, ?)");
    $stmt->execute([$name, $price, $type]);

    header('Location: manage_products.php');
}
?>
