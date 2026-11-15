<?php
require_once __DIR__ . '/../includes/i18n.php';
require_once __DIR__ . '/../api/db.php';
$lang = current_lang();

try {
    $pdo = hoquei5_pdo();
    
    $category = $_GET['category'] ?? null;
    $searchQuery = $_GET['q'] ?? null;
    $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
    $perPage = 9; // 9 articles per page
    
    $where = ["lang = :lang"];
    $params = [':lang' => $lang];

    if ($category) {
        $where[] = "category = :cat";
        $params[':cat'] = $category;
    }

    if ($searchQuery) {
        $where[] = "(title LIKE :q OR excerpt LIKE :q)";
        $params[':q'] = "%" . trim($searchQuery) . "%";
    }

    $whereClause = implode(" AND ", $where);
    
    // Check count first
    $countSql = "SELECT COUNT(*) FROM blog_articles WHERE $whereClause";
    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute($params);
    $totalArts = $stmtCount->fetchColumn();
    
    if ($totalArts == 0 && $lang !== 'es') {
        // Fallback to es
        $params[':lang'] = 'es';
        $stmtCount->execute($params);
        $totalArts = $stmtCount->fetchColumn();
    }
    
    $totalPages = ceil($totalArts / $perPage);
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT * FROM blog_articles WHERE lang = :lang";
    if ($category) $sql .= " AND category = :cat";
    if ($searchQuery) $sql .= " AND (title LIKE :q OR excerpt LIKE :q)";
    
    $sql .= " ORDER BY published_at DESC LIMIT $perPage OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $catMap = [
        'Hockey Basics' => __('blog.category.basics'),
        'Hockey Strategy' => __('blog.category.strategy'),
        'Hockey Players' => __('blog.category.players'),
        'Hockey Training' => __('blog.category.training'),
        'Hockey Equipment' => __('blog.category.equipment'),
        'Hockey History' => __('blog.category.history'),
    ];

} catch (Exception $e) {
    $articles = [];
    $totalPages = 1;
    $page = 1;
}

