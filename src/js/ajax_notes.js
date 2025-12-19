document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.form-check-input');

    // Lorsqu'une case à cocher est changée (coche ou décoche)
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(event) {
            if (checkbox.checked) {
                // Trouver l'élément parent <li> pour appliquer la classe 'notes-active'
                const listItem = checkbox.closest('li');
                listItem.classList.add('notes-active');  // Ajoute la classe active au <li>

                // Récupérer l'ID du contrôle
                const controleId = checkbox.value;

                // Soumettre le formulaire via AJAX
                const form = checkbox.closest('form'); // Trouve le formulaire parent
                const formData = new FormData(form);

                // Effectuer la requête AJAX pour envoyer les données sans recharger la page
                fetch('./src/php/ajax_notes.php', {
                    method: 'POST',
                    body: formData, // Envoi des données du formulaire
                })
                .then(response => response.text())  // Récupérer la réponse en texte
                .then(data => {
                    // Traiter la réponse (par exemple, mettre à jour une div avec l'ID 'resultat')
                    const resultDiv = document.getElementById('resultat');
                    if (resultDiv) {
                        resultDiv.innerHTML = data; // Mettre à jour le contenu de la div
                    }
                })
                .catch(error => {
                    const resultDiv = document.getElementById('resultat');
                    if (resultDiv) {
                        resultDiv.innerHTML = 'Une erreur est survenue.';
                    }
                });

                // Ne pas désactiver la checkbox, juste la décocher après la requête
            }
        });
    });

    // Ajouter un écouteur d'événement global pour détecter un clic ailleurs
    document.addEventListener('click', function(event) {
        // Si le clic est en dehors des checkboxes et de leurs parents
        checkboxes.forEach(checkbox => {
            const listItem = checkbox.closest('li');
            if (!listItem.contains(event.target) && checkbox.checked) {
                // Si la case est cochée et que l'utilisateur clique ailleurs
                checkbox.checked = false;  // Décoche la case
                listItem.classList.remove('notes-active');  // Retire la classe active
            }
        });
    });
});
