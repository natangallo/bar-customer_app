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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $rooms = $_POST['room_number'];
    $group = $_POST['group_name'];
    $week = $_POST['week_date'];

    $stmt = $db->prepare("INSERT INTO users (first_name, last_name, room_number, group_name, week_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $rooms, $group, $week]);

    header('Location: manage_users.php');
}
?>
