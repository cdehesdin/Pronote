<?php
session_start();

// Définition du titre de la page
if (!isset($_SESSION['login'])) {
    $title = "Connexion";
} else {
    $title = "Saisie des appreciations";
}

// Si utilisateur non connecté, renvoie vers la page de connexion
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
}

// Sélection du tri des notes (par élève ou par classe)
if (!isset($_POST['ordres'])){
    $_POST['ordres'] = 'eleve';
} elseif ($_POST['ordres'] == 'classe'){
    unset($_GET);
}

// Définition des listes utilisées
$listeDesDevoirs = $moyenneClasse = [];

require 'assets/header.php';

$queryListIdClasse = "SELECT DISTINCT Classe.nom, Classe.idClasse FROM Classe INNER JOIN Enseignement ON Classe.idClasse = Enseignement.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . ";";
$reqListIdClasse = mysqli_query($link,$queryListIdClasse);
while ($idListIdClasse = mysqli_fetch_array($reqListIdClasse)) {
    $queryEnseign = "SELECT idEnseignement FROM Enseignement WHERE idClasse = " . $idListIdClasse['idClasse'] . " AND idProf = " . $_SESSION['id'] . ";";
    $reqEnseign = mysqli_query($link,$queryEnseign);
    while ($idEnseign = mysqli_fetch_array($reqEnseign)) {
        if (isset($_POST['appreciationClasse-' . $idEnseign['idEnseignement']])) {
            $_POST['ordres'] = 'classe';
            if ($_POST['appreciationClasse-' . $idEnseign['idEnseignement']] == '') {
                $queryModifApprec = "UPDATE AppClasse SET app = NULL WHERE idEnseignement = " . $idEnseign['idEnseignement'] . ";";
                if (mysqli_query($link,$queryModifApprec)) {
                    unset($_POST['appreciationClasse-' . $idEnseign['idEnseignement']]);
                }
            } else {
                $queryModifApprec = "UPDATE AppClasse SET app = '" . $_POST['appreciationClasse-' . $idEnseign['idEnseignement']] . "' WHERE idEnseignement = " . $idEnseign['idEnseignement'] . ";";
                if (mysqli_query($link,$queryModifApprec)) {
                    unset($_POST['appreciationClasse-' . $idEnseign['idEnseignement']]);
                }
            }
        }
    }
}

if (isset($_GET['classe'])) {
    $queryNameEtListe = "SELECT Eleves.idEleve, Enseignement.idEnseignement FROM Matiere INNER JOIN Profs ON Profs.idMatiere = Matiere.idMatiere INNER JOIN Enseignement ON Profs.idProf = Enseignement.idProf INNER JOIN Classe ON Classe.idClasse = Enseignement.idClasse INNER JOIN Eleves ON Eleves.idClasse = Enseignement.idClasse WHERE Classe.nom = '" . $_GET['classe'] . "';";
    $reqNameEtListe = mysqli_query($link,$queryNameEtListe);
    while ($idNameEtListe = mysqli_fetch_array($reqNameEtListe)) {
        if (isset($_POST['appreciation-' . $idNameEtListe['idEleve']]) && ($_POST['appreciation-' . $idNameEtListe['idEleve']] == '')) {
            $queryModifAppreciation = "UPDATE AppEleve SET app = NULL WHERE idEnseignement = " . $idNameEtListe['idEnseignement'] . " AND idEleve = " . $idNameEtListe['idEleve'] . ";";
            if (mysqli_query($link,$queryModifAppreciation)) {
                unset($_POST['appreciation-' . $idNameEtListe['idEleve']]);
            }
        } elseif (isset($_POST['appreciation-' . $idNameEtListe['idEleve']])) {
            $queryModifAppreciation = "UPDATE AppEleve SET app = '" . $_POST['appreciation-' . $idNameEtListe['idEleve']] . "' WHERE idEnseignement = " . $idNameEtListe['idEnseignement'] . " AND idEleve = " . $idNameEtListe['idEleve'] . ";";
            if (mysqli_query($link,$queryModifAppreciation)) {
                unset($_POST['appreciation-' . $idNameEtListe['idEleve']]);
            }
        }
    }
    
    // Enregistrement des moyennes de la classe par matière dans des tableaux
    $queryNameMatiere = "SELECT DISTINCT Matiere.nom, Classe.idClasse FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere INNER JOIN Classe ON Enseignement.idClasse = Classe.idClasse WHERE Classe.nom = '" . $_GET['classe'] . "' AND Profs.idProf = " . $_SESSION['id'] . " AND Notes.note <> 'NULL' AND Notes.note <> 'Abs' AND Notes.note <> 'N.Not' AND Notes.note <> 'Disp';";
    $reqNameMatiere = mysqli_query($link,$queryNameMatiere);
    while ($idNameMatiere = mysqli_fetch_array($reqNameMatiere)) {
        $queryClasseEleves = "SELECT idEleve FROM Eleves WHERE idClasse = " . $idNameMatiere['idClasse'] . ";";
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
                $moyenneClasse[$idClasseEleves['idEleve']] = $sommeNote / $sommeNoteNbre;
            }
        }
    }
}
?>

