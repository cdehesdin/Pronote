<?php
session_start();
require_once __DIR__ . '/config.php';

/* =========================
   Sécurité minimale
========================= */
if (!isset($_SESSION['role'])) {
    http_response_code(403);
    exit('Session invalide');
}

/* =========================
   Helpers
========================= */
function esc($str, $link) {
    return mysqli_real_escape_string($link, $str);
}

function isNoteValide($note) {
    return $note !== null && !in_array($note, ['Abs', 'N.Not', 'Disp']);
}

/* =========================
   Initialisation
========================= */
$moyenneParMatiere = [];
$nbreNoteMaximum   = 0;

$classe  = $_POST['classe']  ?? null;
$enfants = $_POST['enfants'] ?? null;

/* =========================
   CAS 1 — PROF : classe → liste élèves
========================= */
if ($_SESSION['role'] === 'Professeur' && $classe !== null && $enfants === null) {

    $classeSafe = esc($classe, $link);

    $query = "
        SELECT Eleves.idEleve, Eleves.nom, Eleves.prenom
        FROM Eleves
        INNER JOIN Classe ON Classe.idClasse = Eleves.idClasse
        WHERE Classe.nom = '$classeSafe'
        ORDER BY Eleves.nom, Eleves.prenom
    ";

    $res = mysqli_query($link, $query);

    echo '<select id="classe" name="enfants" class="form-select form-select-sm">';
    while ($e = mysqli_fetch_assoc($res)) {
        echo '<option value="'.$e['idEleve'].'">'
            . htmlspecialchars($e['prenom'].' '.$e['nom']) .
            '</option>';
    }
    echo '</select>';
    exit;
}

/* =========================
   Détermination élève / classe
========================= */
if ($_SESSION['role'] === 'Eleve') {

    $IdEleve     = $_SESSION['id'];
    $ClasseEleve = $_SESSION['classe'];

} elseif ($_SESSION['role'] === 'Parent') {

    $index       = $enfants ?? 0;
    $IdEleve     = $_SESSION['idEleve'][$index];
    $ClasseEleve = $_SESSION['classe'][$index];

} elseif ($_SESSION['role'] === 'Professeur' && $classe && $enfants !== null) {

    $IdEleve     = (int) $enfants;
    $classeSafe  = esc($classe, $link);
    $ClasseEleve = mysqli_fetch_assoc(
        mysqli_query($link, "SELECT idClasse FROM Classe WHERE nom = '$classeSafe'")
    )['idClasse'];

} else {
    http_response_code(400);
    exit('Paramètres insuffisants');
}

/* =========================
   Calcul des moyennes par matière
========================= */
$queryMatieres = "
    SELECT DISTINCT Matiere.nom
    FROM Notes
    INNER JOIN Controles ON Notes.idControle = Controles.idControle
    INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
    INNER JOIN Profs ON Enseignement.idProf = Profs.idProf
    INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
    WHERE Notes.idEleve = $IdEleve
";

$resMatieres = mysqli_query($link, $queryMatieres);

while ($m = mysqli_fetch_assoc($resMatieres)) {

    $matiereSafe = esc($m['nom'], $link);

    $queryNotes = "
        SELECT Notes.note, Controles.coefficient
        FROM Notes
        INNER JOIN Controles ON Notes.idControle = Controles.idControle
        INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
        INNER JOIN Profs ON Enseignement.idProf = Profs.idProf
        INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
        WHERE Notes.idEleve = $IdEleve
        AND Matiere.nom = '$matiereSafe'
    ";

    $resNotes = mysqli_query($link, $queryNotes);

    $somme = $coef = $nb = 0;

    while ($n = mysqli_fetch_assoc($resNotes)) {
        if (isNoteValide($n['note'])) {
            $somme += (float)$n['note'] * $n['coefficient'];
            $coef  += $n['coefficient'];
        }
        $nb++;
    }

    $moyenneParMatiere[$m['nom']] = [
        $nb,
        $coef > 0 ? $somme / $coef : null
    ];

    $nbreNoteMaximum = max($nbreNoteMaximum, $nb);
}
?>

