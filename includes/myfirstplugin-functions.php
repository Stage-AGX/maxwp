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
?>
