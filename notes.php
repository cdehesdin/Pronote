<?php
session_start();
require_once __DIR__ . '/src/php/config.php';

/* =========================
   Sécurité & titre de page
   ========================= */
function getPageTitle(): string {
    if (!isset($_SESSION['login'])) {
        header('Location: ./index.php');
        exit;
    }

    return match ($_SESSION['role']) {
        'Eleve'  => 'Mes notes',
        'Parent' => 'Les notes',
        default  => 'Notes'
    };
}

$title = getPageTitle();


/* =========================
   Gestion du tri
   ========================= */
if (isset($_POST['notes'])) {
    $_POST['ordres'] = 'chronologique';
}
if (isset($_POST['notesM']) || isset($_POST['matieres'])) {
    $_POST['ordres'] = 'matiere';
}

/* =========================
   Élève courant
   ========================= */
$_GET['enfants'] ??= 0;

function getEleveCourant(): array {
    if ($_SESSION['role'] === 'Eleve') {
        return [
            'id'     => $_SESSION['id'],
            'classe' => $_SESSION['classe']
        ];
    }

    if ($_SESSION['role'] === 'Parent' && isset($_GET['enfants'])) {
        return [
            'id'     => $_SESSION['idEleve'][$_GET['enfants']],
            'classe' => $_SESSION['classe'][$_GET['enfants']]
        ];
    }

    return [];
}

$eleve = getEleveCourant();
$IdEleve = $eleve['id'] ?? null;
$ClasseEleve = $eleve['classe'] ?? null;

// Récupérer le parent et ses enfants
$stmt = $link->prepare(
    "SELECT E.idEleve, E.idClasse, E.nom AS nomEleve, E.prenom AS prenomEleve
     FROM Parents P
     LEFT JOIN Eleves E 
       ON P.idParent = E.respLegal1Id 
       OR P.idParent = E.respLegal2Id
     WHERE P.idUtilisateur = ?"
);
$stmt->bind_param("i", $_SESSION['idUtilisateur']);
$stmt->execute();
$res = $stmt->get_result();

$enfants = []; // tableau pour stocker les enfants
while ($row = $res->fetch_assoc()) {
    if (!is_null($row['idEleve'])) {
        $enfants[] = [
            'idEleve'   => $row['idEleve'],
            'idClasse'  => $row['idClasse'],
            'nom'       => $row['nomEleve'],
            'prenom'    => $row['prenomEleve']
        ];
    }
}

/* =========================
   Calcul des moyennes
   ========================= */
$moyenneClasse = [];
$moyenneParMatiere = [];

function notesValides(): string
{
    return "Notes.note <> 'NULL'
            AND Notes.note <> 'Abs'
            AND Notes.note <> 'N.Not'
            AND Notes.note <> 'Disp'";
}

if ($IdEleve && $ClasseEleve) {

    // Matières de la classe
    $sqlMatieres = "
        SELECT DISTINCT Matiere.nom
        FROM Notes
        INNER JOIN Controles ON Notes.idControle = Controles.idControle
        INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
        INNER JOIN Profs ON Enseignement.idProf = Profs.idProf
        INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
        WHERE Enseignement.idClasse = $ClasseEleve
        AND " . notesValides();

    $matieres = mysqli_query($link, $sqlMatieres);

    while ($matiere = mysqli_fetch_assoc($matieres)) {

        $elevesClasse = mysqli_query(
            $link,
            "SELECT idEleve FROM Eleves WHERE idClasse = $ClasseEleve"
        );

        while ($eleveClasse = mysqli_fetch_assoc($elevesClasse)) {

            $sqlNotes = "
                SELECT Notes.note, Controles.coefficient
                FROM Notes
                INNER JOIN Controles ON Notes.idControle = Controles.idControle
                INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
                INNER JOIN Profs ON Enseignement.idProf = Profs.idProf
                INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
                WHERE Matiere.nom = '{$matiere['nom']}'
                AND Notes.idEleve = {$eleveClasse['idEleve']}
                AND " . notesValides();

            $notes = mysqli_query($link, $sqlNotes);

            $total = $coef = 0;

            while ($n = mysqli_fetch_assoc($notes)) {
                $total += (float)$n['note'] * $n['coefficient'];
                $coef  += $n['coefficient'];
            }

            if ($coef > 0) {
                $moyenneClasse[$matiere['nom']][$eleveClasse['idEleve']] = $total / $coef;
            }
        }
    }

    // Moyenne de classe par matière
    foreach ($moyenneClasse as $matiere => $moyennesEleves) {
        $moyenneParMatiere[$matiere] =
            array_sum($moyennesEleves) / count($moyennesEleves);
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
                <ul id="liste-notes"></ul>
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
                <div class="d-flex justify-content-between">
                    <div>
                        <form id="form-tri-notes">
                            <input type="radio"
                                class="btn-check"
                                name="ordres"
                                id="chronologique"
                                value="chronologique"
                                autocomplete="off"
                                <?php if (!isset($_POST['ordres']) || $_POST['ordres'] !== 'matiere') echo 'checked'; ?>>

                            <label class="btn btn-sm" for="chronologique">
                                Par ordre chronologique
                            </label>

                            <input type="radio"
                                class="btn-check"
                                name="ordres"
                                id="matiere"
                                value="matiere"
                                autocomplete="off"
                                <?php if (isset($_POST['ordres']) && $_POST['ordres'] === 'matiere') echo 'checked'; ?>>

                            <label class="btn btn-sm" for="matiere">
                                Par matière
                            </label>
                        </form>
                    </div>
                    <?php if ($_SESSION['role'] == 'Parent'): ?>
                        <div>
                            <select id="select-enfant" class="form-select form-select-sm">
                                <?php foreach ($enfants as $index => $enfant): ?>
                                    <option value="<?= $index ?>" 
                                        <?= (isset($_GET['enfants']) && $_GET['enfants'] == $index) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($enfant['prenom'] . ' ' . $enfant['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
                <hr>
                <div id="resultat">Sélectionnez un devoir</div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/src/php/layout.php';
?>