$breadcrumbs = [
    ['label' => 'Inicio', 'url' => url('home')],
    ['label' => 'Blog', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/ads.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog de Hockey sobre Patines | <?= __('header.title') ?></title>
    <meta name="description" content="Noticias, guías y tutoriales sobre hockey patines.">
    <link rel="stylesheet" href="<?= asset_url('assets/styles.css') ?>">
    <link rel="icon" type="image/x-icon" href="<?= asset_url('favicon.ico') ?>">
    <style>
        .blog-header { text-align: center; padding: 40px 18px; }
        .blog-header h1 { font-size: 36px; margin-bottom: 10px; }
        .blog-header p { color: var(--muted); font-size: 18px; }
        .blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px 0; }
        .blog-post { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); padding: 20px; display: flex; flex-direction: column; transition: transform 0.2s; }
        .blog-post:hover { transform: translateY(-5px); border-color: var(--brand2); }
        .blog-category { display: inline-block; padding: 4px 10px; background: rgba(25, 211, 255, 0.15); color: var(--brand2); border-radius: 999px; font-size: 12px; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; align-self: flex-start; }
        .blog-post h2 { font-size: 18px; margin: 0 0 10px; line-height: 1.3; }
        .blog-post h2 a { text-decoration: none; color: var(--text); }
        .blog-post p { color: var(--muted); font-size: 14px; line-height: 1.5; margin-bottom: 15px; flex-grow: 1; }
        .blog-meta { display: flex; justify-content: space-between; font-size: 12px; color: var(--muted); border-top: 1px solid var(--line); padding-top: 12px; }
        
        .blog-categories { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-bottom: 30px; }
        .blog-categories a { padding: 8px 16px; border-radius: 20px; border: 1px solid var(--line); font-size: 14px; transition: all 0.2s; }
        .blog-categories a:hover, .blog-categories a.active { background: rgba(255, 255, 255, 0.1); border-color: var(--text); color: var(--text); }
        
        .pagination { display: flex; justify-content: center; gap: 10px; padding: 40px 0; }
        .pagination a, .pagination span { padding: 8px 16px; border-radius: 8px; border: 1px solid var(--line); color: var(--text); text-decoration: none; }
        .pagination a.active, .pagination span.active { background: var(--brand); color: #fff; border-color: var(--brand); }
        .pagination a:hover:not(.active) { background: rgba(255,255,255,0.1); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="container page-content">
        <?php include __DIR__ . '/../includes/breadcrumbs.php'; ?>
        
        <div class="blog-header">
            <h1><?php echo __('blog.header.title'); ?></h1>
            <p><?php echo __('blog.header.desc'); ?></p>
        </div>

        <div class="blog-categories">
            <a href="/<?php echo $lang; ?>/blog" class="<?php echo !$category ? 'active' : ''; ?>"><?php echo __('blog.category.all'); ?></a>
            <a href="?category=Hockey+Basics" class="<?php echo $category === 'Hockey Basics' ? 'active' : ''; ?>"><?php echo __('blog.category.basics'); ?></a>
            <a href="?category=Hockey+Strategy" class="<?php echo $category === 'Hockey Strategy' ? 'active' : ''; ?>"><?php echo __('blog.category.strategy'); ?></a>
            <a href="?category=Hockey+Players" class="<?php echo $category === 'Hockey Players' ? 'active' : ''; ?>"><?php echo __('blog.category.players'); ?></a>
            <a href="?category=Hockey+Training" class="<?php echo $category === 'Hockey Training' ? 'active' : ''; ?>"><?php echo __('blog.category.training'); ?></a>
            <a href="?category=Hockey+Equipment" class="<?php echo $category === 'Hockey Equipment' ? 'active' : ''; ?>"><?php echo __('blog.category.equipment'); ?></a>
            <a href="?category=Hockey+History" class="<?php echo $category === 'Hockey History' ? 'active' : ''; ?>"><?php echo __('blog.category.history'); ?></a>
        </div>

        <div class="blog-grid">
            <?php if (empty($articles)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: var(--muted);"><?php echo __('blog.empty'); ?></p>
            <?php else: ?>
                <?php foreach ($articles as $article): 
                    $excerpt = preg_replace('/<!--.*?-->/s', '', $article['excerpt'] ?? '');
                    $displayDate = $article['published_at'] ?? '';
                    
                    if (empty($displayDate) || $displayDate === '0000-00-00 00:00:00') {
                        $displayDate = date('Y-m-d H:i:s'); // Fallback to current
                    }
                ?>
                    <article class="blog-post">
                        <span class="blog-category"><?= htmlspecialchars($catMap[$article['category']] ?? $article['category']) ?></span>
                        <h2><a href="/<?php echo $lang; ?>/blog/<?= htmlspecialchars($article['slug']) ?>"><?= htmlspecialchars($article['title']) ?></a></h2>
                        <p><?= htmlspecialchars($excerpt) ?></p>
                        <div class="blog-meta">
                            <span class="blog-author"><?php echo __('blog.author_prefix'); ?> <?= htmlspecialchars($article['author']) ?></span>
                            <span class="blog-date"><?= date('d M, Y', strtotime($displayDate)) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a href="?p=<?= $page - 1 ?><?= $category ? '&category='.urlencode($category) : '' ?>">&laquo; <?php echo __('blog.pagination.prev', '&laquo; Anterior'); ?></a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?p=<?= $i ?><?= $category ? '&category='.urlencode($category) : '' ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?p=<?= $page + 1 ?><?= $category ? '&category='.urlencode($category) : '' ?>"><?php echo __('blog.pagination.next', 'Siguiente &raquo;'); ?> &raquo;</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="<?= asset_url('assets/script.js') ?>"></script>
</body>
</html>
