<?php
/**
 * A base start object. 
 *
 * @package		Charitable/Classes/Charitable_Start_Object
 * @version 	1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Start_Object' ) ) : 

/**
 * Charitable_Start_Object
 *
 * @since 		1.0.0
 * @abstract
 */
abstract class Charitable_Start_Object {

	/**
	 * Instantiate the class, but only during the start phase.
	 *
	 * @uses 	charitable_start
	 * @param 	Charitable 	$charitable 
	 * @return 	void
	 * @static 
	 * @access 	public
	 * @since 	1.0.0
	 */
	public static function charitable_start( Charitable $charitable ) {
		if ( ! $charitable->is_start() ) {
			return;
		}

		$class = get_called_class();
		$charitable->register_object( new $class );
	}

	/**
	 * Set up the class. 
	 * 
	 * Note that the only way to instantiate an object is with the charitable_start method, 
	 * which can only be called during the start phase. In other words, don't try 
	 * to instantiate this object. 
	 *
	 * @access 	protected
	 * @since 	1.0.0
	 */
	abstract protected function __construct();
}

endif; // End class_exists check