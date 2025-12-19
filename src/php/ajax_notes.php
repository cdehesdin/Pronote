<?php
session_start();
require_once __DIR__ . '/config.php';

$IdEleve = $_SESSION['id'];
$ClasseEleve = $_SESSION['classe'];

// Définition des listes utilisés
$listMatiere = [
    'Maths' => [1 => '#ff8c00', 2 => 'Mathématiques'],
    'Fran' => [1 => '#27ae60', 2 => 'Français'],
    'Ang' => [1 => '#d35400', 2 => 'Anglais'],
    'All' => [1 => '#2980b9', 2 => 'Allemand'],
    'PC' => [1 => '#f1c40f', 2 => 'Physique Chimie'],
    'SVT' => [1 => '#c0392b', 2 => 'Sciences de la vie et de la Terre'],
    'EPS' => [1 => '#9b59b6', 2 => 'Éducation physique et sportive'],
    'HG' => [1 => '#00cccc', 2 => 'Histoire Géographie'],
];
$moyenneClasse = $moyenneParMatiere = [];

if ($_SESSION['role'] == 'Eleve' || ($_SESSION['role'] == 'Parent' && isset($_GET['enfants']))) {
    // Enregistrement des moyennes de la classe par matière dans des tableaux
    $queryNameMatiere = "SELECT DISTINCT Matiere.nom FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Enseignement.idClasse = " . $ClasseEleve . " AND Notes.note <> 'NULL' AND Notes.note <> 'Abs' AND Notes.note <> 'N.Not' AND Notes.note <> 'Disp';";
    $reqNameMatiere = mysqli_query($link,$queryNameMatiere);
    while ($idNameMatiere = mysqli_fetch_array($reqNameMatiere)) {
        $queryClasseEleves = "SELECT idEleve FROM Eleves WHERE idClasse = " . $ClasseEleve . ";";
        $reqClasseEleves = mysqli_query($link,$queryClasseEleves);
        $moyenneFinale = $moyennePlus = $moyennePlus = null;
        $nbreEleve = 0;
        while ($idClasseEleves = mysqli_fetch_array($reqClasseEleves)) {
            $queryMoyenneParEleve = "SELECT Notes.note, Controles.coefficient FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Matiere.nom = '" . $idNameMatiere['nom'] . "' AND Notes.idEleve = " . $idClasseEleves['idEleve'] . " AND Notes.note <> 'NULL' AND Notes.note <> 'Abs' AND Notes.note <> 'N.Not' AND Notes.note <> 'Disp';";
            $reqMoyenneParEleve = mysqli_query($link,$queryMoyenneParEleve);
            $sommeNote = $sommeNoteNbre = $moyenne = NULL;
            while ($idMoyenneParEleve = mysqli_fetch_array($reqMoyenneParEleve)) {
                $sommeNote += (float)$idMoyenneParEleve['note'] * $idMoyenneParEleve['coefficient'];
                $sommeNoteNbre += $idMoyenneParEleve['coefficient'];
            }
            if (gettype($sommeNote) != 'NULL') {
                $moyenneClasse[$idNameMatiere['nom']][$idClasseEleves['idEleve']] = $sommeNote / $sommeNoteNbre;
            }
        }
    }

    // Enregistrement des moyennes de la classe par matière dans des tableaux
    foreach ($moyenneClasse as $matiereMoyenneEleve => $moyenneEleve) {
        $sommeNote = $sommeNoteNbre = NULL;
        foreach ($moyenneEleve as $noteMoyenneEleve) {
            $sommeNote += $noteMoyenneEleve;
            $sommeNoteNbre++;
        }
        $moyenneParMatiere[$matiereMoyenneEleve] = $sommeNote / $sommeNoteNbre;
    }
}
?>

