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

if (isset($_SESSION['role']) && ($_SESSION['role'] == "Eleve" || $_SESSION['role'] == "Parent")) {

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
}

checkAccess();
$title = getPageTitle();
?>


<?php ob_start(); ?>

<?php if (isset($_SESSION['role']) && ($_SESSION['role'] == "Eleve" || $_SESSION['role'] == "Parent")): ?>
    <link rel="stylesheet" href="./src/css/notes.css">
    <script src="./src/js/ajax_notes.js" defer></script>
<?php endif; ?>

<?php $style_script = ob_get_clean(); ?>



<?php ob_start(); ?>

<?php if (isset($_SESSION['role']) && ($_SESSION['role'] == "Eleve" || $_SESSION['role'] == "Parent")): ?>
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
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/src/php/layout.php';
?>