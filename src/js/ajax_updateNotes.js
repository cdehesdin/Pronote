document.addEventListener("click", function(e) {
    // Création devoir
    if (e.target && e.target.id === "btnCreateDevoir") {
        const form = document.getElementById("formCreateDevoir");
        if (form) sendForm(form, "create", "creeDevoir");
    }

    // Update devoir
    if (e.target && e.target.classList.contains("btnUpdateDevoir")) {
        const form = e.target.closest("form");
        if (form) sendForm(form, "update", form.closest(".modal").id);
    }

    // Delete devoir
    if (e.target && e.target.classList.contains("btnDeleteDevoir")) {
        const form = e.target.closest("form");
        if (form) sendForm(form, "delete", form.closest(".modal").id);
    }
});

// Fonction AJAX
function sendForm(form, action, modalId) {
    const data = new FormData(form);
    var classe = document.getElementById('classe').value;
    data.append("action", action);
    data.append("classe", classe);

    fetch("./src/php/ajax_updateNotes.php", {
        method: "POST",
        body: data
    })
    .then(r => r.json())
    .then(res => {
        submitClasse();

        // Ferme la modal
        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
        }
    })
    .catch(err => console.error(err));
}