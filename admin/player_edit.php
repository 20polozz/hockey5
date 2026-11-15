<?php
$pageTitle = isset($_GET['id']) ? 'Editar Jugador' : 'Añadir Jugador';
$currentPage = 'players';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? '';
$success = '';
$error = '';
$previousTeams = [];

// Filtres de la llista (lliga/equip/cerca) rebuts per URL, per poder tornar-hi sense perdre'ls.
$returnQuery = http_build_query(array_filter([
    'search' => $_GET['search'] ?? '',
    'league_id' => $_GET['league_id'] ?? '',
    'team_id' => $_GET['team_id'] ?? '',
]));

try {
    $pdo = hoquei5_pdo();
    
    // Get master data for dropdowns
    $leagues = $pdo->query("SELECT * FROM `leagues` ORDER BY name ASC")->fetchAll();
    $teams = $pdo->query("SELECT t.*, l.name as league_name FROM `teams` t LEFT JOIN `leagues` l ON t.league_id = l.id ORDER BY l.name, t.name ASC")->fetchAll();
    $seleccions = $pdo->query("SELECT * FROM `seleccio` ORDER BY nom ASC")->fetchAll();

    $player = [
        'name' => '',
        'full_name' => '',
        'country' => '',
        'birth_year' => '',
        'position' => '',
        'league_id' => '',
        'current_team_id' => '',
        'current_team_name' => '',
        'Numero' => '',
        'seleccio_id' => ''
    ];

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM `players` WHERE `id` = ?");
        $stmt->execute([$id]);
        $player = $stmt->fetch();
        if (!$player) die("Jugador no encontrado.");

        // Load previous teams
        $stmt = $pdo->prepare("SELECT * FROM `player_previous_teams` WHERE `player_id` = ? ORDER BY `team_name` ASC");
        $stmt->execute([$id]);
        $previousTeams = $stmt->fetchAll();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'name' => $_POST['name'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'country' => $_POST['country'] ?? '',
            'birth_year' => $_POST['birth_year'] ?: null,
            'position' => $_POST['position'] ?? '',
            'league_id' => $_POST['league_id'] ?? '',
            'current_team_id' => $_POST['current_team_id'] ?: null,
            'current_team_name' => '',
            'Numero' => $_POST['Numero'] ?: 0,
            'seleccio_id' => $_POST['seleccio_id'] ?: null
        ];

        // Fetch team name if team_id is selected
        if ($data['current_team_id']) {
            $stmt = $pdo->prepare("SELECT name FROM `teams` WHERE `id` = ?");
            $stmt->execute([$data['current_team_id']]);
            $data['current_team_name'] = $stmt->fetchColumn() ?: '';
        }

        if ($id) {
            $sql = "UPDATE `players` SET 
                    `name` = :name, `full_name` = :full_name, `country` = :country, 
                    `birth_year` = :birth_year, `position` = :position, `league_id` = :league_id, 
                    `current_team_id` = :current_team_id, `current_team_name` = :current_team_name, 
                    `Numero` = :Numero, `seleccio_id` = :seleccio_id 
                    WHERE `id` = :id";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $success = "Jugador actualizado correctamente.";
        } else {
            $sql = "INSERT INTO `players` (`name`, `full_name`, `country`, `birth_year`, `position`, `league_id`, `current_team_id`, `current_team_name`, `Numero`, `seleccio_id`) 
                    VALUES (:name, :full_name, :country, :birth_year, :position, :league_id, :current_team_id, :current_team_name, :Numero, :seleccio_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $id = $pdo->lastInsertId();
            $success = "Jugador añadido correctamente.";
        }

        // --- Save Previous Teams ---
        // Delete all existing previous teams for this player
        $stmt = $pdo->prepare("DELETE FROM `player_previous_teams` WHERE `player_id` = ?");
        $stmt->execute([$id]);

        // Insert the new list from POST
        $prevTeamNames = $_POST['prev_team_name'] ?? [];
        if (is_array($prevTeamNames)) {
            $insertStmt = $pdo->prepare("INSERT INTO `player_previous_teams` (`player_id`, `team_name`, `normalized_team_name`) VALUES (?, ?, ?)");
            foreach ($prevTeamNames as $teamName) {
                $teamName = trim($teamName);
                if ($teamName === '') continue;
                $normalized = strtolower(trim($teamName));
                $insertStmt->execute([$id, $teamName, $normalized]);
            }
        }
        
        // Refresh player data
        $stmt = $pdo->prepare("SELECT * FROM `players` WHERE `id` = ?");
        $stmt->execute([$id]);
        $player = $stmt->fetch();

        // Refresh previous teams
        $stmt = $pdo->prepare("SELECT * FROM `player_previous_teams` WHERE `player_id` = ? ORDER BY `team_name` ASC");
        $stmt->execute([$id]);
        $previousTeams = $stmt->fetchAll();
    }

} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;"><?php echo $pageTitle; ?></h2>
        <a href="players.php<?php echo $returnQuery ? '?' . $returnQuery : ''; ?>" class="btn" style="width: auto; background: #e2e8f0;"><i class="fas fa-arrow-left"></i> Volver</a>
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
                <label>Nombre Público (Corto)</label>
                <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($player['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="full_name" class="form-input" value="<?php echo htmlspecialchars($player['full_name']); ?>">
            </div>

            <div class="form-group">
                <label>País</label>
                <input type="text" name="country" class="form-input" value="<?php echo htmlspecialchars($player['country']); ?>">
            </div>

            <div class="form-group">
                <label>Año de Nacimiento</label>
                <input type="number" name="birth_year" class="form-input" value="<?php echo htmlspecialchars($player['birth_year']); ?>">
            </div>

            <div class="form-group">
                <label>Posición</label>
                <select name="position" class="form-input">
                    <option value="">Seleccionar...</option>
                    <option value="Delantero" <?php echo ($player['position'] == 'Delantero' || $player['position'] == 'Forward' || $player['position'] == 'Davanter' || $player['position'] == 'Avançado') ? 'selected' : ''; ?>>Delantero</option>
                    <option value="Defensa" <?php echo ($player['position'] == 'Defensa') ? 'selected' : ''; ?>>Defensa</option>
                    <option value="Medio" <?php echo ($player['position'] == 'Medio') ? 'selected' : ''; ?>>Medio</option>
                    <option value="Portero" <?php echo ($player['position'] == 'Portero' || $player['position'] == 'Porter') ? 'selected' : ''; ?>>Portero</option>
                </select>
            </div>

            <div class="form-group">
                <label>Dorsal (Número)</label>
                <input type="number" name="Numero" class="form-input" value="<?php echo htmlspecialchars($player['Numero']); ?>">
            </div>

            <div class="form-group">
                <label>Liga Actual</label>
                <select name="league_id" class="form-input" required>
                    <option value="">Seleccionar Liga...</option>
                    <?php foreach ($leagues as $l): ?>
                        <option value="<?php echo $l['id']; ?>" <?php echo ($player['league_id'] == $l['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($l['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Equipo Actual</label>
                <select name="current_team_id" id="team_select" class="form-input">
                    <option value="">Ninguno / Libre</option>
                </select>
            </div>

            <div class="form-group">
                <label>Selección Nacional</label>
                <select name="seleccio_id" class="form-input">
                    <option value="">Ninguna</option>
                    <?php foreach ($seleccions as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo ($player['seleccio_id'] == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- PREVIOUS TEAMS SECTION -->
            <div style="grid-column: 1 / -1; margin-top: 1.5rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--text);">
                        <i class="fas fa-history" style="margin-right: 6px; color: var(--secondary);"></i> Equipos Anteriores (Previous Teams)
                    </h3>
                    <button type="button" id="addPrevTeam" class="btn btn-primary btn-sm" style="width: auto;">
                        <i class="fas fa-plus"></i> Añadir Equipo
                    </button>
                </div>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">
                    Selecciona los equipos donde ha jugado anteriormente. Esto se usa para el HockeyGrid.
                </p>
                <div id="prevTeamsContainer">
                    <?php if (empty($previousTeams)): ?>
                        <div style="text-align: center; padding: 1.5rem; color: var(--text-muted); background: #f8fafc; border-radius: 8px; border: 2px dashed #e2e8f0;" id="noPrevTeams">
                            <i class="fas fa-info-circle"></i> Sin equipos anteriores registrados.
                        </div>
                    <?php else: ?>
                        <?php foreach ($previousTeams as $pt): ?>
                        <div class="prev-team-row" style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
                            <select name="prev_team_name[]" class="form-input" style="flex: 1;">
                                <option value="">Seleccionar equipo...</option>
                                <?php 
                                // Grupar equipos por liga para el select
                                $lastLeague = '';
                                foreach ($teams as $t): 
                                    if ($lastLeague != $t['league_name']):
                                        if ($lastLeague != '') echo '</optgroup>';
                                        echo '<optgroup label="'.htmlspecialchars($t['league_name']).'">';
                                        $lastLeague = $t['league_name'];
                                    endif;
                                ?>
                                    <option value="<?php echo htmlspecialchars($t['name']); ?>" <?php echo ($pt['team_name'] == $t['name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($t['name']); ?>
                                    </option>
                                <?php endforeach; if ($lastLeague != '') echo '</optgroup>'; ?>
                            </select>
                            <button type="button" class="btn btn-danger btn-sm removePrevTeam" style="width: auto; flex-shrink: 0;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div style="grid-column: 1 / -1; margin-top: 1rem; border-top: 1px solid #e2e8f0; padding-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="width: auto; min-width: 200px;">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// All teams data from PHP
const allTeams = <?php echo json_encode($teams); ?>;
const currentTeamId = "<?php echo $player['current_team_id'] ?? ''; ?>";

const leagueSelect = document.querySelector('select[name="league_id"]');
const teamSelect = document.getElementById('team_select');

function filterTeams() {
    const selectedLeagueId = leagueSelect.value;
    teamSelect.innerHTML = '<option value="">Ninguno / Libre</option>';
    if (!selectedLeagueId) return;
    const filtered = allTeams.filter(t => String(t.league_id) === String(selectedLeagueId));
    filtered.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t.id;
        opt.textContent = t.name;
        if (String(t.id) === String(currentTeamId)) opt.selected = true;
        teamSelect.appendChild(opt);
    });
}
leagueSelect.addEventListener('change', filterTeams);
filterTeams();

// --- Previous Teams Management ---
const container = document.getElementById('prevTeamsContainer');
const addBtn = document.getElementById('addPrevTeam');

// Generate options HTML for reuse
function getTeamsOptionsHtml() {
    let html = '<option value="">Seleccionar equipo...</option>';
    let lastLeague = '';
    allTeams.forEach(t => {
        if (lastLeague !== t.league_name) {
            if (lastLeague !== '') html += '</optgroup>';
            html += `<optgroup label="${t.league_name}">`;
            lastLeague = t.league_name;
        }
        html += `<option value="${t.name}">${t.name}</option>`;
    });
    if (lastLeague !== '') html += '</optgroup>';
    return html;
}

const teamsOptionsHtml = getTeamsOptionsHtml();

addBtn.addEventListener('click', function() {
    const placeholder = document.getElementById('noPrevTeams');
    if (placeholder) placeholder.remove();

    const row = document.createElement('div');
    row.className = 'prev-team-row';
    row.style.cssText = 'display: flex; gap: 10px; align-items: center; margin-bottom: 8px;';
    row.innerHTML = `
        <select name="prev_team_name[]" class="form-input" style="flex: 1;">
            ${teamsOptionsHtml}
        </select>
        <button type="button" class="btn btn-danger btn-sm removePrevTeam" style="width: auto; flex-shrink: 0;">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(row);
});

container.addEventListener('click', function(e) {
    const removeBtn = e.target.closest('.removePrevTeam');
    if (removeBtn) {
        removeBtn.closest('.prev-team-row').remove();
        if (container.querySelectorAll('.prev-team-row').length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 1.5rem; color: var(--text-muted); background: #f8fafc; border-radius: 8px; border: 2px dashed #e2e8f0;" id="noPrevTeams">
                    <i class="fas fa-info-circle"></i> Sin equipos anteriores registrados.
                </div>
            `;
        }
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

