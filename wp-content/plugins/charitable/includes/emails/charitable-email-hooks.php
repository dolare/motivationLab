<?php
/**
 * Charitable Email Hooks.
 *
 * Action/filter hooks used for Charitable emails.
 *
 * @package     Charitable/Functions/Emails
 * @version     1.0.3
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Register Charitable emails.
 *
 * @see     Charitable_Emails::register_emails()
 */
add_action( 'init', array( Charitable_Emails::get_instance(), 'register_emails' ) );

/**
 * Send the Donation Receipt and Donation Notification emails.
 *
 * Both of these emails are sent immediately a donation has been completed.
 *
 * @see     Charitable_Email_Donation_Receipt::send_with_donation_id()
 * @see     Charitable_Email_New_Donation::send_with_donation_id()
 */
add_action( 'charitable_after_save_donation', array( 'Charitable_Email_Donation_Receipt', 'send_with_donation_id' ) );
add_action( 'charitable_after_save_donation', array( 'Charitable_Email_New_Donation', 'send_with_donation_id' ) );

foreach ( charitable_get_approval_statuses() as $status ) {
	add_action( $status . '_' . Charitable::DONATION_POST_TYPE, array( 'Charitable_Email_Donation_Receipt', 'send_with_donation_id' ) );
	add_action( $status . '_' . Charitable::DONATION_POST_TYPE, array( 'Charitable_Email_New_Donation', 'send_with_donation_id' ) );
}

/**
 * Send the Campaign Ended email.
 *
 * This email can be sent to any recipients, within 24 hours after a campaign has reached its end date.
 *
 * @see     Charitable_Email_Campaign_End::send_with_campaign_id()
 */
add_action( 'charitable_campaign_end', array( 'Charitable_Email_Campaign_End', 'send_with_campaign_id' ) );

/**
 * Enable & disable emails.
 *
 * @see     Charitable_Emails::handle_email_settings_request()
 */
add_action( 'charitable_enable_email', array( Charitable_Emails::get_instance(), 'handle_email_settings_request' ) );
add_action( 'charitable_disable_email', array( Charitable_Emails::get_instance(), 'handle_email_settings_request' ) );

/**
 * Set the current email before sending or previewing an email.
 *
 * @see     Charitable_Emails::set_current_email()
 */
add_action( 'charitable_before_send_email', array( Charitable_Emails::get_instance(), 'set_current_email' ) );
add_action( 'charitable_before_preview_email', array( Charitable_Emails::get_instance(), 'set_current_email' ) );

/**
 * Register an email's settings.
 *
 * @see     Charitable_Emails::register_email_settings()
 */
add_filter( 'charitable_settings_fields_emails_email', array( Charitable_Emails::get_instance(), 'register_email_settings' ), 10, 2 );
