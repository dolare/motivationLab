<?php
/* 
 * Plugin Name: Born To Give Core
 * Plugin URI:  http://www.imithemes.com
 * Description: Create Post Types for Born To Give Theme
 * Author:      imithemes
 * Version:     1.3
 * Author URI:  http://www.imithemes.com
 * Licence:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright:   (c) 2016 imithemes. All rights reserved
 * Text Domain: borntogive-core
 * Domain Path: /language
 */

// Do not allow direct access to this file.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$path = plugin_dir_path( __FILE__ );
/* CUSTOM POST TYPES
================================================== */
//require_once $path . '/imic-post-type-permalinks.php';
require_once $path . '/custom-post-types/testimonial-type.php';
require_once $path . '/custom-post-types/gallery-type.php';
require_once $path . '/custom-post-types/event-type.php';
require_once $path . '/custom-post-types/team-type.php';
/* SET LANGUAGE FILE FOLDER
=================================================== */
add_action('after_setup_theme', 'btg_core_setup');
function btg_core_setup() {
    load_theme_textdomain('borntogive-core', plugin_dir_path( __FILE__ ) . '/language');
}
// Add Demo Importer and other Extensions for Redux Framework
if(!function_exists('borntogive_register_custom_extension_loader')) :
global $borntogive_options, $opt_name;
$opt_name = "borntogive_options";
	function borntogive_register_custom_extension_loader($ReduxFramework) {
		$path = plugin_dir_path( __FILE__ ). '/extensions/';
		$folders = scandir( $path);		   
		foreach($folders as $folder) {
			if ($folder === '.' or $folder === '..' or !is_dir($path . $folder) ) {
				continue;	
			} 
			$extension_class = 'ReduxFramework_Extension_' . $folder;
			if( !class_exists( $extension_class ) ) {
				// In case you wanted override your override, hah.
				$class_file = $path . $folder . '/extension_' . $folder . '.php';
				$class_file = apply_filters( 'redux/extension/'.$ReduxFramework->args['opt_name'].'/'.$folder, $class_file );
				if( $class_file ) {
					require_once( $class_file );
					$extension = new $extension_class( $ReduxFramework );
				}
			}
		}
	}
	add_action("redux/extensions/{$opt_name}/before", 'borntogive_register_custom_extension_loader', 0);
endif;