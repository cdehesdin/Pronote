<?php
session_start();
require_once __DIR__ . '/config.php';

if ((!isset($_SESSION['role']) || $_SESSION['role'] != "Professeur") && !isset($_POST['classe'])) {
    header('Location: ./notes.php');
    exit;
}

// Requêtes
$classe = $_POST['classe'];

// Fonction pour exécuter une requête et récupérer un seul résultat
function fetch_single_result($query, $link) {
    $result = mysqli_query($link, $query);
    return mysqli_fetch_array($result);
}

// Récupération des devoirs
$queryListDevoirs = "SELECT Controles.dates, Controles.coefficient, Controles.idControle, Controles.nomControle 
                    FROM Controles 
                    INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
                    INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse
                    WHERE Enseignement.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $classe . "' ORDER BY Controles.dates;";
$reqListDevoirs = mysqli_query($link, $queryListDevoirs);
$nbreDevoirs = 0;
$listeDesDevoirs = [];
while ($idListDevoirs = mysqli_fetch_array($reqListDevoirs)) {
    $listeDesDevoirs[] = [
        'coefficient' => $idListDevoirs['coefficient'],
        'dates' => $idListDevoirs['dates'],
        'idControle' => $idListDevoirs['idControle'],
        'nomControle' => $idListDevoirs['nomControle']
    ];
    $nbreDevoirs++;
}

// Nombre d'élèves
$queryNbreEleve = "SELECT COUNT(*) FROM Eleves 
                    INNER JOIN Classe ON Eleves.idClasse = Classe.idClasse
                    INNER JOIN Enseignement ON Classe.idClasse = Enseignement.idClasse
                    WHERE Enseignement.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $classe . "';";
$nbreEleves = fetch_single_result($queryNbreEleve, $link)['COUNT(*)'];

// Nom de la matière
$queryNomMatiere = "SELECT Matiere.description FROM Profs 
                    INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere
                    INNER JOIN Enseignement ON Enseignement.idProf = Profs.idProf
                    INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse
                    WHERE Profs.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $classe . "';";
$nomMatiere = fetch_single_result($queryNomMatiere, $link)['description'];
?>

