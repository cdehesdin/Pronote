<?php
session_start();

// Définition du titre de la page
if (!isset($_SESSION['login'])) {
    $title = "Connexion";
} else {
    if ($_SESSION['role'] == 'Eleve') {
        $title = "Mon relevé de notes";
    } else {
        $title = "Relevé de notes";
    };
}

// Si utilisateur non connecté, renvoie vers la page de connexion
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
}

// Définition des listes utilisés
$moyenneParMatiere = [];

require 'elements/header.php';

if (isset($_GET['classe'])) {
    $queryNameEtListe = "SELECT DISTINCT Matiere.idMatiere, Eleves.idEleve, Classe.idClasse, Enseignement.idEnseignement FROM Matiere INNER JOIN Profs ON Profs.idMatiere = Matiere.idMatiere INNER JOIN Enseignement ON Profs.idProf = Enseignement.idProf INNER JOIN Classe ON Classe.idClasse = Enseignement.idClasse INNER JOIN Eleves ON Eleves.idClasse = Enseignement.idClasse WHERE Classe.nom = '" . $_GET['classe'] . "';";
    $reqNameEtListe = mysqli_query($link,$queryNameEtListe);
    while ($idNameEtListe = mysqli_fetch_array($reqNameEtListe)) {
        if (!isset($_POST['eleves']) && isset($_POST['appreciation-' . $idNameEtListe['idEleve'] . '-' . $idNameEtListe['idMatiere']])) {
            $_POST['eleves'] = $idNameEtListe['idClasse'] . '-' . $idNameEtListe['idEleve'];
        }
        if (isset($_POST['eleves']) && isset($_POST['appreciation-' . $idNameEtListe['idEleve'] . '-' . $idNameEtListe['idMatiere']]) && ($_POST['appreciation-' . $idNameEtListe['idEleve'] . '-' . $idNameEtListe['idMatiere']] == '')) {
            $queryModifAppreciation = "UPDATE AppEleve SET app = NULL WHERE idEnseignement = " . $idNameEtListe['idEnseignement'] . " AND idEleve = " . $idNameEtListe['idEleve'] . ";";
            if (mysqli_query($link,$queryModifAppreciation)) {
                unset($_POST['appreciation-' . $idNameEtListe['idEleve'] . '-' . $idNameEtListe['idMatiere']]);
            }
        } elseif (isset($_POST['eleves']) && isset($_POST['appreciation-' . $idNameEtListe['idEleve'] . '-' . $idNameEtListe['idMatiere']])) {
            $queryModifAppreciation = "UPDATE AppEleve SET app = '" . $_POST['appreciation-' . $idNameEtListe['idEleve'] . '-' . $idNameEtListe['idMatiere']] . "' WHERE idEnseignement = " . $idNameEtListe['idEnseignement'] . " AND idEleve = " . $idNameEtListe['idEleve'] . ";";
            if (mysqli_query($link,$queryModifAppreciation)) {
                unset($_POST['appreciation-' . $idNameEtListe['idEleve'] . '-' . $idNameEtListe['idMatiere']]);
            }
        }
    }
}

if (!isset($_GET['enfants'])) {$_GET['enfants'] = 0;}

if ($_SESSION['role'] == 'Eleve') {
    $IdEleve = $_SESSION['id'];
    $ClasseEleve = $_SESSION['classe'];
} elseif ($_SESSION['role'] == 'Parent') {
    $IdEleve = $_SESSION['idEleve'][$_GET['enfants']];
    $ClasseEleve = $_SESSION['classe'][$_GET['enfants']];
} elseif ($_SESSION['role'] == 'Professeur' && isset($_POST['eleves'])) {
    $tableInfo = explode('-', $_POST['eleves']);
    $IdEleve = $tableInfo[1];
    $ClasseEleve = $tableInfo[0];
}

