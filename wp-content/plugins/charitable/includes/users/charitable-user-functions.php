<?php
/**
 * Charitable User Functions.
 *
 * User related functions.
 *
 * @package     Charitable/Functions/User
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Returns a Charitable_User object for the given user.
 *
 * This will first attempt to retrieve it from the object cache to prevent duplicate objects.
 *
 * @param   int     $user_id The ID of the user to retrieve.
 * @param   boolean $force Optional. Whether to force an update of the local cache from the persistent.
 * @return  Charitable_User
 * @since   1.0.0
 */
function charitable_get_user( $user_id, $force = false ) {
	if ( is_a( $user_id, 'WP_User' ) ) {
		$user_id = $user_id->ID;
	}
	$user = wp_cache_get( $user_id, 'charitable_user', $force );

	if ( ! $user ) {
		$user = new Charitable_User( $user_id );
		wp_cache_set( $user_id, $user, 'charitable_user' );
	}

	return $user;
}

/**
 * Returns a mapping of user keys.
 *
 * This is needed because the key used in forms is not always the
 * same as they key used for storing the database value.
 *
 * @return 	string[]
 * @since 	1.4.0
 */
function charitable_get_user_mapped_keys() {
	return apply_filters( 'charitable_donor_mapped_keys', array(
		'email' 		   => 'user_email',
		'company' 		   => 'donor_company',
		'address' 		   => 'donor_address',
		'address_2' 	   => 'donor_address_2',
		'city' 			   => 'donor_city',
		'state' 		   => 'donor_state',
		'postcode' 		   => 'donor_postcode',
		'zip' 			   => 'donor_postcode',
		'country' 		   => 'donor_country',
		'phone' 		   => 'donor_phone',
		'user_description' => 'description',
	) );
}

/**
 * Returns a list of the core user keys.
 *
 * Core user keys are any keys that can be passed to wp_update_user or wp_insert_user.
 *
 * @see 	wp_update_user
 * @see 	wp_insert_user
 *
 * @return 	string[]
 * @since 	1.4.0
 */
function charitable_get_user_core_keys() {
	return array(
		'ID',
		'user_pass',
		'user_login',
		'user_nicename',
		'user_url',
		'user_email',
		'display_name',
		'nickname',
		'first_name',
		'last_name',
		'rich_editing',
		'date_registered',
		'role',
		'jabber',
		'aim',
		'yim',
	);
}
