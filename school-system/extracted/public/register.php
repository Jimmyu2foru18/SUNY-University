<?php
// public/register.php
session_start();
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
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card p-4 p-md-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">Begin Your Journey</h2>
                        <p class="text-muted">Join the SUNY University community today</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" name="firstName" class="form-control" placeholder="John" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" name="lastName" class="form-control" placeholder="Doe" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">University ID</label>
                            <input type="number" name="userID" class="form-control" placeholder="12345678" required>
                            <div class="form-text text-muted small">Your unique student or staff identification number.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="john.doe@example.com" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-semibold">Account Type</label>
                            <select name="userType" class="form-select" required>
                                <option value="" disabled selected>Select your role...</option>
                                <option value="Student">Student</option>
                                <option value="Faculty">Faculty</option>
                                <option value="Admin">Admin</option>
                                <option value="StatStaff">Statistical Staff</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <p class="text-muted small">Already have an account? <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--secondary-color);">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
