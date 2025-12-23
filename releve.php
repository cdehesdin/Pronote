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
        'Eleve'  => 'Mon relevé de notes',
        'Parent' => 'Relevé de notes',
        'Professeur' => 'Saisie des appréciations',
        default  => 'Relevé de notes'
    };
}

checkAccess();
$title = getPageTitle();
?>


<?php ob_start(); ?>

<link rel="stylesheet" href="./src/css/releve.css">
<script src="./src/js/ajax_releve.js" defer></script>
<?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Professeur"): ?>
    <script src="./src/js/ajax_appreciation.js" defer></script>
<?php endif; ?>

<?php $style_script = ob_get_clean(); ?>


<?php ob_start(); ?>

<?php if (isset($_SESSION['login'])):?>
    <div class="choice-bar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <?php if ($_SESSION['role'] == 'Parent' || $_SESSION['role'] == 'Professeur'): ?>
                <?php if ($_SESSION['role'] == 'Professeur'): ?>
                    <div class="dropdown d-inline ms-2">
                        <form id="form-classe">
                            <select id="classeNom"
                                    name="classe"
                                    class="form-select form-select-sm">
                                <?php
                                $nbClasse = 0;
                                $queryListClasse = "SELECT DISTINCT nom FROM Classe INNER JOIN Enseignement ON Classe.idClasse = Enseignement.idClasse WHERE Enseignement.idProf = " . $_SESSION['id'] . ";";
                                $reqListClasse = mysqli_query($link,$queryListClasse);
                                while ($idListClasse = mysqli_fetch_array($reqListClasse)): ?>
                                    <option value="<?= $idListClasse['nom'] ?>"
                                        <?= (($_POST['classe'] ?? 0) == $nbClasse) ? 'selected' : '' ?>>
                                        <?= $idListClasse['nom'] ?>
                                    </option>
                                    <?php $nbClasse++; ?>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>
                <?php endif; ?>
                <div class="dropdown d-inline me-2">
                    <form id="form-eleve">
                        <?php if ($_SESSION['role'] == "Parent"): ?>
                            <?php
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

                            $enfants = ($_SESSION['role'] === 'Parent')
                                ? getEnfants($link, $_SESSION['idUtilisateur'])
                                : [];
                            ?>
                            <select id="classe"
                                    name="enfants"
                                    class="form-select form-select-sm">
                                <?php foreach ($enfants as $index => $enfant): ?>
                                    <option value="<?= $index ?>"
                                        <?= (($_POST['enfants'] ?? 0) == $index) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($enfant['prenom'].' '.$enfant['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="listEleve table-responsive" id="resultat"></div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/src/php/layout.php';
?>