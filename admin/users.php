<?php
$pageTitle = 'Gestión de Usuarios';
$currentPage = 'users';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

try {
    $pdo = hoquei5_pdo();
    $users = $pdo->query("SELECT * FROM `users` ORDER BY id ASC")->fetchAll();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;">Gestión de Usuarios</h2>
        <a href="user_edit.php" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Añadir Usuario</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td>#<?php echo $u['id']; ?></td>
                <td><div style="font-weight: 600;"><?php echo htmlspecialchars($u['username']); ?></div></td>
                <td><?php echo htmlspecialchars($u['email'] ?? '-'); ?></td>
                <td><span class="badge badge-info"><?php echo htmlspecialchars($u['role']); ?></span></td>
                <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                <td class="actions">
                    <a href="user_edit.php?id=<?php echo $u['id']; ?>" class="btn btn-warning btn-sm" style="width: auto;"><i class="fas fa-edit"></i></a>
                    <a href="user_delete.php?id=<?php echo $u['id']; ?>" class="btn btn-danger btn-sm" style="width: auto;" onclick="return confirm('¿Eliminar este usuario?');"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
