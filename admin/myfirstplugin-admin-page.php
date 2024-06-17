<?php
// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// Ajouter un élément de menu pour la page des paramètres du plugin
function myfirstplugin_add_admin_menu() {
    // Menu principal - Dashboard
    add_menu_page(
        'Dashboard',                        // Titre de la page
        'Carte SVG',                        // Titre du menu
        'manage_options',                   // Capacité
        'myfirstplugin',                    // Identifiant du menu
        'myfirstplugin_admin_page',         // Fonction pour afficher le contenu de la page
        'dashicons-admin-generic',          // Icône
        100                                 // Position
    );

    // Sous-menu "À propos"
    add_submenu_page(
        'myfirstplugin',                    // Identifiant du parent (menu principal)
        'À propos',                         // Titre de la page
        'À propos',                         // Titre du sous-menu
        'manage_options',                   // Capacité
        'myfirstplugin_about',              // Identifiant du sous-menu
        'myfirstplugin_about_page'          // Fonction pour afficher le contenu de la page "À propos"
    );
}
add_action('admin_menu', 'myfirstplugin_add_admin_menu');

// Charger les données des pays depuis le fichier JSON
function myfirstplugin_load_countries() {
    $json_file = plugin_dir_path(__FILE__) . '../json/countries.json'; // Chemin vers le fichier JSON
    $json_data = file_get_contents($json_file);
    return json_decode($json_data, true);
}

