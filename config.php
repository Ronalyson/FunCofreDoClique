<?php
// Configuração de timezone e banco
date_default_timezone_set('America/Fortaleza');

$db_host = 'localhost';
$db_name = 'cofre_clique';
$db_user = 'root';
$db_pass = '';

$pdo = new PDO(
    "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
    $db_user,
    $db_pass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

function sanitize_name($name) {
    $name = trim($name);
    $name = preg_replace('/\s+/', ' ', $name);
    return substr($name, 0, 80);
}

function sanitize_reason($reason) {
    $reason = trim($reason);
    return substr($reason, 0, 500);
}
