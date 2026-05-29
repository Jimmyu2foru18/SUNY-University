<?php
// portal/statstaff/views/holds.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/StatStaffController.php';
require_once '../../../includes/portal_header.php';

$auth = new StatStaffController($pdo);

// Fetch Student Holds with student and hold details
$stmt = $pdo->query("
    SELECT U.firstName, U.lastName, S.studentID, H.holdID, H.holdDescription 
    FROM StudentHold SH
    JOIN Student S ON SH.studentID = S.studentID
    JOIN User U ON S.studentID = U.userID
    JOIN Hold H ON SH.holdID = H.holdID
    ORDER BY U.lastName, U.firstName
");
$data = $stmt->fetchAll();
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
            <h2 class="fw-bold">View Student Holds</h2>
        </div>
        <input type="text" id="search" class="form-control w-25" placeholder="Search...">
    </div>
    <div class="card border-0 shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Student Name</th><th>Student ID</th><th>Hold ID</th><th>Description</th></tr>
            </thead>
            <tbody id="tableBody">
                <?php foreach ($data as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['firstName'] . ' ' . $d['lastName']) ?></td>
                        <td><?= htmlspecialchars($d['studentID']) ?></td>
                        <td><?= htmlspecialchars($d['holdID']) ?></td>
                        <td><?= htmlspecialchars($d['holdDescription']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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
