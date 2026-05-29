<?php
// public/research.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/public_header.php';

try {
    $stmt = $pdo->query("
        SELECT R.title, R.description, U.firstName, U.lastName 
        FROM Research R 
        JOIN User U ON R.facultyID = U.userID
    ");
    $researchProjects = $stmt->fetchAll();
} catch (Exception $e) {
    $researchProjects = [];
}
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold">Research at SUNY</h1>
            <p class="lead text-muted mx-auto" style="max-width: 800px;">Our faculty and students are pushing boundaries in innovation, technology, and social progress.</p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row">
            <?php foreach ($researchProjects as $project): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 p-4 border-0 shadow-sm">
                        <h3 class="fw-bold"><?= htmlspecialchars($project['title']) ?></h3>
                        <p class="text-muted"><?= htmlspecialchars($project['description']) ?></p>
                        <p class="small text-muted fw-bold">Lead Researcher: <?= htmlspecialchars($project['firstName'] . ' ' . $project['lastName']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