if ($_SESSION['role'] == 'Eleve' || ($_SESSION['role'] == 'Parent' && isset($_GET['enfants'])) || ($_SESSION['role'] == 'Professeur' && isset($_POST['eleves']) && isset($_GET['classe']))) {
    // Enregistrement des moyennes de par matière dans des tableaux
    $queryNameMatiere = "SELECT DISTINCT Matiere.nom FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Notes.idEleve = " . $IdEleve . ";";
    $reqNameMatiere = mysqli_query($link,$queryNameMatiere);
    while ($idNameMatiere = mysqli_fetch_array($reqNameMatiere)) {
        $queryNotesEleves = "SELECT Notes.note, Controles.coefficient FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Matiere.nom = '" . $idNameMatiere['nom'] . "' AND Notes.idEleve = " . $IdEleve . ";";;
        $reqNotesEleves = mysqli_query($link,$queryNotesEleves);
        $sommeNote = $sommeNoteNbre = $moyenne = $nbreNote = NULL;
        while ($idNotesEleves = mysqli_fetch_array($reqNotesEleves)) {
            if (gettype($idNotesEleves['note']) != 'NULL' && $idNotesEleves['note'] != 'Abs' && $idNotesEleves['note'] != 'N.Not' && $idNotesEleves['note'] != 'Disp') {
                $sommeNote += (float)$idNotesEleves['note'] * $idNotesEleves['coefficient'];
                $sommeNoteNbre += $idNotesEleves['coefficient'];
            }
            $nbreNote ++;
        }
        if (gettype($sommeNote) != 'NULL') {
            $moyenneParMatiere[$idNameMatiere['nom']] = [$nbreNote, $sommeNote / $sommeNoteNbre];
        } else {
            $moyenneParMatiere[$idNameMatiere['nom']] = [$nbreNote, NULL];
        }
    }
    foreach ($moyenneParMatiere as $infoMatiere) {
        if (!isset($nbreNoteMaximum)) {
            $nbreNoteMaximum = $infoMatiere[0];
        } elseif ($nbreNoteMaximum < $infoMatiere[0]) {
            $nbreNoteMaximum = $infoMatiere[0];
        }
    }
}
?>

