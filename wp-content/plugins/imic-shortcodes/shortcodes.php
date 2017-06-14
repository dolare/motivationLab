 <?php
/* 
 * Plugin Name: imic Shortcodes
 * Plugin URI:  http://www.imithemes.com
 * Description: Add shortcode generator to posts editor
 * Author:      imithemes
 * Version:     1.2
 * Author URI:  http://www.imithemes.com
 * Licence:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: imithemes-shortcodes
 * Domain Path: /language
 */
// Do not allow direct access to this file.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$path = plugin_dir_path( __FILE__ );
//Load language text domain
add_action('after_setup_theme', 'imic_imithemes_shortcode_setup');
function imic_imithemes_shortcode_setup() {
    load_theme_textdomain('imithemes-shortcodes', plugin_dir_path( __FILE__ ) . '/language');
}
/* CUSTOM SHORTCODES FUNCTIONS
================================================== */
require_once $path . '/shortcodes-functions.php';