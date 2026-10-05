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

// Intentamos conectar a la base de datos
try {
    $dbConfig = $config['database'];

    // DSN: la "dirección" de conexión con host, nombre de base y charset
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['db_name']};charset={$dbConfig['charset']}";

    // Creamos la conexión usando PDO
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);

    // Si algo sale mal en una consulta, que lance una excepción (en vez de fallar en silencio)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Acá todavía no decidimos qué hacer con el error - eso lo resolvemos en el próximo paso
    die('Error de conexión: ' . $e->getMessage());
}