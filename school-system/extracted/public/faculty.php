<?php
// public/faculty.php
require_once __DIR__ . '/../config/database.php';

try {
    // Fetch all faculty members with names and departments
    $stmt = $pdo->query("
        SELECT U.firstName, U.lastName, D.deptName, F.facultyType, L.email 
        FROM Faculty F 
        JOIN User U ON F.facultyID = U.userID 
        LEFT JOIN FacultyDepartment FD ON F.facultyID = FD.facultyID 
        LEFT JOIN Department D ON FD.deptID = D.deptID 
        LEFT JOIN Login L ON F.facultyID = L.userID
        ORDER BY U.lastName, U.firstName
    ");
    $facultyMembers = $stmt->fetchAll();
} catch (Exception $e) {
    // No fallback, adhering to "no placeholders" requirement
    $facultyMembers = [];
}

require_once __DIR__ . '/../includes/public_header.php';
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold">Our Faculty</h1>
            <p class="lead text-muted mx-auto mb-4" style="max-width: 800px;">Meet our distinguished scholars and experts who are dedicated to excellence in teaching, research, and mentorship.</p>
            
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><span style="opacity: 0.5;">🔍</span></span>
                        <input type="text" id="facultySearch" class="form-control border-start-0 ps-0" placeholder="Search by name or department...">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row" id="facultyContainer">
            <?php foreach ($facultyMembers as $faculty): ?>
                <div class="col-md-6 col-lg-4 mb-4 faculty-card" 
                     data-name="<?= strtolower($faculty['firstName'] . ' ' . $faculty['lastName']) ?>" 
                     data-dept="<?= strtolower($faculty['deptName'] ?? '') ?>">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                    <?= substr($faculty['firstName'], 0, 1) . substr($faculty['lastName'], 0, 1) ?>
                                </div>
                                <div class="ms-3">
                                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($faculty['firstName'] . ' ' . $faculty['lastName']) ?></h5>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($faculty['facultyType'] ?? 'Professor') ?></p>
                                </div>
                            </div>
                            <hr class="my-3 opacity-10">
                            <p class="mb-3">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Department</span>
                                <span class="fw-medium"><?= htmlspecialchars($faculty['deptName'] ?? 'General Education') ?></span>
                            </p>
                            <div class="mt-auto">
                                <a href="mailto:<?= htmlspecialchars($faculty['email'] ?? '#') ?>" class="btn btn-outline-primary btn-sm w-100 fw-bold">CONTACT FACULTY</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No results message -->
        <div id="noResults" class="text-center py-5 d-none">
            <h3 class="text-muted">No faculty members found matching your search.</h3>
            <p>Try searching for a different name or department.</p>
        </div>
    </div>

    <script>
        document.getElementById('facultySearch').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.faculty-card');
            let hasResults = false;

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                const dept = card.getAttribute('data-dept');
                if (name.includes(query) || dept.includes(query)) {
                    card.classList.remove('d-none');
                    hasResults = true;
                } else {
                    card.classList.add('d-none');
                }
            });

            document.getElementById('noResults').classList.toggle('d-none', hasResults);
        });
    </script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