<table class="table-releve">
    <col class="matiere" style="background-color:#e2e2e2;">
    <col class="moyenne">
    <?php for ($i=0;$i<$nbreNoteMaximum;$i++): ?>
        <col class="devoirs">
    <?php endfor; ?>
    <?php if ($IdEleve && $ClasseEleve): ?>
        <col style="min-width:319px;max-width:319px;">
    <?php endif; ?>

    <thead>
        <tr>
            <th>Matières</th>
            <th>Moy.</th>
            <th colspan="<?= $nbreNoteMaximum ?>">Devoirs</th>
            <th>Appréciation</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $queryMatiereClasse = "
            SELECT Matiere.idMatiere, Matiere.nom, Matiere.description,
                Profs.nom AS nomProf, Profs.sexe,
                Profs.idProf, Enseignement.idEnseignement
            FROM Matiere
            INNER JOIN Profs ON Matiere.idMatiere = Profs.idMatiere
            INNER JOIN Enseignement ON Profs.idProf = Enseignement.idProf
            WHERE Enseignement.idClasse = $ClasseEleve
            ORDER BY Matiere.description
        ";

        $res = mysqli_query($link, $queryMatiereClasse);

        while ($m = mysqli_fetch_assoc($res)):
        ?>
            <tr>
                <td>
                    <span class="nom-matiere"><?= $m['description'] ?></span><br>
                    <?= ($m['sexe']=='F'?'Mme ':'M. ') . $m['nomProf'] ?>
                </td>

                <td class="moyenne-elem">
                    <?php
                    $moy = $moyenneParMatiere[$m['nom']][1] ?? null;
                    echo $moy !== null ? number_format($moy,2,',',' ') : 'N.Not';
                    ?>
                </td>

                <?php
                $queryNotes = "
                    SELECT Notes.note, Controles.coefficient, Controles.dates
                    FROM Notes
                    INNER JOIN Controles ON Notes.idControle = Controles.idControle
                    INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
                    INNER JOIN Profs ON Enseignement.idProf = Profs.idProf
                    INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
                    WHERE Notes.idEleve = $IdEleve
                    AND Matiere.nom = '".esc($m['nom'],$link)."'
                    ORDER BY Controles.dates
                ";

                $resNotes = mysqli_query($link,$queryNotes);
                $count = 0;

                while ($n = mysqli_fetch_assoc($resNotes)):
                    $count++;
                ?>
                    <td class="note-elem">
                        <span class="note-elem-important">
                            <?= isNoteValide($n['note'])
                                ? number_format($n['note'],2,',',' ')
                                : $n['note']; ?>
                        </span>
                        <span class="note-elem-sur-20">/20</span>
                        <div class="note-elem-info">
                            Coef. <?= number_format($n['coefficient'],2,',',' ') ?><br>
                            <?php
                            $d = explode('|', date_format(date_create($n['dates']), 'd|n'));
                            echo $d[0].' '.$month[$d[1]-1][1];
                            ?>
                        </div>
                    </td>
                <?php endwhile; ?>

                <?php for (;$count<$nbreNoteMaximum;$count++): ?>
                    <td class="note-elem"></td>
                <?php endfor; ?>

                <?php
                $queryApp = "
                    SELECT app
                    FROM AppEleve
                    WHERE idEnseignement = {$m['idEnseignement']}
                    AND idEleve = $IdEleve
                ";
                $reqApp = mysqli_query($link, $queryApp);
                $appRow = mysqli_fetch_assoc($reqApp);
                $appreciation = $appRow['app'] ?? null;
                
                if (($_SESSION['role'] === 'Eleve' || $_SESSION['role'] === 'Parent') && isset($IdEleve, $ClasseEleve)): ?>

                    <td class="appreciation app-elv-parent">
                        <?= $appreciation !== null
                            ? htmlspecialchars($appreciation)
                            : "Aucune appréciation n'a été saisie !"; ?>
                    </td>

                <?php elseif ($_SESSION['role'] === 'Professeur' && isset($_POST['enfants'], $_POST['classe'])): ?>

                    <td class="appreciation">
                        <form action="" method="post">
                            <textarea
                                class="enter-note"
                                data-eleve="<?= $IdEleve ?>"
                                data-enseignement="<?= $m['idEnseignement'] ?>"
                                <?php if ($m['idProf'] != $_SESSION['id']) echo 'disabled'; ?>
                            ><?= htmlspecialchars($appreciation ?? '') ?></textarea>
                        </form>
                    </td>

                <?php endif; ?>

            </tr>
        <?php endwhile; ?>
    </tbody>

    <tfoot>
        <tr>
            <td>Moyenne générale</td>
            <td class="moyenne-elem">
                <?php
                $sum = $cnt = 0;
                foreach ($moyenneParMatiere as $m) {
                    if ($m[1] !== null) {
                        $sum += $m[1];
                        $cnt++;
                    }
                }
                echo $cnt ? number_format($sum/$cnt,2,',',' ') : 'N.Not';
                ?>
            </td>
            <?php for ($i=0;$i<$nbreNoteMaximum;$i++): ?><td></td><?php endfor; ?>
            <td></td>
        </tr>
    </tfoot>
</table>
