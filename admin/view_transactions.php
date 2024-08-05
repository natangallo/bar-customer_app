<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Query per ottenere le transazioni ordinate dalla più recente alla più vecchia
// e per ottenere il nome e cognome degli utenti
$transactionsQuery = "
    SELECT t.*, u.first_name, u.last_name 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY transaction_date DESC
";

$transactions = $db->query($transactionsQuery)->fetchAll(PDO::FETCH_ASSOC);

include 'views/header.php';
?>

<main>
    <h2>Storico Transazioni</h2>
    <table id="transactions-table" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black; padding: 8px; cursor: pointer;" onclick="sortTable(0)">Data <span id="date-arrow">▼</span></th>
                <th style="border: 1px solid black; padding: 8px; cursor: pointer;" onclick="toggleFilter('description-filter')">Descrizione <span id="description-arrow">▼</span></th>
                <th style="border: 1px solid black; padding: 8px; cursor: pointer;" onclick="sortTable(2)">Ammontare (€) <span id="amount-arrow">▼</span></th>
                <th style="border: 1px solid black; padding: 8px; cursor: pointer;" onclick="toggleFilter('user-filter')">Nome Utente <span id="user-arrow">▼</span></th>
            </tr>
            <!-- Filtro per descrizione -->
            <tr id="description-filter" style="display: none; border: 1px solid black;">
                <td colspan="4" style="border: 1px solid black; padding: 8px;">
                    <select id="description-select" multiple style="width: 100%;" onchange="filterTable('description-select', 1)">
                        <?php
                        $descriptions = array_unique(array_column($transactions, 'description'));
                        foreach ($descriptions as $description) {
                            echo "<option value='".htmlspecialchars($description)."'>".htmlspecialchars($description)."</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <!-- Filtro per nome utente -->
            <tr id="user-filter" style="display: none; border: 1px solid black;">
                <td colspan="4" style="border: 1px solid black; padding: 8px;">
                    <select id="user-select" multiple style="width: 100%;" onchange="filterTable('user-select', 3)">
                        <?php
                        $users = array_unique(array_map(function($transaction) {
                            return $transaction['first_name'] . ' ' . $transaction['last_name'];
                        }, $transactions));
                        foreach ($users as $user) {
                            echo "<option value='".htmlspecialchars($user)."'>".htmlspecialchars($user)."</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black; padding: 8px;"><?php echo date('d-m-Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                    <td style="border: 1px solid black; padding: 8px;"><?php echo htmlspecialchars($transaction['description']); ?></td>
                    <td style="border: 1px solid black; padding: 8px;"><?php echo htmlspecialchars($transaction['amount']); ?> €</td>
                    <td style="border: 1px solid black; padding: 8px;"><?php echo htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include 'views/footer.php'; ?>

<script>
    // Funzione per ordinare la tabella
    function sortTable(columnIndex) {
        const table = document.getElementById('transactions-table');
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);
        const arrowIds = ['date-arrow', 'description-arrow', 'amount-arrow', 'user-arrow'];
        const columnArrows = {
            0: arrowIds[0],
            1: arrowIds[1],
            2: arrowIds[2],
            3: arrowIds[3]
        };
        
        let asc = true;

        if (table.dataset.sortColumn == columnIndex) {
            asc = table.dataset.sortOrder !== 'asc';
        }

        rows.sort((a, b) => {
            const cellA = a.cells[columnIndex].textContent.trim();
            const cellB = b.cells[columnIndex].textContent.trim();

            if (!isNaN(cellA) && !isNaN(cellB)) {
                return asc ? cellA - cellB : cellB - cellA;
            } else {
                return asc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            }
        });

        rows.forEach(row => tbody.appendChild(row));

        table.dataset.sortColumn = columnIndex;
        table.dataset.sortOrder = asc ? 'asc' : 'desc';

        arrowIds.forEach(id => document.getElementById(id).textContent = '▼');
        document.getElementById(columnArrows[columnIndex]).textContent = asc ? '▲' : '▼';
    }

    // Funzione per filtrare la tabella
    function filterTable(selectId, columnIndex) {
        const table = document.getElementById('transactions-table');
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);
        const selectedOptions = Array.from(document.getElementById(selectId).selectedOptions).map(option => option.value);

        rows.forEach(row => {
            const cellValue = row.cells[columnIndex].textContent.trim();
            row.style.display = selectedOptions.length === 0 || selectedOptions.includes(cellValue) ? '' : 'none';
        });
    }

    // Funzione per visualizzare o nascondere i filtri
    function toggleFilter(filterId) {
        const filterRow = document.getElementById(filterId);
        filterRow.style.display = filterRow.style.display === 'none' ? 'table-row' : 'none';
    }
</script>

<style>
    th span {
        font-size: 0.8em;
        margin-left: 5px;
    }
</style>
