<?php
// public/register.php
session_start(); // Start session BEFORE any output
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';

$auth = new AuthController($pdo);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userData = [
        'userID' => $_POST['userID'],
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'userType' => $_POST['userType']
    ];
    $loginData = [
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ];

    if ($auth->register($userData, $loginData)) {
        header("Location: login.php?msg=registered");
        exit();
    } else {
        $message = "Registration failed. Please try again.";
    }
}

require_once __DIR__ . '/../includes/public_header.php';
?>
    <h2>Register</h2>
    <?php if ($message): ?><p><?php echo $message; ?></p><?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">User ID:</label>
            <input type="number" name="userID" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">First Name:</label>
            <input type="text" name="firstName" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Last Name:</label>
            <input type="text" name="lastName" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">User Type:</label>
            <select name="userType" class="form-control" required>
                <option value="Student">Student</option>
                <option value="Faculty">Faculty</option>
                <option value="Admin">Admin</option>
                <option value="StatStaff">StatStaff</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
