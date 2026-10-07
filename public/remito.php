<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;

// El número de remito viene por la URL, ej: remito.php?id=5
$numeroRemito = $_GET['id'] ?? null;

if (!$numeroRemito) {
    die('Falta especificar el número de remito.');
}

try {
    // 1. Traemos los datos generales del despacho
    $stmtDespacho = $pdo->prepare('SELECT * FROM despachos WHERE numero_remito = ?');
    $stmtDespacho->execute([$numeroRemito]);
    $despacho = $stmtDespacho->fetch(PDO::FETCH_ASSOC);

    if (!$despacho) {
        die('No se encontró el remito solicitado.');
    }

    // 2. Traemos el detalle: qué insumos y cantidades se despacharon
    $stmtDetalle = $pdo->prepare(
        'SELECT i.descripcion, dd.cantidad
         FROM detalle_despachos dd
         JOIN insumos i ON i.id_insumo = dd.id_insumo
         WHERE dd.numero_remito = ?'
    );
    $stmtDetalle->execute([$numeroRemito]);
    $detalle = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

    // 3. Armamos el HTML que se va a convertir en PDF
    $html = '<h2>Remito de Despacho N° ' . $numeroRemito . '</h2>';
    $html .= '<p><strong>Fecha:</strong> ' . $despacho['fecha_hora'] . '</p>';
    $html .= '<p><strong>Destino:</strong> ' . htmlspecialchars($despacho['destino']) . '</p>';
    $html .= '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
    $html .= '<tr><th>Insumo</th><th>Cantidad</th></tr>';

    foreach ($detalle as $item) {
        $html .= '<tr><td>' . htmlspecialchars($item['descripcion']) . '</td><td>' . $item['cantidad'] . '</td></tr>';
    }

    $html .= '</table>';

    // 4. Generamos el PDF con DomPDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 5. Lo enviamos al navegador para descargar
    $dompdf->stream("remito_$numeroRemito.pdf", ['Attachment' => true]);

} catch (PDOException $e) {
    manejarError('Error al generar el remito: ' . $e->getMessage());
}