<?php if (isset($_POST['notes'])): ?>
    <?php
    $queryNoteEleve = "SELECT Notes.note, Controles.idControle, Controles.nomControle, Controles.dates, Matiere.nom, Matiere.description FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Notes.idControle = " . $_POST['notes'] . " AND Notes.idEleve = " . $IdEleve . " AND Notes.note <> 'NULL';";
    $reqNoteEleve = mysqli_query($link,$queryNoteEleve);
    while ($idNoteEleve = mysqli_fetch_array($reqNoteEleve)): ?>
        <div class="d-flex pb-3">
            <div class="column-note-icon"> <span class="logo-note"> &#8530; </span> </div>
                <div class="titre-de-la-note">
                    <span class="descriptif-notes-matiere"> <?= $idNoteEleve['description'] ?> </span> <br>
                    <span class="descriptif-notes-name-controle"> <?= $idNoteEleve['nomControle'] ?> </span> <br>
                    <span class="descriptif-notes-moyenne">
                        Note du
                        <?php
                        $date = explode('|', date_format(date_create($idNoteEleve['dates']), 'd|n|Y'));
                        echo $date[0] . ' ' . $month[$date[1]-1][0] . ' ' . $date[2];
                        ?>
                    </span>
                </div>
            </div>
            <div class="description-de-la-note">
                <div class="description-note">
                    <span class="notes-eleves"> Note élève : </span>
                    <span class="notes-eleves"> <?php if (gettype($idNoteEleve['note']) != 'NULL' && $idNoteEleve['note'] != 'Abs' && $idNoteEleve['note'] != 'N.Not' && $idNoteEleve['note'] != 'Disp') {echo number_format((float)$idNoteEleve['note'], 2, ',', ' ');} elseif (gettype($idNoteEleve['note']) == 'NULL') {echo 'N.Not';} else {echo $idNoteEleve['note'];} ?> </span>
                </div>
                <?php
                $queryControleEleveMoyenne = "SELECT Notes.note, Controles.coefficient FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle WHERE Notes.idControle = " . $idNoteEleve['idControle'] . " AND Notes.note <> 'NULL' AND Notes.note <> 'Abs' AND Notes.note <> 'N.Not' AND Notes.note <> 'Disp';";
                $reqControleEleveMoyenne = mysqli_query($link,$queryControleEleveMoyenne);
                $sommeNote = $sommeNoteNbre = $notePlus = $noteMoins = NULL;
                while ($idControleEleveMoyenne = mysqli_fetch_array($reqControleEleveMoyenne)) {
                    if (gettype($idControleEleveMoyenne['note']) != 'NULL' && $idControleEleveMoyenne['note'] != 'N.Not' && $idControleEleveMoyenne['note'] != 'Abs' && $idControleEleveMoyenne['note'] != 'Disp') {
                        $sommeNote += (float)$idControleEleveMoyenne['note'];
                        $sommeNoteNbre ++;
                        $coefficient = $idControleEleveMoyenne['coefficient'];
                        if (!isset($notePlus)) {
                            $notePlus = (float)$idControleEleveMoyenne['note'];
                        } elseif ($notePlus < (float)$idControleEleveMoyenne['note']) {
                            $notePlus = (float)$idControleEleveMoyenne['note'];
                        }
                        if (!isset($noteMoins)) {
                            $noteMoins = (float)$idControleEleveMoyenne['note'];
                        } elseif ($noteMoins > (float)$idControleEleveMoyenne['note']) {
                            $noteMoins = (float)$idControleEleveMoyenne['note'];
                        }
                    }
                }
                ?>
                <div class="description-note">
                    <span class=""> Moyenne classe : </span> <?php if (gettype($sommeNote) != 'NULL') {echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');} else {echo 'N.Not';} ?>
                </div>
                <div class="description-note">
                    <span class=""> Note la plus haute : </span> <?= number_format($notePlus, 2, ',', ' ') ?>
                </div>
                <div class="description-note">
                    <span class=""> Note la plus basse : </span> <?= number_format($noteMoins, 2, ',', ' ') ?>
                </div>
                <div class="description-note"> <span class=""> Coefficient : </span> <?= $coefficient ?> </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php elseif (isset($_POST['matieres'])): ?>
    <div class="d-flex pb-3">
        <div class="column-note-icon"> <span class="logo-note"> &#8530; </span> </div>
        <div class="titre-de-la-note mt-2"> <span class="descriptif-notes-matiere"> <?= $listMatiere[$_POST['matieres']][2] ?> </span> </div>
    </div>
    <div class="description-de-la-note">
        <div class="description-note">
            <span class="notes-eleves"> Moyenne élève : </span>
            <span class="notes-eleves"> <?php if (isset($moyenneClasse[$_POST['matieres']][$IdEleve])) {echo number_format($moyenneClasse[$_POST['matieres']][$IdEleve], 2, ',', ' ');} else {echo 'N.Not';} ?> </span>
        </div>
        <div class="description-note">
            <span class=""> Moyenne classe : </span> <?php if (isset($moyenneParMatiere[$_POST['matieres']])) {echo number_format($moyenneParMatiere[$_POST['matieres']], 2, ',', ' ');} else {echo 'None';} ?> </span>
        </div>
        <?php
        $querynbreNote = "SELECT COUNT(Controles.idControle) FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Matiere.nom= '" . $_POST['matieres'] . "' AND Notes.idEleve = " . $IdEleve . " AND Notes.note <> 'NULL';";
        $reqnbreNote = mysqli_query($link,$querynbreNote);
        while ($idnbreNote = mysqli_fetch_array($reqnbreNote)) {$nbreNote = $idnbreNote[0];}
        $moyenneMoins = $moyennePlus = null;
        foreach ($moyenneClasse as $matiereMoyenneEleve => $moyenneEleve) {
            if ($matiereMoyenneEleve == $_POST['matieres']) {
                foreach ($moyenneEleve as $noteMoyenneEleve) {
                    if (!isset($moyennePlus)) {
                        $moyennePlus = $noteMoyenneEleve;
                    } elseif ($moyennePlus < $noteMoyenneEleve) {
                        $moyennePlus = $noteMoyenneEleve;
                    }
                    if (!isset($moyenneMoins)) {
                        $moyenneMoins = $noteMoyenneEleve;
                    } elseif ($moyenneMoins > $noteMoyenneEleve) {
                        $moyenneMoins = $noteMoyenneEleve;
                    }
                }
            }
        }
        ?>
        <div class="description-note">
            <span class=""> Moyenne la plus haute : </span> <?= number_format($moyennePlus, 2, ',', ' ') ?> </span>
        </div>
        <div class="description-note">
            <span class=""> Moyenne la plus basse : </span> <?= number_format($moyenneMoins, 2, ',', ' ') ?> </span>
        </div>
        <div class="description-note"> <span class=""> Nombre de notes : </span> <?= $nbreNote ?> </div>
    </div>
<?php endif; ?>