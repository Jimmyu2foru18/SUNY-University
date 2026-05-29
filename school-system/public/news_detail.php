<?php
// public/news_detail.php - Debugging version
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config/database.php';

$newsID = $_GET['id'] ?? null;
if (!$newsID) {
    die("No News ID provided.");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM News WHERE newsID = :id");
    $stmt->execute(['id' => $newsID]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$article) {
        die("Article not found. ID: " . htmlspecialchars($newsID));
    }
} catch (Exception $e) {
    die("Error loading news article: " . $e->getMessage());
}

require_once __DIR__ . '/../includes/public_header.php';
?>
    <section class="py-5 bg-light border-bottom mb-5">
        <div class="container text-center py-5">
            <span class="badge bg-primary-subtle text-primary mb-3"><?= htmlspecialchars($article['category'] ?? 'N/A') ?></span>
            <h1 class="display-4 fw-bold"><?= htmlspecialchars($article['title'] ?? 'N/A') ?></h1>
            <p class="text-muted"><?= isset($article['date']) ? date('F j, Y', strtotime($article['date'])) : 'N/A' ?></p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="news-content">
                    <?php 
                    if (isset($article['content']) && !empty($article['content'])) {
                        echo $article['content'];
                    } else {
                        echo "<p>No content available for this article.</p>";
                    }
                    ?>
                </div>
                <hr class="my-5">
                <a href="news.php" class="btn btn-outline-dark">&larr; Back to News</a>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
