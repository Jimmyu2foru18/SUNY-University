<?php
// public/programs.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/public_header.php';

try {
    // Fetch all majors with department info
    $stmt = $pdo->query("
        SELECT M.majorID, M.majorName, D.departmentName, M.creditsRequired
        FROM Major M 
        JOIN Department D ON M.departmentID = D.departmentID 
        ORDER BY D.departmentName, M.majorName
    ");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by department
    $groupedPrograms = [];
    foreach ($programs as $p) {
        $groupedPrograms[$p['departmentName']][] = $p;
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<section class="py-5 bg-light border-bottom mb-5">
    <div class="container text-center py-5">
        <h1 class="display-4 fw-bold">Academic Programs</h1>
        <p class="lead text-muted mx-auto mb-4" style="max-width: 800px;">Explore our diverse range of undergraduate and graduate programs designed to prepare you for the challenges of the future.</p>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><span style="opacity: 0.5;">🔍</span></span>
                    <input type="text" id="programSearch" class="form-control border-start-0 ps-0" placeholder="Search for a major or department...">
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mb-5">
    <div id="programContainer">
        <?php foreach ($groupedPrograms as $dept => $majors): ?>
            <div class="dept-section mb-5">
                <h2 class="fw-bold mb-4 border-bottom pb-2"><?= htmlspecialchars($dept) ?></h2>
                <div class="row">
                    <?php foreach ($majors as $major): ?>
                        <div class="col-md-6 col-lg-4 mb-4 program-card" data-major="<?= strtolower($major['majorName']) ?>" data-dept="<?= strtolower($dept) ?>">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <div class="p-4">
                                    <div class="mb-2">
                                        <span class="badge bg-light text-dark fw-normal border"><?= htmlspecialchars($dept) ?></span>
                                    </div>
                                    <h4 class="fw-bold"><?= htmlspecialchars($major['majorName']) ?></h4>
                                    <p class="text-muted small"><strong>Credits Required:</strong> <?= htmlspecialchars($major['creditsRequired']) ?></p>
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

    <!-- No results message -->
    <div id="noResults" class="text-center py-5 d-none">
        <h3 class="text-muted">No programs found matching your search.</h3>
        <p>Try searching for a different keyword or department.</p>
    </div>
</div>

<script>
    document.getElementById('programSearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.program-card');
        const sections = document.querySelectorAll('.dept-section');
        let hasResults = false;

        cards.forEach(card => {
            const major = card.getAttribute('data-major');
            const dept = card.getAttribute('data-dept');
            if (major.includes(query) || dept.includes(query)) {
                card.classList.remove('d-none');
                hasResults = true;
            } else {
                card.classList.add('d-none');
            }
        });

        // Hide/show sections based on visibility of cards within them
        sections.forEach(section => {
            const visibleCards = section.querySelectorAll('.program-card:not(.d-none)');
            if (visibleCards.length === 0) {
                section.classList.add('d-none');
            } else {
                section.classList.remove('d-none');
            }
        });

        document.getElementById('noResults').classList.toggle('d-none', hasResults);
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
