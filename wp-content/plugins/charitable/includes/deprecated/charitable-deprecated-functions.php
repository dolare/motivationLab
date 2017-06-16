<?php
/**
 * Charitable Deprecated Functions.
 *
 * @package     Charitable/Functions/Deprecated
 * @version     1.0.1
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Functions deprecated in version 1.0.1.
 */

function charitable_user_dashboard() {
	// _deprecated_function( __FUNCTION__, '1.0.1', 'charitable_get_user_dashboard' );
	return charitable_get_user_dashboard();
}
