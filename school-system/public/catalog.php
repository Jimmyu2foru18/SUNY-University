<?php
// public/catalog.php
require_once __DIR__ . '/../config/database.php';

// Context setup for dual layout (Public vs. Portal)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch Courses with aggregated prerequisites, sections, and enrollment info
try {
    $stmt = $pdo->query("
        SELECT CS.CRN, C.courseID, C.courseName, C.courseDescription, C.credits, 
               CS.semesterID, CS.capacity, CS.roomID,
               (SELECT COUNT(*) FROM Enrollment E WHERE E.CRN = CS.CRN) as enrolled,
               U.firstName as instrFirst, U.lastName as instrLast,
               (SELECT GROUP_CONCAT(prerequisiteID) FROM CoursePrerequisite CP WHERE CP.courseID = C.courseID) as prerequisites
        FROM CourseSection CS
        JOIN Course C ON CS.courseID = C.courseID
        JOIN User U ON CS.facultyID = U.userID
        ORDER BY C.courseID
    ");
    $rawSections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Transform for JS Template
    $courses = [];
    foreach ($rawSections as $s) {
        $courses[] = [
            'code' => $s['courseID'],
            'title' => $s['courseName'],
            'department' => 'N/A', // Assuming standard data mapping if possible
            'departmentId' => 'all',
            'level' => (int)filter_var($s['courseID'], FILTER_SANITIZE_NUMBER_INT),
            'credits' => (int)$s['credits'],
            'semester' => [strtolower($s['semesterID'])],
            'description' => $s['courseDescription'],
            'prerequisites' => $s['prerequisites'] ?? 'None',
            'instructor' => $s['instrFirst'] . ' ' . $s['instrLast'],
            'schedule' => 'TBA',
            'location' => $s['roomID'],
            'enrolled' => (int)$s['enrolled'],
            'capacity' => (int)$s['capacity'],
            'availability' => ($s['enrolled'] >= $s['capacity']) ? 'full' : 'available'
        ];
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

// Load appropriate header
if ($isLoggedIn) {
    require_once __DIR__ . '/../includes/portal_header.php';
} else {
    require_once __DIR__ . '/../includes/public_header.php';
}
?>

<div class="container my-5">
    <h1 class="fw-bold mb-2">Course Catalog</h1>
    <p class="text-muted mb-4">Browse our complete list of academic offerings and prerequisites.</p>

    <!-- Search/Filter Area -->
    <div class="card border-0 shadow-sm p-4 mb-4">
        <input type="text" id="courseSearch" class="form-control" placeholder="Search by course name or ID...">
    </div>

    <!-- Course Grid -->
    <div id="courseGrid" class="row g-4">
        <!-- Courses will be rendered here by JS -->
    </div>
</div>

<script>
    const courses = <?= json_encode($courses) ?>;
    const courseGrid = document.getElementById('courseGrid');

    function renderCourses(data) {
        courseGrid.innerHTML = data.map(course => `
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <h5 class="fw-bold text-primary">${course.code}</h5>
                        <span class="badge ${course.availability === 'available' ? 'bg-success' : 'bg-danger'}">${course.availability}</span>
                    </div>
                    <h6 class="fw-bold">${course.title}</h6>
                    <p class="small text-muted mb-1">Instr: ${course.instructor}</p>
                    <p class="small text-muted mb-1">Room: ${course.location}</p>
                    <p class="small text-muted mb-2">${course.credits} Credits</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="alert('Prerequisites: ${course.prerequisites}')">Details</button>
                </div>
            </div>
        `).join('');
    }

    document.getElementById('courseSearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const filtered = courses.filter(c => 
            c.code.toLowerCase().includes(query) || 
            c.title.toLowerCase().includes(query)
        );
        renderCourses(filtered);
    });

    // Initial render
    renderCourses(courses);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
