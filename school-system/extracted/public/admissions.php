<?php
// public/admissions.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/public_header.php';

try {
    $stmt = $pdo->query("SELECT * FROM AdmissionsProcess ORDER BY stepNumber ASC");
    $steps = $stmt->fetchAll();
} catch (Exception $e) {
    $steps = [];
}
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold">Admissions</h1>
            <p class="lead text-muted mx-auto" style="max-width: 800px;">Your future starts here. Join a community of thinkers, creators, and leaders at SUNY University.</p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <h2 class="fw-bold mb-4">How to Apply</h2>
                <ol class="list-group list-group-flush mb-4">
                    <?php foreach ($steps as $step): ?>
                        <li class="list-group-item bg-transparent px-0 py-3">
                            <span class="badge bg-primary rounded-pill me-2"><?= htmlspecialchars($step['stepNumber']) ?></span>
                            <strong><?= htmlspecialchars($step['title']) ?></strong> - <?= htmlspecialchars($step['description']) ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
                <a href="/public/register.php" class="btn btn-primary btn-lg">Start Your Application</a>
            </div>
            <div class="col-lg-5 offset-lg-1">
                <div class="card p-4 border-0 shadow-sm bg-primary text-white">
                    <h3 class="fw-bold mb-3">Important Dates</h3>
                    <p>Refer to the portal for specific deadline information.</p>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
