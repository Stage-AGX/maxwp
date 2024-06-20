<?php

/**
 * @package cartewp
 * @version 1.1.7
 * 
 * Plugin Name: agerixcarte
 * Plugi URI: https://julie.agerix.wf/wordpress/
 * Author: Julie Hatteville
 * Description: this plugin is a test pluging in order to know how to create a plugin, to install it, to uninstall it, to encapsulate an article in shortcode, and add SVG
 * Version 1.1.1
 * Author URI: https://www.agerix.fr/
 */


// If this file is called directly, abort.
if (!defined( 'WPINC' ) ) {
  die;
}

// Function to display "Hello, World!" text
function display_hello_world() {
  echo '<p>Hello, World!</p>';
}

// Shortcode to use the function in posts or pages
function hello_world_shortcode() {
  return '<p>Hello, World!</p>';
}
add_shortcode('hello_world', 'hello_world_shortcode');

// Upload of translation
function load_country_translation() {
  load_plugin_textdomain( 'agerixcarte', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_country_translation' );

// Load admin folder
require_once plugin_dir_path(__FILE__) . 'admin.php'; //load dashboard page

// Function to handle the SVG shortcode
function svg_shortcode_handler($atts){ //take the svg from the svg file on the folder
  $svg_file_path = plugin_dir_path( __FILE__ ) . 'worldmap.svg'; //path of the svg

  //check if the file exists
  if(file_exists($svg_file_path)){
  // read the file contents 
    $svg_content = file_get_contents($svg_file_path);

        
    // Return the SVG content wrapped in a div
    return '<div class="svg-container">' . $svg_content . '
    <div>
    <ul id="list-country">

    </ul>
    </div>
    <select id="category-select">
    <option value="reset">Select a category</option>
    </select>
    </div>';
  }
  else{
    // error message
    return '<p>' . __('File SVG not found', 'agerixcarte') . '</p>';
  }
}
add_shortcode('svg', 'svg_shortcode_handler');

// Enqueue les scripts et styles
function agerix_enqueue_scripts() {
  wp_enqueue_style('agerix-style', plugin_dir_url(__FILE__) . '/assets/styles/worldmapagerix.css');
  wp_enqueue_script('agerix-script-worldmapagerix', plugins_url('/assets/js/worldmapagerix.js', __FILE__), array(), '1.0', true);
 
  
  $countries_data = get_option('agerix_countries_data', file_get_contents(plugin_dir_path(__FILE__) . 'assets/js/countries-data.json'));
  $categories_colors = get_option('agerix_categories_colors', [
      'Categorie 1' => '#ff0000',
      'Categorie 2' => '#00ff00',
      'Categorie 3' => '#0000ff',
      'Categorie 4' => '#ffff00',
      'Categorie 5' => '#ff00ff',
      'Categorie 6' => '#00ffff',
  ]);

  wp_localize_script('agerix-script-worldmapagerix', 'agerixcarte_vars', array(
    'plugin_url' => plugins_url('', __FILE__)
  ));

}
add_action('wp_enqueue_scripts', 'agerix_enqueue_scripts');

