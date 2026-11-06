<?php
require_once __DIR__ . '/../includes/config.php';

// Categorías válidas (las mismas que el ENUM de la tabla insumos)
$categorias = ['Alimentos', 'Ropa', 'Medicamentos', 'Sanitarios'];

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
        // Iniciamos una transacción: o se guarda todo, o nada
        $pdo->beginTransaction();

        // 0. Si eligió "Nuevo insumo", primero lo creamos
        if ($idInsumo === 'nuevo') {
            $descripcion = trim($_POST['descripcion_nueva'] ?? '');
            $categoria = $_POST['categoria_nueva'] ?? '';

            if ($descripcion === '' || !in_array($categoria, $categorias, true)) {
                throw new Exception('Para un insumo nuevo hay que indicar una descripción y una categoría válida.');
            }

            $stmtNuevo = $pdo->prepare(
                'INSERT INTO insumos (descripcion, cantidad_stock, categoria) VALUES (?, 0, ?)'
            );
            $stmtNuevo->execute([$descripcion, $categoria]);

            // Nos quedamos con el código que MySQL le asignó al insumo nuevo
            $idInsumo = $pdo->lastInsertId();
        }

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

        // Si todo salió bien, confirmamos los cambios
        $pdo->commit();

        $mensaje = '<div class="alert alert-success">Donación registrada correctamente.</div>';

        // Volvemos a traer los insumos actualizados para que se vea el nuevo stock
        $insumos = $pdo->query('SELECT * FROM insumos ORDER BY descripcion')->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Error de base de datos: deshacemos todo y lo manejamos según el ambiente
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        manejarError('Error al registrar ingreso: ' . $e->getMessage());

    } catch (Exception $e) {
        // Datos inválidos cargados por el usuario: deshacemos y le avisamos
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $mensaje = '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';
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
                    <?= htmlspecialchars($donante['nombre_razon_social']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Insumo</label>
        <select name="id_insumo" id="id_insumo" class="form-select" required>
            <option value="">Seleccionar...</option>
            <option value="nuevo">➕ Nuevo insumo...</option>
            <?php foreach ($insumos as $insumo): ?>
                <option value="<?= $insumo['id_insumo'] ?>">
                    <?= htmlspecialchars($insumo['descripcion']) ?> (stock actual: <?= $insumo['cantidad_stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Este bloque solo se ve cuando se elige "Nuevo insumo" -->
    <div id="bloque-nuevo" class="border rounded p-3 mb-3" style="display: none;">
        <div class="mb-3">
            <label class="form-label">Descripción del nuevo insumo</label>
            <input type="text" name="descripcion_nueva" id="descripcion_nueva" class="form-control" maxlength="200">
        </div>
        <div class="mb-0">
            <label class="form-label">Categoría</label>
            <select name="categoria_nueva" id="categoria_nueva" class="form-select">
                <option value="">Seleccionar...</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat ?>"><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Cantidad</label>
        <input type="number" name="cantidad" class="form-control" min="1" required>
    </div>

    <button type="submit" class="btn btn-primary">Registrar Donación</button>
</form>

<script>
    // Mostramos u ocultamos los campos del insumo nuevo según lo que se elija
    const selectInsumo = document.getElementById('id_insumo');
    const bloqueNuevo = document.getElementById('bloque-nuevo');
    const descripcionNueva = document.getElementById('descripcion_nueva');
    const categoriaNueva = document.getElementById('categoria_nueva');

    function actualizarFormulario() {
        const esNuevo = selectInsumo.value === 'nuevo';
        bloqueNuevo.style.display = esNuevo ? 'block' : 'none';
        descripcionNueva.required = esNuevo;
        categoriaNueva.required = esNuevo;
    }

    selectInsumo.addEventListener('change', actualizarFormulario);
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>