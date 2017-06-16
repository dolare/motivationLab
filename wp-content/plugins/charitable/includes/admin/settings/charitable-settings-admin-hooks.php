<?php
/**
 * Charitable Settings Hooks.
 *
 * Action/filter hooks used for Charitable Settings API.
 *
 * @package     Charitable/Functions/Admin
 * @version     1.2.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Register Charitable settings.
 *
 * @see     Charitable_Settings::register_settings()
 */
add_action( 'admin_init', array( Charitable_Settings::get_instance(), 'register_settings' ) );

/**
 * Maybe add "Licenses" settings tab.
 *
 * @see     Charitable_Settings::maybe_add_extensions_tab()
 */
add_action( 'charitable_settings_tabs', array( Charitable_Licenses_Settings::get_instance(), 'maybe_add_licenses_tab' ), 1 );

/**
 * Maybe add "Extensions" settings tab.
 *
 * @see     Charitable_Settings::maybe_add_extensions_tab()
 */
add_action( 'charitable_settings_tabs', array( Charitable_Settings::get_instance(), 'maybe_add_extensions_tab' ), 2 );

/**
 * Sanitize checkbox values when settings are submitted.
 *
 * @see     Charitable_Settings::sanitize_checkbox_value()
 */
add_filter( 'charitable_sanitize_value', array( Charitable_Settings::get_instance(), 'sanitize_checkbox_value' ), 10, 2 );

/**
 * Save the license when saving settings.
 *
 * @see     Charitable_Licenses_Settings::save_license()
 */
add_filter( 'charitable_save_settings', array( Charitable_Licenses_Settings::get_instance(), 'save_license' ), 10, 2 );

/**
 * Add dynamic settings groups.
 *
 * @see     Charitable_Gateway_Settings::add_gateway_settings_dynamic_groups()
 * @see     Charitable_Email_Settings::add_email_settings_dynamic_groups()
 * @see     Charitable_Email_Settings::add_licenses_group()
 */
add_filter( 'charitable_dynamic_groups', array( Charitable_Gateway_Settings::get_instance(), 'add_gateway_settings_dynamic_groups' ) );
add_filter( 'charitable_dynamic_groups', array( Charitable_Email_Settings::get_instance(), 'add_email_settings_dynamic_groups' ) );
add_filter( 'charitable_dynamic_groups', array( Charitable_Licenses_Settings::get_instance(), 'add_licenses_group' ) );

/**
 * Add settings to the General tab.
 *
 * @see     Charitable_General_Settings::add_general_fields()
 */
add_filter( 'charitable_settings_tab_fields_general', array( Charitable_General_Settings::get_instance(), 'add_general_fields' ), 5 );

/**
 * Add settings to the Payment Gateways tab.
 *
 * @see     Charitable_Gateway_Settings::add_gateway_fields()
 */
add_filter( 'charitable_settings_tab_fields_gateways', array( Charitable_Gateway_Settings::get_instance(), 'add_gateway_fields' ), 5 );

/**
 * Add settings to the Email tab.
 *
 * @see     Charitable_Email_Settings::add_email_fields()
 */
add_filter( 'charitable_settings_tab_fields_emails', array( Charitable_Email_Settings::get_instance(), 'add_email_fields' ), 5 );

/**
 * Add settings for the Licenses tab.
 *
 * @see     Charitable_Licenses_Settings::add_licenses_fields()
 */
add_filter( 'charitable_settings_tab_fields_licenses', array( Charitable_Licenses_Settings::get_instance(), 'add_licenses_fields' ), 5 );

/**
 * Add settings to the Advanced tab.
 *
 * @see     Charitable_Advanced_Settings::add_advanced_fields()
 */
add_filter( 'charitable_settings_tab_fields_advanced', array( Charitable_Advanced_Settings::get_instance(), 'add_advanced_fields' ), 5 );

/**
 * Add extra settings for the individual gateways & emails tabs.
 *
 * @see     Charitable_Gateway_Settings::add_individual_gateway_fields()
 * @see     Charitable_Email_Settings::add_individual_email_fields()
 */
add_filter( 'charitable_settings_tab_fields', array( Charitable_Gateway_Settings::get_instance(), 'add_individual_gateway_fields' ), 5 );
add_filter( 'charitable_settings_tab_fields', array( Charitable_Email_Settings::get_instance(), 'add_individual_email_fields' ), 5 );

/**
 * Add the PayPal sandbox testing tool to the gateway settings page.
 *
 * @see     Charitable_Gateway_Settings::render_paypal_sandbox_test()
 */
add_action( 'charitable_after_admin_settings', array( Charitable_Gateway_Settings::get_instance(), 'render_paypal_sandbox_test' ) );

/**
 * Redirect the user to PayPal for the sandbox test.
 *
 * @see     Charitable_Gateway_Settings::redirect_paypal_sandbox_test()
 */
add_action( 'charitable_do_paypal_sandbox_test', array( Charitable_Gateway_Settings::get_instance(), 'redirect_paypal_sandbox_test' ) );

/**
 * Redirect to the PayPal gateway settings after being returned from PayPal.
 *
 * @see     Charitable_Gateway_Settings::redirect_paypal_sandbox_test_return()
 */
add_action( 'charitable_paypal_sandbox_test_return', array( Charitable_Gateway_Settings::get_instance(), 'redirect_paypal_sandbox_test_return' ) );
