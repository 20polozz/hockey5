<?php
$pageTitle = isset($_GET['id']) ? 'Editar Selección' : 'Añadir Selección';
$currentPage = 'seleccions';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? '';
$success = '';
$error = '';

try {
    $pdo = hoquei5_pdo();

    $seleccion = [
        'nom' => '',
        'logo_url' => ''
    ];

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM `seleccio` WHERE `id` = ?");
        $stmt->execute([$id]);
        $seleccion = $stmt->fetch();
        if (!$seleccion) die("Selección no encontrada.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'nom' => $_POST['nom'] ?? '',
            'logo_url' => $_POST['logo_url'] ?? ''
        ];

        if ($id) {
            $sql = "UPDATE `seleccio` SET `nom` = :nom, `logo_url` = :logo_url WHERE `id` = :id";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $success = "Selección actualizada correctamente.";
        } else {
            $sql = "INSERT INTO `seleccio` (`nom`, `logo_url`) VALUES (:nom, :logo_url)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $id = $pdo->lastInsertId();
            $success = "Selección añadida correctamente.";
        }
        
        $stmt = $pdo->prepare("SELECT * FROM `seleccio` WHERE `id` = ?");
        $stmt->execute([$id]);
        $seleccion = $stmt->fetch();
    }
} catch (Exception $e) {
    if ($e->getCode() == 23000) $error = "Ya existe una selección con ese nombre.";
    else $error = "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;"><?php echo $pageTitle; ?></h2>
        <a href="seleccions.php" class="btn" style="width: auto; background: #e2e8f0;"><i class="fas fa-arrow-left"></i> Volver</a>
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
                <label>Nombre de la Selección</label>
                <input type="text" name="nom" class="form-input" value="<?php echo htmlspecialchars($seleccion['nom']); ?>" required placeholder="ej: Italia">
            </div>

            <div class="form-group">
                <label>URL de la Bandera</label>
                <input type="text" name="logo_url" class="form-input" value="<?php echo htmlspecialchars($seleccion['logo_url'] ?? ''); ?>" placeholder="https://flagcdn.com/...">
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
