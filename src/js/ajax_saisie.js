document.addEventListener('DOMContentLoaded', function () {
    // On appelle submitClasse au chargement de la page pour la première fois
    submitClasse();
});

function submitClasse() {
    var classe = document.getElementById('classe').value;

    // Si une classe est sélectionnée
    if (classe) {
        fetch('./src/php/ajax_saisie.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({ classe: classe })
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('classeInfo').innerHTML = data;
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
        });
    }
}

function updateNote(idEleve, idControle, note) {
    var classe = document.getElementById('classe').value;

    // Envoi des données via AJAX
    fetch('./src/php/ajax_addNotes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            action: 'updateNote',
            idEleve: idEleve,
            idControle: idControle,
            note: note,
            classe: classe,
        })
    })
    .then(response => response.text())
    .then(data => {
        submitClasse();
    })
    .catch(error => {
        console.error('Erreur lors de la mise à jour de la note:', error);
    });
}

