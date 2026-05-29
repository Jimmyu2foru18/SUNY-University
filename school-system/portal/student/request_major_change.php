<?php
// portal/student/request_major_change.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start(); // Must be first!
require_once '../../config/database.php';
require_once '../../includes/portal_header.php';

$studentID = $_SESSION['user_id'] ?? null;
if (!$studentID) {
    header("Location: ../../public/login.php");
    exit;
}


// Handle Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO MajorChangeRequest (studentID, newMajorID) VALUES (?, ?)");
    $stmt->execute([$studentID, $_POST['newMajorID']]);
    $message = "Major change request submitted successfully.";
}

// Fetch Majors
$majors = $pdo->query("SELECT majorID, majorName FROM Major")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php $currentPage = 'profile'; include '../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Request Major Change</h1>
            <?php if (isset($message)): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <div class="card p-4 shadow-sm border-0">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">New Major</label>
                        <select name="newMajorID" class="form-select" required>
                            <?php foreach ($majors as $m): ?>
                                <option value="<?= htmlspecialchars($m['majorID']) ?>"><?= htmlspecialchars($m['majorName']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once '../../includes/footer.php'; ?>
