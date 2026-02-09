<?php
$host = 'sql210.infinityfree.com';
$db = 'if0_38903136_stock_chacra';
$user = 'if0_38903136';
$pass = 'Lchpedro2025'; // reemplazá por la que se ve oculta en la captura
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
