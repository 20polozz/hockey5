<?php
$pageTitle = 'Clubes / Equipos';
$currentPage = 'teams';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$search = $_GET['search'] ?? '';
$league_filter = $_GET['league_id'] ?? '';

try {
    $pdo = hoquei5_pdo();
    
    // Get leagues for filter
    $leagues = $pdo->query("SELECT * FROM `leagues` ORDER BY name ASC")->fetchAll();
    
    // Build query
    $query = "SELECT t.*, l.name as league_name FROM `teams` t LEFT JOIN `leagues` l ON t.league_id = l.id WHERE 1=1";
    $params = [];
    
    if ($search) {
        $query .= " AND t.name LIKE ?";
        $params[] = "%$search%";
    }
    
    if ($league_filter) {
        $query .= " AND t.league_id = ?";
        $params[] = $league_filter;
    }
    
    $query .= " ORDER BY l.name, t.name ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $teams = $stmt->fetchAll();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;">Gestión de Clubes</h2>
        <a href="team_edit.php" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Añadir Club</a>
    </div>

    <div style="padding: 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
        <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Buscar por nombre..." class="form-input" style="max-width: 300px;" value="<?php echo htmlspecialchars($search); ?>">
            
            <select name="league_id" class="form-input" style="max-width: 200px;">
                <option value="">Todas las ligas</option>
                <?php foreach ($leagues as $l): ?>
                    <option value="<?php echo $l['id']; ?>" <?php echo ($league_filter == $l['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($l['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn btn-primary btn-sm" style="width: auto;">Filtrar</button>
            <?php if ($search || $league_filter): ?>
                <a href="teams.php" class="btn btn-sm" style="width: auto; background: #e2e8f0;">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Nombre</th>
                <th>Liga</th>
                <th>Ciudad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($teams)): ?>
                <tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">No se encontraron clubes.</td></tr>
            <?php endif; ?>
            <?php foreach ($teams as $t): ?>
            <tr>
                <td>
                    <?php if ($t['logo_url']): ?>
                        <img src="<?php echo htmlspecialchars($t['logo_url']); ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain;">
                    <?php else: ?>
                        <div style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 4px;"></div>
                    <?php endif; ?>
                </td>
                <td><div style="font-weight: 600;"><?php echo htmlspecialchars($t['name']); ?></div></td>
                <td><span class="badge badge-info"><?php echo htmlspecialchars($t['league_name'] ?? 'Sin liga'); ?></span></td>
                <td><?php echo htmlspecialchars($t['city'] ?? 'N/A'); ?></td>
                <td class="actions">
                    <a href="team_edit.php?id=<?php echo $t['id']; ?>" class="btn btn-warning btn-sm" style="width: auto;"><i class="fas fa-edit"></i></a>
                    <a href="team_delete.php?id=<?php echo $t['id']; ?>" class="btn btn-danger btn-sm" style="width: auto;" onclick="return confirm('¿Eliminar este club? (Se desvincularán los jugadores)');"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
