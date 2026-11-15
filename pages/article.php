<?php
require_once __DIR__ . '/../includes/i18n.php';
require_once __DIR__ . '/../api/db.php';
$lang = current_lang();
$slug = $_GET['slug'] ?? ''; // Set by router

if (empty($slug)) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

try {
    $pdo = hoquei5_pdo();
    $stmt = $pdo->prepare("SELECT * FROM blog_articles WHERE slug = ? AND lang = ? LIMIT 1");
    $stmt->execute([$slug, $lang]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article && $lang !== 'es') {
        $stmt->execute([$slug, 'es']);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $article = null;
}

if (!$article) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}


// Setup Breadcrumbs
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => url('home')],
    ['label' => 'Blog', 'url' => url('blog_index')],
    ['label' => $article['category'], 'url' => url('blog_index') . '?category=' . urlencode($article['category'])],
    ['label' => $article['title'], 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> | <?= __('header.title') ?></title>
    <meta name="description" content="<?= htmlspecialchars($article['excerpt']) ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/styles.css') ?>">
    <link rel="icon" type="image/x-icon" href="<?= asset_url('favicon.ico') ?>">
    <style>
        .article-page { max-width: 800px; margin: 0 auto; padding: 40px 18px; }
        .article-header { margin-bottom: 40px; text-align: center; }
        .article-category { display: inline-block; padding: 6px 14px; background: rgba(25, 211, 255, 0.15); color: var(--brand2); border-radius: 999px; font-size: 13px; font-weight: 800; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.05em; }
        .article-title { font-size: clamp(32px, 5vw, 48px); line-height: 1.1; margin: 0 0 24px; font-weight: 900; }
        .article-meta { display: flex; align-items: center; justify-content: center; gap: 20px; color: var(--muted); font-size: 14px; }
        .article-meta .author-photo { width: 40px; height: 40px; border-radius: 50%; background: var(--line); display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--text); }
        .author-info { display: flex; align-items: center; gap: 10px; }
        .article-content { font-size: 18px; line-height: 1.7; color: rgba(255, 255, 255, 0.9); }
        .article-content h2 { margin: 40px 0 20px; font-size: 28px; color: var(--text); }
        .article-content h3 { margin: 30px 0 15px; font-size: 24px; color: var(--text); }
        .article-content p { margin-bottom: 25px; }
        .article-content a { color: var(--brand2); text-decoration: underline; }
        .article-content ul, .article-content ol { margin-bottom: 25px; padding-left: 20px; }
        .article-content li { margin-bottom: 10px; }
        
        /* Author Bio Box */
        .author-box .author-avatar { width: 80px; height: 80px; border-radius: 50%; background: var(--brand); flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: bold; }
        .author-box-content h3 { margin: 0 0 8px; font-size: 20px; }
        .author-box-content p { margin: 0; color: var(--muted); font-size: 15px; line-height: 1.6; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="container page-content">
        <?php include __DIR__ . '/../includes/breadcrumbs.php'; ?>
        
        <article class="article-page">
            <header class="article-header">
                <span class="article-category"><?= htmlspecialchars($article['category']) ?></span>
                <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
                
                <div class="article-meta">
                    <div class="author-info">
                        <div class="author-photo">H5</div>
                        <span><?php echo __('blog.author_prefix'); ?> <strong><?= htmlspecialchars($article['author']) ?></strong></span>
                    </div>
                    <?php 
                        $displayDate = $article['published_at'] ?? '';
                        if (empty($displayDate) || $displayDate === '0000-00-00 00:00:00') {
                            $displayDate = date('Y-m-d H:i:s');
                        }
                    ?>
                    <time datetime="<?= $displayDate ?>"><?= date('d M, Y', strtotime($displayDate)) ?></time>
                </div>
            </header>

            <div class="article-content">
                <?= $article['content'] // Raw HTML from json, assuming it's safe since we generated it ?>
            </div>

            <aside class="author-box" style="margin-bottom: 40px; border-bottom: 1px solid var(--line); padding-bottom: 30px;">
                <div class="author-avatar">H5</div>
                <div class="author-box-content">
                    <h3><?= htmlspecialchars($article['author']) ?></h3>
                    <p><?php echo __('article.author_desc'); ?></p>
                </div>
            </aside>

            <section class="related-posts">
                <h3 style="margin-bottom: 20px;"><?php echo __('article.related_posts'); ?></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
                    <?php 
                    try {
                        $stmtRelated = $pdo->prepare("SELECT * FROM blog_articles WHERE lang = ? AND id != ? ORDER BY published_at DESC LIMIT 3");
                        $stmtRelated->execute([$article['lang'], $article['id']]);
                        $recentArticles = $stmtRelated->fetchAll(PDO::FETCH_ASSOC);
                        if (empty($recentArticles)) {
                            $stmtRelated->execute(['es', $article['id']]);
                            $recentArticles = $stmtRelated->fetchAll(PDO::FETCH_ASSOC);
                        }
                    } catch(Exception $e) {
                        $recentArticles = [];
                    }
                    
                    foreach($recentArticles as $ra): 
                    ?>
                        <article style="background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); padding: 15px;">
                            <span class="article-category" style="font-size: 10px; margin-bottom: 5px;"><?= htmlspecialchars($ra['category']) ?></span>
                            <h4 style="font-size: 16px; margin: 0 0 10px;"><a href="/<?php echo $lang; ?>/blog/<?= htmlspecialchars($ra['slug']) ?>" style="color:var(--text); text-decoration:none;"><?= htmlspecialchars($ra['title']) ?></a></h4>
                            <p style="font-size: 13px; color: var(--muted); margin:0;"><?= mb_substr(htmlspecialchars($ra['excerpt']), 0, 60) ?>...</p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </article>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
