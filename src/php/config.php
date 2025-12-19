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

$day = ["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"]; 
$month = [["janvier","Jan"], ["février","Fév"], ["mars","Mar"], ["avril","Avr"], ["mai","Mai"], ["juin","Juin"], ["juillet","Juil"], ["août","Août"], ["septembre","Sept"], ["octobre","Oct"], ["novembre","Nov"], ["décembre","Déc"]]; 
// Now
$date = explode('|', date("w|d|n|Y"));
// Given time
$timestamp = time();

?>