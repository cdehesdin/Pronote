<?php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != "Eleve" && $_SESSION['role'] != "Parent")) {
    header('Location: ./notes.php');
    exit;
}

/* =======================
   SESSION
======================= */

$IdEleve     = $_SESSION['id'];
$ClasseEleve = $_SESSION['classe'];

if ($_SESSION['role'] == 'Parent') {
    if (!isset($_POST['enfants'])) {
        $_POST['enfants'] = 0;
    }

    $IdEleve     = $_SESSION['idEleve'][$_POST['enfants']];
    $ClasseEleve = $_SESSION['classe'][$_POST['enfants']];
}

$ordre = $_POST['ordres'] ?? 'chronologique';

/* =======================
   LISTE MATIERES
======================= */

$listMatiere = [
    'Maths' => ['#ff8c00', 'Mathématiques'],
    'Fran'  => ['#27ae60', 'Français'],
    'Ang'   => ['#d35400', 'Anglais'],
    'All'   => ['#2980b9', 'Allemand'],
    'PC'    => ['#f1c40f', 'Physique Chimie'],
    'SVT'   => ['#c0392b', 'SVT'],
    'EPS'   => ['#9b59b6', 'EPS'],
    'HG'    => ['#00cccc', 'Histoire Géographie'],
];

/* =======================
   FONCTIONS
======================= */

function noteValide($note): bool {
    return !in_array($note, ['NULL', 'Abs', 'N.Not', 'Disp'], true);
}

function formatNote($note): string {
    return noteValide($note)
        ? number_format((float)$note, 2, ',', ' ')
        : $note;
}

function moyenneControle(mysqli $link, int $idControle): ?float {
    $sql = "SELECT note FROM Notes WHERE idControle = $idControle";
    $res = mysqli_query($link, $sql);

    $notes = [];
    while ($row = mysqli_fetch_assoc($res)) {
        if (noteValide($row['note'])) {
            $notes[] = (float)$row['note'];
        }
    }

    return count($notes) ? array_sum($notes) / count($notes) : null;
}

function moyennesClasse(mysqli $link, int $idClasse): array {
    $data = [];

    $sql = "
        SELECT Matiere.nom, Notes.idEleve, Notes.note, Controles.coefficient
        FROM Notes
        JOIN Controles ON Notes.idControle = Controles.idControle
        JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
        JOIN Profs ON Enseignement.idProf = Profs.idProf
        JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
        WHERE Enseignement.idClasse = $idClasse
    ";

    $res = mysqli_query($link, $sql);

    while ($r = mysqli_fetch_assoc($res)) {
        if (!noteValide($r['note'])) continue;

        $m = $r['nom'];
        $e = $r['idEleve'];

        $data[$m][$e]['s'] = ($data[$m][$e]['s'] ?? 0) + $r['note'] * $r['coefficient'];
        $data[$m][$e]['c'] = ($data[$m][$e]['c'] ?? 0) + $r['coefficient'];
    }

    $result = [];
    foreach ($data as $m => $eleves) {
        foreach ($eleves as $e => $calc) {
            $result[$m][$e] = $calc['s'] / $calc['c'];
        }
    }

    return $result;
}

/* =======================
   CALCUL MOYENNES
======================= */

$moyenneClasse = moyennesClasse($link, $ClasseEleve);

// Moyenne de classe par matière
$moyenneParMatiere = [];
foreach ($moyenneClasse as $matiere => $moyennesEleves) {
    $moyenneParMatiere[$matiere] =
        array_sum($moyennesEleves) / count($moyennesEleves);
}
?>

<!-- =======================
     AFFICHAGE
======================= -->

<ul>
<?php if ($ordre === "chronologique"): ?>

<?php
$sql = "
SELECT Notes.note, Controles.idControle, Controles.nomControle, Controles.dates,
       Matiere.description, Matiere.nom
FROM Notes
JOIN Controles ON Notes.idControle = Controles.idControle
JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
JOIN Profs ON Enseignement.idProf = Profs.idProf
JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
WHERE Notes.idEleve = $IdEleve
AND Notes.note <> 'NULL'
ORDER BY Controles.dates DESC
";
$res = mysqli_query($link, $sql);
?>

<?php while ($row = mysqli_fetch_assoc($res)): ?>
<li class="<?php if (isset($_POST['notes']) && $_POST['notes'] == $row['idControle']) echo 'notes-active'; ?>">
<?php $moy = moyenneControle($link, $row['idControle']); ?>
<form class="forms_Notes" method="POST">
<input class="form-check-input d-none" type="radio" name="notes"
       id="note<?= $row['idControle'] ?>"
       value="<?= $row['idControle'] ?>" />

