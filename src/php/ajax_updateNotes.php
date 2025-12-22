<?php
session_start();
require_once __DIR__ . '/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Sécurité minimale
if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "error" => "Non autorisé"]);
    exit;
}

/* =========================
   CRÉATION DEVOIR
========================= */
if (isset($_POST['commentaire'], $_POST['coeff'], $_POST['date'], $_POST['classe'])) {

    $queryIdEnseignement = "
        SELECT Enseignement.idEnseignement
        FROM Enseignement
        INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse
        WHERE Enseignement.idProf = {$_SESSION['id']}
        AND Classe.nom = '{$_POST['classe']}'
    ";

    $req = mysqli_query($link, $queryIdEnseignement);

    while ($row = mysqli_fetch_assoc($req)) {

        $queryAjoutDevoir = "
            INSERT INTO Controles (idEnseignement, nomControle, coefficient, dates)
            VALUES ({$row['idEnseignement']}, '{$_POST['commentaire']}', {$_POST['coeff']}, '{$_POST['date']}')
        ";

        if (mysqli_query($link, $queryAjoutDevoir)) {

            $queryEleves = "
                SELECT Eleves.idEleve, Controles.idControle
                FROM Enseignement
                INNER JOIN Controles ON Controles.idEnseignement = Enseignement.idEnseignement
                INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse
                INNER JOIN Eleves ON Eleves.idClasse = Classe.idClasse
                WHERE Enseignement.idProf = {$_SESSION['id']}
                AND Classe.nom = '{$_POST['classe']}'
                AND Controles.nomControle = '{$_POST['commentaire']}'
                AND Controles.dates = '{$_POST['date']}'
            ";

            $res = mysqli_query($link, $queryEleves);
            $values = [];

            while ($e = mysqli_fetch_assoc($res)) {
                $values[] = "({$e['idEleve']}, {$e['idControle']})";
            }

            if ($values) {
                mysqli_query($link, "INSERT INTO Notes (idEleve, idControle) VALUES " . implode(',', $values));
            }
        }
    }

    echo json_encode(["success" => true]);
    exit;
}

/* =========================
    SUPPRESSION
========================= */
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            // ton code création
            break;
        case 'update':
            $id = $_POST['idControle'];
            $nom = $_POST['nomDevoir'];
            $date = $_POST['dateDevoir'];
            $coeff = $_POST['coeffDevoir'];
            mysqli_query($link, "
                UPDATE Controles
                SET nomControle='$nom', coefficient=$coeff, dates='$date'
                WHERE idControle=$id
            ");
            echo json_encode(["success"=>true]);
            exit;
        case 'delete':
            $id = $_POST['idControle'];
            mysqli_query($link, "DELETE FROM Notes WHERE idControle=$id");
            mysqli_query($link, "DELETE FROM Controles WHERE idControle=$id");
            echo json_encode(["success"=>true]);
            exit;
    }
}
?>