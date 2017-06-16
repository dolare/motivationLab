<?php

/**
 * Charitable Donation Functions.
 *
 * Donation related functions.
 *
 * @package     Charitable/Functions/Donation
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Returns the given donation.
 *
 * This will first attempt to retrieve it from the object cache to prevent duplicate objects.
 *
 * @param   int     $donation_id
 * @param   boolean $force
 * @return  Charitable_Donation|false
 * @since   1.0.0
 */
function charitable_get_donation( $donation_id, $force = false ) {
	if ( ! did_action( 'charitable_start' ) && false === ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

		charitable_get_deprecated()->doing_it_wrong(
			__FUNCTION__,
			__( 'charitable_get_donation should not be called before the charitable_start action.', 'charitable' ),
			'1.0.0'
		);

		return false;

	}

	$donation = wp_cache_get( $donation_id, 'charitable_donation', $force );

	if ( ! $donation ) {
		$donation = charitable()->donation_factory->get_donation( $donation_id );
		wp_cache_set( $donation_id, $donation, 'charitable_donation' );
	}

	return $donation;
}

/**
 * Returns the donation for the current request.
 *
 * @return  Charitable_Donation
 * @since   1.0.0
 */
function charitable_get_current_donation() {
	return charitable_get_helper( 'request' )->get_current_donation();
}

/**
 * Create a donation.
 *
 * @param   array $args Values for the donation.
 * @return  int
 * @since   1.4.0
 */
function charitable_create_donation( array $args ) {
	$donation_id = Charitable_Donation_Processor::get_instance()->save_donation( $args );

	Charitable_Donation_Processor::destroy();

	return $donation_id;
}

/**
 * Find and return a donation based on the given donation key.
 *
 * @param   string $donation_key
 * @return  int|null
 * @since   1.4.0
 */
function charitable_get_donation_by_key( $donation_key ) {
	global $wpdb;

	$sql = "SELECT post_id 
			FROM $wpdb->postmeta 
			WHERE meta_key = 'donation_key' 
			AND meta_value = %s";

	return $wpdb->get_var( $wpdb->prepare( $sql, $donation_key ) );
}

/**
 * Find and return a donation using a gateway transaction ID.
 *
 * @param   string $transaction_id
 * @return  int|null
 * @since   1.4.7
 */
function charitable_get_donation_by_transaction_id( $transaction_id ) {
	global $wpdb;

	$sql = "SELECT post_id 
			FROM $wpdb->postmeta 
			WHERE meta_key = '_gateway_transaction_id' 
			AND meta_value = %s";

	return $wpdb->get_var( $wpdb->prepare( $sql, $transaction_id ) );
}

/**
 * Return the IPN url for this gateway.
 *
 * IPNs in Charitable are structured in this way: charitable-listener=gateway
 *
 * @param 	strign $gateway
 * @return  string
 * @since   1.4.0
 */
function charitable_get_ipn_url( $gateway ) {
	return add_query_arg( 'charitable-listener', $gateway, home_url( 'index.php' ) );
}

/**
 * Checks for calls to our IPN.
 *
 * This method is called on the init hook.
 *
 * IPNs in Charitable are structured in this way: charitable-listener=gateway
 *
 * @return  boolean True if this is a call to our IPN. False otherwise.
 * @since   1.4.0
 */
function charitable_ipn_listener() {
	if ( isset( $_GET['charitable-listener'] ) ) {

		$gateway = $_GET['charitable-listener'];
		do_action( 'charitable_process_ipn_' . $gateway );
		return true;
	}

	return false;
}

/**
 * Checks if this is happening right after a donation.
 *
 * This method is called on the init hook.
 *
 * @return  boolean
 * @access  public
 * @since   1.4.0
 */
function charitable_is_after_donation() {
	$processor = get_transient( 'charitable_donation_' . charitable_get_session()->get_session_id() );

	if ( ! $processor ) {
		return;
	}

	do_action( 'charitable_after_donation', $processor );

	foreach ( $processor->get_campaign_donations_data() as $campaign_donation ) {

		charitable_get_session()->remove_donation( $campaign_donation['campaign_id'] );

	}

	delete_transient( 'charitable_donation_' . charitable_get_session()->get_session_id() );
}

/**
 * Returns whether the donation status is valid.
 *
 * @return  boolean
 * @since   1.4.0
 */
