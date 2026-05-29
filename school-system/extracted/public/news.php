<?php
// public/news.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/public_header.php';

try {
    $stmt = $pdo->query("SELECT * FROM News ORDER BY date DESC");
    $newsArticles = $stmt->fetchAll();
} catch (Exception $e) {
    $newsArticles = [];
}
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold">University News</h1>
            <p class="lead text-muted mx-auto mb-4" style="max-width: 800px;">The latest updates, stories, and breakthroughs from across the SUNY University community.</p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row">
            <?php foreach ($newsArticles as $article): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary-subtle text-primary border-primary-subtle"><?= htmlspecialchars($article['category']) ?></span>
                                <small class="text-muted"><?= htmlspecialchars($article['date']) ?></small>
                            </div>
                            <h4 class="fw-bold mb-3"><?= htmlspecialchars($article['title']) ?></h4>
                            <p class="text-muted mb-0 small"><?= htmlspecialchars($article['summary']) ?></p>
                            <div class="mt-3">
                                <a href="/public/news_detail.php?id=<?= $article['newsID'] ?>" class="text-decoration-none fw-bold small" style="color: var(--secondary-color);">READ FULL STORY &rarr;</a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
