<?php
session_start();

// Définition du titre de la page
if (!isset($_SESSION['login'])) {
    $title = "Connexion";
} else {
    $title = "Liste des élèves";
}

// Si utilisateur non connecté, renvoie vers la page de connexion
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
}

require 'elements/header.php';
?>

<?php if ($_SESSION['role'] == 'Professeur'): ?>
    <div class="choice-bar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="choice-title"> Liste des élèves </div>
            <div class="dropdown d-inline ms-2">
                <form action="" method="POST">
                    <select class="form-select form-select-sm" name="classe" id="classe" onchange="this.form.submit()">
                        <option class="classe-disabled" <?php if (!isset($_POST['classe'])) {echo 'selected';} ?> disabled> Classe </option>
                        <?php
                        $queryListClasse = "SELECT DISTINCT nom FROM Classe";
                        $reqListClasse = mysqli_query($link,$queryListClasse);
                        while ($idListClasse = mysqli_fetch_array($reqListClasse)): ?>
                            <option <?php if (isset($_POST['classe']) && $_POST['classe'] == $idListClasse['nom']) {echo 'selected';} ?> value="<?= $idListClasse['nom'] ?>">
                                <?= $idListClasse['nom'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="choice-title float-end"> <i class="fa-solid fa-print"></i> </div>
    </div>
    <div class="listEleve table-responsive" style="overflow: hidden !important;">
        <?php if (isset($_POST['classe'])): ?>
            <div style="overflow-x:auto!important;">
                <table class="table-releve">
                    <col style="min-width: 171px; background-color: #e2e2e2;">
                    <col style="min-width: 41px; background-color: #e2e2e2;">
                    <col style="min-width: 71px; background-color: #e2e2e2;">
                    <col style="min-width: 90px; background-color: #e2e2e2;">
                    <col style="min-width: 300px; background-color: #e2e2e2;">
                    <col style="min-width: 150px; background-color: #e2e2e2;">
                    <col style="min-width: 150px; background-color: #e2e2e2;">
                    <col style="min-width: 310px; background-color: #e2e2e2;">
                    <thead>
                        <tr>
                            <th> Nom Prénom </th>
                            <th> Sexe </th>
                            <th> Né(e) le </th>
                            <th> Téléphone </th>
                            <th> Email </th>
                            <th> Resp. légal 1 </th>
                            <th> Resp. légal 2 </th>
                            <th> Adresse </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $queryListEleve = "SELECT DISTINCT Eleves.nom, Eleves.prenom, Eleves.sexe, Eleves.dateNaissance, Eleves.adresse, Eleves.telephone, Eleves.email, Eleves.respLegal1Id, Eleves.respLegal2Id FROM Classe INNER JOIN Eleves ON Classe.idClasse = Eleves.idClasse WHERE Classe.nom = " . "'" . $_POST['classe'] . "'" . ";";
                        $reqListEleve = mysqli_query($link,$queryListEleve);
                        while ($idListEleve = mysqli_fetch_array($reqListEleve)): ?>
                            <tr>
                                <td> <?= $idListEleve['nom'] . ' ' . $idListEleve['prenom'] ?> </td>
                                <td> <?= $idListEleve['sexe'] ?> </td>
                                <td> <?= date_format(date_create($idListEleve['dateNaissance']), 'd/m/o') ?> </td>
                                <td> <?= $idListEleve['telephone'] ?> </td>
                                <td> <?= $idListEleve['email'] ?> </td>
                                <td>
                                    <?php
                                    $reqListParent = mysqli_query($link,"SELECT DISTINCT idParent, nom, prenom FROM Parents;");
                                    while ($idListParent = mysqli_fetch_array($reqListParent)) {
                                        if ($idListParent['idParent'] == $idListEleve['respLegal1Id']) {
                                            echo $idListParent['nom'] . ' ' . $idListParent['prenom'];
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $reqListParent = mysqli_query($link,"SELECT DISTINCT idParent, nom, prenom FROM Parents;");
                                    while ($idListParent = mysqli_fetch_array($reqListParent)) {
                                        if ($idListParent['idParent'] == $idListEleve['respLegal2Id']) {
                                            echo $idListParent['nom'] . ' ' . $idListParent['prenom'];
                                        }
                                    }
                                    ?>
                                </td>
                                <td> <?= $idListEleve['adresse'] ?> </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bandeau-annonce">Sélectionnez une classe</div>
        <?php endif ?>
    </div>
<?php endif ?>

<?php
require 'elements/footer.php';
?>