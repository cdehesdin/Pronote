<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if (isset($title) && $title != "Accueil") echo $title . " •"; ?> Pronote</title>
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
            <div class="d-flex">
                <img src="./src/img/logo.jpeg" alt="Logo">
                <div>
                    <div>Pronote</div>
                    <div class="copyright">&copy; Clément DEHESDIN. 2025-26. Licence MIT.</div>
                </div>
            </div>
            <div>
                <?php if (!isset($_SESSION['login'])): ?>
                    <a class="btn btn-primary" href="./index.php">Connexion</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="./src/php/logout.php">Déconnexion</a>
                <?php endif; ?>
                <button class="btn btn-secondary d-md-none ms-3" data-bs-toggle="offcanvas" data-bs-target="#x-navbar-items" aria-controls="x-navbar-items">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Navbar -->
        <nav class="d-flex flex-md-row justify-content-between align-items-center">
            <div class="offcanvas-md offcanvas-start" tabindex="-1" id="x-navbar-items" aria-labelledby="x-navbar-items-label">
                <div class="offcanvas-body" data-bs-theme="dark">
                    <button type="button" class="btn-close d-md-none" data-bs-dismiss="offcanvas" data-bs-target="#x-navbar-items"  aria-label="Close"></button>
                    <div class="x-link">
                        <!-- Page -->
                        <div class="<?php if (isset($title) && $title == "Accueil") echo "x-active"; ?>">
                            <a href="."><i class="fa-solid fa-house me-2"></i>Accueil</a>
                        </div>
                        <div class="<?php if (isset($title) && $title == "Mes notes") echo "x-active"; ?>">
                            <a href="./notes.php"><i class="fa-solid fa-graduation-cap me-2"></i>Mes notes</a>
                        </div>
                        <div class="<?php if (isset($title) && $title == "Relevé de notes") echo "x-active"; ?>">
                            <a href="./releve.php"><i class="fa-solid fa-user-graduate me-2"></i>Relevé de notes</a>
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
</body>

</html>