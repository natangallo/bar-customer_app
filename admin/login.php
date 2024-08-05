<?php 
include 'views/header.php'; 
require_once '../includes/config.php';
?>

<main>
    <h2>Login Amministratore</h2>
    <form action="authenticate.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Accedi</button>
    </form>
</main>

<?php include 'views/footer.php'; ?>
