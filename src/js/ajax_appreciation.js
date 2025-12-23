document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener("input", (e) => {
        if (!e.target.classList.contains("enter-note")) return;

        const textarea = e.target;

        const formData = new FormData();
        formData.append("eleve", textarea.dataset.eleve);
        formData.append("enseignement", textarea.dataset.enseignement);
        formData.append("appreciation", textarea.value);

        fetch("./src/php/ajax_appreciation.php", {
            method: "POST",
            body: formData
        })
    });
});