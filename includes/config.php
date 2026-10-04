<?php

// Ruta al archivo de configuración
$configPath = __DIR__ . '/../config/config.json';

// Verificamos que el archivo exista antes de intentar leerlo
if (!file_exists($configPath)) {
    die('Error crítico: no se encontró el archivo de configuración.');
}

// Leemos el contenido del archivo como texto
$configContent = file_get_contents($configPath);

// Convertimos ese texto JSON en un array asociativo de PHP
$config = json_decode($configContent, true);

// Verificamos que el JSON sea válido
if ($config === null) {
    die('Error crítico: el archivo de configuración tiene un formato inválido.');
}

// Definimos el ambiente activo como una constante global,
// para poder usarlo en cualquier parte de la aplicación
define('APP_ENV', $config['environment']);