<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'updateNote') {
        $idEleve = $_POST['idEleve'];
        $idControle = $_POST['idControle'];
        $note = $_POST['note'];  // La nouvelle note envoyée
        $classe = $_POST['classe'];  // La classe sélectionnée

        // Sécuriser les données (important pour éviter les injections SQL)
        $note = mysqli_real_escape_string($link, $note);
        $classe = mysqli_real_escape_string($link, $classe);

        // Récupère la classe et l'élève
        $queryCheckClass = "SELECT idClasse FROM Classe WHERE nom = '$classe';";
        $result = mysqli_query($link, $queryCheckClass);
        $classData = mysqli_fetch_assoc($result);
        $idClasse = $classData['idClasse'];

        // Vérifier si l'élève fait partie de cette classe
        $queryCheckEleve = "SELECT * FROM Eleves WHERE idClasse = $idClasse AND idEleve = $idEleve;";
        $reqCheckEleve = mysqli_query($link, $queryCheckEleve);

        if (mysqli_num_rows($reqCheckEleve) > 0) {
            // Si l'élève est bien dans la classe, on met à jour la note
            if ($note == '') {
                $queryAjoutNote = "UPDATE Notes SET note = NULL WHERE idEleve = $idEleve AND idControle = $idControle;";
            } elseif ($note == 'A') {
                $queryAjoutNote = "UPDATE Notes SET note = 'Abs' WHERE idEleve = $idEleve AND idControle = $idControle;";
            } elseif ($note == 'D') {
                $queryAjoutNote = "UPDATE Notes SET note = 'Disp' WHERE idEleve = $idEleve AND idControle = $idControle;";
            } elseif ($note == 'N') {
                $queryAjoutNote = "UPDATE Notes SET note = 'N.Not' WHERE idEleve = $idEleve AND idControle = $idControle;";
            } elseif (floatval(str_replace(",", ".", $note)) >= 0 && floatval(str_replace(",", ".", $note)) <= 20) {
                $noteFloat = floatval(str_replace(",", ".", $note));
                $queryAjoutNote = "UPDATE Notes SET note = $noteFloat WHERE idEleve = $idEleve AND idControle = $idControle;";
            }

            // Exécute la mise à jour de la note
            if (mysqli_query($link, $queryAjoutNote)) {
                echo "Note mise à jour avec succès";
            } else {
                echo "Erreur lors de la mise à jour de la note: " . mysqli_error($link);
            }
        } else {
            echo "L'élève ne fait pas partie de cette classe.";
        }
    }
}
?>