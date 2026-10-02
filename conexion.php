<?php

$servername = getenv('DB_HOST');
$port       = getenv('DB_PORT') ?: '3306';
$username   = getenv('DB_USER');
$password   = getenv('DB_PASSWORD');
$dbname     = getenv('DB_NAME');

if (!$servername || !$username || !$dbname) {

    $localFile = __DIR__ . '/conexion.local.php';

    if (!file_exists($localFile)) {
        die("No se encontró la configuración de la base de datos.");
    }

    $local = require $localFile;

    $servername = $local['host'];
    $port       = $local['port'];
    $username   = $local['user'];
    $password   = $local['password'];
    $dbname     = $local['dbname'];
}

try {

    $conn = new PDO(
        "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("Error de conexión: " . $e->getMessage());

}
?>