<?php
// portal/profile.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config/database.php';
require_once '../src/controllers/BaseController.php';
require_once '../includes/portal_header.php';

// Auth Check (using BaseController for basic auth)
$auth = new class($pdo) extends BaseController { 
    public function __construct($pdo) { parent::__construct($pdo); } 
};
$userID = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];

$message = '';
$error = '';

// Handle Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE User SET addressLine1 = ?, city = ?, state = ?, zipCode = ? WHERE userID = ?");
        $stmt->execute([$_POST['addressLine1'], $_POST['city'], $_POST['state'], $_POST['zipCode'], $userID]);
        $message = "Profile updated successfully.";
    } catch (Exception $e) {
        $error = "Failed to update profile: " . $e->getMessage();
    }
}

// Fetch Data
$stmt = $pdo->prepare("SELECT U.*, L.email FROM User U JOIN Login L ON U.userID = L.userID WHERE U.userID = :id");
$stmt->execute(['id' => $userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1 class="fw-bold mb-4">My Profile</h1>
            
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>

            <div class="card p-4 shadow-sm border-0 mb-4">
                <h3 class="fw-bold">Account Information</h3>
                <hr>
                <div class="row">
                    <div class="col-md-4"><p><strong>Name:</strong><br> <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></p></div>
                    <div class="col-md-4"><p><strong>Email:</strong><br> <?= htmlspecialchars($user['email']); ?></p></div>
                    <div class="col-md-4"><p><strong>Type:</strong><br> <?= htmlspecialchars($user['userType']); ?></p></div>
                </div>
            </div>

            <div class="card p-4 shadow-sm border-0">
                <h3 class="fw-bold">Contact Information</h3>
                <hr>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Street Address</label>
                        <input type="text" name="addressLine1" class="form-control" value="<?= htmlspecialchars($user['addressLine1'] ?? ''); ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">State</label>
                            <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($user['state'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Zip Code</label>
                            <input type="text" name="zipCode" class="form-control" value="<?= htmlspecialchars($user['zipCode'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once '../includes/footer.php'; ?>
