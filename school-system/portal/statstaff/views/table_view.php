<?php
// portal/statstaff/views/table_view.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['StatStaff']);

$tableName = $_GET['table'] ?? '';
// Validate against all tables in the database
$stmt = $pdo->query("SHOW TABLES");
$allowedTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!in_array($tableName, $allowedTables)) {
    $auth->renderError("Table Not Found", "The requested table does not exist.");
}

// Fetch data
$stmt = $pdo->query("SELECT * FROM `$tableName`");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$columns = $data ? array_keys($data[0]) : [];
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">View Data: <?= htmlspecialchars($tableName) ?></h2>
    
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($row as $val): ?>
                                <td><?= htmlspecialchars($val) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
