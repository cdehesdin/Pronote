<?php

$host = 'localhost';
$db   = 'pronote';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $link = new mysqli($host, $user, $pass, $db);
    $link->set_charset($charset);
} catch (Exception $e) {
    die('Erreur BDD : ' . $e->getMessage());
}
?>