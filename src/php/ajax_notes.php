<?php
session_start();
require_once __DIR__ . '/config.php';

$IdEleve     = $_SESSION['id'];
$ClasseEleve = $_SESSION['classe'];

if (!isset($_GET['enfants'])) {
    $_GET['enfants'] = 0;
}

if ($_SESSION['role'] === 'Parent') {
    $IdEleve     = $_SESSION['idEleve'][$_GET['enfants']];
    $ClasseEleve = $_SESSION['classe'][$_GET['enfants']];
}

/* =======================
   CONSTANTES & LISTES
======================= */

const NOTES_INVALIDES = ['NULL', 'Abs', 'N.Not', 'Disp'];

$listMatiere = [
    'Maths' => ['#ff8c00', 'Mathématiques'],
    'Fran'  => ['#27ae60', 'Français'],
    'Ang'   => ['#d35400', 'Anglais'],
    'All'   => ['#2980b9', 'Allemand'],
    'PC'    => ['#f1c40f', 'Physique Chimie'],
    'SVT'   => ['#c0392b', 'Sciences de la vie et de la Terre'],
    'EPS'   => ['#9b59b6', 'Éducation physique et sportive'],
    'HG'    => ['#00cccc', 'Histoire Géographie'],
];

/* =======================
   FONCTIONS UTILITAIRES
======================= */

function noteValide($note): bool {
    return !in_array($note, NOTES_INVALIDES, true);
}

function moyennePonderee(array $notes): ?float {
    $total = $coefTotal = 0;
    foreach ($notes as $n) {
        $total     += $n['note'] * $n['coef'];
        $coefTotal += $n['coef'];
    }
    return $coefTotal > 0 ? $total / $coefTotal : null;
}

function statsSimples(array $valeurs): array {
    return [
        'moyenne' => count($valeurs) ? array_sum($valeurs) / count($valeurs) : null,
        'max'     => count($valeurs) ? max($valeurs) : null,
        'min'     => count($valeurs) ? min($valeurs) : null,
    ];
}

/* =======================
   MOYENNES CLASSE
======================= */

$moyenneClasse = [];
$moyenneParMatiere = [];

if ($_SESSION['role'] === 'Eleve' || ($_SESSION['role'] === 'Parent' && isset($_GET['enfants']))) {

    $sqlMatieres = "
        SELECT DISTINCT Matiere.nom
        FROM Notes
        JOIN Controles ON Notes.idControle = Controles.idControle
        JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
        JOIN Profs ON Enseignement.idProf = Profs.idProf
        JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
        WHERE Enseignement.idClasse = $ClasseEleve
    ";
    $reqMatieres = mysqli_query($link, $sqlMatieres);

    while ($matiere = mysqli_fetch_assoc($reqMatieres)) {

        $sqlEleves = "SELECT idEleve FROM Eleves WHERE idClasse = $ClasseEleve";
        $reqEleves = mysqli_query($link, $sqlEleves);

        while ($eleve = mysqli_fetch_assoc($reqEleves)) {

            $sqlNotes = "
                SELECT Notes.note, Controles.coefficient
                FROM Notes
                JOIN Controles ON Notes.idControle = Controles.idControle
                JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
                JOIN Profs ON Enseignement.idProf = Profs.idProf
                JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
                WHERE Matiere.nom = '{$matiere['nom']}'
                AND Notes.idEleve = {$eleve['idEleve']}
            ";
            $reqNotes = mysqli_query($link, $sqlNotes);

            $notes = [];
            while ($n = mysqli_fetch_assoc($reqNotes)) {
                if (noteValide($n['note'])) {
                    $notes[] = [
                        'note' => (float)$n['note'],
                        'coef' => (int)$n['coefficient']
                    ];
                }
            }

            $moy = moyennePonderee($notes);
            if ($moy !== null) {
                $moyenneClasse[$matiere['nom']][$eleve['idEleve']] = $moy;
            }
        }
    }

    foreach ($moyenneClasse as $matiere => $moyennes) {
        $moyenneParMatiere[$matiere] = statsSimples($moyennes)['moyenne'];
    }
}
?>

