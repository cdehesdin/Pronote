<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Sécurité
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Professeur') {
    echo json_encode([
        'success' => false,
        'message' => 'Accès refusé'
    ]);
    exit;
}

// Récupération des données
$idEleve        = intval($_POST['eleve'] ?? 0);
$idEnseignement = intval($_POST['enseignement'] ?? 0);
$appreciation   = trim($_POST['appreciation'] ?? '');

// Vérifications
if ($idEleve === 0 || $idEnseignement === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Données invalides'
    ]);
    exit;
}

// Requête SQL
if ($appreciation === '') {
    $sql = "UPDATE AppEleve 
            SET app = NULL 
            WHERE idEleve = ? AND idEnseignement = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEleve, $idEnseignement);
} else {
    $sql = "UPDATE AppEleve 
            SET app = ? 
            WHERE idEleve = ? AND idEnseignement = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $appreciation, $idEleve, $idEnseignement);
}

mysqli_stmt_execute($stmt);

echo json_encode([
    'success' => true
]);
