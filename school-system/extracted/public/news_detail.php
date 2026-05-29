<?php
// public/news_detail.php
require_once __DIR__ . '/../config/database.php';

$newsID = $_GET['id'] ?? null;
if (!$newsID) {
    header("Location: news.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM News WHERE newsID = :id");
    $stmt->execute(['id' => $newsID]);
    $article = $stmt->fetch();
    
    if (!$article) {
        header("Location: news.php");
        exit();
    }
} catch (Exception $e) {
    die("Error loading news article.");
}

require_once __DIR__ . '/../includes/public_header.php';
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <span class="badge bg-primary-subtle text-primary mb-3"><?= htmlspecialchars($article['category']) ?></span>
            <h1 class="display-4 fw-bold"><?= htmlspecialchars($article['title']) ?></h1>
            <p class="text-muted"><?= date('F j, Y', strtotime($article['date'])) ?></p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="news-content">
                    <!-- Rendering content as is to allow HTML/XHTML stories -->
                    <?= $article['content'] ?>
                </div>
                <hr class="my-5">
                <a href="news.php" class="btn btn-outline-dark">&larr; Back to News</a>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
