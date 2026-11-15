<?php
$pageTitle = isset($_GET['id']) ? 'Editar Liga' : 'Añadir Liga';
$currentPage = 'leagues';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? '';
$success = '';
$error = '';

try {
    $pdo = hoquei5_pdo();

    $league = [
        'code' => '',
        'name' => ''
    ];

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM `leagues` WHERE `id` = ?");
        $stmt->execute([$id]);
        $league = $stmt->fetch();
        if (!$league) die("Liga no encontrada.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'code' => $_POST['code'] ?? '',
            'name' => $_POST['name'] ?? ''
        ];

        if ($id) {
            $sql = "UPDATE `leagues` SET `code` = :code, `name` = :name WHERE `id` = :id";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $success = "Liga actualizada correctamente.";
        } else {
            $sql = "INSERT INTO `leagues` (`code`, `name`) VALUES (:code, :name)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $id = $pdo->lastInsertId();
            $success = "Liga añadida correctamente.";
        }
        
        $stmt = $pdo->prepare("SELECT * FROM `leagues` WHERE `id` = ?");
        $stmt->execute([$id]);
        $league = $stmt->fetch();
    }
} catch (Exception $e) {
    if ($e->getCode() == 23000) $error = "Ya existe una liga con ese código.";
    else $error = "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;"><?php echo $pageTitle; ?></h2>
        <a href="leagues.php" class="btn" style="width: auto; background: #e2e8f0;"><i class="fas fa-arrow-left"></i> Volver</a>
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
                <label>Código de la Liga (Slug)</label>
                <input type="text" name="code" class="form-input" value="<?php echo htmlspecialchars($league['code']); ?>" required placeholder="ej: ok_plata">
            </div>

            <div class="form-group">
                <label>Nombre de la Liga</label>
                <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($league['name']); ?>" required placeholder="ej: OK Liga Plata">
            </div>

            <div style="grid-column: 1 / -1; margin-top: 1rem; border-top: 1px solid #e2e8f0; padding-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="width: auto; min-width: 200px;">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