function charitable_is_valid_donation_status( $status ) {
	return array_key_exists( $status, charitable_get_valid_donation_statuses() );
}

/**
 * Returns the donation statuses that signify a donation was complete.
 *
 * By default, this is just 'charitable-completed'. However, 'charitable-preapproval'
 * is also counted.
 *
 * @return  string[]
 * @since   1.4.0
 */
function charitable_get_approval_statuses() {
	return apply_filters( 'charitable_approval_donation_statuses', array( 'charitable-completed' ) );
}

/**
 * Returns whether the passed status is an confirmed status.
 *
 * @param   string $key
 * @return  boolean
 * @since   1.4.0
 */
function charitable_is_approved_status( $status ) {
	return in_array( $status, charitable_get_approval_statuses() );
}

/**
 * Return array of valid donations statuses.
 *
 * @return  array
 * @since   1.4.0
 */
function charitable_get_valid_donation_statuses() {
	return apply_filters( 'charitable_donation_statuses', array(
		'charitable-completed'  => __( 'Paid', 'charitable' ),
		'charitable-pending'    => __( 'Pending', 'charitable' ),
		'charitable-failed'     => __( 'Failed', 'charitable' ),
		'charitable-cancelled'  => __( 'Cancelled', 'charitable' ),
		'charitable-refunded'   => __( 'Refunded', 'charitable' ),
	) );
}

/**
 * Cancel a donation.
 *
 * @global 	WP_Query $wp_query
 *
 * @return  boolean True if the donation was cancelled. False otherwise.
 * @since   1.4.0
 */
function charitable_cancel_donation() {
	global $wp_query;

	if ( ! charitable_is_page( 'donation_cancel_page' ) ) {
		return false;
	}

	if ( ! isset( $wp_query->query_vars['donation_id'] ) ) {
		return false;
	}

	$donation = charitable_get_donation( $wp_query->query_vars['donation_id'] );

	if ( ! $donation ) {
		return false;
	}

	/* Donations can only be cancelled if they are currently pending. */
	if ( 'charitable-pending' != $donation->get_status() ) {
		return false;
	}

	if ( ! $donation->is_from_current_user() ) {
		return false;
	}

	$donation->update_status( 'charitable-cancelled' );

	return true;
}

/**
 * Load the donation form script.
 *
 * @return  void
 * @since   1.4.0
 */
function charitable_load_donation_form_script() {
	wp_enqueue_script( 'charitable-donation-form' );
}

/**
 * Add a message to a donation's log.
 *
 * @param   string $message
 * @return  void
 * @since   1.0.0
 */
function charitable_update_donation_log( $donation_id, $message ) {
	charitable_get_donation( $donation_id )->update_donation_log( $message );
}

/**
 * Get a donation's log.
 *
 * @return  array
 * @since   1.0.0
 */
function charitable_get_donation_log( $donation_id ) {
	charitable_get_donation( $donation_id )->get_donation_log();
}

/**
 * Get the gateway used for the donation.
 *
 * @param   int $donation_id
 * @return  string
 * @since   1.0.0
 */
function charitable_get_donation_gateway( $donation_id ) {
	return get_post_meta( $donation_id, 'donation_gateway', true );
}

/**
 * Sanitize meta values before they are persisted to the database.
 *
 * @param   mixed   $value
 * @param   string  $key
 * @return  mixed
 * @since   1.0.0
 */
function charitable_sanitize_donation_meta( $value, $key ) {
	if ( 'donation_gateway' == $key ) {
		if ( empty( $value ) || ! $value ) {
			$value = 'manual';
		}
	}

	return apply_filters( 'charitable_sanitize_donation_meta-' . $key, $value );
}

/**
 * Flush the donations cache for every campaign receiving a donation.
 *
 * @param   int $donation_id
 * @return  void
 * @since   1.0.0
 */
function charitable_flush_campaigns_donation_cache( $donation_id ) {
	$campaign_donations = charitable_get_table( 'campaign_donations' )->get_donation_records( $donation_id );

	foreach ( $campaign_donations as $campaign_donation ) {
		Charitable_Campaign::flush_donations_cache( $campaign_donation->campaign_id );
	}

	wp_cache_delete( $donation_id, 'charitable_donation' );
}
