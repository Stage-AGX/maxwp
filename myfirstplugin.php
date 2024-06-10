<?php
/*
Plugin Name: Mon premier plugin.
Description: Ceci est mon premier plugin.
Version: 1.0
Author: Maxence
*/

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// Inclure le fichier contenant les fonctions du plugin
require_once plugin_dir_path(__FILE__) . 'includes/myfirstplugin-functions.php';

// Inclure le fichier contenant les fonctions de la page d'administration
if (is_admin()) { //Grace à ça
    require_once plugin_dir_path(__FILE__) . 'admin/myfirstplugin-admin-page.php';
}

