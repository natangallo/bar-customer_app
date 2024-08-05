<?php require_once 'includes/config.php';?>
<?php include 'includes/functions.php';?>
<?php include 'views/header.php';?>

<?php $users = getAllUsers();?>

<main>
    <h2>Registrazione</h2>
    <form action="register.php" method="post">
        <label for="user_search">Cerca Nome:</label>
        <input type="text" id="user_search" name="user_search" oninput="filterUsers()">
        
        <label for="user_id">Seleziona Nome:</label>
        <select id="user_id" name="user_id">
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="pin">PIN:</label>
        <input type="password" id="pin" name="pin" required>
        
        <label for="confirm_pin">Conferma PIN:</label>
        <input type="password" id="confirm_pin" name="confirm_pin" required>
        
        <button type="submit">Registrati</button>
    </form>
</main>

<script>
function filterUsers() {
    var input = document.getElementById('user_search').value.toLowerCase();
    var options = document.getElementById('user_id').options;
    for (var i = 0; i < options.length; i++) {
        var optionText = options[i].text.toLowerCase();
        options[i].style.display = optionText.includes(input) ? '' : 'none';
    }
}
</script>

<?php include 'views/footer.php';?>