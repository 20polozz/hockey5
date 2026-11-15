<?php
$pageTitle = 'Blog / Noticias';
$currentPage = 'blog';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$search = $_GET['search'] ?? '';
$lang_filter = $_GET['lang'] ?? '';
$category_filter = $_GET['category'] ?? '';

try {
    $pdo = hoquei5_pdo();
    
    // Get unique categories and langs for filters
    $categories = $pdo->query("SELECT DISTINCT category FROM `blog_articles` WHERE category IS NOT NULL AND category != '' ORDER BY category ASC")->fetchAll();
    $langs = $pdo->query("SELECT DISTINCT lang FROM `blog_articles` ORDER BY lang ASC")->fetchAll();

    $query = "SELECT * FROM `blog_articles` WHERE 1=1";
    $params = [];
    
    if ($search) {
        $query .= " AND (title LIKE ? OR slug LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($lang_filter) {
        $query .= " AND lang = ?";
        $params[] = $lang_filter;
    }

    if ($category_filter) {
        $query .= " AND category = ?";
        $params[] = $category_filter;
    }
    
    $query .= " ORDER BY published_at DESC LIMIT 100";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $articles = $stmt->fetchAll();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;">Gestión del Blog</h2>
        <a href="blog_edit.php" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Nuevo Artículo</a>
    </div>

    <div style="padding: 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
        <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Buscar por título o slug..." class="form-input" style="max-width: 300px;" value="<?php echo htmlspecialchars($search); ?>">
            
            <select name="lang" class="form-input" style="max-width: 150px;">
                <option value="">Todos los idiomas</option>
                <?php foreach ($langs as $l): ?>
                    <option value="<?php echo htmlspecialchars($l['lang']); ?>" <?php echo ($lang_filter == $l['lang']) ? 'selected' : ''; ?>>
                        <?php echo strtoupper(htmlspecialchars($l['lang'])); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="category" class="form-input" style="max-width: 200px;">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo htmlspecialchars($c['category']); ?>" <?php echo ($category_filter == $c['category']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['category']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn btn-primary btn-sm" style="width: auto;">Filtrar</button>
            <?php if ($search || $lang_filter || $category_filter): ?>
                <a href="blog.php" class="btn btn-sm" style="width: auto; background: #e2e8f0;">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Título</th>
                <th>Idioma</th>
                <th>Categoría</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($articles)): ?>
                <tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No se encontraron artículos.</td></tr>
            <?php endif; ?>
            <?php foreach ($articles as $a): ?>
            <tr>
                <td>
                    <?php if ($a['image']): ?>
                        <img src="<?php echo htmlspecialchars($a['image']); ?>" alt="Article" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px;">
                    <?php endif; ?>
                </td>
                <td>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($a['title']); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($a['slug']); ?></div>
                </td>
                <td><span class="badge badge-info"><?php echo strtoupper($a['lang']); ?></span></td>
                <td><?php echo htmlspecialchars($a['category'] ?? 'N/A'); ?></td>
                <td><?php echo date('d/m/Y', strtotime($a['published_at'])); ?></td>
                <td class="actions">
                    <a href="blog_edit.php?id=<?php echo $a['id']; // ID is composite in this table but used as primary ?>&lang=<?php echo $a['lang']; ?>" class="btn btn-warning btn-sm" style="width: auto;"><i class="fas fa-edit"></i></a>
                    <a href="blog_delete.php?id=<?php echo $a['id']; ?>&lang=<?php echo $a['lang']; ?>" class="btn btn-danger btn-sm" style="width: auto;" onclick="return confirm('¿Eliminar este artículo?');"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
