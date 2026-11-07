<?php
require_once __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-2"><?= htmlspecialchars($config['app_name']) ?></h1>
<p class="text-muted mb-4">Gestión, auditoría y control de stock de suministros y donaciones.</p>

<div class="row g-3">

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Inventario</h5>
                <p class="card-text">Consultá las categorías y cantidades actuales de suministros.</p>
                <a href="inventario.php" class="btn btn-dark">Ver inventario</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Registrar ingreso</h5>
                <p class="card-text">Cargá una donación nueva o sumá stock a un insumo existente.</p>
                <a href="ingreso.php" class="btn btn-dark">Registrar ingreso</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Registrar despacho</h5>
                <p class="card-text">Entregá materiales a un centro comunitario y descargá el remito en PDF.</p>
                <a href="despacho.php" class="btn btn-dark">Registrar despacho</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Historial de logs</h5>
                <p class="card-text">Revisá los eventos y errores registrados por el sistema.</p>
                <a href="logs.php" class="btn btn-dark">Ver logs</a>
            </div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>