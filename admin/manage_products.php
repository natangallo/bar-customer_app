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

$products = getAllProducts();

// Dividi i prodotti per tipo
$productsByType = [];
foreach ($products as $product) {
    $productsByType[$product['type']][] = $product;
}

include 'views/header.php';
?>

<main>
    <h2>Gestione Prodotti</h2>

    <h3>Prodotti Esistenti</h3>
    <div class="product-columns">
        <?php foreach ($productsByType as $type => $typeProducts): ?>
            <div class="product-column">
                <h4><?php echo htmlspecialchars($type); ?></h4>
                <?php
                // Chunk products to display in two columns with a max of 10 per column
                $chunks = array_chunk($typeProducts, 10);
                foreach ($chunks as $chunk): ?>
                    <div class="product-list" style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($chunk as $product): ?>
                            <div class="product-item">
                                <span><?php echo htmlspecialchars($product['name']); ?></span>
                                <span><?php echo htmlspecialchars($product['price']); ?> €</span>
                                <button class="edit-btn" data-id="<?php echo $product['id']; ?>">Modifica</button>
                                <form class="edit-form" id="edit-form-<?php echo $product['id']; ?>" action="update_product.php" method="post" style="display:none;">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <label for="price-<?php echo $product['id']; ?>">Prezzo:</label>
                                    <input type="text" id="price-<?php echo $product['id']; ?>" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                                    <button type="submit">Aggiorna Prezzo</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h3>Aggiungi Nuovo Prodotto</h3>
    <form action="add_product.php" method="post">
        <label for="name">Nome Prodotto:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="price">Prezzo:</label>
        <input type="text" id="price" name="price" required>

        <label for="type">Tipo:</label>
        <select id="type" name="type" required>
            <option value="gelato">Gelati</option>
            <option value="bibita">Bibite</option>
        </select>
        
        <button type="submit">Aggiungi Prodotto</button>
    </form>
</main>

<?php include 'views/footer.php'; ?>


<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.id;
        const form = document.getElementById('edit-form-' + productId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });
});
</script>