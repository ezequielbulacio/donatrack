<?php
require_once __DIR__ . '/../includes/config.php';

// Traemos todos los insumos de la base de datos, ordenados por categoría
$stmt = $pdo->query('SELECT * FROM insumos ORDER BY categoria, descripcion');
$insumos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Inventario Disponible</h1>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Código</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($insumos as $insumo): ?>
        <tr>
            <td><?= $insumo['id_insumo'] ?></td>
            <td><?= htmlspecialchars($insumo['descripcion']) ?></td>
            <td><?= htmlspecialchars($insumo['categoria']) ?></td>
            <td><?= $insumo['cantidad_stock'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../includes/footer.php'; ?>