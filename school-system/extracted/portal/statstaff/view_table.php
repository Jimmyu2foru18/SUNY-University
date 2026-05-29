<?php
// portal/statstaff/view_table.php
require_once '../../config/database.php';
require_once '../../src/controllers/BaseController.php';
require_once '../../includes/portal_header.php';

class StatStaffView extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['StatStaff']);
    }

    public function render() {
        $tableName = $_GET['table'] ?? '';
        // Basic whitelist for security
        $allowed = ['Student', 'Faculty', 'Department', 'CourseSection', 'Enrollment', 'Course', 'TimeSlot', 'Hold'];
        
        if (!in_array($tableName, $allowed)) {
            echo "Invalid table.";
            return;
        }

        $stmt = $this->pdo->query("SELECT * FROM $tableName");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columns = $data ? array_keys($data[0]) : [];
?>
    <div class="container my-5">
        <a href="index.php" class="btn btn-outline-secondary mb-3">&larr; Back to Dashboard</a>
        <h2 class="fw-bold mb-4">Viewing Table: <?= htmlspecialchars($tableName) ?></h2>
        
        <table class="table table-striped shadow-sm">
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
<?php
    }
}

$view = new StatStaffView($pdo);
$view->render();
require_once '../../includes/footer.php';
?>
