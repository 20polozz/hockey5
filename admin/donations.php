<?php
$pageTitle = 'Donaciones';
$currentPage = 'donations';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = trim($_POST['donation_current'] ?? '');
    $goal = trim($_POST['donation_goal'] ?? '');

    if (!is_numeric($current) || (float)$current < 0) {
        $error = "El importe recaudado debe ser un número válido (0 o más).";
    } elseif (!is_numeric($goal) || (float)$goal <= 0) {
        $error = "La meta debe ser un número mayor que 0.";
    } else {
        set_setting('donation_current', (string)$current);
        set_setting('donation_goal', (string)$goal);
        $success = "Datos de donación actualizados correctamente.";
    }
}

$donationCurrent = get_setting('donation_current', '0');
$donationGoal = get_setting('donation_goal', '150');
?>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.1rem; font-weight: 700;">Meta de Donaciones (Mantenimiento Web)</h2>
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

        <p style="color:#64748b; margin-bottom: 1.5rem;">
            Estos valores controlan la barra de progreso de donaciones que se muestra en la portada,
            junto al botón de PayPal. Actualiza el importe recaudado manualmente a medida que recibas donaciones.
        </p>

        <form method="POST" class="form-grid">
            <div class="form-group">
                <label>Importe recaudado actualmente (€)</label>
                <input type="number" step="0.01" min="0" name="donation_current" class="form-input"
                       value="<?php echo htmlspecialchars($donationCurrent); ?>" required>
            </div>

            <div class="form-group">
                <label>Meta / objetivo (€)</label>
                <input type="number" step="0.01" min="0.01" name="donation_goal" class="form-input"
                       value="<?php echo htmlspecialchars($donationGoal); ?>" required>
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