<label for="note<?= $row['idControle'] ?>" class="d-flex" id="idNote<?= $row['idControle'] ?>">
    <div class="column-notes-dates">
        <div class="date-notes">
            <?php
            $d = explode('|', date_format(date_create($row['dates']), 'd|n'));
            echo $d[0].' '.$month[$d[1]-1][1];
            ?>
        </div>
        <div class="after-date-notes" style="background-color:<?= $listMatiere[$row['nom']][0] ?>"></div>
    </div>

    <div class="descriptif-notes">
        <div class="d-flex w-100 justify-content-between">
            <span class="descriptif-notes-matiere"><?= $row['description'] ?></span>
            <span class="descriptif-notes-de-la-matiere"><?= formatNote($row['note']) ?></span>
        </div>
        <span class="descriptif-notes-name-controle"><?= $row['nomControle'] ?></span><br>
        <span class="descriptif-notes-moyenne">
            Moyenne de la classe :
            <?= $moy !== null ? number_format($moy,2,',',' ') : 'N.Not' ?>
        </span>
    </div>
</label>
</form>
</li>
<?php endwhile; ?>

<?php else: ?>

<?php
$sqlMat = "
SELECT DISTINCT Matiere.description, Matiere.nom
FROM Notes
JOIN Controles ON Notes.idControle = Controles.idControle
JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
JOIN Profs ON Enseignement.idProf = Profs.idProf
JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
WHERE Notes.idEleve = $IdEleve
AND Notes.note <> 'NULL'
ORDER BY Matiere.description
";
$resMat = mysqli_query($link, $sqlMat);
?>

<?php while ($mat = mysqli_fetch_assoc($resMat)): ?>

<!-- ===== MATIERE ===== -->
<form class="forms_Matieres" method="POST">
<li class="<?php if (isset($_POST['matieres']) && $_POST['matieres'] == $mat['nom']) echo 'notes-active'; ?>"
    style="margin-bottom:0;padding:0;">
<input class="form-check-input d-none" type="radio"
       name="matieres"
       id="mat<?= $mat['nom'] ?>"
       value="<?= $mat['nom'] ?>" />

<label for="mat<?= $mat['nom'] ?>" class="d-flex" id="idMatiere<?= $mat['nom'] ?>">
<div class="d-flex w-100 justify-content-between">
<div class="d-flex align-items-center">
<div class="bordure-nom-matiere"
     style="background-color:<?= $listMatiere[$mat['nom']][0] ?>"></div>
<div class="nom-matiere"><?= $mat['description'] ?></div>
</div>

<span class="nom-note">
<?= isset($moyenneClasse[$mat['nom']][$IdEleve])
    ? number_format($moyenneClasse[$mat['nom']][$IdEleve], 2, ',', ' ')
    : 'N.Not' ?>
</span>
</div>
</label>
</li>
</form>

<!-- ===== NOTES DE LA MATIERE ===== -->
<?php
$sqlNotes = "
SELECT Notes.note, Controles.idControle, Controles.nomControle, Controles.dates
FROM Notes
JOIN Controles ON Notes.idControle = Controles.idControle
JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
JOIN Profs ON Enseignement.idProf = Profs.idProf
JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
WHERE Matiere.nom = '{$mat['nom']}'
AND Notes.idEleve = $IdEleve
AND Notes.note <> 'NULL'
ORDER BY Controles.dates DESC
";
$resNotes = mysqli_query($link, $sqlNotes);
?>

<?php while ($note = mysqli_fetch_assoc($resNotes)): ?>
<?php $moyCtrl = moyenneControle($link, $note['idControle']); ?>

<form class="forms_Notes" method="POST">
<li class="<?php if (isset($_POST['notes']) && $_POST['notes'] == $note['idControle']) echo 'notes-active'; ?>"
    style="margin-bottom:0;padding:0;">

<input class="form-check-input d-none" type="radio"
       name="notes"
       id="note<?= $note['idControle'] ?>"
       value="<?= $note['idControle'] ?>" />

<label for="note<?= $note['idControle'] ?>"
       class="d-flex"
       id="idNote<?= $note['idControle'] ?>">

<div class="column-notes-dates">
<div class="date-notes">
<?php
$d = explode('|', date_format(date_create($note['dates']), 'd|n'));
echo $d[0] . ' ' . $month[$d[1]-1][1];
?>
</div>
</div>

<div class="descriptif-notes">
<div class="d-flex w-100 justify-content-between">
<span class="descriptif-notes-name-controle"><?= $note['nomControle'] ?></span>
<span class="descriptif-notes-de-la-matiere"><?= formatNote($note['note']) ?></span>
</div>

<span class="descriptif-notes-moyenne">
Moyenne de la classe :
<?= $moyCtrl !== null ? number_format($moyCtrl, 2, ',', ' ') : 'N.Not' ?>
</span>
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