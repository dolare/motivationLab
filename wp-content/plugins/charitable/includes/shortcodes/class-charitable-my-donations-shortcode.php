<?php
/**
 * My Donations shortcode class.
 *
 * @version     1.4.0
 * @package     Charitable/Shortcodes/My Donations
 * @category    Class
 * @author      Eric Daams
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_My_Donations_Shortcode' ) ) :

	/**
	 * Charitable_My_Donations_Shortcode class.
	 *
	 * @since 	1.4.0
	 */
	class Charitable_My_Donations_Shortcode {

		/**
		 * The callback method for the campaigns shortcode.
		 *
		 * This receives the user-defined attributes and passes the logic off to the class.
		 *
		 * @param   array $atts User-defined shortcode attributes.
		 * @return  string
		 * @access  public
		 * @static
		 * @since   1.4.0
		 */
		public static function display( $atts ) {
			$defaults = array();

			$args = shortcode_atts( $defaults, $atts, 'charitable_my_donations' );

			ob_start();

			/* If the user is logged out, redirect to login/registration page. */
			if ( ! is_user_logged_in() ) {

				echo Charitable_Login_Shortcode::display( array(
					'redirect' => charitable_get_current_url(),
				) );

				return;
	        }

	        $donor_id = charitable_get_user( get_current_user_id() )->get_donor_id();

	        /* Only fetch donations if the donor ID exists. */
	        if ( $donor_id ) {

	        	$donations = new Charitable_Donations_Query( array(
					'output'   => 'posts',
					'donor_id' => $donor_id,
					'orderby'  => 'date',
					'order'    => 'DESC',
					'number'   => -1,
				) );

	        } else {

	        	$donations = array();

	        }

	        $view_args = array(
				'donations' => $donations,
			);

			charitable_template( 'shortcodes/my-donations.php', $view_args );

			return apply_filters( 'charitable_my_donations_shortcode', ob_get_clean(), $view_args, $args );
		}
	}

endif;