// Afficher le contenu de la page des paramètres du plugin
function myfirstplugin_admin_page() {
    // Charger les données des pays depuis le fichier JSON
    $countries = myfirstplugin_load_countries();
    ?>
    <div class="wrap">
        <h1>Dashboard</h1>
        <h2>Liste des pays</h2>
        <!-- Dropdown avec la liste de tous les pays -->
        <select id="country-list">
            <option value="">Sélectionnez un pays</option> <!-- -->
            <!--Divise chaque element du tab $countries en 2 variables-->
            <?php foreach ($countries as $country_code => $country_data) : ?>
                <option value="<?php echo esc_attr($country_code); ?>">
                    <?php echo esc_html($country_data['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    
        <h2>Politique du pays sélectionné</h2>
        <!-- Zone où la catégorie s'affichera en fonction du pays sélectionné dans la dropdown -->
        <textarea id="country-category" readonly rows="1" cols="17"></textarea>
        <button id="edit-category-button" style="margin-left: 10px;">Éditer</button>

       <!-- Formulaire modal pour éditer la catégorie -->
       <div id="edit-category-modal" style="display:none;">
            <form id="edit-category-form">
            <input type="hidden" id="country-code">
            <label for="new-category">Nouvelle catégorie :</label>
            <select id="new-category">
                <option value="Républiques semi présidentiel">Républiques semi présidentiel</option>
                <option value="République">République</option>
                <option value="République avec un parti unique">République avec un parti unique</option>
                <option value="Monarchies absolues">Monarchies absolues</option>
                <option value="Monarchies constitutionnelles">Monarchies constitutionnelles</option>
                <option value="Dictatures militaire">Dictatures militaire</option>
                <option value="République à régime parlementaire">République à régime parlementaire</option>
            </select>
        <button type="submit">Sauvegarder</button>
</form>
        </div>


<!-- Script jQuery pour mettre à jour la catégorie en fonction du pays sélectionné -->
 <script>
jQuery(document).ready(function($) {
    // Récupérer les données des pays les stocker dans une variable JavaScript $countries
    var countriesData = <?php echo json_encode($countries); ?>;

    // Gérer le changement de sélection dans la dropdown des pays
    $('#country-list').change(function() {
        // Récupérer le code du pays sélectionné
        var selectedCountryCode = $(this).val();
        var category = '';

        // Vérifier si le code du pays sélectionné est valide et récupérer sa catégorie
        if (selectedCountryCode && countriesData[selectedCountryCode]) {
            category = countriesData[selectedCountryCode].category || 'Aucune catégorie';
        }

        // Mettre à jour la valeur de la textarea avec la catégorie du pays sélectionné
        $('#country-category').val(category);
    });

    // Gérer le clic sur le bouton "Éditer"
    $('#edit-category-button').click(function() {
        // Récupérer le code du pays sélectionné dans la dropdown
        var selectedCountryCode = $('#country-list').val();

        // Vérifier si un pays est sélectionné
        if (selectedCountryCode) {
            // Pré-remplir les champs du formulaire de modification avec les valeurs actuelles
            $('#country-code').val(selectedCountryCode);
            $('#new-category').val($('#country-category').val());

            // Afficher la modale de modification
            $('#edit-category-modal').show();
        } else {
            // Afficher une alerte si aucun pays n'est sélectionné
            alert('Veuillez sélectionner un pays.');
        }
    });

    // Gérer le clic sur le bouton de fermeture de la modale
    $('#close-modal').click(function() {
        // Cacher la modale de modification
        $('#edit-category-modal').hide();
    });

    // Gérer la soumission du formulaire de modification de catégorie
    $('#edit-category-form').submit(function(event) {
        event.preventDefault();

        // Récupérer le code du pays et la nouvelle catégorie à partir des champs du formulaire
        var countryCode = $('#country-code').val();
        var newCategory = $('#new-category').val();

        // Convertir les espaces de la nouvelle catégorie sélecionné en - et mettre tout en minuscule
        var categoryKey = newCategory.toLowerCase().replace(/\s+/g, '-');

        // Envoyer les données au serveur via AJAX pour mettre à jour la catégorie du pays sans avoir besoin de f5
        $.ajax({
            url: ajaxurl,
            type: 'POST', //type de requete
            data: {
                action: 'myfirstplugin_update_category', // Action WordPress pour le traitement PHP
                country_code: countryCode, // Code du pays à mettre à jour
                category: categoryKey // Nouvelle catégorie formatée avec des tirets
            },
            success: function(response) {
                // Afficher un message de succès et recharger la page après la mise à jour
                if (response.success) {
                    alert('Catégorie mise à jour avec succès !');
                    location.reload();                                  // J'ai 2 messages a chaque fois jsp pq
                } else {
                    alert('Erreur lors de la mise à jour de la catégorie');
                }
            }
        });
    });
});

</script>



        <!-- Zone de texte où les pays sans catégories sont affichés -->
        <h2>Liste des pays sans politiques</h2>
        <?php foreach ($countries as $country_code => $country_data) : ?>
            <?php if (empty($country_data['category'])) : ?>
                <div>
                    <?php echo esc_html($country_data['name']); ?>
                     <!--bouton editer-->
                    <button class="edit-category-button" data-country-code="<?php echo esc_attr($country_code); ?>">Éditer</button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Quand on clique sur le bouton : -->
        <div id="edit-category-modal" style="display: none;">
            <!--modale = force une réponse avant de poursuivre-->
            <h2>Éditer la politique du pays</h2>
            <form id="edit-category-form">
                <label for="category">Nouvelle politique:</label>
                <input type="text" id="new-category" name="category">
                <input type="hidden" id="country-code" name="country_code">
                <button type="submit">Sauvegarder</button>
            </form>
        </div>
        <!--Scirpt jQuery-->
        <script>
            jQuery(document).ready(function($) {
                // Gère le clic sur le bouton d'édition de catégorie.
                $('.edit-category-button').click(function() {
                    var countryCode = $(this).data('country-code'); // Récupère le code du pays à partir du country-code.
                    $('#country-code').val(countryCode);
                    $('#edit-category-modal').show();
                });
                //Gère le submit (l'envoie du formulaire)
                $('#edit-category-form').submit(function(event) {
                    event.preventDefault();

                    var countryCode = $('#country-code').val();//Recupère le code du pays en une variable
                    var newCategory = $('#new-category').val();//Récupère la nouvelle categ saisie en une variable
                    //requete ajax
                    $.ajax({
                        url: ajaxurl, //url de l'action ajax de wp
                        type: 'POST', //type de la requête http
                        data: {
                            action: 'myfirstplugin_update_category', 
                            country_code: countryCode, //code du pays
                            category: newCategory //nouvelle categ
                        },
                        //si ça fonctionne
                        success: function(response) {
                            if (response.success) {
                                alert('Catégorie mise à jour avec succès !');
                                location.reload(); //recharge
                                //sinon
                            } else { //sinon
                                alert('Erreur lors de la mise à jour de la catégorie');
                            }
                        }
                    });
                });
            });
        </script>
    </div>
    <?php
}

// Fonction pour mettre à jour la catégorie d'un pays et son "name-category"
function myfirstplugin_update_category() {
    // Vérifie que country_code et category sont présents dans la requête POST.
    if (isset($_POST['country_code']) && isset($_POST['category'])) {
        $countries = myfirstplugin_load_countries();
        $country_code = sanitize_text_field($_POST['country_code']);
        $category = sanitize_text_field($_POST['category']);

        if (isset($countries[$country_code])) {
            // Mettre à jour la catégorie en minuscules avec des tirets
            $categoryKey = strtolower(str_replace(' ', '-', $category));
            $countries[$country_code]['category'] = $categoryKey;

            // Mettre à jour "name-category"
            $nameCategory = ucwords(str_replace('-', ' ', $categoryKey));
            $countries[$country_code]['name-category'] = $nameCategory;

            // Enregistrer les modifications dans le fichier JSON
            $json_data = json_encode($countries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            // Répertoire
            if (file_put_contents(plugin_dir_path(__FILE__) . '../json/countries.json', $json_data) !== false) {
                wp_send_json_success();
            } else {
                error_log("Erreur lors de l'écriture du fichier JSON");
                wp_send_json_error();
            }
        } else {
            error_log("Pays non trouvé: " . $country_code);
            wp_send_json_error();
        }
    } else {
        error_log("Paramètres manquants pour la mise à jour");
        wp_send_json_error();
    }
}
add_action('wp_ajax_myfirstplugin_update_category', 'myfirstplugin_update_category');
///////////////////////////////////

// Afficher le contenu de la page "À propos"
function myfirstplugin_about_page() {
    ?>
    <div class="wrap">
        <h1>A propos</h1>
        <h3>Ceci est mon plugin</h3> <br>

        <p>
        Ceci est un plugin qui a pour but d'afficher une carte svg interactive et modifiable en backend grace à un Dashboard.<br><br>
        - Avec le shortcode <strong>[hello_world]</strong> vous pouvez afficher Hello World !<br>
        - Avec le shortcode <strong>[svg_map]</strong> vous pouvez afficher une carte interactive !
        </p>
    </div>
    <?php
}
?>