<?php if ($_SESSION['role'] == 'Professeur'): ?>
    <div class="choice-bar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="choice-title"> Saisie des appreciations </div>
            <div class="dropdown d-inline ms-2">
                <form action="" method="POST">
                    <input type="radio" class="btn-check" name="ordres" id="eleve" value="eleve" autocomplete="off" onchange="this.form.submit()" <?php if (!isset($_POST['ordres']) || $_POST['ordres'] != 'classe') {echo 'checked';} ?>>
                    <label class="btn btn-sm" for="eleve"> Par élève </label>
                    <input type="radio" class="btn-check" name="ordres" id="classe" value="classe" autocomplete="off" onchange="this.form.submit()" <?php if (isset($_POST['ordres']) && $_POST['ordres'] == 'classe') {echo 'checked';} ?>>
                    <label class="btn btn-sm" for="classe"> Par classe </label>
                </form>
            </div>
            <?php if (isset($_POST['ordres']) && $_POST['ordres'] == 'eleve'): ?>
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
            <?php endif; ?>
        </div>
    </div>
    <div class="listEleve table-responsive">
        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'Professeur') && isset($_POST['ordres']) && $_POST['ordres'] == 'eleve'): ?>
            <?php if (isset($_GET['classe'])): ?>
                <table class="table-appreciation">
                    <col style="min-width: 171px; background-color: var(--main2);">
                    <col style="min-width: 62px; background-color: var(--main1);">
                    <col class="moyenne">
                    <col style="min-width: 400px; max-width: 400px;">
                    <thead>
                        <tr>
                            <th> Élèves </th>
                            <th> N.Not </th>
                            <th> Moy. </th>
                            <th> Appreciation </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $queryListEleve = "SELECT DISTINCT Eleves.nom, Eleves.prenom, Eleves.idEleve, Classe.idClasse FROM Eleves INNER JOIN Classe ON Classe.idClasse = Eleves.idClasse WHERE Classe.nom = '" . $_GET['classe'] . "';";
                        $reqListEleve = mysqli_query($link,$queryListEleve);
                        while ($idListEleve = mysqli_fetch_array($reqListEleve)): ?>
                            <tr>
                                <td class="devoir"> <?= $idListEleve['nom'] . ' ' . $idListEleve['prenom'] ?> </td>
                                <td class="note-elem">
                                    <?php
                                    $queryNbreControleTotale = "SELECT COUNT(*) FROM Notes INNER JOIN Controles ON Controles.idControle = Notes.idControle INNER JOIN Enseignement ON Enseignement.idEnseignement = Controles.idEnseignement INNER JOIN Classe ON Classe.idClasse = Enseignement.idClasse WHERE Notes.idEleve = " . $idListEleve['idEleve'] . " AND Classe.nom = '" . $_GET['classe'] . "' AND Enseignement.idProf = " . $_SESSION['id'] . ";";
                                    $queryNbreControle = "SELECT COUNT(*) FROM Notes INNER JOIN Controles ON Controles.idControle = Notes.idControle INNER JOIN Enseignement ON Enseignement.idEnseignement = Controles.idEnseignement INNER JOIN Classe ON Classe.idClasse = Enseignement.idClasse WHERE Notes.idEleve = " . $idListEleve['idEleve'] . " AND Classe.nom = '" . $_GET['classe'] . "' AND Enseignement.idProf = " . $_SESSION['id'] . " AND Notes.note <> 'NULL' AND Notes.note <> 'Abs' AND Notes.note <> 'N.Not' AND Notes.note <> 'Disp';";
                                    $reqNbreControleTotale = mysqli_query($link,$queryNbreControleTotale);
                                    $reqNbreControle = mysqli_query($link,$queryNbreControle);
                                    while ($idNbreControle = mysqli_fetch_array($reqNbreControle)) {
                                        while ($idNbreControleTotale = mysqli_fetch_array($reqNbreControleTotale)) {
                                            echo $idNbreControle['COUNT(*)'] . '/' . $idNbreControleTotale['COUNT(*)'] ;
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="moyenne-elem">
                                    <?php if (isset($moyenneClasse[$idListEleve['idEleve']])) {echo number_format($moyenneClasse[$idListEleve['idEleve']], 2, ',', ' ');} else {echo 'N.Not';} ?>
                                </td>
                                <td class="appreciation">
                                    <?php
                                    $queryEns = "SELECT idEnseignement FROM Enseignement WHERE idClasse = " . $idListEleve['idClasse'] . " AND idProf = " . $_SESSION['id'] . ";";
                                    $reqEns = mysqli_query($link,$queryEns);
                                    while ($idEns = mysqli_fetch_array($reqEns)): ?>
                                        <?php
                                        $queryApp = "SELECT * FROM AppEleve WHERE idEnseignement = " . $idEns['idEnseignement'] . " AND idEleve = " . $idListEleve['idEleve'] . ";";
                                        $reqApp = mysqli_query($link,$queryApp);
                                        while ($idApp = mysqli_fetch_array($reqApp)): ?>
                                            <form action="" method="post">
                                                <textarea class="enter-note" name="<?= 'appreciation-' . $idListEleve['idEleve'] ?>" size="255" onchange="this.form.submit()"><?= $idApp['app'] ?></textarea>
                                            </form>
                                        <?php endwhile; ?>
                                    <?php endwhile; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="releve-moyenne-generale"> Moyenne générale </td>
                            <td class="releve-moyenne-generale-note moyenne-elem">
                                <?php
                                $sommeNote = $sommeNoteNbre = NULL;
                                foreach ($moyenneClasse as $idEleve => $moyenneEleve) {
                                    $sommeNote += $moyenneEleve;
                                    $sommeNoteNbre++;
                                }
                                if (gettype($sommeNote) != 'NULL') {
                                    echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');
                                } else {
                                    echo 'N.Not';
                                }
                                ?>
                            </td>
                            <td> </td>
                        </tr>
                    </tfoot>
                </table>
            <?php elseif (!isset($_GET['classe'])): ?>
                <div class="bandeau-annonce">Sélectionnez une classe</div>
            <?php endif ?>
        <?php elseif (isset($_POST['ordres']) && $_POST['ordres'] == 'classe'): ?>
            <table class="table-appreciation">
                    <col style="min-width: 171px; background-color: var(--main2);">
                    <col class="moyenne">
                    <col style="min-width: 400px; max-width: 400px;">
                    <thead>
                        <tr>
                            <th> Classes </th>
                            <th> Moy. </th>
                            <th> Appreciation </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $queryListDesClasse = "SELECT DISTINCT Classe.nom, Classe.idClasse, Enseignement.idEnseignement FROM Classe INNER JOIN Enseignement ON Classe.idClasse = Enseignement.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . ";";
                        $reqListDesClasse = mysqli_query($link,$queryListDesClasse);
                        while ($idListDesClasse = mysqli_fetch_array($reqListDesClasse)): ?>
                            <tr>
                                <td class="devoir"> <?= $idListDesClasse['nom'] ?> </td>
                                <td class="moyenne-elem">
                                    <?php
                                    $queryMoyenne = "SELECT Notes.note, Controles.coefficient FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement WHERE Controles.idEnseignement = " . $idListDesClasse['idEnseignement'] . " AND Enseignement.idProf = " . $_SESSION['id'] . " AND Notes.note <> 'NULL' AND Notes.note <> 'Abs' AND Notes.note <> 'N.Not' AND Notes.note <> 'Disp';";
                                    $reqMoyenne = mysqli_query($link,$queryMoyenne);
                                    $sommeNote = $sommeNoteNbre = NULL;
                                    while ($idMoyenne = mysqli_fetch_array($reqMoyenne)) {
                                        $sommeNote += (float)$idMoyenne['note'] * $idMoyenne['coefficient'];
                                        $sommeNoteNbre += $idMoyenne['coefficient'];
                                    }
                                    if (gettype($sommeNote) != 'NULL') {echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');} else {echo 'N.Not';} ?>
                                </td>
                                <td class="appreciation">
                                    <?php
                                    $queryAppreciation = "SELECT * FROM AppClasse WHERE idEnseignement = " . $idListDesClasse['idEnseignement'] . ";";
                                    $reqAppreciation = mysqli_query($link,$queryAppreciation);
                                    while ($idApp = mysqli_fetch_array($reqAppreciation)): ?>
                                        <form action="" method="post">
                                            <textarea class="enter-note" name="<?= 'appreciationClasse-' . $idListDesClasse['idEnseignement'] ?>" size="255" onchange="this.form.submit()"><?= $idApp['app'] ?></textarea>
                                        </form>
                                    <?php endwhile; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
        <?php endif ?>
    </div>
<?php endif ?>

<?php
require 'assets/footer.php';
?>