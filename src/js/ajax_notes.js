document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-tri-notes');
    const listeNotes = document.getElementById('liste-notes');

    // Fonction pour envoyer le formulaire en AJAX
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
        .catch(error => {
            console.error(error);
            listeNotes.innerHTML = '<li>Erreur de chargement</li>';
        });
    };

    // Envoi lors du changement du formulaire
    form.addEventListener('change', () => {
        const formData = new FormData(form);
        envoyerForm(formData);
    });

    // Envoi initial par défaut (chronologique)
    const defaultFormData = new FormData();
    defaultFormData.append('ordres', 'chronologique');
    envoyerForm(defaultFormData);
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