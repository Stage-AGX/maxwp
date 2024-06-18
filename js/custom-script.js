
document.addEventListener("DOMContentLoaded", function() {
    // Assurez-vous que countriesData et categData sont définis
    if (typeof countriesData === 'undefined' || typeof categData === 'undefined') {
        console.error('countriesData or categData is not defined');
        return;
    }

    // Sélectionner tous les chemins (pays) dans le SVG et ajouter des gestionnaires d'événements
    var svgPaths = document.querySelectorAll("svg path");

    svgPaths.forEach(function(path) {
        path.addEventListener("mouseover", function(event) {
            var countryID = path.getAttribute("id"); // Récupérer l'ID du pays
            var countryData = countriesData[countryID]; // Trouver les données du pays

            // Vérifier si les données du pays existent
            if (countryData) {
                var countryName = countryData.name; // Récupérer le nom du pays

                // Trouver la catégorie politique à partir de categData
                var policyCategory = 'Non classé';
                for (var category in categData) {
                    if (categData[category].countries.includes(countryID)) {
                        policyCategory = category;
                        break;
                    }
                }

                // Afficher le nom et la politique du pays dans le tooltip
                var tooltip = document.getElementById("tooltip");
                tooltip.textContent = `${countryName} : Ce pays est ${policyCategory}`;
                tooltip.style.display = "block";

                // Ajouter la classe de la catégorie pour la couleur de survol
                path.classList.add(policyCategory.toLowerCase() + "-hover");
                path.classList.add("hover-active"); // Ajouter une classe pour garder le survol actif
            }
        });

        path.addEventListener("mousemove", function(event) {
            // Déplacer le tooltip avec la souris
            var tooltip = document.getElementById("tooltip");
            tooltip.style.left = event.pageX + 10 + "px"; // Décalage horizontal
            tooltip.style.top = event.pageY + 10 + "px"; // Décalage vertical
        });

        path.addEventListener("mouseout", function(event) {
            var tooltip = document.getElementById("tooltip");
            tooltip.style.display = "none"; // Masquer le tooltip
            path.classList.remove("hover-active"); // Retirer la classe pour désactiver le survol
            path.classList.remove("republique-hover", "monarchies-hover", "autre-hover"); // Retirer la classe de la catégorie
        });
    });

    // Gestion de l'événement de changement sur la liste déroulante
    var policySelect = document.getElementById("policy-select");
    policySelect.addEventListener("change", function(event) {
        var selectedPolicy = event.target.value;

        // Réinitialiser les couleurs des pays
        svgPaths.forEach(function(path) {
            path.style.fill = ""; // Réinitialiser la couleur
        });

        if (selectedPolicy !== "") {
            var selectedCategoryData = categData[selectedPolicy];

            if (selectedCategoryData) {
                var countryIDs = selectedCategoryData.countries;
                var color = selectedCategoryData.color;

                // Appliquer la couleur aux pays de la catégorie sélectionnée
                countryIDs.forEach(function(countryID) {
                    var countryPath = document.getElementById(countryID);
                    if (countryPath) {
                        countryPath.style.fill = color;
                    }
                });
            }
        }
    });
});
