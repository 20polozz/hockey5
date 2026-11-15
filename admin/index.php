<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

try {
    $pdo = hoquei5_pdo();
    
    // Get stats
    $totalPlayers = $pdo->query("SELECT COUNT(*) FROM `players`")->fetchColumn();
    $totalTeams = $pdo->query("SELECT COUNT(*) FROM `teams`")->fetchColumn();
    $totalLeagues = $pdo->query("SELECT COUNT(*) FROM `leagues`")->fetchColumn();
    $totalSeleccions = $pdo->query("SELECT COUNT(*) FROM `seleccio`")->fetchColumn();
    
    // Get recent players
    $recentPlayers = $pdo->query("SELECT p.*, t.name as team_name FROM `players` p LEFT JOIN `teams` t ON p.current_team_id = t.id ORDER BY p.id DESC LIMIT 5")->fetchAll();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #dbeafe; color: #1e40af;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>Jugadores</h3>
            <p><?php echo $totalPlayers; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #fef3c7; color: #92400e;">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="stat-info">
            <h3>Clubes</h3>
            <p><?php echo $totalTeams; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #dcfce7; color: #166534;">
            <i class="fas fa-trophy"></i>
        </div>
        <div class="stat-info">
            <h3>Ligas</h3>
            <p><?php echo $totalLeagues; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f3e8ff; color: #6b21a8;">
            <i class="fas fa-flag"></i>
        </div>
        <div class="stat-info">
            <h3>Selecciones</h3>
            <p><?php echo $totalSeleccions; ?></p>
        </div>
    </div>

</div>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;">Jugadores Recientes</h2>
        <a href="players.php" class="btn btn-primary btn-sm" style="width: auto;">Ver todos</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Equipo</th>
                <th>País</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentPlayers as $p): ?>
            <tr>
                <td>#<?php echo $p['id']; ?></td>
                <td>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($p['name']); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($p['full_name'] ?? ''); ?></div>
                </td>
                <td><?php echo htmlspecialchars($p['team_name'] ?? $p['current_team_name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($p['country'] ?? 'N/A'); ?></td>
                <td class="actions">
                    <a href="player_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-warning btn-sm" style="width: auto;"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
