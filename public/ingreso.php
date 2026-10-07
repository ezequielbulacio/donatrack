<?php
require_once __DIR__ . '/../includes/config.php';

// Traemos los donantes e insumos para mostrarlos como opciones del formulario
$donantes = $pdo->query('SELECT * FROM donantes ORDER BY nombre_razon_social')->fetchAll(PDO::FETCH_ASSOC);
$insumos = $pdo->query('SELECT * FROM insumos ORDER BY descripcion')->fetchAll(PDO::FETCH_ASSOC);

$mensaje = '';

// Si el formulario fue enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idDonante = $_POST['id_donante'];
    $idInsumo = $_POST['id_insumo'];
    $cantidad = $_POST['cantidad'];

    try {
        // Iniciamos una transacción: o se guardan las dos cosas, o ninguna
        $pdo->beginTransaction();

        // 1. Insertamos el registro en la tabla ingresos
        $stmtIngreso = $pdo->prepare(
            'INSERT INTO ingresos (id_donante, id_insumo, cantidad, fecha_hora) VALUES (?, ?, ?, NOW())'
        );
        $stmtIngreso->execute([$idDonante, $idInsumo, $cantidad]);

        // 2. Sumamos esa cantidad al stock del insumo
        $stmtStock = $pdo->prepare(
            'UPDATE insumos SET cantidad_stock = cantidad_stock + ? WHERE id_insumo = ?'
        );
        $stmtStock->execute([$cantidad, $idInsumo]);

        // Si ambas consultas salieron bien, confirmamos los cambios
        $pdo->commit();

        $mensaje = '<div class="alert alert-success">Donación registrada correctamente.</div>';

        // Volvemos a traer los insumos actualizados para que se vea el nuevo stock
        $insumos = $pdo->query('SELECT * FROM insumos ORDER BY descripcion')->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Si algo falló, deshacemos todo lo hecho en la transacción
        $pdo->rollBack();
        manejarError('Error al registrar ingreso: ' . $e->getMessage());
    }
}

require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Registrar Ingreso / Donación</h1>

<?= $mensaje ?>

<form method="POST" class="col-md-6">

    <div class="mb-3">
        <label class="form-label">Donante</label>
        <select name="id_donante" class="form-select" required>
            <option value="">Seleccionar...</option>
            <?php foreach ($donantes as $donante): ?>
                <option value="<?= $donante['id_donante'] ?>">
                                                <option value="<?= $donante['id_donante'] ?>">
                    <?= htmlspecialchars($donante['nombre_razon_social']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Insumo</label>
        <select name="id_insumo" class="form-select" required>
            <option value="">Seleccionar...</option>
            <?php foreach ($insumos as $insumo): ?>
                <option value="<?= $insumo['id_insumo'] ?>">
                    <?= htmlspecialchars($insumo['descripcion']) ?> (stock actual: <?= $insumo['cantidad_stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Cantidad</label>
        <input type="number" name="cantidad" class="form-control" min="1" required>
    </div>

    <button type="submit" class="btn btn-primary">Registrar Donación</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>