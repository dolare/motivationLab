<?php
/**
 * Sets up Charitable templates for specific views.
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Templates
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Templates' ) ) :

	/**
	 * Charitable_Templates
	 *
	 * @since       1.0.0
	 */
	class Charitable_Templates {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Templates|null
		 * @access  private
		 * @static
		 */
		private static $instance = null;

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @return  Charitable_Templates
		 * @access  public
		 * @since   1.2.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Templates();
			}

			return self::$instance;
		}

		/**
		 * Set up the class.
		 *
		 * Note that the only way to instantiate an object is with the charitable_start method,
		 * which can only be called during the start phase. In other words, don't try
		 * to instantiate this object.
		 *
		 * @access  private
		 * @since   1.0.0
		 */
		private function __construct() {

			/* If you want to unhook any of the callbacks attached above, use this hook. */
			do_action( 'charitable_templates_start', $this );
		}

		/**
		 * Load the correct template based on the current request.
		 *
		 * @return  string $template
		 * @access  public
		 * @since   1.3.0
		 */
		public function template_loader( $template ) {

			if ( charitable_is_page( 'donation_receipt_page' ) ) {
				return $this->get_donation_receipt_template( $template );
			}

			if ( charitable_is_page( 'donation_processing_page' ) ) {
				return $this->get_donation_processing_template( $template );
			}

			if ( charitable_is_page( 'campaign_donation_page', array( 'strict' => true ) ) ) {
				return $this->get_donate_template( $template );
			}

			if ( charitable_is_page( 'campaign_widget_page' ) ) {
				return $this->get_widget_template( $template );
			}

			if ( charitable_is_page( 'email_preview' ) ) {
				return $this->get_email_template( $template );
			}

			if ( charitable_is_page( 'forgot_password_page' ) ) {
				return $this->get_forgot_password_template( $template );
			}

			if ( charitable_is_page( 'reset_password_page' ) ) {
				return $this->get_reset_password_template( $template );
			}

			return $template;
		}

		/**
		 * Load the donation receipt template if we're looking at a donation receipt.
		 *
		 * @param   string $template
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_donation_receipt_template( $template ) {

			if ( 'auto' != charitable_get_option( 'donation_receipt_page', 'auto' ) ) {
				return $template;
			}

			$donation_receipt_page_title = apply_filters( 'charitable_donation_receipt_page_title', __( 'Your Receipt', 'charitable' ) );

			new Charitable_Ghost_Page( 'donation-receipt-page', array(
				'title'     => $donation_receipt_page_title,
				'content'   => sprintf( '<p>%s</p>', __( 'Thank you for your donation!', 'charitable' ) ),
			) );

			$new_template = apply_filters( 'charitable_donation_receipt_page_template', array( 'donation-receipt-page.php', 'page.php', 'index.php' ) );

			return charitable_get_template_path( $new_template, $template );

		}

		/**
		 * Load the donation processing template if we're looking at the donation processing page.
		 *
		 * @param   string $template
		 * @return  string
		 * @access  protected
		 * @since   1.2.0
		 */
		protected function get_donation_processing_template( $template ) {

			new Charitable_Ghost_Page( 'donation-processing-page', array(
				'title'     => __( 'Thank you for your donation', 'charitable' ),
				'content'   => sprintf( '<p>%s</p>', __( 'You will shortly be redirected to the payment gateway to complete your donation.', 'charitable' ) ),
			) );

			$new_template = apply_filters( 'charitable_donation_processing_page_template', array( 'donation-processing-page.php', 'page.php', 'index.php' ) );

			return charitable_get_template_path( $new_template, $template );

		}

		/**
		 * Load the donation template if we're looking at the donate page.
		 *
		 * @param   string $template
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_donate_template( $template ) {

			/* If a donation ID is included, make sure it belongs to the current user. */
			$donation_id = get_query_var( 'donation_id', false );

			if ( $donation_id ) {

				$donation = charitable_get_donation( $donation_id );

				if ( ! $donation || ! $donation->is_from_current_user() ) {

					wp_safe_redirect( charitable_get_permalink( 'campaign_donation_page' ) );
					exit();

				}
			}

			/* If the campaign has exired, redirect the user to the campaign page. */
			$campaign = charitable_get_current_campaign();

			if ( ! $campaign || $campaign->has_ended() ) {

				wp_safe_redirect( get_permalink( $campaign->ID ) );
				exit();

			}

			do_action( 'charitable_is_donate_page' );

			$new_template = apply_filters( 'charitable_donate_page_template', array( 'campaign-donation-page.php', 'page.php', 'index.php' ) );

			return charitable_get_template_path( $new_template, $template );

		}

		/**
		 * Load the widget template if we're looking at the widget page.
		 *
		 * @param   string $template
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_widget_template( $template ) {

			do_action( 'charitable_is_widget' );

			add_filter( 'show_admin_bar', '__return_false' );
			add_action( 'wp_head', 'charitable_hide_admin_bar' );

			$new_template = apply_filters( 'charitable_widget_page_template', 'campaign-widget.php' );
			return charitable_get_template_path( $new_template, $template );

		}

		/**
		 * Load the email template if we're looking at the email page.
		 *
		 * @param   string $template
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_email_template( $template ) {

			do_action( 'charitable_email_preview' );

			return charitable_get_template_path( 'emails/preview.php' );

		}

		/**
		 * Load the forgot password template.
		 *
		 * @param   string $template
		 * @return  string
		 * @access  protected
		 * @since   1.4.0
		 */
		protected function get_forgot_password_template( $template ) {

			if ( 'wp' == charitable_get_option( 'login_page', 'wp' ) ) {
				return $template;
			}

			new Charitable_Ghost_Page( 'forgot-password-page', array(
				'title'   => __( 'Forgot Password', 'charitable' ),
				'content' => '<!-- Silence is golden -->',
			) );

			$new_template = apply_filters( 'charitable_forgot_password_page_template', array(
				'forgot-password-page.php',
				'page.php',
				'index.php',
			) );

			return charitable_get_template_path( $new_template, $template );

		}

		/**
		 * Load the reset password template.
		 *
		 * @param   string $template
		 * @return  string
		 * @access  protected
		 * @since   1.4.0
		 */
		protected function get_reset_password_template( $template ) {

			if ( 'wp' == charitable_get_option( 'login_page', 'wp' ) ) {
				return $template;
			}

			new Charitable_Ghost_Page( 'reset-password-page', array(
				'title'   => __( 'Reset Password', 'charitable' ),
				'content' => '<!-- Silence is golden -->',
			) );

			$new_template = apply_filters( 'charitable_reset_password_page_template', array(
				'reset-password-page.php',
				'page.php',
				'index.php',
			) );

			return charitable_get_template_path( $new_template, $template );

		}

		/***********************************************/
		/* HERE BE DEPRECATED METHODS
		/***********************************************/

		/**
		 * @deprecated 1.3.0
		 */
		public function add_donation_page_body_class( $classes ) {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.0',
				'charitable_add_body_classes()'
			);

			return $classes;
		}

		/**
		 * @deprecated 1.3.0
		 */
		public function add_widget_page_body_class( $classes ) {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.0',
				'charitable_add_body_classes()'
			);

			return $classes;
		}

		/**
		 * @deprecated 1.3.0
		 */
		public function remove_admin_bar_from_widget_template() {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.0',
				'charitable_hide_admin_bar()'
			);

			return charitable_hide_admin_bar();
		}

		/**
		 * @deprecated 1.3.0
		 */
		public function donation_receipt_template( $template ) {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.0',
				'Charitable_Templates::template_loader() or Charitable_Templates::get_donation_receipt_template()'
			);

			return $this->get_donation_receipt_template( $template );
		}

		/**
		 * @deprecated 1.3.0
		 */
		public function donation_processing_template( $template ) {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.0',
				'Charitable_Templates::template_loader() or Charitable_Templates::get_donation_processing_template()'
			);

			return $this->get_donation_processing_template( $template );
		}

		/**
		 * @deprecated 1.3.0
		 */
		public function donate_template( $template ) {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.0',
				'Charitable_Templates::template_loader() or Charitable_Templates::get_donate_template()'
			);

			return $this->get_donate_template( $template );
		}

		/**
		 * @deprecated 1.3.0
		 */
		public function widget_template( $template ) {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.0',
				'Charitable_Templates::template_loader() or Charitable_Templates::get_widget_template()'
			);

			return $this->get_widget_template( $template );
		}

		/**
		 * @deprecated 1.3.0
		 */
		public function email_template( $template ) {
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.0',
				'Charitable_Templates::template_loader() or Charitable_Templates::get_email_template()'
			);

			return $this->get_email_template( $template );
		}
	}

endif; // End class_exists check
