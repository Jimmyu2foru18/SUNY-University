<?php
// portal/statstaff/views/sections.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/StatStaffController.php';
require_once '../../../includes/portal_header.php';

$auth = new StatStaffController($pdo);

$stmt = $pdo->query("
    SELECT CS.CRN, CS.courseID, C.courseName 
    FROM CourseSection CS
    JOIN Course C ON CS.courseID = C.courseID
");
$data = $stmt->fetchAll();
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
            <h2 class="fw-bold">View Sections</h2>
        </div>
        <input type="text" id="search" class="form-control w-25" placeholder="Search...">
    </div>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>CRN</th><th>Course ID</th><th>Course Name</th></tr>
        </thead>
        <tbody id="tableBody">
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['CRN']) ?></td>
                    <td><?= htmlspecialchars($d['courseID']) ?></td>
                    <td><?= htmlspecialchars($d['courseName']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    document.getElementById('search').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('#tableBody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
        });
    });
</script>
<?php require_once '../../../includes/footer.php'; ?>
