<?php
// portal/admin/management/approve_major_changes.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
session_start(); // Ensure session is started
require_once '../../../includes/portal_header.php';

// Handle Approvals
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reqID = $_POST['requestID'];
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT studentID, newMajorID FROM MajorChangeRequest WHERE requestID = ?");
        $stmt->execute([$reqID]);
        $req = $stmt->fetch();
        
        $pdo->prepare("DELETE FROM StudentMajor WHERE studentID = ?")->execute([$req['studentID']]);
        $pdo->prepare("INSERT INTO StudentMajor (studentID, majorID) VALUES (?, ?)")->execute([$req['studentID'], $req['newMajorID']]);
        $pdo->prepare("UPDATE MajorChangeRequest SET status = 'Approved' WHERE requestID = ?")->execute([$reqID]);
        $pdo->commit();
    } else {
        $pdo->prepare("UPDATE MajorChangeRequest SET status = 'Rejected' WHERE requestID = ?")->execute([$reqID]);
    }
}

// Fetch Requests
$stmt = $pdo->query("
    SELECT MCR.*, U.firstName, U.lastName, M.majorName
    FROM MajorChangeRequest MCR
    JOIN User U ON MCR.studentID = U.userID
    JOIN Major M ON MCR.newMajorID = M.majorID
    WHERE MCR.status = 'Pending'
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentPage = 'approvals'; // Correct page ID for sidebar
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Approve Major Changes</h1>
            <div class="card border-0 shadow-sm">
                <table class="table">
                    <thead><tr><th>Student</th><th>New Major</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['firstName'].' '.$r['lastName']) ?></td>
                                <td><?= htmlspecialchars($r['majorName']) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="requestID" value="<?= $r['requestID'] ?>">
                                        <button name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                        <button name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php require_once '../../../includes/footer.php'; ?>
