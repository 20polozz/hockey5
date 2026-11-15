<?php
$pageTitle = 'Selecciones';
$currentPage = 'seleccions';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

try {
    $pdo = hoquei5_pdo();
    $seleccions = $pdo->query("SELECT * FROM `seleccio` ORDER BY nom ASC")->fetchAll();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;">Gestión de Selecciones</h2>
        <a href="seleccion_edit.php" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Añadir Selección</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Bandera</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($seleccions as $s): ?>
            <tr>
                <td>
                    <?php if ($s['logo_url']): ?>
                        <img src="<?php echo htmlspecialchars($s['logo_url']); ?>" alt="Bandera" style="width: 32px; height: 20px; object-fit: cover; border-radius: 2px;">
                    <?php endif; ?>
                </td>
                <td><div style="font-weight: 600;"><?php echo htmlspecialchars($s['nom']); ?></div></td>
                <td class="actions">
                    <a href="seleccion_edit.php?id=<?php echo $s['id']; ?>" class="btn btn-warning btn-sm" style="width: auto;"><i class="fas fa-edit"></i></a>
                    <a href="seleccion_delete.php?id=<?php echo $s['id']; ?>" class="btn btn-danger btn-sm" style="width: auto;" onclick="return confirm('¿Eliminar esta selección?');"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
