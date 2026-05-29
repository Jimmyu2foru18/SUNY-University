<?php
// portal/student/views/update_profile.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$studentId = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];

    try {
        $pdo->beginTransaction();

        // Update User table
        $stmt = $pdo->prepare("UPDATE User SET firstName = :first, lastName = :last WHERE userID = :id");
        $stmt->execute(['first' => $firstName, 'last' => $lastName, 'id' => $studentId]);

        // Update Login table
        $stmt = $pdo->prepare("UPDATE Login SET email = :email WHERE userID = :id");
        $stmt->execute(['email' => $email, 'id' => $studentId]);

        $pdo->commit();
        $message = "Profile updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error updating profile: " . $e->getMessage();
    }
}

// Fetch current info
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, L.email 
    FROM User U 
    JOIN Login L ON U.userID = L.userID 
    WHERE U.userID = :id
");
$stmt->execute(['id' => $studentId]);
$user = $stmt->fetch();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Update Profile</h2>

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>" role="alert">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm border-0">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" name="firstName" class="form-control" value="<?= htmlspecialchars($user['firstName']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" name="lastName" class="form-control" value="<?= htmlspecialchars($user['lastName']) ?>" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
