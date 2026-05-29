<?php
// public/program_detail.php
require_once __DIR__ . '/../config/database.php';

$majorID = $_GET['id'] ?? null;

if (!$majorID) {
    header("Location: programs.php");
    exit();
}

try {
    // Fetch Major info
    $stmt = $pdo->prepare("SELECT * FROM Major WHERE majorID = :id");
    $stmt->execute(['id' => $majorID]);
    $major = $stmt->fetch();

    if (!$major) {
        header("Location: programs.php");
        exit();
    }

    // Fetch Required Courses
    $stmt = $pdo->prepare("
        SELECT C.courseID, C.courseName, C.credits 
        FROM Course C
        JOIN MajorRequirement MR ON C.courseID = MR.courseID
        WHERE MR.majorID = :id
    ");
    $stmt->execute(['id' => $majorID]);
    $courses = $stmt->fetchAll();

} catch (Exception $e) {
    die("Error fetching program details.");
}

require_once __DIR__ . '/../includes/public_header.php';
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold"><?= htmlspecialchars($major['majorName']) ?></h1>
            <p class="lead text-muted mx-auto" style="max-width: 800px;">Explore the requirements and foundational courses for this degree program.</p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="card shadow-sm border-0 p-4">
            <h3 class="fw-bold mb-4">Required Courses</h3>
            <?php if (count($courses) > 0): ?>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Course ID</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['courseID']) ?></td>
                                <td><?= htmlspecialchars($course['courseName']) ?></td>
                                <td><?= htmlspecialchars($course['credits']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No specific course requirements listed in the database.</p>
            <?php endif; ?>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