<?php if (isset($_POST['notes'])): ?>

<?php
$sql = "
    SELECT Notes.note, Controles.idControle, Controles.nomControle, Controles.dates,
           Matiere.nom, Matiere.description
    FROM Notes
    JOIN Controles ON Notes.idControle = Controles.idControle
    JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
    JOIN Profs ON Enseignement.idProf = Profs.idProf
    JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
    WHERE Notes.idControle = {$_POST['notes']}
    AND Notes.idEleve = $IdEleve
";
$req = mysqli_query($link, $sql);
?>

<?php while ($note = mysqli_fetch_assoc($req)): ?>

<div class="d-flex pb-3">
    <div class="column-note-icon"><span class="logo-note">&#8530;</span></div>
    <div class="titre-de-la-note">
        <span class="descriptif-notes-matiere"><?= $note['description'] ?></span><br>
        <span class="descriptif-notes-name-controle"><?= $note['nomControle'] ?></span><br>
        <span class="descriptif-notes-moyenne">
            <?php
            $date = explode('|', date_format(date_create($note['dates']), 'd|n|Y'));
            echo $date[0] . ' ' . $month[$date[1]-1][0] . ' ' . $date[2];
            ?>
        </span>
    </div>
</div>

<?php
$sqlStats = "
    SELECT note, coefficient
    FROM Notes
    JOIN Controles ON Notes.idControle = Controles.idControle
    WHERE Notes.idControle = {$note['idControle']}
";
$reqStats = mysqli_query($link, $sqlStats);

$notesClasse = [];
$coef = null;

while ($n = mysqli_fetch_assoc($reqStats)) {
    if (noteValide($n['note'])) {
        $notesClasse[] = (float)$n['note'];
        $coef = $n['coefficient'];
    }
}

$stats = statsSimples($notesClasse);
?>

<div class="description-de-la-note">
    <div class="description-note">
        Note élève :
        <?= noteValide($note['note']) ? number_format($note['note'],2,',',' ') : $note['note'] ?>
    </div>
    <div class="description-note">Moyenne classe : <?= number_format($stats['moyenne'],2,',',' ') ?></div>
    <div class="description-note">Note max : <?= number_format($stats['max'],2,',',' ') ?></div>
    <div class="description-note">Note min : <?= number_format($stats['min'],2,',',' ') ?></div>
    <div class="description-note">Coefficient : <?= $coef ?></div>
</div>

<?php endwhile; ?>

<?php elseif (isset($_POST['matieres'])): ?>

<div class="d-flex pb-3">
    <div class="column-note-icon"><span class="logo-note">&#8530;</span></div>
    <div class="titre-de-la-note mt-2">
        <?= $listMatiere[$_POST['matieres']][1] ?>
    </div>
</div>

<div class="description-de-la-note">
    <div class="description-note">
        Moyenne élève :
        <?= isset($moyenneClasse[$_POST['matieres']][$IdEleve])
            ? number_format($moyenneClasse[$_POST['matieres']][$IdEleve],2,',',' ')
            : 'N.Not' ?>
    </div>

    <div class="description-note">
        Moyenne classe :
        <?= isset($moyenneParMatiere[$_POST['matieres']])
            ? number_format($moyenneParMatiere[$_POST['matieres']],2,',',' ')
            : 'N.Not' ?>
    </div>

    <?php
    $stats = statsSimples($moyenneClasse[$_POST['matieres']] ?? []);
    ?>

    <div class="description-note">Max : <?= number_format($stats['max'],2,',',' ') ?></div>
    <div class="description-note">Min : <?= number_format($stats['min'],2,',',' ') ?></div>
</div>

<?php endif; ?>
