<?php
session_start();
require_once __DIR__ . '/src/php/config.php';

$title = !isset($_SESSION['login']) ? "Connexion" : "Accueil";
$error = null;

if (!isset($_SESSION['login']) && isset($_POST['login'], $_POST['mdp'])) {

    $login = trim($_POST['login']);
    $mdp   = $_POST['mdp'];

    $stmt = $link->prepare(
        "SELECT idUtilisateur, login, mdp, role 
         FROM Utilisateur 
         WHERE login = ?"
    );
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        if (password_verify($mdp, $user['mdp'])) {

            session_regenerate_id(true);

            $_SESSION['idUtilisateur'] = $user['idUtilisateur'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {

                case 'Eleve':
                    $stmt = $link->prepare(
                        "SELECT * FROM Eleves WHERE idUtilisateur = ?"
                    );
                    $stmt->bind_param("i", $_SESSION['idUtilisateur']);
                    $stmt->execute();
                    $eleve = $stmt->get_result()->fetch_assoc();

                    if ($eleve) {
                        $_SESSION['id'] = $eleve['idEleve'];
                        $_SESSION['nom'] = $eleve['nom'];
                        $_SESSION['prenom'] = $eleve['prenom'];
                        $_SESSION['classe'] = $eleve['idClasse'];
                    }
                    break;

                case 'Parent':
                    $stmt = $link->prepare(
                        "SELECT P.*, E.idEleve, E.idClasse, E.nom AS nomEleve, E.prenom AS prenomEleve
                         FROM Parents P
                         LEFT JOIN Eleves E 
                           ON P.idParent = E.respLegal1Id 
                           OR P.idParent = E.respLegal2Id
                         WHERE P.idUtilisateur = ?"
                    );
                    $stmt->bind_param("i", $_SESSION['idUtilisateur']);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    $_SESSION['idEleve'] = [];
                    $_SESSION['classe'] = [];

                    while ($row = $res->fetch_assoc()) {
                        $_SESSION['id'] = $row['idParent'];
                        $_SESSION['nom'] = $row['nom'];
                        $_SESSION['prenom'] = $row['prenom'];
                        $_SESSION['idEleve'][] = $row['idEleve'];
                        $_SESSION['classe'][] = $row['idClasse'];
                    }
                    break;

                case 'Professeur':
                    $stmt = $link->prepare(
                        "SELECT * FROM Profs WHERE idUtilisateur = ?"
                    );
                    $stmt->bind_param("i", $_SESSION['idUtilisateur']);
                    $stmt->execute();
                    $prof = $stmt->get_result()->fetch_assoc();

                    if ($prof) {
                        $_SESSION['id'] = $prof['idProf'];
                        $_SESSION['nom'] = $prof['nom'];
                        $_SESSION['prenom'] = $prof['prenom'];
                    }
                    break;
            }
        } else {
            $error = "Identifiant ou mot de passe incorrect";
        }

        header('Location: ./index.php');
        exit;
    } else {
        $error = "Identifiant ou mot de passe incorrect";
    }
}
?>

<?php ob_start(); ?>

<?php if (!isset($_SESSION['login'])): ?>
    <script src="./src/js/password.js" defer></script>
<?php endif; ?>

<?php $style_script = ob_get_clean(); ?>

<?php ob_start(); ?>

<?php if (!isset($_SESSION['login'])): ?>
    <div class="container">
        <div class="card w-50 p-3 position-absolute top-50 start-50 translate-middle">
            <div class="card-body connexion">

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post">

                    <div class="form-floating mb-3">
                        <input class="form-control" name="login" required>
                        <label>Identifiant</label>
                    </div>

                    <div id="x-password" class="form-floating mb-3">
                        <input type="password" class="form-control" name="mdp" id="x-mdp" required>
                        <label>Mot de passe </label>
                        <div class="x-view-password" onclick="viewPassword(event, '#x-mdp')"><i class="fa-solid fa-eye"></i></div>
                    </div>
                    
                    <button class="btn btn-primary w-100">Connexion</button>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <h2>Bienvenue <?= htmlspecialchars($_SESSION['login']) ?></h2>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/src/php/layout.php';
?>