<?php
// public/contact.php
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM SiteInfo");
    $info = [];
    foreach ($stmt->fetchAll() as $row) {
        $info[$row['keyName']] = $row['value'];
    }
} catch (Exception $e) {
    $info = [];
}

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    try {
        $stmt = $pdo->prepare("INSERT INTO ContactMessage (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]);
        $success = true;
    } catch (Exception $e) {
        // Log error or handle gracefully
    }
}

require_once __DIR__ . '/../includes/public_header.php';
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold">Contact Us</h1>
            <p class="lead text-muted mx-auto" style="max-width: 800px;">Have questions? We're here to help. Reach out to our team for any inquiries or support.</p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-7 mb-5">
                <div class="card p-4 p-md-5 border-0 shadow-sm">
                    <?php if ($success): ?>
                        <div class="alert alert-success mb-4" role="alert">
                            <h4 class="alert-heading">Message Sent!</h4>
                            <p>Thank you for reaching out. We have received your message and will get back to you shortly.</p>
                        </div>
                    <?php endif; ?>

                    <h2 class="fw-bold mb-4">Send a Message</h2>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Your Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Jane Doe" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="jane@example.com" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="General Inquiry" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Message</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="How can we help you?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4 offset-lg-1">
                <div class="mb-5">
                    <h4 class="fw-bold">Contact Information</h4>
                    <p class="text-muted mb-4">Feel free to contact us through any of the channels below.</p>
                    <div class="mb-3">
                        <strong>Address:</strong><br>
                        <?= htmlspecialchars($info['address'] ?? 'N/A') ?>
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        <?= htmlspecialchars($info['phone'] ?? 'N/A') ?>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <?= htmlspecialchars($info['email'] ?? 'N/A') ?>
                    </div>
                </div>
                <div>
                    <h4 class="fw-bold">Office Hours</h4>
                    <p class="text-muted"><?= htmlspecialchars($info['hours'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
