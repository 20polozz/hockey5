<?php
$pageTitle = 'Jugadores';
$currentPage = 'players';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$search = $_GET['search'] ?? '';
$league_filter = $_GET['league_id'] ?? '';
$team_filter = $_GET['team_id'] ?? '';

try {
    $pdo = hoquei5_pdo();

    // Get leagues for filter
    $leagues = $pdo->query("SELECT * FROM `leagues` ORDER BY name ASC")->fetchAll();

    // Get teams for filter (agrupats per lliga al dropdown)
    $teams = $pdo->query("SELECT t.id, t.name, t.league_id, l.name as league_name
                           FROM `teams` t
                           LEFT JOIN `leagues` l ON t.league_id = l.id
                           ORDER BY l.name ASC, t.name ASC")->fetchAll();

    // Build query
    $query = "SELECT p.*, t.name as team_name, l.name as league_name
              FROM `players` p
              LEFT JOIN `teams` t ON p.current_team_id = t.id
              LEFT JOIN `leagues` l ON p.league_id = l.id
              WHERE 1=1";
    $params = [];

    if ($search) {
        $query .= " AND (p.name LIKE ? OR p.full_name LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($league_filter) {
        $query .= " AND p.league_id = ?";
        $params[] = $league_filter;
    }

    if ($team_filter) {
        $query .= " AND p.current_team_id = ?";
        $params[] = $team_filter;
    }

    $query .= " ORDER BY p.name ASC LIMIT 100";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $players = $stmt->fetchAll();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Filtres actuals, per arrossegar-los als enllaços d'editar/eliminar i que no es perdin
// quan es torna a la llista.
$filterQuery = http_build_query(array_filter([
    'search' => $search,
    'league_id' => $league_filter,
    'team_id' => $team_filter,
]));
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;">Gestión de Jugadores</h2>
        <a href="player_edit.php<?php echo $filterQuery ? '?' . $filterQuery : ''; ?>" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Añadir Jugador</a>
    </div>
    
    <div style="padding: 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
        <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Buscar por nombre..." class="form-input" style="max-width: 300px;" value="<?php echo htmlspecialchars($search); ?>">
            
            <select name="league_id" id="league_filter_select" class="form-input" style="max-width: 200px;">
                <option value="">Todas las ligas</option>
                <?php foreach ($leagues as $l): ?>
                    <option value="<?php echo $l['id']; ?>" <?php echo ($league_filter == $l['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($l['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="team_id" id="team_filter_select" class="form-input" style="max-width: 220px;">
                <option value="">Todos los equipos</option>
                <?php
                $lastLeagueName = '';
                foreach ($teams as $t):
                    if ($lastLeagueName !== $t['league_name']):
                        if ($lastLeagueName !== '') echo '</optgroup>';
                        echo '<optgroup label="' . htmlspecialchars($t['league_name'] ?? 'Sin liga') . '">';
                        $lastLeagueName = $t['league_name'];
                    endif;
                ?>
                    <option value="<?php echo $t['id']; ?>" data-league-id="<?php echo $t['league_id']; ?>" <?php echo ($team_filter == $t['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($t['name']); ?>
                    </option>
                <?php endforeach; if ($lastLeagueName !== '') echo '</optgroup>'; ?>
            </select>

            <button type="submit" class="btn btn-primary btn-sm" style="width: auto;">Filtrar</button>
            <?php if ($search || $league_filter || $team_filter): ?>
                <a href="players.php" class="btn btn-sm" style="width: auto; background: #e2e8f0;">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Jugador</th>
                <th>Equipo / Liga</th>
                <th>Dorsal</th>
                <th>Posición</th>
                <th>País</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($players)): ?>
                <tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">No se encontraron jugadores.</td></tr>
            <?php endif; ?>
            <?php foreach ($players as $p): ?>
            <tr>
                <td>#<?php echo $p['id']; ?></td>
                <td>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($p['name']); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($p['full_name'] ?? ''); ?></div>
                </td>
                <td>
                    <div style="font-weight: 500;"><?php echo htmlspecialchars($p['team_name'] ?? $p['current_team_name'] ?? 'Sin equipo'); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($p['league_name'] ?? 'Sin liga'); ?></div>
                </td>
                <td><?php echo htmlspecialchars($p['Numero'] ?? '-'); ?></td>
                <td><span class="badge badge-info"><?php echo htmlspecialchars($p['position'] ?? 'N/A'); ?></span></td>
                <td><?php echo htmlspecialchars($p['country'] ?? 'N/A'); ?></td>
                <td class="actions">
                    <a href="player_edit.php?id=<?php echo $p['id']; ?><?php echo $filterQuery ? '&' . $filterQuery : ''; ?>" class="btn btn-warning btn-sm" style="width: auto;"><i class="fas fa-edit"></i></a>
                    <a href="player_delete.php?id=<?php echo $p['id']; ?><?php echo $filterQuery ? '&' . $filterQuery : ''; ?>" class="btn btn-danger btn-sm" style="width: auto;" onclick="return confirm('¿Estás seguro de que deseas eliminar este jugador?');"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Quan es tria una lliga al filtre, amaga al desplegable d'equips els que no en formen part.
(function() {
    const leagueSelect = document.getElementById('league_filter_select');
    const teamSelect = document.getElementById('team_filter_select');
    if (!leagueSelect || !teamSelect) return;

    function applyLeagueFilter() {
        const selectedLeagueId = leagueSelect.value;
        let selectedTeamStillVisible = false;

        Array.from(teamSelect.options).forEach(function(opt) {
            if (opt.value === '') { return; } // "Todos los equipos"
            const belongs = !selectedLeagueId || opt.dataset.leagueId === selectedLeagueId;
            opt.hidden = !belongs;
            opt.disabled = !belongs;
            if (belongs && opt.selected) selectedTeamStillVisible = true;
        });

        // Si l'equip seleccionat no pertany a la lliga triada, reiniciem la selecció d'equip.
        if (teamSelect.value && !selectedTeamStillVisible) {
            teamSelect.value = '';
        }

        Array.from(teamSelect.querySelectorAll('optgroup')).forEach(function(group) {
            const hasVisible = Array.from(group.children).some(opt => !opt.hidden);
            group.hidden = !hasVisible;
        });
    }

    leagueSelect.addEventListener('change', applyLeagueFilter);
    applyLeagueFilter();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