<table class="table-saisie">
    <thead>
        <tr>
            <th colspan="2" style="text-align: left;">
                <button type="button" class="btn btn-primary btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#creeDevoir" style="padding: 0.03rem 0.4rem; background-color: var(--main1); border-color: var(--main1); border-radius: 15px; font-size: 0.75rem; font-weight: 800;">
                    Créer un devoir
                </button>
            </th>
            <?php foreach ($listeDesDevoirs as $devoir): ?>
                <th style="padding: 0!important;" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" data-bs-custom-class="custom-popover" data-bs-content="<p><b><?= $nomMatiere ?></b><br/><?= date_format(date_create($devoir['dates']), 'd/m/Y') . ' - <i>' . $devoir['nomControle'] . '</i>' ?></p>Coefficient : <?= $devoir['coefficient'] ?>" data-bs-html="true">
                    <button type="button" class="btn btn-primary modifier-devoir" data-bs-toggle="modal" data-bs-target="<?= '#modifDevoir' . $devoir['idControle'] ?>">
                        <?= date_format(date_create($devoir['dates']), 'd/m') ?>
                    </button>
                </th>
            <?php endforeach; ?>
        </tr>
        <tr>
            <th> <?= isset($nbreEleves) ? $nbreEleves . ' élèves' : '' ?> </th>
            <th> Moyenne </th>
            <?php foreach ($listeDesDevoirs as $devoir): ?>
                <th style="font-weight: 500; font-style: italic; text-align: center;"> Coef. <?= $devoir['coefficient'] ?> </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        // Récupération des élèves
        $queryListEleve = "SELECT DISTINCT Eleves.nom, Eleves.prenom, Eleves.idEleve FROM Eleves
                           INNER JOIN Classe ON Classe.idClasse = Eleves.idClasse 
                           WHERE Classe.nom = '" . $classe . "';";
        $reqListEleve = mysqli_query($link, $queryListEleve);
        while ($idListEleve = mysqli_fetch_array($reqListEleve)): ?>
            <tr>
                <td class="devoir"> <?= $idListEleve['nom'] . ' ' . $idListEleve['prenom'] ?> </td>
                <td class="note-elem devoir">
                    <?php
                    $sommeNote = 0;
                    $sommeNoteNbre = 0;
                    foreach ($listeDesDevoirs as $devoir):
                        $queryNoteEleve = "SELECT Notes.note 
                                           FROM Notes 
                                           INNER JOIN Controles ON Notes.idControle = Controles.idControle
                                           INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
                                           WHERE Notes.idEleve = " . $idListEleve['idEleve'] . " 
                                           AND Enseignement.idProf = " . $_SESSION['id'] . " 
                                           AND Controles.idControle = " . $devoir['idControle'] . ";";
                        $reqNoteEleve = mysqli_query($link, $queryNoteEleve);
                        while ($idNoteEleve = mysqli_fetch_array($reqNoteEleve)) {
                            if (in_array($idNoteEleve['note'], ['Abs', 'N.Not', 'Disp']) === false) {
                                $sommeNote += (float)$idNoteEleve['note'] * $devoir['coefficient'];
                                $sommeNoteNbre += $devoir['coefficient'];
                            }
                        }
                    endforeach;
                    echo $sommeNoteNbre > 0 ? number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ') : 'N.Not';
                    ?>
                </td>
                <?php foreach ($listeDesDevoirs as $devoir): ?>
                    <td class="note-elem enter-des-notes">
                        <form action="" method="POST">
                            <input class="enter-note" 
                                name="note<?= $devoir['idControle'] ?>" 
                                type="text" 
                                value="<?php 
                                        // Récupération des notes
                                        $queryNoteEleve = "SELECT Notes.note 
                                                        FROM Notes 
                                                        INNER JOIN Controles ON Notes.idControle = Controles.idControle
                                                        INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement
                                                        WHERE Notes.idEleve = " . $idListEleve['idEleve'] . " 
                                                        AND Enseignement.idProf = " . $_SESSION['id'] . " 
                                                        AND Controles.idControle = " . $devoir['idControle'] . ";";
                                        $reqNoteEleve = mysqli_query($link, $queryNoteEleve);
                                        $note = mysqli_fetch_array($reqNoteEleve)['note'];
                                        echo $note ? (in_array($note, ['Abs', 'N.Not', 'Disp']) ? $note : number_format($note, 2, ',', ' ')) : '';
                                ?>" 
                                onchange="updateNote(<?= $idListEleve['idEleve'] ?>, <?= $devoir['idControle'] ?>, this.value)">
                        </form>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="releve-moyenne-generale"> Moyenne générale </td>
            <td class="releve-moyenne-generale-note moyenne-elem">
                <?php
                $sommeNote = 0;
                $sommeNoteNbre = 0;
                foreach ($listeDesDevoirs as $devoir):
                    $queryControleMoyenneG = "SELECT Notes.note, Controles.coefficient 
                                            FROM Notes 
                                            INNER JOIN Controles ON Notes.idControle = Controles.idControle
                                            WHERE Notes.idControle = " . $devoir['idControle'] . " AND Notes.note <> 'NULL';";
                    $reqControleMoyenneG = mysqli_query($link, $queryControleMoyenneG);
                    while ($idControleMoyenneG = mysqli_fetch_array($reqControleMoyenneG)) {
                        if (in_array($idControleMoyenneG['note'], ['Abs', 'N.Not', 'Disp']) === false) {
                            $sommeNote += (float)$idControleMoyenneG['note'] * $idControleMoyenneG['coefficient'];
                            $sommeNoteNbre += $idControleMoyenneG['coefficient'];
                        }
                    }
                endforeach;
                echo $sommeNoteNbre > 0 ? number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ') : 'N.Not';
                ?>
            </td>
            <?php foreach ($listeDesDevoirs as $devoir): ?>
                <td class="releve-moyenne-generale-note moyenne-elem">
                    <?php
                    $sommeNote = 0;
                    $sommeNoteNbre = 0;
                    $queryControleMoyenne = "SELECT Notes.note, Controles.coefficient 
                                             FROM Notes 
                                             INNER JOIN Controles ON Notes.idControle = Controles.idControle
                                             WHERE Notes.idControle = " . $devoir['idControle'] . " AND Notes.note <> 'NULL';";
                    $reqControleMoyenne = mysqli_query($link, $queryControleMoyenne);
                    while ($idControleMoyenne = mysqli_fetch_array($reqControleMoyenne)) {
                        if (in_array($idControleMoyenne['note'], ['Abs', 'N.Not', 'Disp']) === false) {
                            $sommeNote += (float)$idControleMoyenne['note'] * $idControleMoyenne['coefficient'];
                            $sommeNoteNbre += $idControleMoyenne['coefficient'];
                        }
                    }
                    echo $sommeNoteNbre > 0 ? number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ') : 'N.Not';
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
    </tfoot>
