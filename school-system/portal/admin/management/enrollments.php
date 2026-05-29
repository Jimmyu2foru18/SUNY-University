<?php
// portal/admin/management/enrollments.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/AdminController.php';
require_once '../../../includes/portal_header.php';

$auth = new AdminController($pdo);

// Handle AJAX Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM Enrollment WHERE studentID = ? AND CRN = ?");
            $stmt->execute([$_POST['studentID'], $_POST['CRN']]);
            echo json_encode(['ok' => true, 'message' => 'Enrollment removed.']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Pagination Setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

// 1. Get total count
$countStmt = $pdo->query("SELECT COUNT(*) FROM Enrollment");
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// 2. Fetch Data with Pagination
$stmt = $pdo->prepare("
    SELECT E.studentID, E.CRN, E.grade, U.firstName, U.lastName, C.courseName
    FROM (SELECT studentID, CRN, grade FROM Enrollment ORDER BY studentID ASC LIMIT :offset, :limit) E
    JOIN Student S ON E.studentID = S.studentID
    JOIN User U ON S.studentID = U.userID
    JOIN CourseSection CS ON E.CRN = CS.CRN
    JOIN Course C ON CS.courseID = C.courseID
");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Manage Enrollments</h1>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <input type="text" id="enrollmentSearch" class="form-control" placeholder="Search enrollments (Student Name, Course, CRN)...">
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="enrollmentTable">
                        <thead class="table-light">
                            <tr><th>Student ID</th><th>Name</th><th>Course</th><th>CRN</th><th>Grade</th><th>Actions</th></tr>
                        </thead>
                        <tbody id="enrollmentBody">
                            <?php foreach ($data as $d): ?>
                                <tr class="enrollment-row">
                                    <td><strong><?= htmlspecialchars($d['studentID']) ?></strong></td>
                                    <td><?= htmlspecialchars($d['firstName'] . ' ' . $d['lastName']) ?></td>
                                    <td><?= htmlspecialchars($d['courseName']) ?></td>
                                    <td><?= htmlspecialchars($d['CRN']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($d['grade'] ?: 'IP') ?></span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteEnrollment('<?= htmlspecialchars($d['studentID']) ?>', '<?= htmlspecialchars($d['CRN']) ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination Controls -->
            <nav class="mt-4">
                <ul class="pagination">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                    <li class="page-item disabled"><span class="page-link">Page <?= $page ?> of <?= $totalPages ?></span></li>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </main>
</div>

<script>
function deleteEnrollment(studentID, CRN) {
    if (!confirm('Are you sure?')) return;
    fetch('enrollments.php', { 
        method: 'POST', 
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}, 
        body: new URLSearchParams({ action: 'delete', studentID: studentID, CRN: CRN }) 
    })
    .then(r => r.json()).then(res => { alert(res.message); if (res.ok) location.reload(); });
}

document.getElementById('enrollmentSearch').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.enrollment-row').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
    });
});
</script>
<?php require_once '../../../includes/footer.php'; ?>
