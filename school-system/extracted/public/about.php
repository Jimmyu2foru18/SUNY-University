<?php
// public/about.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/public_header.php';

try {
    $stmt = $pdo->query("SELECT * FROM About");
    $about = [];
    foreach ($stmt->fetchAll() as $row) {
        $about[$row['section']] = $row['content'];
    }
} catch (Exception $e) {
    die("Content currently unavailable.");
}
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold">About SUNY University</h1>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <h2 class="fw-bold mb-4">Our Mission</h2>
                <p><?= htmlspecialchars($about['Mission'] ?? 'No mission statement available.') ?></p>
            </div>
            <div class="col-lg-6 mb-4">
                <h2 class="fw-bold mb-4">Our Vision</h2>
                <p><?= htmlspecialchars($about['Vision'] ?? 'No vision statement available.') ?></p>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
