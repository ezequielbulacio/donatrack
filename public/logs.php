<?php
require_once __DIR__ . '/../includes/config.php';

$rutaLog = __DIR__ . '/../logs/sistema_error.log';
$lineas = [];

// Verificamos que el archivo exista antes de intentar leerlo
if (file_exists($rutaLog)) {
    $lineas = file($rutaLog, FILE_IGNORE_NEW_LINES);
}

require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Historial de Logs y Auditoría</h1>

<p>Ambiente activo: <strong><?= APP_ENV ?></strong></p>

<?php if (empty($lineas)): ?>

    <div class="alert alert-info">No hay eventos registrados en el log todavía.</div>

<?php else: ?>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Evento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_reverse($lineas) as $linea): ?>
            <tr>
                <td><?= htmlspecialchars($linea) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>