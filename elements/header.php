<?php
$link = mysqli_connect('localhost','root','', 'NSINote');
mysqli_set_charset($link, "utf8");

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
require_once './elements/data.php';
?>

<!doctype html>
<html lang="fr" data-bs-theme="auto">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="NSI">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.2/css/all.css">
        <link href="elements/style.css" rel="stylesheet">
        <link rel="icon" href="./img/logo-pronote.png" />
        <title>
            <?php if (isset($title)) : ?>
                <?= $title . ' | Pronote' ?>
            <?php else : ?>
                <?= "Pronote" ?>
            <?php endif ?>
        </title>
    </head>
    <body style="background-image: url(./img/background.png)!important;">
        <header style="z-index: 10000;">
            <div class="d-flex justify-content-between align-items-center p-1 bg-white">
                <div class="ps-1"><img src="./img/logo-pronote.png" class="rounded" height="40px" alt="Logo de Pronote"></div>
                <div class="text-center name-school">
                    <a class="btn btn-logout name-school" data-bs-toggle="modal" data-bs-target="#contactEtablissement">
                        <i class="fa-regular fa-address-card pe-1"></i> Lycée général et technologique Descartes
                    </a>
                    <?php if (isset($_SESSION['login'])): ?> <br>
                        <span class="name-user">
                        <?php
                            if ($_SESSION['role'] == 'Eleve') {
                                $role = 'Élèves';
                            } elseif ($_SESSION['role'] == 'Parent') {
                                $role = 'Parents';
                            } elseif ($_SESSION['role'] == 'Professeur') {
                                $role = 'Professeurs';
                            }
                            echo 'Espace ' . $role . ' - ' . $_SESSION['nom'] . ' ' . $_SESSION['prenom'] . '&nbsp;&thinsp;';
                            ?>
                            <form method="post" action="" class="btn-logout d-inline-flex">
                                <button type="submit" class="btn btn-logout" name="logout"> <i class="fa-solid fa-right-from-bracket icon-logout"></i> </button>
                            </form>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="pe-1">
                    <a class="btn btn-logout" data-bs-toggle="modal" data-bs-target="#contactEtablissement">
                        <img src="./img/logo-etablissement.png" class="rounded" height="45px" alt="Logo de l'établissement">
                    </a>
                </div>
            </div>
            <?php if (isset($_SESSION['login'])): ?>
                <nav class="navbar navbar-expand-lg color-navbar" style="z-index: 1000;">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarText">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <?= nav_item('./','<i class="fa-solid fa-house-chimney"></i>') ?>
                                <?php if ($_SESSION['role'] == 'Eleve'): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="modal" data-bs-target="#monCompte" aria-current="page"> Mes données </a>
                                    </li>
                                <?php elseif ($_SESSION['role'] == 'Parent'): ?>
                                    <?= nav_item('./info-perso.php','Mes données') ?>
                                <?php elseif ($_SESSION['role'] == 'Professeur'): ?>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Mes données
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li class="nav-item">
                                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#monCompte" aria-current="page" href="#"> Mon compte </a>
                                            </li>
                                            <?php if ($_SESSION['role'] == 'Parent') {echo nav_subitem('./compte-enfant.php','Compte enfant');} ?>
                                            <?php if ($_SESSION['role'] == 'Professeur') {echo '<hr class="dropdown-divider">';} ?>
                                            <?php if ($_SESSION['role'] == 'Professeur') {echo nav_subitem('./classes-eleves.php','Classes/élèves');} ?>
                                        </ul>
                                    </li>
                                <?php endif ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Notes
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if ($_SESSION['role'] == 'Eleve') {echo nav_subitem('./notes.php','Mes notes');} ?>
                                        <?php if ($_SESSION['role'] == 'Parent') {echo nav_subitem('./notes.php','Les notes');} ?>
                                        <?php if ($_SESSION['role'] == 'Professeur') {echo nav_subitem('./saisie.php','Saisie');} ?>
                                        <?= nav_subitem('./releve.php','Relevé') ?>
                                        <?php if ($_SESSION['role'] == 'Professeur') {echo nav_subitem('./appreciation.php','Appréciations');} ?>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="navbar-nav">
                                <?= nav_item('./','<i class="fa-regular fa-paper-plane"></i>') ?>
                            </ul>
                        </div>
                    </div>
                </nav>
            <?php endif; ?>
        </header>
        <main>