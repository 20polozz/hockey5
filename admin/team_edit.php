<?php
$pageTitle = isset($_GET['id']) ? 'Editar Club' : 'Añadir Club';
$currentPage = 'teams';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? '';
$success = '';
$error = '';

try {
    $pdo = hoquei5_pdo();
    $leagues = $pdo->query("SELECT * FROM `leagues` ORDER BY name ASC")->fetchAll();

    $team = [
        'name' => '',
        'normalized_name' => '',
        'league_id' => '',
        'city' => '',
        'logo_url' => ''
    ];

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM `teams` WHERE `id` = ?");
        $stmt->execute([$id]);
        $team = $stmt->fetch();
        if (!$team) die("Club no encontrado.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'name' => $_POST['name'] ?? '',
            'normalized_name' => strtolower(trim($_POST['name'] ?? '')),
            'league_id' => $_POST['league_id'] ?? '',
            'city' => $_POST['city'] ?? '',
            'logo_url' => $_POST['logo_url'] ?? ''
        ];

        if ($id) {
            $sql = "UPDATE `teams` SET `name` = :name, `normalized_name` = :normalized_name, `league_id` = :league_id, `city` = :city, `logo_url` = :logo_url WHERE `id` = :id";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $success = "Club actualizado correctamente.";
        } else {
            $sql = "INSERT INTO `teams` (`name`, `normalized_name`, `league_id`, `city`, `logo_url`) VALUES (:name, :normalized_name, :league_id, :city, :logo_url)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $id = $pdo->lastInsertId();
            $success = "Club añadido correctamente.";
        }
        
        $stmt = $pdo->prepare("SELECT * FROM `teams` WHERE `id` = ?");
        $stmt->execute([$id]);
        $team = $stmt->fetch();
    }
} catch (Exception $e) {
    if ($e->getCode() == 23000) $error = "Ya existe un club con ese nombre en esta liga.";
    else $error = "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;"><?php echo $pageTitle; ?></h2>
        <a href="teams.php" class="btn" style="width: auto; background: #e2e8f0;"><i class="fas fa-arrow-left"></i> Volver</a>
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
                <label>Nombre del Club</label>
                <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($team['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Liga</label>
                <select name="league_id" class="form-input" required>
                    <option value="">Seleccionar Liga...</option>
                    <?php foreach ($leagues as $l): ?>
                        <option value="<?php echo $l['id']; ?>" <?php echo ($team['league_id'] == $l['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($l['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Ciudad</label>
                <input type="text" name="city" class="form-input" value="<?php echo htmlspecialchars($team['city'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>URL del Logo</label>
                <input type="text" name="logo_url" class="form-input" value="<?php echo htmlspecialchars($team['logo_url'] ?? ''); ?>" placeholder="https://...">
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
