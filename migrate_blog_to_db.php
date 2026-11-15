<?php
require_once __DIR__ . '/api/db.php';

try {
    $pdo = hoquei5_pdo();

    // 1. Create table if it doesn't exist
    $sql = "
    CREATE TABLE IF NOT EXISTS blog_articles (
        id VARCHAR(64) NOT NULL,
        slug VARCHAR(128) NOT NULL,
        lang VARCHAR(10) NOT NULL,
        title VARCHAR(255) NOT NULL,
        excerpt TEXT,
        content LONGTEXT,
        author VARCHAR(100),
        category VARCHAR(100),
        published_at DATETIME,
        image VARCHAR(255),
        PRIMARY KEY (id, lang),
        INDEX idx_slug_lang (slug, lang),
        INDEX idx_category (category)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Table 'blog_articles' checked/created.\n";

    // 2. Clear existing (optional, but good for a fresh migration)
    $pdo->exec("TRUNCATE TABLE blog_articles");
    echo "Table 'blog_articles' truncated.\n";

    // 3. Migrate data
    $locales = ['es', 'en', 'ca', 'fr', 'pt'];
    $stmt = $pdo->prepare("
        INSERT INTO blog_articles (id, slug, lang, title, excerpt, content, author, category, published_at, image)
        VALUES (:id, :slug, :lang, :title, :excerpt, :content, :author, :category, :date, :image)
    ");

    $totalInserted = 0;
    foreach ($locales as $lang) {
        $file = __DIR__ . "/data/articles_{$lang}.json";
        if (!file_exists($file)) {
            echo "Skipping $lang: $file not found.\n";
            continue;
        }

        $json = file_get_contents($file);
        $articles = json_decode($json, true);

        if (!is_array($articles)) {
            echo "Failed to decode JSON for $lang.\n";
            continue;
        }

        foreach ($articles as $a) {
            $stmt->execute([
                ':id'       => $a['id'],
                ':slug'     => $a['slug'],
                ':lang'     => $lang,
                ':title'    => $a['title'],
                ':excerpt'  => $a['excerpt'],
                ':content'  => $a['content'],
                ':author'   => $a['author'],
                ':category' => $a['category'],
                ':date'     => $a['date'],
                ':image'    => $a['image'] ?? ''
            ]);
            $totalInserted++;
        }
        echo "Inserted " . count($articles) . " articles for $lang\n";
    }

    echo "Migration complete! Total articles inserted: $totalInserted\n";

} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
