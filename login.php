<?php require_once 'includes/config.php';?>
<?php include 'includes/functions.php';?>
<?php include 'views/header.php';?>

<?php $users = getAllUsers();?>

<main>
    <h2>Login</h2>
    <form action="authenticate.php" method="post">
        <label for="user_search">Cerca Nome:</label>
        <input type="text" id="user_search" name="user_search" oninput="filterUsers()">
        
        <label for="user_id">Seleziona Nome Utente:</label>
        <select id="user_id" name="user_id">
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="pin">PIN:</label>
        <input type="password" id="pin" name="pin" required>

        <label for="user_type">Tipo Utente:</label>
        <select id="user_type" name="user_type">
            <option value="customer">Cliente</option>
            <option value="admin">Amministratore</option>
        </select>
        
        <button type="submit">Accedi</button>
    </form>
</main>

<script>
function filterUsers() {
    var input = document.getElementById('user_search');
    var filter = input.value.toLowerCase();
    var select = document.getElementById('user_id');
    var options = select.getElementsByTagName('option');

    for (var i = 0; i < options.length; i++) {
        var txtValue = options[i].textContent || options[i].innerText;
        if (txtValue.toLowerCase().indexOf(filter) > -1) {
            options[i].style.display = "";
        } else {
            options[i].style.display = "none";
        }
    }
}
</script>

<?php include 'views/footer.php'; ?>

