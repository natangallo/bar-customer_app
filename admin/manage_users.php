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

$users = getAllUsers(); // Recupera tutti gli utenti, eccetto l'admin

// Recupera le stanze, i gruppi e le settimane dal database
$rooms = $db->query("SELECT DISTINCT room_number FROM users")->fetchAll(PDO::FETCH_ASSOC);
$groups = $db->query("SELECT DISTINCT group_name FROM users")->fetchAll(PDO::FETCH_ASSOC);
$weeks = $db->query("SELECT DISTINCT week_date FROM users")->fetchAll(PDO::FETCH_ASSOC);

include 'views/header.php';
?>
<main>
    <h2>Gestione Utenti</h2>
    
    <h3>Filtra Utenti</h3>
    <form id="filter-form">
        <label for="name-filter">Nome:</label>
        <input type="text" id="name-filter" name="name" placeholder="Nome">
        
        <label for="status-filter">Stato:</label>
        <select id="status-filter" name="status">
            <option value="">Tutti</option>
            <option value="active">Attivo</option>
            <option value="pending">In Attesa</option>
        </select>
    </form>
    
    <button id="approve-all-btn" onclick="approveAllUsers()">Approva Tutti gli Utenti Non Attivi</button>
    
    <h3>Utenti Esistenti</h3>
    <table>
        <thead>
            <tr>
                <th data-sort="name">Nome <span>&#9660;</span></th>
                <th data-sort="status">Stato <span>&#9660;</span></th>
                <th data-sort="balance">Credito Disponibile <span>&#9660;</span></th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody id="user-table-body">
            <?php foreach ($users as $user): ?>
                <?php if ($user['id'] !== 5): // Ignora l'admin ?>
                    <tr data-name="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" data-status="<?php echo htmlspecialchars($user['status']); ?>">
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['status']); ?></td>
                        <td><?php echo htmlspecialchars($user['balance']); ?> €</td>
                        <td>
                            <button class="edit-balance-btn" data-id="<?php echo $user['id']; ?>">Modifica Credito</button>
                            <form class="edit-form" id="edit-form-<?php echo $user['id']; ?>" action="update_user_balance.php" method="post" style="display:none;">
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <label for="balance-<?php echo $user['id']; ?>">Credito:</label>
                                <input type="number" id="balance-<?php echo $user['id']; ?>" name="balance" required>
                                <button type="submit">Aggiorna Credito</button>
                            </form>
                            <button class="approve-user-btn" data-id="<?php echo $user['id']; ?>">Approva</button>
                            <a href="user_dashboard.php?id=<?php echo $user['id']; ?>">Dashboard</a>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Aggiungi Nuovo Utente</h3>
    <form action="add_users.php" method="post">
        <div style="display: flex; gap: 10px;">
            <div>
                <label for="first_name">Nome:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div>
                <label for="last_name">Cognome:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
        </div>
        <label for="room_number">Stanza:</label>
        <select id="room_number" name="room_number" required>
            <option value="">Seleziona una stanza</option>
            <?php foreach ($rooms as $room): ?>
                <option value="<?php echo htmlspecialchars($room['room_number']); ?>"><?php echo htmlspecialchars($room['room_number']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="group_name">Gruppo:</label>
        <select id="group_name" name="group_name" required>
            <option value="">Seleziona un gruppo</option>
            <?php foreach ($groups as $group): ?>
                <option value="<?php echo htmlspecialchars($group['group_name']); ?>"><?php echo htmlspecialchars($group['group_name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="week_date">Settimana:</label>
        <select id="week_date" name="week_date" required>
            <option value="">Seleziona una settimana</option>
            <?php foreach ($weeks as $week): ?>
                <option value="<?php echo htmlspecialchars($week['week_date']); ?>"><?php echo htmlspecialchars($week['week_date']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Aggiungi Utente</button>
    </form>
</main>

<?php include 'views/footer.php'; ?>

<script>
    document.getElementById('name-filter').addEventListener('input', filterUsers);
    document.getElementById('status-filter').addEventListener('change', filterUsers);

    function filterUsers() {
        const nameInput = document.getElementById('name-filter').value.toLowerCase();
        const statusSelect = document.getElementById('status-filter').value;
        
        const rows = document.querySelectorAll('#user-table-body tr');
        rows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const status = row.dataset.status;

            const nameMatch = name.includes(nameInput);
            const statusMatch = !statusSelect || status === statusSelect;

            row.style.display = nameMatch && statusMatch ? '' : 'none';
        });
    }

    function approveAllUsers() {
        const userIds = [];
        const rows = document.querySelectorAll('#user-table-body tr');

        rows.forEach(row => {
            if (row.dataset.status !== 'active') {
                userIds.push(row.querySelector('.approve-user-btn').dataset.id);
            }
        });

        fetch('approve_users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_ids: userIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                rows.forEach(row => {
                    if (userIds.includes(row.querySelector('.approve-user-btn').dataset.id)) {
                        row.dataset.status = 'active';
                        row.querySelector('td:nth-child(2)').textContent = 'active';
                    }
                });
            }
        });
    }

    document.querySelectorAll('.edit-balance-btn').forEach(button => {
        button.addEventListener('click', function() {
            const form = document.getElementById('edit-form-' + this.getAttribute('data-id'));
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
    });

    document.querySelectorAll('.approve-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            fetch('update_user_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = this.closest('tr');
                    row.dataset.status = 'active';
                    row.querySelector('td:nth-child(2)').textContent = 'active';
                }
            });
        });
    });

    document.querySelectorAll('th[data-sort]').forEach(header => {
        header.addEventListener('click', function() {
            const tableBody = document.getElementById('user-table-body');
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            const index = Array.from(header.parentNode.children).indexOf(header);
            const isAscending = header.classList.toggle('ascending');
            const sortType = header.dataset.sort;

            rows.sort((a, b) => {
                let aText, bText;

                switch (sortType) {
                    case 'name':
                        aText = a.dataset.name.toLowerCase();
                        bText = b.dataset.name.toLowerCase();
                        break;
                    case 'status':
                        aText = a.dataset.status.toLowerCase();
                        bText = b.dataset.status.toLowerCase();
                        break;
                    case 'balance':
                        aText = parseFloat(a.querySelector('td:nth-child(3)').textContent);
                        bText = parseFloat(b.querySelector('td:nth-child(3)').textContent);
                        break;
                    default:
                        return 0;
                }

                if (aText < bText) return isAscending ? -1 : 1;
                if (aText > bText) return isAscending ? 1 : -1;
                return 0;
            });

            header.querySelector('span').textContent = isAscending ? '▲' : '▼';
            tableBody.append(...rows);
        });
    });
</script>
