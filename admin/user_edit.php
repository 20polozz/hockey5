<?php
$pageTitle = 'Editar Usuario';
$currentPage = 'users';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? null;
$pdo = hoquei5_pdo();

$user = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'admin';
    $password = $_POST['password'] ?? '';

    $params = [$username, $email, $role];
    
    try {
        if ($id) {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE `users` SET username=?, email=?, role=?, password_hash=? WHERE id=?");
                $params[] = $hash;
                $params[] = $id;
            } else {
                $stmt = $pdo->prepare("UPDATE `users` SET username=?, email=?, role=? WHERE id=?");
                $params[] = $id;
            }
            $stmt->execute($params);
        } else {
            if(empty($password)) $password = '123456'; // fallback
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $params[] = $hash;
            $stmt = $pdo->prepare("INSERT INTO `users` (username, email, role, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute($params);
        }
        
        // Redirect via JS
        echo "<script>window.location.href='users.php';</script>";
        exit;
    } catch (Exception $e) {
        $error = "Error al guardar el usuario: " . $e->getMessage();
    }
}
?>

<div class="form-container">
    <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">
        <?php echo $id ? 'Editar Usuario' : 'Nuevo Usuario'; ?>
    </h2>

    <?php if ($error): ?>
        <div style="padding: 1rem; background: #fee2e2; color: #b91c1c; border-radius: 4px; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Nombre de usuario</label>
            <input type="text" name="username" class="form-input" required 
                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" 
                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Rol</label>
            <select name="role" class="form-input" required>
                <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="editor" <?php echo ($user['role'] ?? '') === 'editor' ? 'selected' : ''; ?>>Editor</option>
                <option value="viewer" <?php echo ($user['role'] ?? '') === 'viewer' ? 'selected' : ''; ?>>Viewer</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Contraseña <?php if($id) echo '<small>(Dejar en blanco para no cambiar)</small>'; ?></label>
            <input type="password" name="password" class="form-input" <?php echo !$id ? 'required' : ''; ?>>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="width: auto;">Guardar Usuario</button>
            <a href="users.php" class="btn" style="width: auto; background: #e2e8f0;">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
