<?php
// public/calendar.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/public_header.php';

try {
    // Fetch unique semesters from Events
    $stmt = $pdo->query("SELECT DISTINCT category FROM Events ORDER BY category DESC");
    $semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch Events grouped by semester/category
    $stmt = $pdo->query("SELECT title, eventDate, category FROM Events ORDER BY eventDate ASC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $groupedEvents = [];
    foreach ($events as $e) {
        $groupedEvents[$e['category']][] = $e;
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<div class="container my-5">
    <h1 class="fw-bold mb-4">Academic Calendar</h1>
    
    <?php foreach ($groupedEvents as $semester => $semesterEvents): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light border-0">
                <h2 class="h4 fw-bold mb-0"><?= htmlspecialchars($semester) ?></h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Event</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semesterEvents as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['title']) ?></td>
                                <td><?= htmlspecialchars(date('F j, Y', strtotime($e['eventDate']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
