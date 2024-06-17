jQuery(document).ready(function($) {
    // Vérifie que countriesData est défini 
    if (typeof countriesData === 'undefined') {
        console.error('countriesData is not defined');
        return;
    }

    // Sélectionner tous les chemins (pays) dans le SVG et ajouter des gestionnaires d'événements
    $("svg path").hover(
        function() {
            var countryID = $(this).attr("id"); // Récupérer l'ID du pays
            var countryData = countryData[countryID]; // Trouver les données du pays

            // Vérifier si les données du pays existent
            if (countryData) {
                var countryName = countryData.name; // Récupérer le nom du pays
                var categoryName = countryData.name-categorie; // Récupérer le nom de la catégorie

                // Afficher le nom et la catégorie du pays dans le tooltip
                $("#tooltip").text(`${countryName} : Ce pays est ${categoryName}`).show();
                $(this).addClass(countryData.category + "-hover"); // Ajouter la classe de la catégorie pour la couleur de survol
            }
        }, 
        function() {
            var countryID = $(this).attr("id"); // Récupérer l'ID du pays
            var countryData = countriesData[countryID]; // Trouver les données du pays

            // Vérifier si les données du pays existent
            if (countryData) {
                $("#tooltip").hide(); // Masquer le tooltip
                $(this).removeClass(countryData.category + "-hover"); // Retirer la classe de la catégorie
            }
        }
    ).mousemove(function(event) {
        // Déplacer le tooltip avec la souris
        $("#tooltip").css({
            left: event.pageX + 10, // Décalage horizontal
            top: event.pageY + 10 // Décalage vertical
        });
    });
});
