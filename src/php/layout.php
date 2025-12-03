<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if (isset($title) && $title != "Accueil") echo $title . " •"; ?> ETEAM</title>
    <link rel="icon" href="./src/img/logo.jpeg">

    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" defer crossorigin="anonymous"></script>

    <!-- Style and script created -->
    <link rel="stylesheet" href="./src/css/style.css">
    <?php if (isset($style_script)) echo $style_script; ?>
</head>
<body>
    <!-------------------------------------->
    <!--              HEADER             --->
    <!-------------------------------------->
    <header>
        <div>
            <div><img src="./src/img/logo.jpeg" alt="Logo">Pronote</div>
            <div>
                <button class="btn btn-primary">Se connecter</button>
                <button class="btn btn-secondary d-md-none ms-3" data-bs-toggle="offcanvas" data-bs-target="#x-navbar-items" aria-controls="x-navbar-items">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Navbar -->
        <nav class="d-flex flex-lg-row justify-content-between align-items-center">
            <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="x-navbar-items" aria-labelledby="x-navbar-items-label">
                <div class="offcanvas-body" data-bs-theme="dark">
                    <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#x-navbar-items"  aria-label="Close"></button>
                    <div class="x-link">
                        <!-- Page -->
                        <div class="<?php if (isset($title) && $title == "Accueil") echo "x-active"; ?>">
                            <a href="."><i class="fa-solid fa-house me-2"></i>Accueil</a>
                        </div>
                        <div class="<?php if (isset($title) && $title == "Notes") echo "x-active"; ?>">
                            <a href="./tournaments.php"><i class="fa-solid fa-graduation-cap me-2"></i>Mes notes</a>
                        </div>
                        <div class="<?php if (isset($title) && $title == "Relevé de notes") echo "x-active"; ?>">
                            <a href="./conseils.php"><i class="fa-solid fa-user-graduate me-2"></i>Relevé de notes</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-------------------------------------->
    <!--              CONTENT            --->
    <!-------------------------------------->
    <main <?php if (isset($title) && ($title == "Accueil")) {echo 'style="z-index: 2;"';} ?> >
        <?= $content ?>
    </main>

    <!-------------------------------------->
    <!--              FOOTER             --->
    <!-------------------------------------->
    <footer>
        <div class="d-flex align-items-center justify-content-md-between flex-md-row flex-column">
            <!-- Copyright -->
            <div class="text-center">© 2025-<?= date("y") ?> • Tournois Français des Jeunes Mathématiciennes et Mathématiciens</div>
            <!-- Socials networks -->
            <div class="mt-1 mt-md-0">
                <a href="#" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" target="_blank"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fa-solid fa-envelope"></i></a>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-2 mt-md-1">
            <a data-bs-toggle="modal" data-bs-target="#x-tab-legal-notices">Mentions légales</a>
            &nbsp; • &nbsp;
            <a data-bs-toggle="modal" data-bs-target="#x-tab-legal-notices">Politique de confidentialité</a>
            &nbsp; • &nbsp;
            <a data-bs-toggle="modal" data-bs-target="#x-tab-legal-notices">À propos</a>
            &nbsp; • &nbsp;
            <a href="" target="_blank">Documentation</a>
        </div>
    </footer>

    <!-------------------------------------->
    <!--        MODAL LEGAL NOTICES      --->
    <!-------------------------------------->
    <div class="modal fade" id="x-tab-legal-notices" tabindex="-1" aria-labelledby="x-tab-legal-notices-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="active" id="x-legal-notices-tab" data-bs-toggle="pill" data-bs-target="#x-legal-notices" role="tab" aria-controls="x-legal-notices" aria-selected="true">Mentions légales</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a id="x-cgu-tab" data-bs-toggle="pill" data-bs-target="#x-cgu" role="tab" aria-controls="x-cgu" aria-selected="false">Politique de confidentialité</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a id="x-about-tab" data-bs-toggle="pill" data-bs-target="#x-about" role="tab" aria-controls="x-about" aria-selected="false">À propos</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="x-legal-notices" role="tabpanel" aria-labelledby="x-legal-notices-tab" tabindex="0">
                        <h3 class="mb-3">Mentions légales</h3>
                        ...
                    </div>
                    <div class="tab-pane fade" id="x-cgu" role="tabpanel" aria-labelledby="x-cgu-tab" tabindex="0">
                        <h3>Politique de confidentialité</h3>
                        ...
                    </div>
                    <div class="tab-pane fade" id="x-about" role="tabpanel" aria-labelledby="x-about-tab" tabindex="0">
                        <h3>À propos</h3>
                        ...
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>