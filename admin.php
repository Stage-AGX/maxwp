<?php

// Security : avoid access on the file from WP settings
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('admin_enqueue_scripts', 'agerixmap_enqueue_admin_scripts');
function agerixmap_enqueue_admin_scripts($hook) {
    // Vérifiez que vous êtes sur la page d'administration de votre plugin
    if ($hook != 'toplevel_page_monplugin') {
        return;
    }

    wp_enqueue_script('monplugin_admin_js', plugin_dir_url(__FILE__) . 'assets/admin.js', array(), '1.0', true);

    // Localisez l'URL d'Ajax et d'autres données si nécessaire
    wp_localize_script('monplugin_admin_js', 'monplugin_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('monplugin_nonce')
    ));
}

add_action('wp_ajax_monplugin_action', 'monplugin_handle_ajax');

function monplugin_handle_ajax() {
    // Vérifiez le nonce pour la sécurité
    check_ajax_referer('monplugin_nonce', 'nonce');

    // Traitez les données reçues
    $additional_data = isset($_POST['additional_data']) ? sanitize_text_field($_POST['additional_data']) : '';

    // Effectuez votre traitement ici
    $response = array('status' => 'success', 'message' => 'Données traitées avec succès', 'data' => $additional_data);

    // Envoyez la réponse en JSON
    wp_send_json($response);

    // Arrêtez l'exécution du script
    wp_die();
}

// Add administration menu
function agerix_add_admin_menu() {
    add_menu_page(
        'Agerix Carte', // Title of the page
        'Agerix Carte', // Title of the menu
        'manage_options', // Capacity required
        'agerix-carte', // Id of the menu
        'agerix_admin_page', // function to show the contenu of the page
        plugin_dir_url(__FILE__) . 'assets/petale-agerix.svg', // logo of the menu
        20 // position in the menu on the left
    );
}
add_action('admin_menu', 'agerix_add_admin_menu');

// show and parameter the admin page
function agerix_admin_page() {
    ?>
    <div class="wrap">
        <h1>Agerix Carte - Paramètres</h1>  
        <form method="post" action="options.php">
            <?php
            settings_fields('agerix_settings_group');
            do_settings_sections('agerix-carte');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Enregistrer les paramètres
function agerix_register_settings() {
    register_setting('agerix_settings_group', 'agerix_categories_colors', 'sanitize_agerix_colors');
    
    add_settings_section(
        'agerix_settings_section',
        'Paramètres des Catégories',
        'agerix_settings_section_callback',
        'agerix-carte'
    );

    $categories_colors = get_option('agerix_categories_colors', [
        'Categorie 1' => '#ff0000',
        'Categorie 2' => '#00ff00',
        'Categorie 3' => '#0000ff',
        'Categorie 4' => '#ffff00',
        'Categorie 5' => '#ff00ff',
        'Categorie 6' => '#00ffff',
    ]);

    foreach ($categories_colors as $category => $color) {
        add_settings_field(
            'agerix_category_' . sanitize_title($category),
            'Nom et Couleur de ' . $category,
            'agerix_category_field_callback',
            'agerix-carte',
            'agerix_settings_section',
            array('category' => $category, 'color' => $color)
        );
    }
}
add_action('admin_init', 'agerix_register_settings');

// Callback pour la section de paramètres
function agerix_settings_section_callback() {
    echo 'Modifiez les noms et les couleurs des catégories ci-dessous :';
}

// Callback pour chaque champ de catégorie
function agerix_category_field_callback($args) {
    $category = $args['category'];
    $color = $args['color'];
    ?>
    <input type="text" name="agerix_categories_colors[<?php echo esc_attr($category); ?>][name]" value="<?php echo esc_attr($category); ?>" />
    <input type="text" class="color-picker" name="agerix_categories_colors[<?php echo esc_attr($category); ?>][color]" value="<?php echo esc_attr($color); ?>" data-default-color="<?php echo esc_attr($color); ?>" />
    <?php
}

// Sanitize les entrées
function sanitize_agerix_colors($input) {
    $output = [];
    foreach ($input as $category => $values) {
        $output[sanitize_text_field($values['name'])] = sanitize_hex_color($values['color']);
    }
    return $output;
}

// Enqueue le script de la color picker
function agerix_enqueue_color_picker($hook_suffix) {
    if ('toplevel_page_agerix-carte' !== $hook_suffix) {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('agerix-admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin.js', array('wp-color-picker'), false, true);
}
add_action('admin_enqueue_scripts', 'agerix_enqueue_color_picker');

// add the style of the admin page
add_action('admin_enqueue_scripts', 'agerixmap_admin_styles');

function agerixmap_admin_styles($hook) {
    if ($hook!= 'toplevel_page_monplugin') {
      return;
    }  
    wp_enqueue_style('agerixmap_admin_css', plugin_dir_url(__FILE__) . 'assets/styles/admin-style.css');
}
?>

