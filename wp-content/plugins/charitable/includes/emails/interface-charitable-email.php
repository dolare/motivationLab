<?php
/**
 * Charitable Email interface.
 *
 * This defines a strict interface that emails must implement.
 *
 * @version     1.2.0
 * @package     Charitable/Interfaces/Charitable_Email_Interface
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! interface_exists( 'Charitable_Email_Interface' ) ) : 

/**
 * Charitable_Email_Interface interface. 
 *
 * @since       1.2.0
 */
interface Charitable_Email_Interface {
    public static function get_email_id();
}

endif; // End interface_exists check.