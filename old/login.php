<?php
session_start();

// Définition du titre de la page
if (!isset($_SESSION['login'])) {
    $title = "Connexion";
} else {
    $title = "Accueil";
}

require 'assets/header.php';

if (!isset($_SESSION['login']) && isset($_POST['role'])) {
    $query = "SELECT DISTINCT * FROM Utilisateur";
    $requete = mysqli_query($link,$query);
    while ($identification = mysqli_fetch_array($requete)) {
        if ($identification['role'] == $_POST['role'] && $identification['login'] == $_POST['login'] && $identification['mdp'] == $_POST['mdp']) {
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['mdp'] = $_POST['mdp'];
            $_SESSION['role'] = $_POST['role'];
            $_SESSION['idUtilisateur'] = $identification['idUtilisateur'];
            if ($_SESSION['role'] == 'Eleve') {   // Si l'utilisateur est un élève : les données de SESSION sont ajoutés
                $queryEleve = "SELECT DISTINCT * FROM Utilisateur INNER JOIN Eleves ON Utilisateur.idUtilisateur = Eleves.idUtilisateur WHERE Utilisateur.idUtilisateur=" . $_SESSION['idUtilisateur'] . ';';
                $requeteEleve = mysqli_query($link,$queryEleve);
                while ($identification = mysqli_fetch_array($requeteEleve)) {
                    $_SESSION['id'] = $identification['idEleve'];
                    $_SESSION['nom'] = $identification['nom'];
                    $_SESSION['prenom'] = $identification['prenom'];
                    $_SESSION['sexe'] = $identification['sexe'];
                    $_SESSION['dateNaissance'] = $identification['dateNaissance'];
                    $_SESSION['adresse'] = $identification['adresse'];
                    $_SESSION['telephone'] = $identification['telephone'];
                    $_SESSION['mail'] = $identification['mail'];
                    $_SESSION['classe'] = $identification['idClasse'];
                }
            } elseif ($_SESSION['role'] == 'Parent') {   // Si l'utilisateur est un parent : les données de SESSION sont ajoutés
                $queryParent = "SELECT DISTINCT Parents.idParent, Parents.nom, Parents.prenom, Parents.sexe, Parents.dateNaissance, Parents.adresse, Parents.telephone, Parents.email, Eleves.idUtilisateur AS idEl, Eleves.idClasse, Eleves.nom AS nomEl, Eleves.prenom AS prenomEl FROM Utilisateur INNER JOIN Parents ON Utilisateur.idUtilisateur = Parents.idUtilisateur INNER JOIN Eleves ON Parents.idParent = Eleves.respLegal1Id OR Parents.idParent = Eleves.respLegal2Id WHERE Parents.idUtilisateur=" . $_SESSION['idUtilisateur'] . ';';
                $requeteParent = mysqli_query($link,$queryParent);
                while ($identification = mysqli_fetch_array($requeteParent)) {
                    $_SESSION['id'] = $identification['idParent'];
                    $_SESSION['idEleve'][] = $identification['idEl'];
                    $_SESSION['nom'] = $identification['nom'];
                    $_SESSION['prenom'] = $identification['prenom'];
                    $_SESSION['sexe'] = $identification['sexe'];
                    $_SESSION['dateNaissance'] = $identification['dateNaissance'];
                    $_SESSION['adresse'] = $identification['adresse'];
                    $_SESSION['telephone'] = $identification['telephone'];
                    $_SESSION['mail'] = $identification['mail'];
                    $_SESSION['classe'][] = $identification['idClasse'];
                    $_SESSION['nomEleve'][] = $identification['nomEl'];
                    $_SESSION['prenomEleve'][] = $identification['prenomEl'];
                }
            } else {   // Si l'utilisateur est un professeur : les données de SESSION sont ajoutés
                $queryProf = "SELECT DISTINCT * FROM Utilisateur INNER JOIN Profs ON Utilisateur.idUtilisateur = Profs.idUtilisateur WHERE Utilisateur.idUtilisateur=" . $_SESSION['idUtilisateur'] . ';';
                $requeteProf = mysqli_query($link,$queryProf);
                while ($identification = mysqli_fetch_array($requeteProf)) {
                    $_SESSION['id'] = $identification['idProf'];
                    $_SESSION['nom'] = $identification['nom'];
                    $_SESSION['prenom'] = $identification['prenom'];
                    $_SESSION['sexe'] = $identification['sexe'];
                    $_SESSION['dateNaissance'] = $identification['dateNaissance'];
                    $_SESSION['adresse'] = $identification['adresse'];
                    $_SESSION['telephone'] = $identification['telephone'];
                    $_SESSION['mail'] = $identification['mail'];
                }
            }
            header('Location: index.php');
            exit;
        }
    }
    // Message d'erreur si les informations entrer sont incorrectes
    if (isset($_POST['login']) && !isset($_SESSION['login'])) {
        $error = 'Une des informations saisies est incorrecte !';
    }
}
?>

