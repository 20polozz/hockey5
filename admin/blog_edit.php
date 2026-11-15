<?php
$pageTitle = (isset($_GET['id']) && isset($_GET['lang'])) ? 'Editar Artículo' : 'Nuevo Artículo';
$currentPage = 'blog';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? '';
$lang = $_GET['lang'] ?? 'es';
$success = '';
$error = '';

try {
    $pdo = hoquei5_pdo();

    $article = [
        'id' => '',
        'slug' => '',
        'lang' => 'es',
        'title' => '',
        'excerpt' => '',
        'content' => '',
        'author' => 'Admin',
        'category' => 'General',
        'published_at' => date('Y-m-d H:i:s'),
        'image' => ''
    ];

    $isEdit = false;
    if ($id && $lang) {
        $stmt = $pdo->prepare("SELECT * FROM `blog_articles` WHERE `id` = ? AND `lang` = ?");
        $stmt->execute([$id, $lang]);
        $article = $stmt->fetch();
        if ($article) {
            $isEdit = true;
        } else {
            // If ID exists but not in this lang, suggest adding it for this lang
            $article['id'] = $id;
            $article['lang'] = $lang;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'id' => $_POST['id'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'lang' => $_POST['lang'] ?? 'es',
            'title' => $_POST['title'] ?? '',
            'excerpt' => $_POST['excerpt'] ?? '',
            'content' => $_POST['content'] ?? '',
            'author' => $_POST['author'] ?? '',
            'category' => $_POST['category'] ?? '',
            'published_at' => $_POST['published_at'] ?? date('Y-m-d H:i:s'),
            'image' => $_POST['image'] ?? ''
        ];

        if ($isEdit) {
            $sql = "UPDATE `blog_articles` SET 
                    `slug` = :slug, `title` = :title, `excerpt` = :excerpt, 
                    `content` = :content, `author` = :author, `category` = :category, 
                    `published_at` = :published_at, `image` = :image 
                    WHERE `id` = :id AND `lang` = :lang";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $success = "Artículo actualizado correctamente.";
        } else {
            // Check if (id, lang) already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `blog_articles` WHERE `id` = ? AND `lang` = ?");
            $stmt->execute([$data['id'], $data['lang']]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Ya existe este artículo para este idioma.";
            } else {
                $sql = "INSERT INTO `blog_articles` (`id`, `slug`, `lang`, `title`, `excerpt`, `content`, `author`, `category`, `published_at`, `image`) 
                        VALUES (:id, :slug, :lang, :title, :excerpt, :content, :author, :category, :published_at, :image)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($data);
                $isEdit = true;
                $id = $data['id'];
                $lang = $data['lang'];
                $success = "Artículo creado correctamente.";
            }
        }
        
        $stmt = $pdo->prepare("SELECT * FROM `blog_articles` WHERE `id` = ? AND `lang` = ?");
        $stmt->execute([$id, $lang]);
        $article = $stmt->fetch();
    }
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;"><?php echo $pageTitle; ?></h2>
        <a href="blog.php" class="btn" style="width: auto; background: #e2e8f0;"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div style="padding: 2rem;">
        <?php if ($success): ?>
            <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-grid">
            <div class="form-group">
                <label>ID Único (ej: noticia-01)</label>
                <input type="text" name="id" class="form-input" value="<?php echo htmlspecialchars($article['id']); ?>" <?php echo $isEdit ? 'readonly' : 'required'; ?>>
            </div>

            <div class="form-group">
                <label>Idioma</label>
                <select name="lang" class="form-input" <?php echo $isEdit ? 'disabled' : 'required'; ?>>
                    <option value="es" <?php echo ($article['lang'] == 'es') ? 'selected' : ''; ?>>Español</option>
                    <option value="ca" <?php echo ($article['lang'] == 'ca') ? 'selected' : ''; ?>>Català</option>
                    <option value="en" <?php echo ($article['lang'] == 'en') ? 'selected' : ''; ?>>English</option>
                    <option value="fr" <?php echo ($article['lang'] == 'fr') ? 'selected' : ''; ?>>Français</option>
                    <option value="pt" <?php echo ($article['lang'] == 'pt') ? 'selected' : ''; ?>>Português</option>
                </select>
                <?php if ($isEdit): ?><input type="hidden" name="lang" value="<?php echo $article['lang']; ?>"><?php endif; ?>
            </div>

            <div class="form-group">
                <label>Título</label>
                <input type="text" name="title" class="form-input" value="<?php echo htmlspecialchars($article['title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Slug (URL amistosa)</label>
                <input type="text" name="slug" class="form-input" value="<?php echo htmlspecialchars($article['slug']); ?>" required placeholder="ej: gran-victoria-del-barca">
            </div>

            <div class="form-group">
                <label>Autor</label>
                <input type="text" name="author" class="form-input" value="<?php echo htmlspecialchars($article['author']); ?>">
            </div>

            <div class="form-group">
                <label>Categoría</label>
                <input type="text" name="category" class="form-input" value="<?php echo htmlspecialchars($article['category']); ?>">
            </div>

            <div class="form-group">
                <label>Fecha de Publicación</label>
                <input type="text" name="published_at" class="form-input" value="<?php echo htmlspecialchars($article['published_at']); ?>" placeholder="YYYY-MM-DD HH:MM:SS">
            </div>

            <div class="form-group">
                <label>URL de Imagen</label>
                <input type="text" name="image" class="form-input" value="<?php echo htmlspecialchars($article['image']); ?>" placeholder="https://...">
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Resumen (Excerpt)</label>
                <textarea name="excerpt" class="form-input" style="height: 80px;"><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Contenido (HTML permitido)</label>
                <textarea name="content" class="form-input" style="height: 300px; font-family: monospace;"><?php echo htmlspecialchars($article['content']); ?></textarea>
            </div>

            <div style="grid-column: 1 / -1; margin-top: 1rem; border-top: 1px solid #e2e8f0; padding-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="width: auto; min-width: 200px;">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Guardar Artículo
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
