<?php
require_once __DIR__ . '/../includes/config.php';

$insumos = $pdo->query('SELECT * FROM insumos ORDER BY descripcion')->fetchAll(PDO::FETCH_ASSOC);
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idInsumo = $_POST['id_insumo'];
    $cantidad = $_POST['cantidad'];
    $destino = $_POST['destino'];

    try {
        // Consultamos el stock ACTUAL del insumo elegido
        $stmtStock = $pdo->prepare('SELECT cantidad_stock FROM insumos WHERE id_insumo = ?');
        $stmtStock->execute([$idInsumo]);
        $insumoActual = $stmtStock->fetch(PDO::FETCH_ASSOC);

        // Validación estricta: si no hay stock suficiente, lanzamos una excepción
        if ($cantidad > $insumoActual['cantidad_stock']) {
            throw new StockInsuficienteException(
                'Stock insuficiente. Disponible: ' . $insumoActual['cantidad_stock'] . ', solicitado: ' . $cantidad . '.'
            );
        }

        $pdo->beginTransaction();

        // 1. Creamos la cabecera del despacho
        $stmtDespacho = $pdo->prepare(
            'INSERT INTO despachos (fecha_hora, destino) VALUES (NOW(), ?)'
        );
        $stmtDespacho->execute([$destino]);
        $numeroRemito = $pdo->lastInsertId();

        // 2. Guardamos el detalle (qué insumo, cuánta cantidad)
        $stmtDetalle = $pdo->prepare(
            'INSERT INTO detalle_despachos (numero_remito, id_insumo, cantidad) VALUES (?, ?, ?)'
        );
        $stmtDetalle->execute([$numeroRemito, $idInsumo, $cantidad]);

        // 3. Restamos del stock
        $stmtRestar = $pdo->prepare(
            'UPDATE insumos SET cantidad_stock = cantidad_stock - ? WHERE id_insumo = ?'
        );
        $stmtRestar->execute([$cantidad, $idInsumo]);

        $pdo->commit();

        $mensaje = '<div class="alert alert-success">
            Despacho N° ' . $numeroRemito . ' registrado correctamente.
            <a href="remito.php?id=' . $numeroRemito . '" class="btn btn-sm btn-outline-success ms-2" target="_blank">
                Descargar remito PDF
            </a>
        </div>';

        $insumos = $pdo->query('SELECT * FROM insumos ORDER BY descripcion')->fetchAll(PDO::FETCH_ASSOC);

    } catch (StockInsuficienteException $e) {
        // Al usuario siempre se le explica qué pasó
        $mensaje = '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';

        if (APP_ENV === 'PRODUCTION') {
            // En producción, el evento queda guardado en el log
            registrarEnLog('Despacho rechazado: ' . $e->getMessage());
        } else {
            // En desarrollo/testing, además mostramos la caja de debug
            $mensaje .= '<div style="background:#f8d7da; border:1px solid #f5c6cb; padding:15px; font-family:monospace;">
                <strong>DEBUG (' . APP_ENV . '):</strong><br>StockInsuficienteException: ' . htmlspecialchars($e->getMessage()) . '
            </div>';
        }

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        manejarError('Error al registrar despacho: ' . $e->getMessage());
    }
}

require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Registrar Despacho de Materiales</h1>

<?= $mensaje ?>

<form method="POST" class="col-md-6">

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
        <label class="form-label">Cantidad a despachar</label>
        <input type="number" name="cantidad" class="form-control" min="1" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Destino</label>
        <input type="text" name="destino" class="form-control" placeholder="Centro comunitario..." required>
    </div>

    <button type="submit" class="btn btn-primary">Registrar Despacho</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>