</table>

<div class="info-note" style="margin-top: 10px;">
    Pour ne pas comptabiliser le devoir dans le calcul de la moyenne : <br>
    <span class="caract">A</span> : L'élève est Absent (Abs) <br>
    <span class="caract">D</span> : L'élève est Dispensé (Disp) <br>
    <span class="caract">N</span> : L'élève est Non noté (N.Not)
</div>

<!-- Modal création devoir -->
<div class="modal fade modal-devoir" id="creeDevoir" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="en-tete-dialog">
                    Créer un devoir :
                    <span class="nom-matiere-devoir"><?= $nomMatiere ?></span> - <?= $_POST['classe'] ?>
                </div>
            </div>

            <div class="modal-body devoir-dialog">
                <form id="formCreateDevoir">
                    <div class="d-flex mb-2">
                        <div class="text-dialog"><label>Nom du devoir :</label></div>
                        <div class="devoir-saisie"><input type="text" name="commentaire" maxlength="255" required></div>
                    </div>

                    <div class="d-flex mb-2">
                        <div class="text-dialog"><label>Devoir du :</label></div>
                        <div class="devoir-saisie"><input type="date" name="date" required></div>
                    </div>

                    <div class="d-flex mb-2">
                        <div class="text-dialog"><label>Coefficient :</label></div>
                        <div class="devoir-saisie"><input type="number" step="0.01" min="0" max="20" name="coeff" value="1" required></div>
                    </div>

                    <div class="footer-dialog mt-2">
                        <input type="button" class="btn btn-close-modal" data-bs-dismiss="modal" value="Annuler">
                        <input type="button" class="btn btn-primary" id="btnCreateDevoir" value="Créer">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal modification / suppression devoirs -->
<?php foreach ($listeDesDevoirs as $devoir): ?>
<div class="modal fade modal-devoir" id="modifDevoir<?= $devoir['idControle'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="en-tete-dialog">
                    Modification d'un devoir :
                    <span class="nom-matiere-devoir"><?= $nomMatiere ?></span> - <?= $_POST['classe'] ?>
                </div>
            </div>

            <div class="modal-body devoir-dialog">
                <form class="formModifDevoir">
                    <input type="hidden" name="idControle" value="<?= $devoir['idControle'] ?>">

                    <div class="d-flex mb-2">
                        <div class="text-dialog"><label>Nom du devoir :</label></div>
                        <div class="devoir-saisie"><input type="text" name="nomDevoir" value="<?= $devoir['nomControle'] ?>" required></div>
                    </div>

                    <div class="d-flex mb-2">
                        <div class="text-dialog"><label>Devoir du :</label></div>
                        <div class="devoir-saisie"><input type="date" name="dateDevoir" value="<?= $devoir['dates'] ?>" required></div>
                    </div>

                    <div class="d-flex mb-2">
                        <div class="text-dialog"><label>Coefficient :</label></div>
                        <div class="devoir-saisie"><input type="number" step="0.01" min="0" max="20" name="coeffDevoir" value="<?= $devoir['coefficient'] ?>" required></div>
                    </div>

                    <div class="footer-dialog mt-2">
                        <input type="button" class="btn delete-button-devoir btnDeleteDevoir" value="Supprimer">
                        <input type="button" class="btn btn-primary btnUpdateDevoir" value="Valider">
                        <input type="button" class="btn btn-close-modal" data-bs-dismiss="modal" value="Annuler">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>