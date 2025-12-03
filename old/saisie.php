<?php
session_start();

// Définition du titre de la page
if (!isset($_SESSION['login'])) {
    $title = "Connexion";
} else {
    $title = "Saisie des notes";
}

// Si utilisateur non connecté, renvoie vers la page de connexion
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
}

// Définition des listes utilisés : gazzuro
$listeDesDevoirs = [];

require 'assets/header.php';

if (isset($_GET['classe'])) {
    $queryListDevoirs = "SELECT Controles.dates, Controles.coefficient, Controles.idControle, Controles.nomControle FROM Controles INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $_GET['classe'] . "' ORDER BY Controles.dates;";
    $reqListDevoirs = mysqli_query($link,$queryListDevoirs);
    $nbreDevoirs = 0;
    while ($idListDevoirs = mysqli_fetch_array($reqListDevoirs)) {
        $listeDesDevoirs[] = [$idListDevoirs['coefficient'],$idListDevoirs['dates'],$idListDevoirs['idControle'],$idListDevoirs['nomControle']];
        $nbreDevoirs ++;
    }
    $queryNbreEleve = "SELECT COUNT(*) FROM Eleves INNER JOIN Classe ON Eleves.idClasse = Classe.idClasse INNER JOIN Enseignement ON Classe.idClasse = Enseignement.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $_GET['classe'] . "';";
    $reqNbreEleve = mysqli_query($link,$queryNbreEleve);
    while ($idNbreEleve = mysqli_fetch_array($reqNbreEleve)) {
        $nbreEleves = $idNbreEleve['COUNT(*)'];
    }
    $queryListElv = "SELECT DISTINCT Notes.idNote FROM Notes INNER JOIN Controles ON Controles.idControle = Notes.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $_GET['classe'] . "';";
    $reqListElv = mysqli_query($link,$queryListElv);
    while ($idListElv = mysqli_fetch_array($reqListElv)) {
        if (isset($_POST['note' . $idListElv['idNote']])) {
            if ($_POST['note' . $idListElv['idNote']] == '') {
                $queryAjoutNote = "UPDATE Notes SET note = NULL WHERE idNote = " . $idListElv['idNote'] . ";";
                if (mysqli_query($link,$queryAjoutNote)) {
                    unset($_POST);
                }
            } elseif ($_POST['note' . $idListElv['idNote']] == 'A') {
                $queryAjoutNote = "UPDATE Notes SET note = 'Abs' WHERE idNote = " . $idListElv['idNote'] . ";";
                if (mysqli_query($link,$queryAjoutNote)) {
                    unset($_POST);
                }
            } elseif ($_POST['note' . $idListElv['idNote']] == 'D') {
                $queryAjoutNote = "UPDATE Notes SET note = 'Disp' WHERE idNote = " . $idListElv['idNote'] . ";";
                if (mysqli_query($link,$queryAjoutNote)) {
                    unset($_POST);
                }
            } elseif ($_POST['note' . $idListElv['idNote']] == 'N') {
                $queryAjoutNote = "UPDATE Notes SET note = 'N.Not' WHERE idNote = " . $idListElv['idNote'] . ";";
                if (mysqli_query($link,$queryAjoutNote)) {
                    unset($_POST);
                }
            } elseif (floatval(str_replace(",", ".", $_POST['note' . $idListElv['idNote']])) >= 0 && floatval(str_replace(",", ".", $_POST['note' . $idListElv['idNote']])) <= 20) {
                $queryAjoutNote = "UPDATE Notes SET note = " . floatval(str_replace(",", ".", $_POST['note' . $idListElv['idNote']])) . " WHERE idNote = " . $idListElv['idNote'] . ";";
                if (mysqli_query($link,$queryAjoutNote)) {
                    unset($_POST);
                }
            } else {
                unset($_POST);
            }
        }
    }
    if (isset($_POST['commentaire']) && isset($_POST['coeff']) && isset($_POST['date'])) {
        $queryIdEnseignement = "SELECT Enseignement.idEnseignement FROM Enseignement INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $_GET['classe'] . "';";
        $reqIdEnseignement = mysqli_query($link,$queryIdEnseignement);
        while ($idIdEnseignement = mysqli_fetch_array($reqIdEnseignement)) {
            $queryAjoutDevoir = "INSERT INTO Controles (idEnseignement, nomControle, coefficient, dates) VALUES (" . $idIdEnseignement['idEnseignement'] . ", '" . $_POST['commentaire'] . "', " . $_POST['coeff'] . ", '" . $_POST['date'] . "');";
            if (mysqli_query($link,$queryAjoutDevoir)) {
                $queryIdEleve = "SELECT Controles.idControle, Eleves.idEleve FROM Enseignement INNER JOIN Controles ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse INNER JOIN Eleves ON Enseignement.idClasse = Eleves.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $_GET['classe'] . "' AND Controles.dates = '" . $_POST['date'] . "' AND Controles.nomControle = '" . $_POST['commentaire'] . "';";
                $reqIdEleve = mysqli_query($link,$queryIdEleve);
                $queryAjoutNoteEleve = "INSERT INTO Notes (idEleve, idControle) VALUES ";
                while ($idIdEleve = mysqli_fetch_array($reqIdEleve)) {
                    $queryAjoutNoteEleve .= "(" . $idIdEleve['idEleve'] . ', ' . $idIdEleve['idControle'] . "), ";
                }
            }
        }
        if (mysqli_query($link,substr($queryAjoutNoteEleve, 0, -2) . ';')) {unset($_POST); echo "<meta http-equiv='refresh' content='0'>";}
    }
    $nbre = 0;
    while ($nbre != $nbreDevoirs) {
        if (isset($_POST['delete-val' . $listeDesDevoirs[$nbre][2]]) && !isset($_POST['valider' . $listeDesDevoirs[$nbre][2]])) {
            $querySupprNote = "DELETE FROM Notes WHERE idControle = " . $_POST['delete-val' . $listeDesDevoirs[$nbre][2]] . ";";
            $querySupprimerNote = "DELETE FROM Controles WHERE idControle = " . $_POST['delete-val' . $listeDesDevoirs[$nbre][2]] . ";";
            if (mysqli_query($link,$querySupprNote) && mysqli_query($link,$querySupprimerNote)) {
                unset($_POST);
                echo "<meta http-equiv='refresh' content='0'>";
            }
        }
        if (isset($_POST['nomDevoir' . $listeDesDevoirs[$nbre][2]]) && isset($_POST['dateDevoir' . $listeDesDevoirs[$nbre][2]]) && isset($_POST['coeffDevoir' . $listeDesDevoirs[$nbre][2]]) && isset($_POST['valider' . $listeDesDevoirs[$nbre][2]]) && !isset($_POST['delete-val' . $listeDesDevoirs[$nbre][2]])) {
            $queryModifControle = "UPDATE Controles SET nomControle = '" . $_POST['nomDevoir' . $listeDesDevoirs[$nbre][2]] . "', coefficient = " . $_POST['coeffDevoir' . $listeDesDevoirs[$nbre][2]] . ", dates = '" . $_POST['dateDevoir' . $listeDesDevoirs[$nbre][2]] . "' WHERE idControle = " . $_POST['valider' . $listeDesDevoirs[$nbre][2]] . ";";
            if (mysqli_query($link,$queryModifControle)) {
                unset($_POST);
                echo "<meta http-equiv='refresh' content='0'>";
            }
        }
        $nbre ++;
    }
    $queryNomMatiere = "SELECT Matiere.description FROM Profs INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere INNER JOIN Enseignement ON Enseignement.idProf = Profs.idProf INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse WHERE Profs.idProf = " . $_SESSION['id'] . " AND Classe.nom = '" . $_GET['classe'] . "';";
    $reqNomMatiere = mysqli_query($link,$queryNomMatiere);
    while ($idNomMatiere = mysqli_fetch_array($reqNomMatiere)) {
        $nomMatiere = $idNomMatiere['description'];
    }
}
?>

