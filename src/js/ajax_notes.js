document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-tri-notes');
    const form_eleve = document.getElementById('form-tri-eleve');
    const selectEnfant = document.getElementById('select-enfant');
    const listeNotes = document.getElementById('liste-notes');

    const envoyerForm = (formData) => {
        fetch('./src/php/ajax_lstNotes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Erreur réseau');
            return response.text();
        })
        .then(html => {
            listeNotes.innerHTML = html;
        })
        .catch(() => {
            listeNotes.innerHTML = '<li>Erreur de chargement</li>';
        });
    };

    // Quand on change d'élève
    if (selectEnfant && form_eleve) {
        selectEnfant.addEventListener('change', () => {
            document.getElementById('chronologique').checked = true;

            const formData = new FormData(form_eleve);
            formData.append('ordres', 'chronologique');
            formData.append('enfants', selectEnfant.value);

            envoyerForm(formData);

            document.getElementById('resultat').innerHTML = "Sélectionnez un devoir";
        });
    }

    // Quand on change l'ordre (chronologique / matière)
    form.addEventListener('change', () => {
        const formData = new FormData(form);

        // Si tu as aussi besoin de l'élève sélectionné
        if (selectEnfant && selectEnfant.value) {
            formData.append('enfants', selectEnfant.value);
        }

        envoyerForm(formData);

        document.getElementById('resultat').innerHTML = "Sélectionnez un devoir";
    });

    // Chargement initial
    const formData = new FormData(form);
    envoyerForm(formData);
});

document.addEventListener('DOMContentLoaded', () => {

    const listeNotes = document.getElementById('liste-notes');
    const resultDiv  = document.getElementById('resultat');

    if (!listeNotes || !resultDiv) return;

    // Délégation d'événement
    listeNotes.addEventListener('change', (event) => {

        const input = event.target;

        if (!input.classList.contains('form-check-input')) return;
        if (!input.checked) return;

        // Nettoyage des classes actives
        listeNotes.querySelectorAll('li.notes-active')
            .forEach(li => li.classList.remove('notes-active'));

        const li = input.closest('li');
        if (li) li.classList.add('notes-active');

        const formData = new FormData();
        formData.append(input.name, input.value);

        fetch('./src/php/ajax_notes.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(html => {
            resultDiv.innerHTML = html;
        })
        .catch(() => {
            resultDiv.innerHTML = 'Erreur de chargement';
        });

        // Décoche immédiatement la checkbox
        input.checked = false;
    });
});