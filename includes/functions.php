<?php
require_once 'db.php';

// aggiungere una funzione di recupero utenti in formato JSON
function getAllUsersJSON() {
    global $db;
    $stmt = $db->query("SELECT id, first_name, last_name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($users);
}

// Funzione per ottenere tutti gli utenti
function getAllUsers() {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funzione per ottenere tutti i prodotti
function getAllProducts() {
    global $db;
    $stmt = $db->prepare("SELECT * FROM products");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllProductsOrdered() {
    global $db;
    $stmt = $db->prepare('SELECT * FROM products ORDER BY name ASC');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funzione per registrare una transazione
function recordTransaction($userId, $type, $amount, $description) {
    global $db;
    $stmt = $db->prepare("INSERT INTO transactions (user_id, type, amount, description, transaction_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $type, $amount, $description]);
}

function logAction($userId, $action) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (:user_id, :action)");
    $stmt->execute(['user_id' => $userId, 'action' => $action]);
}

function addUser($firstName, $lastName, $roomNumber, $groupName, $weekDate, $pinCode) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, room_number, group_name, week_date, pin_code) VALUES (:first_name, :last_name, :room_number, :group_name, :week_date, :pin_code)");
    $stmt->execute([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'room_number' => $roomNumber,
        'group_name' => $groupName,
        'week_date' => $weekDate,
        'pin_code' => password_hash($pinCode, PASSWORD_BCRYPT)
    ]);
    logAction($pdo->lastInsertId(), 'User added');
}

function addProduct($name, $type, $price, $available = true) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO products (name, type, price, available) VALUES (:name, :type, :price, :available)");
    $stmt->execute([
        'name' => $name,
        'type' => $type,
        'price' => $price,
        'available' => $available
    ]);
    logAction($_SESSION['user_id'], 'Product added');
}

function addTransaction($userId, $productId, $amount) {
    global $db;

    // Aggiungere una nuova transazione
    $stmt = $db->prepare("INSERT INTO transactions (user_id, description, amount) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $productId, $amount]);

    // Aggiornare il bilancio dell'utente
    $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt->execute([$amount, $userId]);
    logAction($userId, 'Transaction added');

}

// Recupera l'utente per ID
function getUserById($userId) {
    global $db; // Usa la variabile globale della connessione al database

    $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Recupera le transazioni per l'utente
function getUserTransactions($userId) {
    global $db;
    $stmt = $db->prepare('SELECT * FROM transactions WHERE user_id = :user_id ORDER BY transaction_date DESC');
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($productId) {
    global $db;
    $stmt = $db->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
