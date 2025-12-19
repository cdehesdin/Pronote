<?php
session_start();
require_once __DIR__ . '/src/php/config.php';

// Définition du titre de la page
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'Eleve') {
        $title = "Mes notes";
    } elseif ($_SESSION['role'] == 'Parent') {
        $title = "Les notes";
    };
} else {
    header('Location: ./index.php');
    exit;
}

// Sélection du tri des notes (par chronologie ou par matière)
if (isset($_POST['notes'])) {
    $_POST['ordres'] = 'chronologique';
}
if (isset($_POST['notesM']) || isset($_POST['matieres'])) {
    $_POST['ordres'] = 'matiere';
}

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


if (!isset($_GET['enfants'])) {$_GET['enfants'] = 0;}

if ($_SESSION['role'] == 'Eleve') {
    $IdEleve = $_SESSION['id'];
    $ClasseEleve = $_SESSION['classe'];
} elseif ($_SESSION['role'] == 'Parent') {
    $IdEleve = $_SESSION['idEleve'][$_GET['enfants']];
    $ClasseEleve = $_SESSION['classe'][$_GET['enfants']];
}

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

<?php ob_start(); ?>

<link rel="stylesheet" href="./src/css/notes.css">
<script src="./src/js/ajax_notes.js" defer></script>

<?php $style_script = ob_get_clean(); ?>


<?php ob_start(); ?>

