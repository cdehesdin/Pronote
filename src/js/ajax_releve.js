document.addEventListener('DOMContentLoaded', () => {
    const select       = document.getElementById('classe');
    const selectClasse = document.getElementById('classeNom');
    const zoneEleves   = document.getElementById('form-eleve');
    const zoneReleve   = document.getElementById('resultat');

    // Charger élèves
    function chargerEleves(classe) {
        fetch('./src/php/ajax_releve.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'classe=' + encodeURIComponent(classe)
        })
        .then(r => r.text())
        .then(html => {
            zoneEleves.innerHTML = html;

            const selectEleve = zoneEleves.querySelector('#classe');
            if (selectEleve) {
                chargerReleve(classe, selectEleve.value);
                selectEleve.addEventListener('change', () => {
                    chargerReleve(classe, selectEleve.value);
                });
            }
        });
    }

    // Charger relevé
    function chargerReleve(classe = null, eleve=null) {
        let body = '';
        if (classe !== null) {
            body = 'classe=' + encodeURIComponent(classe);
        }
        if (eleve !== null) {
            body += '&enfants=' + encodeURIComponent(eleve);
        }

        fetch('./src/php/ajax_releve.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: body
        })
        .then(r => r.text())
        .then(html => {
            zoneReleve.innerHTML = html;
        });
    }

    if (selectClasse) {
        chargerEleves(selectClasse.value);
        selectClasse.addEventListener('change', () => {
            chargerEleves(selectClasse.value);
        });
    } else if (select) {
        chargerReleve(null, select.value);
        select.addEventListener('change', () => {
            chargerReleve(null, select.value);
        });
    } else {
        chargerReleve();
    }
});