<?php if (isset($_SESSION['login'])): ?>
    <div class="choice-bar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <?php if ($_SESSION['role'] == 'Parent'): ?>
                <div class="dropdown d-inline me-2">
                    <form action="" method="GET">
                        <select class="form-select form-select-sm" name="enfants" id="classe" onchange="this.form.submit()">
                            <?php
                            foreach ($_SESSION['idEleve'] as $Id => $Identifiant): ?>
                                <option <?php if (isset($_GET['enfants']) && $_GET['enfants'] == $Id) {echo 'selected';} ?> value="<?= $Id ?>">
                                    <?= $_SESSION['nomEleve'][$Id] . ' ' . $_SESSION['prenomEleve'][$Id] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            <?php endif; ?>
            <div class="choice-title">
                <?php if ($_SESSION['role'] == 'Eleve'): ?>
                    Mon relevé de notes
                <?php elseif ($_SESSION['role'] == 'Parent' || $_SESSION['role'] == 'Professeur'): ?>
                    Relevé de notes
                <?php endif; ?>
            </div>
            <?php if ($_SESSION['role'] == 'Professeur'): ?>
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
                <?php if (isset($_GET['classe'])): ?>
                    <div class="dropdown d-inline ms-2">
                        <form action="" method="POST">
                            <select class="form-select form-select-sm" name="eleves" id="eleves" onchange="this.form.submit()" style="padding-top: 0.1rem!important; padding-bottom: 0.1rem!important; width:184px;">
                                <option class="classe-disabled" <?php if (!isset($_POST['eleve'])) {echo 'selected';} ?> disabled> Élève </option>
                                <?php
                                $queryListEleve = "SELECT DISTINCT Eleves.nom, Eleves.prenom, Eleves.idEleve, Classe.idClasse FROM Eleves INNER JOIN Classe ON Classe.idClasse = Eleves.idClasse WHERE Classe.nom = '" . $_GET['classe'] . "';";
                                $reqListEleve = mysqli_query($link,$queryListEleve);
                                while ($idListEleve = mysqli_fetch_array($reqListEleve)): ?>
                                    <option <?php if (isset($_POST['eleves']) && $_POST['eleves'] == ($idListEleve['idClasse'] . '-' . $idListEleve['idEleve'])) {echo 'selected';} ?> value="<?= $idListEleve['idClasse'] . '-' . $idListEleve['idEleve'] ?>">
                                        <?= $idListEleve['nom'] . ' ' . $idListEleve['prenom'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="choice-title float-end"> <i class="fa-solid fa-print"></i> </div>
    </div>
    <div class="listEleve table-responsive">
        <?php if ($_SESSION['role'] == 'Eleve' || ($_SESSION['role'] == 'Parent' && isset($_GET['enfants'])) || ($_SESSION['role'] == 'Professeur' && isset($_POST['eleves']) && isset($_GET['classe']))): ?>
            <table class="table-releve">
                <col class="matiere" style="background-color: #e2e2e2;">
                <?php if ($_SESSION['role'] != 'Professeur'): ?> <col class="moyenne"> <?php endif; ?>
                <?php $nbre=$nbreNoteMaximum; while ($nbre > 0): ?> <?php $nbre-=1 ?> <col class="devoirs"> <?php endwhile ?>
                <?php if (($_SESSION['role'] == 'Professeur' && isset($_POST['eleves']) && isset($_GET['classe'])) || (($_SESSION['role'] == 'Eleve' || $_SESSION['role'] == 'Parent') && isset($IdEleve) && isset($ClasseEleve))): ?>
                    <col style="min-width: 319px; max-width: 319px;">
                <?php endif ?>
                <thead>
                    <tr>
                        <th <?php if ($_SESSION['role'] != 'Professeur'): ?> rowspan="2" <?php endif; ?>> Matières </td>
                        <?php if ($_SESSION['role'] != 'Professeur'): ?> <th> Moy. </th> <?php endif; ?>
                        <th <?php if ($_SESSION['role'] != 'Professeur'): ?> rowspan="2" <?php endif; ?> colspan="<?= $nbreNoteMaximum ?>"> Devoirs </th>
                        <?php if (($_SESSION['role'] == 'Eleve' || $_SESSION['role'] == 'Parent') && isset($IdEleve) && isset($ClasseEleve)): ?>
                            <th rowspan="2"> Appréciation </th>
                        <?php endif; ?>
                        <?php if ($_SESSION['role'] == 'Professeur' && isset($_POST['eleves']) && isset($_GET['classe'])): ?>
                            <th> Appréciation </th>
                        <?php endif; ?>
                    </tr>
                    <?php if ($_SESSION['role'] != 'Professeur'): ?>
                        <tr> <th> Élève </th> </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php
                    $queryNomMatiere = "SELECT DISTINCT Matiere.idMatiere, Matiere.nom, Matiere.description, Profs.nom AS nomProf, Profs.sexe, Profs.idProf, Enseignement.idEnseignement FROM Matiere INNER JOIN Profs ON Matiere.idMatiere = Profs.idMatiere INNER JOIN Enseignement ON Profs.idProf = Enseignement.idProf WHERE Enseignement.idClasse = " . $ClasseEleve ." ORDER BY Matiere.description;";
                    $reqNomMatiere = mysqli_query($link,$queryNomMatiere);
                    while ($idNomMatiere = mysqli_fetch_array($reqNomMatiere)): ?>
                        <tr>
                            <td>
                                <span class="nom-matiere"> <?= $idNomMatiere['description'] ?> </span> <br>
                                <?php if ($idNomMatiere['sexe'] == 'F'): ?>
                                    Mme <?= $idNomMatiere['nomProf'] ?>
                                <?php else: ?>
                                    M. <?= $idNomMatiere['nomProf'] ?>
                                <?php endif ?>
                            </td>
                            <?php if ($_SESSION['role'] != 'Professeur'): ?>
                                <td class="moyenne-elem">
                                    <?php
                                    foreach ($moyenneParMatiere as $matiereEleve => $infoMatiere) {
                                        if ($matiereEleve == $idNomMatiere['nom']) {
                                            if (gettype($infoMatiere[1]) != 'NULL') {
                                                echo number_format($infoMatiere[1], 2, ',', ' ');
                                            } else {
                                                echo 'N.Not';
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                            <?php endif; ?>
                            <?php
                            $queryNoteMatiere = "SELECT Notes.note, Controles.coefficient, Controles.dates FROM Notes INNER JOIN Controles ON Notes.idControle = Controles.idControle INNER JOIN Enseignement ON Controles.idEnseignement = Enseignement.idEnseignement INNER JOIN Profs ON Enseignement.idProf = Profs.idProf INNER JOIN Matiere ON Profs.idMatiere = Matiere.idMatiere WHERE Notes.idEleve = " . $IdEleve . " AND Matiere.nom = '" . $idNomMatiere['nom'] . "' ORDER BY Controles.dates;";
                            $nbre = $nbreNoteMaximum;
                            $reqNoteMatiere = mysqli_query($link,$queryNoteMatiere);
                            while ($idNoteMatiere = mysqli_fetch_array($reqNoteMatiere)): ?>
                                <td class="note-elem">
                                    <span class="note-elem-important"><?php if (gettype($idNoteMatiere['note']) != 'NULL' && $idNoteMatiere['note'] != 'Abs' && $idNoteMatiere['note'] != 'N.Not' && $idNoteMatiere['note'] != 'Disp') {echo number_format((float)$idNoteMatiere['note'], 2, ',', ' ');} else {echo $idNoteMatiere['note'];} ?></span><span class="note-elem-sur-20">/20</span>
                                    <div class="note-elem-info">
                                        Coef. <?= number_format($idNoteMatiere['coefficient'], 2, ',', ' ') ?> <br>
                                        <?php
                                        $date = explode('|', date_format(date_create($idNoteMatiere['dates']), 'd|n'));
                                        echo $date[0] . ' ' . $month[$date[1]-1][1];
                                        ?>
                                    </div>
                                </td>
                                <?php $nbre -= 1 ?>
                            <?php endwhile; ?>
                            <?php while ($nbre > 0): ?>
                                <td class="note-elem"> </td>
                                <?php $nbre -= 1 ?>
                            <?php endwhile; ?>
                            <?php if (($_SESSION['role'] == 'Eleve' || $_SESSION['role'] == 'Parent') && isset($IdEleve) && isset($ClasseEleve)): ?>
                                <td class="appreciation app-elv-parent">
                                    <?php
                                    $queryAppreciation = "SELECT * FROM AppEleve WHERE idEnseignement = " . $idNomMatiere['idEnseignement'] . " AND idEleve = " . $IdEleve . ";";
                                    $reqAppreciation = mysqli_query($link,$queryAppreciation);
                                    while ($idAppreciation = mysqli_fetch_array($reqAppreciation)): ?>
                                        <?php
                                        if (gettype($idAppreciation['app']) != 'NULL') {
                                            echo $idAppreciation['app'];
                                        } else {echo "Aucune appréciation n'a été saisie !";}
                                        ?>
                                    <?php endwhile; ?>
                                </td>
                            <?php endif; ?>
                            <?php if ($_SESSION['role'] == 'Professeur' && isset($_POST['eleves']) && isset($_GET['classe'])): ?>
                                <td class="appreciation">
                                    <?php
                                    $queryApp = "SELECT * FROM AppEleve WHERE idEnseignement = " . $idNomMatiere['idEnseignement'] . " AND idEleve = " . $IdEleve . ";";
                                    $reqApp = mysqli_query($link,$queryApp);
                                    while ($idApp = mysqli_fetch_array($reqApp)): ?>
                                        <form action="" method="post">
                                            <textarea class="enter-note" name="<?= 'appreciation-' . $IdEleve . '-' . $idNomMatiere['idMatiere'] ?>" <?php if ($idNomMatiere['idProf'] != $_SESSION['id']) {echo 'disabled';} ?> size="255" onchange="this.form.submit()"><?= $idApp['app'] ?></textarea>
                                        </form>
                                    <?php endwhile; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <?php if ($_SESSION['role'] != 'Professeur'): ?>
                    <tfoot>
                        <tr>
                            <td class="releve-moyenne-generale"> Moyenne générale </td>
                            <td class="releve-moyenne-generale-note moyenne-elem">
                                <?php
                                $sommeNote = $sommeNoteNbre = NULL;
                                foreach ($moyenneParMatiere as $moyenneEleve) {
                                    if (gettype($moyenneEleve[1]) != 'NULL') {
                                        $sommeNote += $moyenneEleve[1];
                                        $sommeNoteNbre ++;
                                    }
                                }
                                if (gettype($sommeNote) != 'NULL') {
                                    echo number_format($sommeNote / $sommeNoteNbre, 2, ',', ' ');
                                } else {
                                    echo 'N.Not';
                                }
                                ?>
                            </td>
                            <?php while ($nbre > 0): ?>
                                <td> </td>
                                <?php $nbre -= 1 ?>
                            <?php endwhile; ?>
                            <?php if ($_SESSION['role'] == 'Professeur' && isset($_POST['eleves']) && isset($_GET['classe'])): ?>
                                <td> </td>
                            <?php endif; ?>
                        </tr>
                    </tfoot>
                <?php endif; ?>
                </table>
            <?php elseif ($_SESSION['role'] == 'Professeur' && !isset($_POST['eleves']) && isset($_GET['classe'])): ?>
                <div class="bandeau-annonce">Sélectionnez un élève</div>
            <?php elseif ($_SESSION['role'] == 'Professeur' && !isset($_POST['eleves']) && !isset($_GET['classe'])): ?>
                <div class="bandeau-annonce">Sélectionnez une classe</div>
            <?php endif ?>
        </div>
    </div>
<?php endif ?>

<?php
require 'elements/footer.php';
?>