<?php if (isset($_SESSION['login'])):?>
    <?php if ($_SESSION['role'] == 'Eleve' || ($_SESSION['role'] == 'Parent' && isset($_GET['enfants']))): ?>
        <div class="detail-notes">
            <div class="column-notes">
                <ul>
                    <?php if (!isset($_POST['ordres']) || $_POST['ordres'] == 'chronologique'): ?>
                        <?php
                        $queryControleEleve = "SELECT Notes.note, Controles.idControle, Controles.nomControle, Controles.dates, Matiere.description, Matiere.nom FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Notes.idEleve = " . $IdEleve . " AND Notes.note <> 'NULL' ORDER BY Controles.dates DESC;";
                        $reqControleEleve = mysqli_query($link,$queryControleEleve);
                        while ($idControleEleve = mysqli_fetch_array($reqControleEleve)): ?>
                            <li class="<?php if (isset($_POST['notes']) && $_POST['notes'] == $idControleEleve['idControle']) {echo 'notes-active';} ?>">
                                <?php
                                $queryControleMoyenne = "SELECT Notes.note, Controles.coefficient FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle WHERE Notes.idControle = " . $idControleEleve['idControle'] . ";";
                                $reqControleMoyenne = mysqli_query($link,$queryControleMoyenne);
                                $sommeNote = $sommeNoteNbre = NULL;
                                while ($idControleMoyenne = mysqli_fetch_array($reqControleMoyenne)) {
                                    if ($idControleMoyenne['note'] != 'Abs' && $idControleMoyenne['note'] != 'N.Not' && $idControleMoyenne['note'] != 'Disp') {
                                        $sommeNote += (float)$idControleMoyenne['note'];
                                        $sommeNoteNbre++;
                                    }
                                }
                                ?>
                                <form class="forms_Notes" method="POST">
                                    <input class="form-check-input d-none" type="radio" name="notes" 
                                        id="note<?= $idControleEleve['idControle'] ?>" 
                                        value="<?= $idControleEleve['idControle'] ?>" />
                                    <label for="note<?= $idControleEleve['idControle'] ?>" class="d-flex" id="idNote<?= $idControleEleve['idControle'] ?>">
                                        <div class="column-notes-dates">
                                            <div class="date-notes">
                                                <?php
                                                $date = explode('|', date_format(date_create($idControleEleve['dates']), 'd|n'));
                                                echo $date[0] . ' ' . $month[$date[1]-1][1];
                                                ?>
                                            </div>
                                            <div class="after-date-notes" style="background-color: <?= $listMatiere[$idControleEleve['nom']][1] ?>;"></div>
                                        </div>
                                        <div class="descriptif-notes">
                                            <div class="d-flex w-100 justify-content-between">
                                                <span class="descriptif-notes-matiere"><?= $idControleEleve['description'] ?></span>
                                                <span class="descriptif-notes-de-la-matiere">
                                                    <?= $idControleEleve['note'] != 'Abs' && $idControleEleve['note'] != 'N.Not' && $idControleEleve['note'] != 'Disp' ? number_format((float)$idControleEleve['note'], 2, ',', ' ') : $idControleEleve['note'] ?>
                                                </span>
                                            </div>
                                            <span class="descriptif-notes-name-controle"><?= $idControleEleve['nomControle'] ?></span><br>
                                            <span class="descriptif-notes-moyenne">Moyenne de la classe : 
                                                <?php 
                                                    echo (gettype($sommeNote) != 'NULL') ? number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ') : 'N.Not';
                                                ?>
                                            </span>
                                        </div>
                                    </label>
                                </form>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <?php
                        $queryListeMatiere = "SELECT DISTINCT Matiere.description, Matiere.nom, Matiere.idMatiere FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Notes.idEleve = " . $IdEleve . " AND Notes.note <> 'NULL' ORDER BY Matiere.description;";
                        $reqListeMatiere = mysqli_query($link,$queryListeMatiere);
                        while ($idListeMatiere = mysqli_fetch_array($reqListeMatiere)): ?>
                            <form class="forms_Matieres" method="POST">
                                <li class="<?php if (isset($_POST['matieres']) && $_POST['matieres'] == $idListeMatiere['nom']) {echo 'notes-active';} ?>" style="margin-bottom:0; padding: 0;">
                                    <input class="form-check-input d-none" type="radio" name="matieres" 
                                        id="mat<?= $idListeMatiere['nom'] ?>" 
                                        value="<?= $idListeMatiere['nom'] ?>" />
                                    <label for="mat<?= $idListeMatiere['nom'] ?>" class="d-flex" id="idMatiere<?= $idListeMatiere['nom'] ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="bordure-nom-matiere" style="background-color: <?= $listMatiere[$idListeMatiere['nom']][1] ?>;"></div>
                                                <div class="nom-matiere"> <?= $idListeMatiere['description'] ?> </div>
                                            </div>
                                            <span class="nom-note"> <?php if (isset($moyenneClasse[$idListeMatiere['nom']][$IdEleve])) {echo number_format($moyenneClasse[$idListeMatiere['nom']][$IdEleve], 2, ',', ' ');} else {echo 'N.Not';} ?> </span>
                                        </div>
                                    </label>
                                </li>
                            </form>
                            <?php
                            $queryControleMatEleve = "SELECT Notes.note, Controles.idControle, Controles.nomControle, Controles.dates, Matiere.nom FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Matiere.nom = '" . $idListeMatiere['nom'] . "' AND Notes.idEleve = " . $IdEleve . " AND Notes.note <> 'NULL' ORDER BY Controles.dates DESC;";
                            $reqControleMatEleve = mysqli_query($link,$queryControleMatEleve);
                            while ($idControleMatEleve = mysqli_fetch_array($reqControleMatEleve)): ?>
                                <form class="forms_Notes" method="POST">
                                    <li class="<?php if (isset($_POST['notes']) && $_POST['notes'] == $idControleMatEleve['idControle']) {echo 'notes-active';} ?>" style="margin-bottom:0; padding: 0;">
                                        <input class="form-check-input d-none" type="radio" name="notes" 
                                            id="note<?= $idControleMatEleve['idControle'] ?>" 
                                            value="<?= $idControleMatEleve['idControle'] ?>"/>
                                        <?php
                                        $queryControleMatMoy = "SELECT Notes.note, Controles.coefficient FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle WHERE Notes.idControle = " . $idControleMatEleve['idControle'] . " AND Notes.note <> 'NULL';";
                                        $reqControleMatMoy = mysqli_query($link,$queryControleMatMoy);
                                        $sommeNote = $sommeNoteNbre = NULL;
                                        while ($idControleMatMoy = mysqli_fetch_array($reqControleMatMoy)) {
                                            if ($idControleMatMoy['note'] != 'Abs' && $idControleMatMoy['note'] != 'N.Not' && $idControleMatMoy['note'] != 'Disp') {
                                                $sommeNote += (float)$idControleMatMoy['note'];
                                                $sommeNoteNbre ++;
                                            }
                                        }
                                        ?>
                                        <label for="note<?= $idControleMatEleve['idControle'] ?>" class="d-flex" id="idNote<?= $idControleMatEleve['idControle'] ?>">
                                            <div class="column-notes-dates">
                                                <div class="date-notes">
                                                    <?php
                                                    $date = explode('|', date_format(date_create($idControleMatEleve['dates']), 'd|n'));
                                                    echo $date[0] . ' ' . $month[$date[1]-1][1];
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="descriptif-notes">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <span class="descriptif-notes-name-controle"> <?= $idControleMatEleve['nomControle'] ?> </span>
                                                    <span class=".descriptif-notes-de-la-matiere"> <?php if ($idControleMatEleve['note'] != 'Abs' && $idControleMatEleve['note'] != 'N.Not' && $idControleMatEleve['note'] != 'Disp') {echo number_format((float)$idControleMatEleve['note'], 2, ',', ' ');} else {echo $idControleMatEleve['note'];} ?> </span>
                                                </div>
                                                <span class="descriptif-notes-moyenne">Moyenne de la classe : <?php if (gettype($sommeNote) != 'NULL') {echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');} else {echo 'N.Not';} ?> </span>
                                            </div>
                                        </label>
                                    </li>
                                </form>
                            <?php endwhile; ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </ul>
                <div class="moyenne-generale">
                    <div class="moyenne-generale-sous-block">
                        <div class="d-flex w-100 justify-content-between">
                            <?php
                                $sommeNote = $sommeNoteNbre = NULL;
                                foreach ($moyenneClasse as $matiereMoyenneEleve => $moyenneEleve) {
                                    foreach ($moyenneEleve as $idMoyenneEleve => $noteMoyenneEleve) {
                                        if ($idMoyenneEleve == $IdEleve) {
                                            $sommeNote += $noteMoyenneEleve;
                                            $sommeNoteNbre ++;
                                    }
                                }
                            }
                            ?>
                            <span class="nom-moyenne-eleve"> Moyenne générale élève : </span>
                            <span class="nom-moyenne-eleve"> <?php if (gettype($sommeNote) != 'NULL') {echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');} else {echo 'N.Not';} ?> </span>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <?php
                            $sommeNote = $sommeNoteNbre = NULL;
                            foreach ($moyenneParMatiere as $moyenneEleve) {
                                $sommeNote += $moyenneEleve;
                                $sommeNoteNbre ++;
                            }
                            ?>
                            <span class="nom-moyenne-classe"> Moyenne générale classe : </span>
                            <span class="nom-moyenne-classe"> <?php if (gettype($sommeNote) != 'NULL') {echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');} else {echo 'N.Not';} ?> </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column-note">
                <div>
                    <form action="" method="POST">
                        <input type="radio" class="btn-check" name="ordres" id="chronologique" value="chronologique" autocomplete="off" onchange="this.form.submit()" <?php if (!isset($_POST['ordres']) || $_POST['ordres'] != 'matiere') {echo 'checked';} ?>>
                        <label class="btn btn-sm" for="chronologique"> Par ordre chronologique </label>
                        <input type="radio" class="btn-check" name="ordres" id="matiere" value="matiere" autocomplete="off" onchange="this.form.submit()" <?php if (isset($_POST['ordres']) && $_POST['ordres'] == 'matiere') {echo 'checked';} ?>>
                        <label class="btn btn-sm" for="matiere"> Par matière </label>
                    </form>
                </div>
                <div id="resultat">Sélectionnez un devoir</div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/src/php/layout.php';
?>