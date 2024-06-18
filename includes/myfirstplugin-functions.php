<?php
// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// Fonction principale du plugin
function myfirstplugin_function() {
    echo '<p>Bonjour, ceci est mon premier plugin.</p>';
}

// Accrocher la fonction à WordPress
add_action('wp_footer', 'myfirstplugin_function');

// Ajout d'un shortcode avec son nom
add_shortcode('hello_world', 'shortcode_helloworld');
add_shortcode('svg_coucou', 'shortcode_svg_coucou');
add_shortcode('svg_map','shortcode_svg_map'); 
add_shortcode('svg_liste','dropdown_politique'); // Pas encore implémenté

// Fonction qui affiche le hello world
function shortcode_helloworld(){
    return '<p>Hello World !</p>';
}
// Fonction test premeir svg
function shortcode_svg_coucou(){
    return   ' 
    <svg width="100%" height="100%">
        <rect width="100%" height="100%" fill="blue" />
            <text x="50" y="100" font-family="Verdana" font-size="55"
                fill="white" stroke="black" stroke-width="2">
                Coucou !
            </text>
    </svg>';
}

// Enregistrement des scripts
function myfirstplugin_load_scripts() {
    // Récupérer le chemin du plugin
    $plugin_dir = plugin_dir_url(__FILE__);

    // Enregistrer et charger jQuery
    wp_enqueue_script('jquery');

    // Enregistrer et charger le script personnalisé dans le répertoire choisis
    wp_enqueue_script('myfirstplugin_custom_script', $plugin_dir . '../js/custom-script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'myfirstplugin_load_scripts');

// Fonction qui retourne le SVG de la carte avec les données du JSON
function shortcode_svg_map() {
    // Chemin vers le fichier SVG
    $svg_file = plugin_dir_path(__FILE__) . '../svg/world.svg';
    
    // Récupérer le contenu du fichier SVG
    $svg_content = file_get_contents($svg_file);
    
    // Chemin vers les fichiers JSON des pays et des politiques
    $json_countries_file = plugin_dir_path(__FILE__) . '../json/countries.json';
    $json_categ_file = plugin_dir_path(__FILE__) . '../json/categ.json';

    // Récupérer le contenu des fichiers JSON
    $json_countries_data = file_get_contents($json_countries_file);
    $json_categ_data = file_get_contents($json_categ_file);

    // Retourner le SVG avec les scripts intégrés
    return '
    
    <div id="tooltip" style="display: none; position: absolute; background: #fff; border: 1px solid #ccc; padding: 5px; z-index: 1000;"></div>
    <script>
        var countriesData = ' . $json_countries_data . ';
        var categData = ' . $json_categ_data . ';
    </script>
    ' . $svg_content;
}
add_shortcode('svg_map', 'shortcode_svg_map');

function dropdown_politique() {
    return '
    <label for="policy-select">Choisissez une politique:</label>
    <select id="policy-select">
        <option value="">--Sélectionnez une politique--</option>
        <option value="République">République</option>
        <option value="Monarchie">Monarchie</option>
        <option value="Autre">Autre</option>
    </select>';
}
add_shortcode('dropdown_politique', 'dropdown_politique');

