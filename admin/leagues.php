<?php
$pageTitle = 'Ligas';
$currentPage = 'leagues';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

try {
    $pdo = hoquei5_pdo();
    $leagues = $pdo->query("SELECT * FROM `leagues` ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;">Gestión de Ligas</h2>
        <a href="league_edit.php" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Añadir Liga</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Código (Slug)</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leagues as $l): ?>
            <tr>
                <td>#<?php echo $l['id']; ?></td>
                <td><code style="background: #f1f5f9; padding: 2px 4px; border-radius: 4px;"><?php echo htmlspecialchars($l['code']); ?></code></td>
                <td><div style="font-weight: 600;"><?php echo htmlspecialchars($l['name']); ?></div></td>
                <td class="actions">
                    <a href="league_edit.php?id=<?php echo $l['id']; ?>" class="btn btn-warning btn-sm" style="width: auto;"><i class="fas fa-edit"></i></a>
                    <a href="league_delete.php?id=<?php echo $l['id']; ?>" class="btn btn-danger btn-sm" style="width: auto;" onclick="return confirm('¿Eliminar esta liga? Se desvincularán equipos y jugadores asociados.');"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
