<?php
// portal/admin/manage_table.php
require_once '../../config/database.php';
require_once '../../src/controllers/CrudController.php';
require_once '../../includes/portal_header.php';

if (!isset($_GET['table'])) {
    header("Location: index.php");
    exit();
}

$tableName = $_GET['table'];
$crud = new CrudController($pdo, $tableName);

// Dynamically find the primary key column
$stmt = $pdo->prepare("SHOW KEYS FROM {$tableName} WHERE Key_name = 'PRIMARY'");
$stmt->execute();
$pk = $stmt->fetch()['Column_name'];

// Handle Deletion
if (isset($_GET['delete'])) {
    $crud->delete($pk, $_GET['delete']);
    header("Location: manage_table.php?table={$tableName}");
    exit();
}

// Handle Form Submission (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    unset($data['id']); // Remove ID if present in POST
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $crud->update($data, $pk, $_POST['id']);
    } else {
        $crud->create($data);
    }
    header("Location: manage_table.php?table={$tableName}");
    exit();
}

// Fetch data
$data = $crud->getAll();
$columns = $data ? array_keys($data[0]) : [];

// Handle Edit Mode
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $crud->getById($pk, $_GET['edit']);
}
?>
    <a href="index.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <h2>Manage Table: <?= $tableName ?></h2>
    
    <h3><?= $editData ? 'Edit' : 'Add New' ?> Record</h3>
    <form method="POST" class="mb-4">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData[$pk] ?>">
        <?php endif; ?>
        <?php foreach ($columns as $column): ?>
            <div class="mb-2">
                <label class="form-label"><?= $column ?>:</label>
                <input type="text" name="<?= $column ?>" class="form-control" value="<?= $editData[$column] ?? '' ?>" <?= ($column == $pk && $editData) ? 'readonly' : '' ?> required>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary"><?= $editData ? 'Update' : 'Create' ?></button>
    </form>
    <hr>
    
    <h3>Existing Records</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <?php foreach ($columns as $column): ?>
                    <th><?= $column ?></th>
                <?php endforeach; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($row as $value): ?>
                        <td><?= $value ?></td>
                    <?php endforeach; ?>
                    <td>
                        <a href="manage_table.php?table=<?= $tableName ?>&edit=<?= $row[$pk] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="manage_table.php?table=<?= $tableName ?>&delete=<?= $row[$pk] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php require_once '../../includes/footer.php'; ?>
