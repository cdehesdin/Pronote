        </main>
        <?php mysqli_close($link); ?>
        <?php if (isset($_SESSION['login']) && !($_SESSION['role'] == 'Parent')): ?>
            <div class="modal fade" id="monCompte" tabindex="-1" aria-labelledby="monCompteLabel" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" style="z-index: 10000;">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                        <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-body-secondary p-1 border-0 btn-close-contact">
                            <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
                        </span>
                        <div class="modal-header"> <div class="en-tete-dialog"> Mon compte </div> </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-center mt-2 mb-4">
                                <?php if ($_SESSION['role'] == 'Eleve'): ?>
                                    <img src="./assets/img/espace-eleve.svg" class="rounded mx-auto d-block" height="110px" alt="Espace élève image">
                                <?php elseif ($_SESSION['role'] == 'Parent'): ?>
                                    <img src="./assets/img/espace-parent.svg" class="rounded mx-auto d-block" height="110px" alt="Espace parent image">
                                <?php elseif ($_SESSION['role'] == 'Professeur'): ?>
                                    <img src="./assets/img/espace-professeur.svg" class="rounded mx-auto d-block" height="110px" alt="Espace professeur image">
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
                                        <?= substr($_SESSION['dateNaissance'], 8, 2) . '/' . substr($_SESSION['dateNaissance'], 5, 2) . '/' .  substr($_SESSION['dateNaissance'], 0, 4) ?>
                                    </div>
                                </div>
                                <div class="d-flex <?php if (isset($_SESSION['telephone'])): ?> mb-2 <?php endif ?>">
                                    <div class="modal-logo"> <i class="fa-solid fa-location-dot"></i> </div>
                                    <div class="modal-description"> <?= preg_replace('(,)', ', <br>', $_SESSION['adresse']) ?> </div>
                                </div>
                                <?php if (isset($_SESSION['telephone'])): ?>
                                    <div class="d-flex <?php if (isset($_SESSION['email'])): ?> mb-2 <?php endif ?>">
                                        <div class="modal-logo"> <i class="fa-solid fa-mobile-screen-button"></i> </div>
                                        <div class="modal-description"> <?= $_SESSION['telephone'] ?> </div>
                                    </div>
                                <?php endif ?>
                                <?php if (isset($_SESSION['email'])): ?>
                                    <div class="d-flex">
                                        <div class="modal-logo"> <i class="fa-solid fa-at"></i> </div>
                                            <div class="modal-description"> <?= $_SESSION['email'] ?> </div>
                                        </div>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script>
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
</script>
</html>