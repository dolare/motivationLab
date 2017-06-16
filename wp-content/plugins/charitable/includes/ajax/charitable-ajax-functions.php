<?php
/**
 * Charitable AJAX Functions.
 *
 * Functions used with ajax hooks.
 *
 * @package     Charitable/Functions/AJAX
 * @version     1.2.3
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! function_exists( 'charitable_ajax_get_donation_form' ) ) :

	/**
	 * Returns the donation form content for a particular campaign, through AJAX.
	 *
	 * @return  void
	 * @since   1.2.3
	 */
	function charitable_ajax_get_donation_form() {
		if ( ! isset( $_POST['campaign_id'] ) ) {
			wp_send_json_error();
		}

		/* Load the template files. */
		require_once( charitable()->get_path( 'includes' ) . 'public/charitable-template-functions.php' );
		require_once( charitable()->get_path( 'includes' ) . 'public/charitable-template-hooks.php' );

		$campaign = new Charitable_Campaign( $_POST['campaign_id'] );

		ob_start();

		$campaign->get_donation_form()->render();

		$output = ob_get_clean();

		wp_send_json_success( $output );

		die();
	}

endif;

if ( ! function_exists( 'charitable_plupload_image_upload' ) ) :

	/**
	 * Upload an image via plupload.
	 *
	 * @return
	 */
	function charitable_plupload_image_upload() {
		$post_id  = (int) filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$field_id = (string) filter_input( INPUT_POST, 'field_id' );

		check_ajax_referer( 'charitable-upload-images-' . $field_id );

		$file = $_FILES['async-upload'];
		$file_attr = wp_handle_upload( $file, array( 'test_form' => false ) );

		if ( isset( $file_attr['error'] ) ) {
			wp_send_json_error( $file_attr );
		}

		$attachment = array(
			'guid'              => $file_attr['url'],
			'post_mime_type'    => $file_attr['type'],
			'post_title'        => preg_replace( '/\.[^.]+$/', '', basename( $file['name'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		/**
		 * Insert the file as an attachment.
		 */
		$attachment_id = wp_insert_attachment( $attachment, $file_attr['file'], $post_id );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error();
		}

		wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file_attr['file'] ) );

		$size = (string) filter_input( INPUT_POST, 'size' );
		$max_uploads = (int) filter_input( INPUT_POST, 'max_uploads', FILTER_SANITIZE_NUMBER_INT );

		if ( ! $size ) {
			$size = 'thumbnail';
		}

		ob_start();

		charitable_template( 'form-fields/picture-preview.php', array(
			'image' => $attachment_id,
			'field' => array(
			'key' => $field_id,
			'size' => $size,
			'max_uploads' => $max_uploads,
			),
		) );

		wp_send_json_success( ob_get_clean() );
	}

endif;
