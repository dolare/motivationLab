<?php 
/**
 * Charitable AJAX Hooks. 
 *
 * Action/filter hooks used for Charitable AJAX setup.
 * 
 * @package     Charitable/Functions/AJAX
 * @version     1.2.3
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Retrieve a campaign's donation form via AJAX.
 *
 * @see     charitable_template_get_donation_form_ajax
 */
add_action( 'wp_ajax_get_donation_form', 'charitable_ajax_get_donation_form' );
add_action( 'wp_ajax_nopriv_get_donation_form', 'charitable_ajax_get_donation_form' );

/**
 * Upload an image through pupload uploader.
 *
 * @see     charitable_plupload_image_upload
 */
add_action( 'wp_ajax_charitable_plupload_image_upload', 'charitable_plupload_image_upload' );
add_action( 'wp_ajax_nopriv_charitable_plupload_image_upload', 'charitable_plupload_image_upload' );