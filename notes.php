<?php
session_start();
require_once __DIR__ . '/src/php/config.php';

/* =========================
   Sécurité
   ========================= */
function checkAccess(): void {
    if (!isset($_SESSION['login'])) {
        header('Location: ./index.php');
        exit;
    }
}

/* =========================
   Titre de page
   ========================= */
function getPageTitle(): string {
    return match ($_SESSION['role'] ?? '') {
        'Eleve'  => 'Mes notes',
        'Parent' => 'Les notes',
        'Professeur' => 'Saisie des notes',
        default  => 'Notes'
    };
}

checkAccess();
$title = getPageTitle();
?>


<?php ob_start(); ?>

<?php if (isset($_SESSION['role']) && ($_SESSION['role'] == "Eleve" || $_SESSION['role'] == "Parent")): ?>
    <link rel="stylesheet" href="./src/css/notes.css">
    <script src="./src/js/ajax_notes.js" defer></script>
<?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == "Professeur"): ?>
    <link rel="stylesheet" href="./src/css/saisie.css">
    <script src="./src/js/ajax_saisie.js" defer></script>
    <script src="./src/js/ajax_updateNotes.js" defer></script>
<?php endif; ?>

<?php $style_script = ob_get_clean(); ?>



<?php ob_start(); ?>

<?php if (isset($_SESSION['role']) && ($_SESSION['role'] == "Eleve" || $_SESSION['role'] == "Parent")): ?>
    <?php
    /* =========================
    Élève courant
    ========================= */
    function getEleveCourant(): ?array {
        if ($_SESSION['role'] === 'Eleve') {
            return [
                'id'     => $_SESSION['id'],
                'classe' => $_SESSION['classe']
            ];
        }

        if ($_SESSION['role'] === 'Parent') {
            $index = $_GET['enfants'] ?? 0;

            if (isset($_SESSION['idEleve'][$index], $_SESSION['classe'][$index])) {
                return [
                    'id'     => $_SESSION['idEleve'][$index],
                    'classe' => $_SESSION['classe'][$index]
                ];
            }
        }

        return null;
    }

    /* =========================
    Enfants du parent
    ========================= */
    function getEnfants(mysqli $link, int $idUtilisateur): array {
        $stmt = $link->prepare(
            "SELECT E.idEleve, E.idClasse, E.nom, E.prenom
            FROM Parents P
            JOIN Eleves E 
            ON P.idParent = E.respLegal1Id 
            OR P.idParent = E.respLegal2Id
            WHERE P.idUtilisateur = ?"
        );
        $stmt->bind_param("i", $idUtilisateur);
        $stmt->execute();
        $res = $stmt->get_result();

        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /* =========================
    Initialisation
    ========================= */
    $eleve = getEleveCourant();

    $IdEleve     = $eleve['id']     ?? null;
    $ClasseEleve = $eleve['classe'] ?? null;

    $enfants = ($_SESSION['role'] === 'Parent')
        ? getEnfants($link, $_SESSION['idUtilisateur'])
        : [];
    ?>
    <?php if ($IdEleve && $ClasseEleve): ?>
        <div class="detail-notes">
            <div class="column-notes" id="liste-notes"></div>
            <div class="column-note">
                <div class="d-flex justify-content-between">
                    <form id="form-tri-notes">
                        <input type="radio"
                            class="btn-check"
                            name="ordres"
                            id="chronologique"
                            value="chronologique"
                            checked>
                        <label class="btn btn-sm" for="chronologique">
                            Par ordre chronologique
                        </label>

                        <input type="radio"
                            class="btn-check"
                            name="ordres"
                            id="matiere"
                            value="matiere">
                        <label class="btn btn-sm" for="matiere">
                            Par matière
                        </label>
                    </form>
                    <?php if ($_SESSION['role'] === 'Parent'): ?>
                        <form id="form-tri-eleve">
                            <select id="select-enfant"
                                    name="enfants"
                                    class="form-select form-select-sm">
                                <?php foreach ($enfants as $index => $enfant): ?>
                                    <option value="<?= $index ?>"
                                        <?= (($_GET['enfants'] ?? 0) == $index) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($enfant['prenom'].' '.$enfant['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    <?php endif; ?>
                </div>
                <hr>
                <div id="resultat">Sélectionnez un devoir</div>
            </div>
        </div>
    <?php endif; ?>
<?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == "Professeur"): ?>
    <div class="choice-bar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center mb-3">
            <div class="choice-title"> Choisir une classe : </div>
            <div class="dropdown d-inline ms-2">
                <form id="classeForm">
                    <select class="form-select form-select-sm" name="classe" id="classe" onchange="submitClasse()">
                        <?php
                        // Requête pour obtenir la liste des classes
                        $queryListClasse = "SELECT DISTINCT nom FROM Classe INNER JOIN Enseignement ON Classe.idClasse = Enseignement.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . ";";
                        $reqListClasse = mysqli_query($link, $queryListClasse);
                        $classes = []; // Un tableau pour stocker les classes

                        // Récupération des classes
                        while ($idListClasse = mysqli_fetch_array($reqListClasse)) {
                            $classes[] = $idListClasse['nom']; // Ajouter chaque classe à l'array
                        }

                        // Affichage des options de classes avec la première classe sélectionnée par défaut si aucune n'est définie
                        foreach ($classes as $index => $classe) {
                            echo '<option ';
                            // Si la classe est sélectionnée (basée sur $_GET) ou si c'est la première classe et aucune classe n'est sélectionnée
                            if ((isset($_GET['classe']) && $_GET['classe'] == $classe) || (!isset($_GET['classe']) && $index == 0)) {
                                echo 'selected';
                            }
                            echo ' value="' . $classe . '">' . $classe . '</option>';
                        }
                        ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="listEleve table-responsive" id="classeInfo">
        <div class="bandeau-annonce">Sélectionnez une classe</div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/src/php/layout.php';
?>