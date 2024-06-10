<?php
// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// Ajouter un élément de menu pour la page des paramètres du plugin
function myfirstplugin_add_admin_menu() {
    add_menu_page(
        'Paramètre de mon premier plugin',  // Titre de la page
        'Mon premier plugin',               // Titre du menu
        'manage_options',                   // Capacité
        'myfirstplugin',                    // Identifiant du menu
        'myfirstplugin_admin_page',         // Fonction pour afficher le contenu de la page
        'dashicons-admin-generic',          // Icône
        100                                 // Position
    );
}
add_action('admin_menu', 'myfirstplugin_add_admin_menu');

// Afficher le contenu de la page des paramètres du plugin
function myfirstplugin_admin_page() {
    ?>
    <div class="wrap">
        <h1>Salut!</h1>
        <p>Ceci est mon plugin</p>
    </div>
    <?php
}
?>