<?php if ($_SESSION['role'] == 'Professeur'): ?>
    <div class="choice-bar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="choice-title"> Saisie des notes </div>
            <div class="dropdown d-inline ms-2">
                <form action="" method="GET">
                    <select class="form-select form-select-sm" name="classe" id="classe" onchange="this.form.submit()">
                        <option class="classe-disabled" <?php if (!isset($_GET['classe'])) {echo 'selected';} ?> disabled> Classe </option>
                        <?php
                        $queryListClasse = "SELECT DISTINCT nom FROM Classe INNER JOIN Enseignement ON Classe.idClasse = Enseignement.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . ";";
                        $reqListClasse = mysqli_query($link,$queryListClasse);
                        while ($idListClasse = mysqli_fetch_array($reqListClasse)): ?>
                            <option <?php if (isset($_GET['classe']) && $_GET['classe'] == $idListClasse['nom']) {echo 'selected';} ?> value="<?= $idListClasse['nom'] ?>">
                                <?= $idListClasse['nom'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="listEleve table-responsive">
        <?php if (isset($_GET['classe'])): ?>
            <table class="table-saisie">
                <col style="min-width: 171px;">
                <col style="min-width: 62px;">
                <?php $nbre = 0; while ($nbre != $nbreDevoirs): ?>
                    <col style="min-width: 57px;">
                    <?php $nbre ++ ?>
                <?php endwhile; ?>
                <thead>
                    <tr>
                        <th colspan="2" style="text-align: left;">
                            <button type="button" class="btn btn-primary btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#creeDevoir" style="padding: 0.03rem 0.4rem; background-color: var(--main3); border-color: var(--main3); border-radius: 15px; font-size: 0.75rem; font-weight: 800;">
                                Créer un devoir
                            </button>
                        </th>
                        <?php $nbre = 0; while ($nbre != $nbreDevoirs): ?>
                            <th style="padding: 0!important;" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" data-bs-custom-class="custom-popover" data-bs-content="<p><b><?= $nomMatiere ?></b><br/><?= date_format(date_create($listeDesDevoirs[$nbre][1]), 'd/m/Y') . ' - <i>' . $listeDesDevoirs[$nbre][3] . '</i>' ?></p>Coefficient : <?= $listeDesDevoirs[$nbre][0] ?>" data-bs-html="true">
                                <button type="button" class="btn btn-primary modifier-devoir" data-bs-toggle="modal" data-bs-target="<?= '#modifDevoir' . $listeDesDevoirs[$nbre][2] ?>">
                                    <?= date_format(date_create($listeDesDevoirs[$nbre][1]), 'd/m') ?>
                                </button>
                            </th>
                            <?php $nbre ++ ?>
                        <?php endwhile; ?>
                    </tr>
                    <tr>
                        <th> <?php if (isset($nbreEleves)) {echo $nbreEleves . ' élèves';} ?> </th>
                        <th> Moyenne </th>
                        <?php $nbre = 0; while ($nbre != $nbreDevoirs): ?>
                            <th style="font-weight: 500; font-style: italic;"> Coef. <?= $listeDesDevoirs[$nbre][0] ?> </th>
                            <?php $nbre ++ ?>
                        <?php endwhile; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $queryListEleve = "SELECT DISTINCT Eleves.nom, Eleves.prenom, Eleves.idEleve FROM Eleves INNER JOIN Classe ON Classe.idClasse = Eleves.idClasse WHERE Classe.nom = '" . $_GET['classe'] . "';";
                    $reqListEleve = mysqli_query($link,$queryListEleve);
                    while ($idListEleve = mysqli_fetch_array($reqListEleve)): ?>
                        <tr>
                            <td class="devoir"> <?= $idListEleve['nom'] . ' ' . $idListEleve['prenom'] ?> </td>
                            <td class="note-elem devoir">
                                <?php
                                $nbre = 0;
                                $sommeNote = $sommeNoteNbre = NULL;
                                while ($nbre != $nbreDevoirs) {
                                    $queryNoteEleveA = "SELECT Notes.note FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement WHERE Notes.idEleve = " . $idListEleve['idEleve'] . " AND Enseignement.idProf = " . $_SESSION['id'] . " AND Controles.idControle = " . $listeDesDevoirs[$nbre][2] . ";";
                                    $reqNoteEleveA = mysqli_query($link,$queryNoteEleveA);
                                    while ($idNoteEleveA = mysqli_fetch_array($reqNoteEleveA)) {
                                        if (gettype($idNoteEleveA['note']) != 'NULL' && $idNoteEleveA['note'] != 'Abs' && $idNoteEleveA['note'] != 'N.Not' && $idNoteEleveA['note'] != 'Disp') {
                                            $sommeNote += (float)$idNoteEleveA['note'] * $listeDesDevoirs[$nbre][0];
                                            $sommeNoteNbre += $listeDesDevoirs[$nbre][0];
                                        }
                                    }
                                    $nbre ++;
                                }
                                if (gettype($sommeNote) != 'NULL') {echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');} else {echo 'N.Not';}
                                ?>
                            </td>
                            <?php $nbre = 0; while ($nbre != $nbreDevoirs): ?>
                                <?php
                                $queryNoteEleveB = "SELECT Notes.note, Notes.idNote FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement WHERE Notes.idEleve = " . $idListEleve['idEleve'] . " AND Enseignement.idProf = " . $_SESSION['id'] . " AND Controles.idControle = " . $listeDesDevoirs[$nbre][2] . ";";
                                $reqNoteEleveB = mysqli_query($link,$queryNoteEleveB);
                                while ($idNoteEleveB = mysqli_fetch_array($reqNoteEleveB)): ?>
                                    <td class="note-elem enter-des-notes">
                                        <form action="" method="POST">
                                            <input class="enter-note" name="<?= 'note' . $idNoteEleveB['idNote'] ?>" type="text" value="<?php if (gettype($idNoteEleveB['note']) == 'NULL') {echo '';} elseif ($idNoteEleveB['note'] == 'Abs' || $idNoteEleveB['note'] == 'N.Not' || $idNoteEleveB['note'] == 'Disp') {echo $idNoteEleveB['note'];} else {echo number_format($idNoteEleveB['note'], 2, ',', ' ');} ?>" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                <?php endwhile; ?>
                                <?php $nbre ++ ?>
                            <?php endwhile; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="releve-moyenne-generale"> Moyenne générale </td>
                        <td class="releve-moyenne-generale-note moyenne-elem">
                            <?php
                            $nbre=0;
                            $sommeNote = $sommeNoteNbre = NULL;
                            while ($nbre != $nbreDevoirs) {
                                $queryControleMoyenneG = "SELECT Notes.note, Controles.coefficient FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle WHERE Notes.idControle = " . $listeDesDevoirs[$nbre][2] . " AND Notes.note <> 'NULL';";
                                $reqControleMoyenneG = mysqli_query($link,$queryControleMoyenneG);
                                while ($idControleMoyenneG = mysqli_fetch_array($reqControleMoyenneG)) {
                                    if ($idControleMoyenneG['note'] != 'Abs' && $idControleMoyenneG['note'] != 'N.Not' && $idControleMoyenneG['note'] != 'Disp') {
                                        $sommeNote += (float)$idControleMoyenneG['note'] * $idControleMoyenneG['coefficient'];
                                        $sommeNoteNbre += $idControleMoyenneG['coefficient'];
                                    }
                                }
                                $nbre ++;
                            }
                            if (gettype($sommeNote) != 'NULL') {
                                echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');
                            } else {echo 'N.Not';}
                            ?>
                        </td>
                        <?php $nbre=0; while ($nbre != $nbreDevoirs): ?>
                            <?php
                            $queryControleMoyenne = "SELECT Notes.note, Controles.coefficient FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle WHERE Notes.idControle = " . $listeDesDevoirs[$nbre][2] . " AND Notes.note <> 'NULL';";
                            $reqControleMoyenne = mysqli_query($link,$queryControleMoyenne);
                            $sommeNote = $sommeNoteNbre = NULL;
                            while ($idControleMoyenne = mysqli_fetch_array($reqControleMoyenne)) {
                                if ($idControleMoyenne['note'] != 'Abs' && $idControleMoyenne['note'] != 'N.Not' && $idControleMoyenne['note'] != 'Disp') {
                                    $sommeNote += (float)$idControleMoyenne['note'] * $idControleMoyenne['coefficient'];
                                    $sommeNoteNbre += $idControleMoyenne['coefficient'];
                                }
                            }
                            ?>
                            <td class="releve-moyenne-generale-note moyenne-elem">
                                <?php if (gettype($sommeNote) != 'NULL') {echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');} else {echo 'N.Not';} ?>
                            </td>
                            <?php $nbre ++ ?>
                        <?php endwhile; ?>
                    </tr>
                </tfoot>
            </table>
            <div class="info-note" style="margin-top: 10px;">
                Pour ne pas comptabiliser le devoir dans le calcul de la moyenne : <br>
                <span class="caract">A</span> : L'élève est Absent (Abs) <br>
                <span class="caract">D</span> : L'élève est Dispensé (Disp) <br>
                <span class="caract">N</span> : L'élève est Non noté (N.Not)
            </div>
        <?php endif ?>
        <?php if (isset($_SESSION['login']) && ($_SESSION['role'] == 'Professeur') && isset($_GET['classe'])): ?>
            <div class="modal fade modal-devoir" id="creeDevoir" tabindex="-1" aria-labelledby="creeDevoirLabel" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" style="z-index: 10000;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="en-tete-dialog">
                                Créer un devoir : <span class="nom-matiere-devoir"><?= $nomMatiere ?></span> - <?= $_GET['classe'] ?>
                            </div>
                        </div>
                        <div class="modal-body devoir-dialog">
                            <form action="" method="POST">
                                <div class="d-flex mb-2">
                                    <div class="text-dialog"> <label for="commentaire">Nom du devoir :</label> </div>
                                    <div class="devoir-saisie"> <input type="text" id="commentaire" maxlength="255" style="width: 100%;" name="commentaire" required> </div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-dialog"> <label for="date">Devoir du :</label> </div>
                                    <div class="devoir-saisie"> <input type="date" id="date" name="date" style="width: 105px;" required> </div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-dialog"> <label for="coeff">Coefficient :</label> </div>
                                    <div class="devoir-saisie"> <input type="number" step="0.01" id="coeff" min="0" max="20" style="width: 60px;" name="coeff" value="1" required> </div>
                                </div>
                                <div class="footer-dialog mt-2">
                                    <input type="button" class="btn btn-close-modal" data-bs-dismiss="modal" aria-label="Close" value="Annuler">
                                    <input class="btn btn-primary" id="footer-cree" type="submit" value="Créer">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php $nbre = 0; while ($nbre != $nbreDevoirs): ?>
                <div class="modal fade modal-devoir" id="<?= 'modifDevoir' . $listeDesDevoirs[$nbre][2] ?>" tabindex="-1" aria-labelledby="<?= 'modifDevoirLabel' . $listeDesDevoirs[$nbre][2] ?>" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" style="z-index: 10000;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="en-tete-dialog"> Modification d'un devoir : <span class="nom-matiere-devoir"><?= $nomMatiere ?></span> - <?= $_GET['classe'] ?> </div>
                            </div>
                            <div class="modal-body devoir-dialog">
                                <form action="" method="POST">
                                    <?php
                                    $queryInfoDevoir = "SELECT * FROM Controles WHERE idControle = " . $listeDesDevoirs[$nbre][2] . ";";
                                    $reqInfoDevoir = mysqli_query($link,$queryInfoDevoir);
                                    while ($idInfoDevoir = mysqli_fetch_array($reqInfoDevoir)): ?>
                                        <div class="d-flex mb-2">
                                            <div class="text-dialog"> <label for="<?= 'nomDevoir' . $listeDesDevoirs[$nbre][2] ?>">Nom du devoir :</label> </div>
                                            <div class="devoir-saisie"> <input type="text" id="<?= 'nomDevoir' . $listeDesDevoirs[$nbre][2] ?>" maxlength="255" style="width: 100%;" name="<?= 'nomDevoir' . $listeDesDevoirs[$nbre][2] ?>" value="<?= $idInfoDevoir['nomControle'] ?>" required> </div>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <div class="text-dialog"> <label for="<?= 'dateDevoir' . $listeDesDevoirs[$nbre][2] ?>">Devoir du :</label> </div>
                                            <div class="devoir-saisie"> <input type="date" id="<?= 'dateDevoir' . $listeDesDevoirs[$nbre][2] ?>" name="<?= 'dateDevoir' . $listeDesDevoirs[$nbre][2] ?>" value="<?= $idInfoDevoir['dates'] ?>" style="width: 105px;" required> </div>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <div class="text-dialog"> <label for="<?= 'coeffDevoir' . $listeDesDevoirs[$nbre][2] ?>">Coefficient :</label> </div>
                                            <div class="devoir-saisie"> <input type="number" step="0.01" id="<?= 'coeffDevoir' . $listeDesDevoirs[$nbre][2] ?>" min="0" max="20" style="width: 60px;" name="<?= 'coeffDevoir' . $listeDesDevoirs[$nbre][2] ?>" value="<?= $idInfoDevoir['coefficient'] ?>" required> </div>
                                        </div>
                                        <div class="footer-dialog mt-2">
                                            <input type="radio" class="btn-check" name="<?= 'delete-val' . $listeDesDevoirs[$nbre][2] ?>" id="<?= 'supprimer' . $listeDesDevoirs[$nbre][2] ?>" value="<?= $listeDesDevoirs[$nbre][2] ?>" autocomplete="off" onchange="this.form.submit()">
                                            <label class="btn delete-button-devoir" for="<?= 'supprimer' . $listeDesDevoirs[$nbre][2] ?>" style="padding: 3px 9px!important;"> Supprimer </label>
                                            <input type="button" class="btn btn-close-modal" data-bs-dismiss="modal" aria-label="Close" value="Annuler">
                                            <input type="radio" class="btn-check" name="<?= 'valider' . $listeDesDevoirs[$nbre][2] ?>" id="<?= 'valider' . $listeDesDevoirs[$nbre][2] ?>" value="<?= $listeDesDevoirs[$nbre][2] ?>" autocomplete="off" onchange="this.form.submit()">
                                            <label class="btn" id="footer-cree" for="<?= 'valider' . $listeDesDevoirs[$nbre][2] ?>" style="padding: 3px 9px!important;"> Valider </label>
                                        </div>
                                    <?php endwhile; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $nbre ++ ?>
            <?php endwhile; ?>
        <?php elseif (isset($_SESSION['login']) && ($_SESSION['role'] == 'Professeur') && !isset($_GET['classe'])): ?>
            <div class="bandeau-annonce">Sélectionnez une classe</div>
        <?php endif ?>
    </div>
<?php endif ?>

<?php
require 'assets/footer.php';
?>