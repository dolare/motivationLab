<?php
/* 
 * Plugin Name: borntogive vc elements
 * Plugin URI:  http://www.imithemes.com
 * Description: Custom Elements of Visual Composer
 * Author:      imithemes
 * Version:     1.6.2
 * Author URI:  http://www.imithemes.com
 * Text Domain: borntogive-vc
 * Domain Path: /language
 */
// Do not allow direct access to this file.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$path = plugin_dir_path( __FILE__ );
//Load language text domain
add_action('after_setup_theme', 'btg_vcelements_setup');
function btg_vcelements_setup() {
    load_theme_textdomain('borntogive-vc', plugin_dir_path( __FILE__ ) . '/language');
}
/* CUSTOM SHORTCODES FUNCTIONS
================================================== */
require_once $path . '/btg_elements_fields.php';
require_once $path . '/btg_shortcodes_function.php';