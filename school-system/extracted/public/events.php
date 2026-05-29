<?php
// public/events.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/public_header.php';

try {
    $stmt = $pdo->query("SELECT * FROM Events ORDER BY eventDate ASC");
    $events = $stmt->fetchAll();
} catch (Exception $e) {
    $events = [];
}
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold">Upcoming Events</h1>
            <p class="lead text-muted mx-auto mb-4" style="max-width: 800px;">Join us for campus activities, workshops, and community gatherings.</p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php foreach ($events as $event): ?>
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="text-center py-2 px-3 bg-primary text-white rounded me-4">
                                    <div class="h5 fw-bold mb-0"><?= date('d', strtotime($event['eventDate'])) ?></div>
                                    <div class="small text-uppercase"><?= date('M', strtotime($event['eventDate'])) ?></div>
                                </div>
                                <div>
                                    <span class="badge bg-light text-muted border mb-1"><?= htmlspecialchars($event['category']) ?></span>
                                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($event['title']) ?></h4>
                                    <div class="text-muted small">
                                        <span class="me-3">🕒 <?= htmlspecialchars($event['startTime']) ?></span>
                                        <span>📍 <?= htmlspecialchars($event['location']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
