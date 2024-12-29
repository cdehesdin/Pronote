<?php
session_start();

// Définition du titre de la page
$title = "Accueil";

// Si utilisateur non connecté, renvoie vers la page de connexion
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
}

require './assets/header.php';
?>




<?php
require 'assets/footer.php';
?>