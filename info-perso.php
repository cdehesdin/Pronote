<?php
session_start();

// Définition du titre de la page
if (!isset($_SESSION['login'])) {
    $title = "Connexion";
} else {
    $title = "Mes données";
}

// Si utilisateur non connecté, renvoie vers la page de connexion
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
} elseif (!($_SESSION['role'] == 'Parent')) {
    header('Location: index.php');
}

require 'elements/header.php';
?>

<?php if ($_SESSION['role'] == 'Parent'): ?>
    <div class="choice-bar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="choice-title"> Informations liées à mon compte </div>
        </div>
        <div class="choice-title float-end"> <i class="fa-solid fa-print"></i> </div>
    </div>
        <div class="listEleve table-responsive row align-items-start m-2">
            <div class="col-sm-4 pt-2 pb-2" style="width: 300px; box-shadow: 0.6rem 0.05rem 0.6rem -0.5rem rgba(0, 0, 0, 0.1607843137); height: 100%;">
                <div class="nom-col-compte"> Mes informations </div>
                <div class="d-flex justify-content-center m-3">
                    <?php if ($_SESSION['role'] == 'Eleve'): ?>
                        <img src="./img/espace-eleve.svg" class="rounded mx-auto d-block" height="110px" alt="Espace élève image">
                    <?php elseif ($_SESSION['role'] == 'Parent'): ?>
                        <img src="./img/espace-parent.svg" class="rounded mx-auto d-block" height="110px" alt="Espace parent image">
                    <?php elseif ($_SESSION['role'] == 'Professeur'): ?>
                        <img src="./img/espace-professeur.svg" class="rounded mx-auto d-block" height="110px" alt="Espace professeur image">
                    <?php endif ?>
                </div>
                <div>
                    <div class="d-flex mb-2">
                        <div class="modal-logo"> <i class="fa-solid fa-id-badge"></i> </div>
                        <div class="modal-description"> <?= $_SESSION['nom'] . ' ' . $_SESSION['prenom'] ?> </div>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="modal-logo"> <i class="fa-solid fa-venus-mars"></i> </div>
                        <div class="modal-description">
                            <?php
                            if ($_SESSION['sexe'] == 'G') {
                                echo 'Garçon';
                            } elseif ($_SESSION['sexe'] == 'F' && ($_SESSION['role'] == 'Eleve')) {
                                echo 'Fille';
                            } elseif ($_SESSION['sexe'] == 'M') {
                                echo 'Masculin';
                            } elseif ($_SESSION['sexe'] == 'F' && (($_SESSION['role'] == 'Parent') || ($_SESSION['role'] == 'Professeur'))) {
                                echo 'Féminin';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="modal-logo"> <i class="fa-solid fa-cake-candles"></i> </div>
                        <div class="modal-description">
                            <?= date_format(date_create($_SESSION['dateNaissance']), 'd/m/o') ?>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="modal-logo"> <i class="fa-solid fa-location-dot"></i> </div>
                        <div class="modal-description"> <?= preg_replace('(,)', ', <br>', $_SESSION['adresse']) ?> </div>
                    </div>
                    <?php if (isset($_SESSION['telephone'])): ?>
                        <div class="d-flex mb-2">
                            <div class="modal-logo"> <i class="fa-solid fa-mobile-screen-button"></i> </div>
                            <div class="modal-description"> <?= $_SESSION['telephone'] ?> </div>
                        </div>
                    <?php endif ?>
                    <?php if (isset($_SESSION['email'])): ?>
                        <div class="d-flex mb-2">
                        <div class="modal-logo"> <i class="fa-solid fa-at"></i> </div>
                            <div class="modal-description"> <?= $_SESSION['email'] ?> </div>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <div class="col-sm-8 ps-4 pt-2 pb-2">
                <div class="nom-col-compte"> Information de mes enfants </div>
                <div class="row align-items-start">
                    <?php
                    $queryListEnfant = "SELECT DISTINCT Eleves.nom, Eleves.prenom, Eleves.sexe, Eleves.dateNaissance, Eleves.adresse, Eleves.telephone, Eleves.email, Eleves.respLegal1Id, Eleves.respLegal2Id FROM Eleves INNER JOIN Parents ON Eleves.respLegal1Id = Parents.idParent OR Eleves.respLegal2Id = Parents.idParent WHERE Parents.idParent = " . "'" . $_SESSION['id'] . "'" . ";";
                    $reqListEnfant = mysqli_query($link,$queryListEnfant);
                    $nombreEnfant = 0;
                    while ($idListEnfant = mysqli_fetch_array($reqListEnfant)): ?>
                        <?php $nombreEnfant ++; ?>
                        <div class="col-sm-4 pt-2 pb-2" style="width: 300px;">
                            <div class="card" style="width: 18rem;">
                                <div class="card-header"> Enfant <?= $nombreEnfant ?> </div>
                                <div class="card-body">
                                    <div class="d-flex mb-2">
                                        <div class="modal-logo"> <i class="fa-solid fa-id-badge"></i> </div>
                                        <div class="modal-description"> <?= $idListEnfant['nom'] . ' ' . $idListEnfant['prenom'] ?> </div>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <div class="modal-logo"> <i class="fa-solid fa-cake-candles"></i> </div>
                                        <div class="modal-description">
                                            <?= date_format(date_create($idListEnfant['dateNaissance']), 'd/m/o') ?>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <div class="modal-logo"> <i class="fa-solid fa-location-dot"></i> </div>
                                        <div class="modal-description"> <?= preg_replace('(,)', ', <br>', $idListEnfant['adresse']) ?> </div>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <div class="modal-logo"> <i class="fa-solid fa-mobile-screen-button"></i> </div>
                                        <?php if (isset($idListEnfant['telephone'])): ?>
                                            <div class="modal-description"> <?= $idListEnfant['telephone'] ?> </div>
                                        <?php else: ?>
                                            <div class="modal-description"> Pas d'information </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <div class="modal-logo"> <i class="fa-solid fa-at"></i> </div>
                                        <?php if (isset($idListEnfant['email'])): ?>
                                            <div class="modal-description"> <?= $idListEnfant['email'] ?> </div>
                                        <?php else: ?>
                                            <div class="modal-description"> Pas d'information </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
<?php endif ?>

<?php
require 'elements/footer.php';
?>