<?php
// public/index.php
require_once __DIR__ . '/../config/database.php';

// Fetch Live Statistics
$studentCount = $pdo->query("SELECT COUNT(*) FROM Student")->fetchColumn();
$facultyCount = $pdo->query("SELECT COUNT(*) FROM Faculty")->fetchColumn();
$majorCount = $pdo->query("SELECT COUNT(*) FROM Major")->fetchColumn();

// Fetch 3 random majors for features
$stmt = $pdo->query("SELECT majorID, majorName FROM Major ORDER BY RAND() LIMIT 3");
$featuredMajors = $stmt->fetchAll();

require_once __DIR__ . '/../includes/public_header.php';
?>
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">Welcome to <span style="color: var(--secondary-color);">SUNY</span> University</h1>
            <p class="hero-subtitle mx-auto" style="max-width: 700px;">
                A world-class institution dedicated to shaping the leaders of tomorrow through rigorous academics, cutting-edge research, and a vibrant community.
            </p>
            
            <!-- Quick Stats -->
            <div class="row justify-content-center mt-5">
                <div class="col-4 col-md-2">
                    <h4 class="fw-bold mb-0"><?= number_format($studentCount) ?>+</h4>
                    <p class="text-muted small text-uppercase">Students</p>
                </div>
                <div class="col-4 col-md-2">
                    <h4 class="fw-bold mb-0"><?= number_format($facultyCount) ?>+</h4>
                    <p class="text-muted small text-uppercase">Faculty</p>
                </div>
                <div class="col-4 col-md-2">
                    <h4 class="fw-bold mb-0"><?= number_format($majorCount) ?>+</h4>
                    <p class="text-muted small text-uppercase">Majors</p>
                </div>
            </div>

            <div class="mt-5">
                <a href="/public/register.php" class="btn btn-primary btn-lg me-3 shadow-sm">Apply Now</a>
                <a href="/public/about.php" class="btn btn-outline-dark btn-lg">Learn More</a>
            </div>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold">Explore Our Programs</h2>
                <p class="text-muted">Discover the right path for your future career.</p>
            </div>
        </div>
        <div class="row text-center">
            <?php 
            $icons = ['🎓', '🔬', '🌍'];
            foreach ($featuredMajors as $index => $major): 
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 border-0 shadow-sm">
                    <div class="mb-3">
                        <span style="font-size: 2.5rem;"><?= $icons[$index % 3] ?></span>
                    </div>
                    <h3><?= htmlspecialchars($major['majorName']) ?></h3>
                    <p class="text-muted">Join one of our most popular departments and gain hands-on experience in the field of <?= htmlspecialchars($major['majorName']) ?>.</p>
                    <div class="mt-auto">
                        <a href="/public/program_detail.php?id=<?= htmlspecialchars($major['majorID']) ?>" class="btn btn-link text-decoration-none fw-bold" style="color: var(--secondary-color);">Explore &rarr;</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
