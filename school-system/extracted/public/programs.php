<?php
// public/programs.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/public_header.php';

try {
    // Fetch all majors with department info
    $stmt = $pdo->query("
        SELECT M.majorID, M.majorName, D.deptName 
        FROM Major M 
        JOIN Department D ON M.deptID = D.deptID 
        ORDER BY D.deptName, M.majorName
    ");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by department
    $groupedPrograms = [];
    foreach ($programs as $p) {
        $groupedPrograms[$p['deptName']][] = $p;
    }
} catch (Exception $e) {
    die("Error retrieving program data: " . $e->getMessage());
}
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold">Academic Programs</h1>
            <p class="lead text-muted mx-auto mb-4" style="max-width: 800px;">Explore our diverse range of undergraduate and graduate programs.</p>
        </div>
    </section>

    <div class="container mb-5">
        <?php foreach ($groupedPrograms as $dept => $majors): ?>
            <div class="dept-section mb-5">
                <h2 class="fw-bold mb-4 border-bottom pb-2"><?= htmlspecialchars($dept) ?></h2>
                <div class="row">
                    <?php foreach ($majors as $major): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <div class="p-4">
                                    <h4 class="fw-bold"><?= htmlspecialchars($major['majorName']) ?></h4>
                                    <div class="mt-3">
                                        <a href="/public/program_detail.php?id=<?= htmlspecialchars($major['majorID']) ?>" class="text-decoration-none fw-bold small" style="color: var(--secondary-color);">PROGRAM DETAILS &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
