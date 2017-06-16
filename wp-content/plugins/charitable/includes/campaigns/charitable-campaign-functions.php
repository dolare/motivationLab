<?php 

/**
 * Charitable Campaign Functions. 
 *
 * Campaign related functions.
 * 
 * @package     Charitable/Functions/Campaign
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Returns the given campaign.
 *
 * @param   int     $campaign_id
 * @return  Charitable_Campaign
 * @since   1.0.0
 */
function charitable_get_campaign( $campaign_id ) {
	return new Charitable_Campaign( $campaign_id ); // @todo FILTER this
}

/**
 * Returns the current campaign.
 *
 * @return  Charitable_Campaign
 * @since   1.0.0
 */
function charitable_get_current_campaign() {
	return charitable_get_request()->get_current_campaign();
}

/**
 * Returns the current campaign ID.
 *
 * @return  int
 * @since   1.0.0
 */
function charitable_get_current_campaign_id() {
	return charitable_get_request()->get_current_campaign_id();
}

/**
 * Returns whether the current user is the creator of the given campaign.
 *
 * @param   int     $campaign_id
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_current_campaign_creator( $campaign_id = null ) {
	if ( is_null( $campaign_id ) ) {
		$campaign_id = charitable_get_current_campaign_id();
	}

	return get_post_field( 'post_author', $campaign_id ) == get_current_user_id();
}
