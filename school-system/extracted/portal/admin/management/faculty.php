<?php
// portal/admin/management/faculty.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Admin']);

$stmt = $pdo->query("
    SELECT F.facultyID, U.firstName, U.lastName, F.facultyType, L.email 
    FROM Faculty F 
    JOIN User U ON F.facultyID = U.userID 
    JOIN Login L ON F.facultyID = L.userID
    ORDER BY U.lastName, U.firstName
");
$faculty = $stmt->fetchAll();
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
            <h2 class="fw-bold">Manage Faculty</h2>
        </div>
        <div style="width: 300px;">
            <input type="text" id="adminFacultySearch" class="form-control" placeholder="Search faculty...">
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Rank</th><th>Actions</th></tr>
            </thead>
            <tbody id="facultyTableBody">
                <?php foreach ($faculty as $f): ?>
                    <tr class="faculty-row" data-search="<?= strtolower($f['facultyID'] . ' ' . $f['firstName'] . ' ' . $f['lastName'] . ' ' . $f['email']) ?>">
                        <td><?= htmlspecialchars($f['facultyID']) ?></td>
                        <td><?= htmlspecialchars($f['firstName'] . ' ' . $f['lastName']) ?></td>
                        <td><?= htmlspecialchars($f['email']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($f['facultyType']) ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-outline-secondary">Schedule</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('adminFacultySearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.faculty-row').forEach(row => {
            const text = row.getAttribute('data-search');
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
</script>
<?php require_once '../../../includes/footer.php'; ?>
