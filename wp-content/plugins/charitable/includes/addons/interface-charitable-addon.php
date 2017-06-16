<?php
/**
 * Addon interface.
 *
 * This defines a strict interface that all Core Addons must implement
 *
 * @version		1.0.0
 * @package		Charitable/Interfaces/Charitable_Addon_Interface
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! interface_exists( 'Charitable_Addon_Interface' ) ) : 

/**
 * Charitable_Addon_Interface interface. 
 *
 * @since		1.0.0
 */
interface Charitable_Addon_Interface {

	/**
	 * Activate the addon. 
	 *
	 * @return 	void
	 * @access  public
	 * @static
	 * @since 	1.0.0
	 */
	public static function activate();

	/**
	 * Load the addon. 
	 *
	 * @return 	void
	 * @access  public
	 * @static
	 * @since 	1.0.0
	 */
	public static function load();
}

endif; // End interface_exists check.