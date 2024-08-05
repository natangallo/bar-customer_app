<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['id'];
    $newPrice = $_POST['price'];
    
    // Verifica se il prezzo è valido
    if (!is_numeric($newPrice) || $newPrice < 0) {
        echo 'Prezzo non valido.';
        exit();
    }

    // Ottieni i dettagli del prodotto per la transazione
    $stmt = $db->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo 'Prodotto non trovato.';
        exit();
    }

    // Aggiorna il prezzo del prodotto
    $stmt = $db->prepare("UPDATE products SET price = ? WHERE id = ?");
    $stmt->execute([$newPrice, $productId]);

    // Inserisci la transazione nella tabella transactions
    $stmt = $db->prepare("INSERT INTO transactions (product_id, amount, transaction_date, type, description, user_id) VALUES (?, ?, NOW(), 'price_update', ?, ?)");
    $description = 'Prezzo aggiornato per il prodotto: ' . $product['name'];
    $userId = $_SESSION['user_id'] ?? null; // Se disponibile, altrimenti null

    $stmt->execute([$productId, $newPrice, $description, $userId]);

    // Reindirizza a una pagina di successo o gestione prodotti
    header('Location: manage_products.php');
    exit();
}
?>
