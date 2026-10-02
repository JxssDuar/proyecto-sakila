<?php

$railway = [
    'host' => getenv('MYSQLHOST'),
    'port' => getenv('MYSQLPORT'),
    'user' => getenv('MYSQLUSER'),
    'password' => getenv('MYSQLPASSWORD'),
    'dbname' => getenv('MYSQLDATABASE'),
];

$alternative = [
    'host' => getenv('DB_HOST'),
    'port' => getenv('DB_PORT'),
    'user' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'dbname' => getenv('DB_NAME'),
];

if (count(array_filter($railway, static function ($value) { return $value !== false; })) > 0) {
    $config = $railway;
} elseif (count(array_filter($alternative, static function ($value) { return $value !== false; })) > 0) {
    $config = $alternative;
} else {
    $localFile = __DIR__ . '/conexion.local.php';
    if (!is_file($localFile)) {
        die('No se encontró la configuración de la base de datos.');
    }
    $config = require $localFile;
}

if (!is_array($config)) {
    die('La configuración de la base de datos no es válida.');
}

foreach (['host', 'user', 'dbname'] as $key) {
    if (!isset($config[$key]) || !is_string($config[$key]) || trim($config[$key]) === '') {
        die('La configuración de la base de datos está incompleta.');
    }
}

if (!isset($config['password']) || !is_string($config['password'])) {
    die('La configuración de la base de datos está incompleta.');
}

$port = $config['port'] ?? false;
if ($port === false || $port === '') {
    $port = 3306;
}
$port = filter_var($port, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
if ($port === false) {
    die('El puerto de la base de datos no es válido.');
}

try {
    $conn = new mysqli($config['host'], $config['user'], $config['password'], $config['dbname'], $port);
    if ($conn->connect_error || !$conn->set_charset('utf8mb4')) {
        die('Error de conexión a la base de datos.');
    }
} catch (Exception $e) {
    die('Error de conexión a la base de datos.');
}
