<?php
/*
Plugin Name: Carte interactive SVG
Description: Ceci est mon premier plugin.
Version: 1.3
Author: Maxence
*/

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}
/* elle sera utilisé sur chaque page
 * Le code ci-dessus vérifie si la constante ABSPATH est définie. 
 * ABSPATH est une constante définie par WordPress et représente le chemin absolu vers le répertoire d'installation de WordPress.
 * Si ABSPATH n'est pas définie, cela signifie que le fichier est probablement accédé directement via une URL,
 * et non chargé par WordPress lui-même. Dans ce cas, exit; termine l'exécution du script immédiatement,
 * empêchant ainsi un accès direct non sécurisé à ce fichier.
*/

// Inclure le fichier contenant les fonctions du plugin
require_once plugin_dir_path(__FILE__) . 'includes/myfirstplugin-functions.php';

// Inclure le fichier contenant les fonctions de la page d'administration
//test
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/myfirstplugin-admin-page.php';
}

// Charger les styles CSS
function myfirstplugin_load_styles() {
    // Obtenez l'URL du répertoire de votre plugin
    $plugin_dir = plugin_dir_url(__FILE__);

    // Enregistrer et charger styles.css
    wp_enqueue_style('myfirstplugin_styles', $plugin_dir . 'styles/style.css');
}
add_action('wp_enqueue_scripts', 'myfirstplugin_load_styles');