<?php if (!isset($_SESSION['login'])): ?>
    <div class="container">
        <div class="card w-50 p-3 position-absolute top-50 start-50 translate-middle">
            <div class="card-body connexion">
                <form method="post" action="">
                    <!-- Message d'erreur si les informations entrer sont incorrectes -->
                    <?php if (isset($error)):?>
                        <div class="alert alert-danger mt-0 mb-3" role="alert"> <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button> <?= $error?> </div>
                    <?php endif;?>
                    <!-- Image en fonction du type d'utilisateur choisie -->
                    <div class="tab-content space mb-3" id="login-img-tabContent">
                        <div class="tab-pane fade show active" id="login-img-1" role="tabpanel" aria-labelledby="login-img-1-tab" tabindex="0">
                            <img src="assets/img/espace-eleve.svg" class="rounded mx-auto d-block" height="110px" alt="Espace élève image">
                        </div>
                        <div class="tab-pane fade" id="login-img-2" role="tabpanel" aria-labelledby="login-img-2-tab" tabindex="1">
                            <img src="assets/img/espace-parent.svg" class="rounded mx-auto d-block" height="110px" alt="Espace parent image">
                        </div>
                        <div class="tab-pane fade" id="login-img-3" role="tabpanel" aria-labelledby="login-img-3-tab" tabindex="2">
                            <img src="assets/img/espace-professeur.svg" class="rounded mx-auto d-block" height="110px" alt="Espace professeur image">
                        </div>
                    </div>
                    <!-- Choix du type de l'utilisateur -->
                    <div class="d-flex justify-content-center form-floating mb-3" id="login-img-tab" role="tablist">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input active" type="radio" name="role" id="eleve" value="Eleve" checked required id="login-img-1-tab" data-bs-toggle="pill" data-bs-target="#login-img-1" role="tab" aria-controls="login-img-1" aria-selected="true">
                            <label class="form-check-label" for="eleve">Élève</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" id="parent" value="Parent" required id="login-img-2-tab" data-bs-toggle="pill" data-bs-target="#login-img-2" role="tab" aria-controls="login-img-2" aria-selected="true">
                            <label class="form-check-label" for="parent">Parent</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" id="prof" value="Professeur" required id="login-img-3-tab" data-bs-toggle="pill" data-bs-target="#login-img-3" role="tab" aria-controls="login-img-3" aria-selected="true">
                            <label class="form-check-label" for="prof">Professeur</label>
                        </div>
                    </div>
                    <!-- Saisie de l'identifiant -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" placeholder="Identifiant" name="login" id="login" required>
                        <label for="login"> Identifiant </label>
                    </div>
                    <!-- Saisie du mot de passe -->
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" placeholder="Mot de passe" name="mdp" id="mdp" required>
                        <label for="mdp"> Mot de passe </label>
                    </div>
                    <!-- Bouton de vérification des informations saisies -->
                    <button type="submit" class="btn btn-outline-primary button-login">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php header('Location: index.php'); ?>
<?php endif ?>

<?php
require 'assets/footer.php';
?>