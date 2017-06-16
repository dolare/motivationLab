<?php 
/**
 * Charitable Upgrade Hooks. 
 *
 * Action/filter hooks used for Charitable Upgrades. 
 * 
 * @package     Charitable/Functions/Upgrades
 * @version     1.3.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Check if there is an upgrade that needs to happen and if so, display a notice to begin upgrading.
 *
 * @see     Charitable_Upgrade::add_upgrade_notice()
 */
add_action( 'admin_notices', array( Charitable_Upgrade::get_instance(), 'add_upgrade_notice' ) );

/**
 * Trigger an upgrade via AJAX.
 *
 * @see     Charitable_Upgrade::trigger_ajax_upgrade()
 */
// add_action( 'wp_ajax_trigger_ajax_upgrade', array( 'Charitable_Upgrade', 'trigger_ajax_upgrade' ) );

/**
 * Register the admin page.
 *
 * @see     Charitable_Upgrade_Page::register_page()
 */
add_action( 'admin_menu', array( Charitable_Upgrade_Page::get_instance(), 'register_page' ) );

/**
 * Hide the admin page from the menu.
 *
 * @see     Charitable_Upgrade_Page::remove_page_from_menu()
 */
add_action( 'admin_head', array( Charitable_Upgrade_Page::get_instance(), 'remove_page_from_menu' ) );

/**
 * Update the upgrade system.
 *
 * @see     Charitable_Upgrade::update_upgrade_system()
 */
add_action( 'charitable_update_upgrade_system', array( Charitable_Upgrade::get_instance(), 'update_upgrade_system' ) );

/**
 * Run the upgrade for 1.3.0.
 *
 * @see     Charitable_Upgrade::upgrade_1_3_0_fix_gmt_dates()
 */
add_action( 'charitable_fix_donation_dates', array( Charitable_Upgrade::get_instance(), 'fix_donation_dates' ) );


// add_action( 'admin_init', array( Charitable_Upgrade_Page::get_instance(), 'welcome'    ) );
