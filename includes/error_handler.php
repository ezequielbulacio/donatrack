<?php

/**
 * Maneja un error de forma distinta según el ambiente activo.
 * En PRODUCTION: oculta el detalle técnico y lo guarda en el log.
 * En DEVELOPMENT/TESTING: muestra el detalle técnico en pantalla.
 */
function manejarError($mensajeError) {

    if (APP_ENV === 'PRODUCTION') {

        // 1. Guardamos el error real en el archivo de log, con fecha y hora
        $fechaHora = date('Y-m-d H:i:s');
        $lineaLog = "[$fechaHora] $mensajeError" . PHP_EOL;
        file_put_contents(__DIR__ . '/../logs/sistema_error.log', $lineaLog, FILE_APPEND);

        // 2. Mostramos un mensaje amigable al usuario, sin detalles técnicos
        die('Ocurrió un error inesperado. Por favor, intentá más tarde.');

    } else {

        // Mostramos el detalle técnico completo en una "caja de debug"
        die('<div style="background:#f8d7da; border:1px solid #f5c6cb; padding:15px; font-family:monospace;">
                <strong>DEBUG (' . APP_ENV . '):</strong><br>' . htmlspecialchars($mensajeError) . '
             </div>');
    }
}