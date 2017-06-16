<?php
/**
 * Registration shortcode class.
 *
 * @version     1.0.0
 * @package     Charitable/Shortcodes/Registration
 * @category    Class
 * @author      Eric Daams
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Registration_Shortcode' ) ) :

	/**
	 * Charitable_Registration_Shortcode class.
	 *
	 * @since       1.0.0
	 */
	class Charitable_Registration_Shortcode {

		/**
		 * The callback method for the campaigns shortcode.
		 *
		 * This receives the user-defined attributes and passes the logic off to the class.
		 *
		 * @param   array   $atts   User-defined shortcode attributes.
		 * @return  string
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function display( $atts = array() ) {

			$defaults = array(
				'logged_in_message' => __( 'You are already logged in!', 'charitable' ),
				'redirect'          => false,
				'login_link_text'   => __( 'Signed up already? Login instead.', 'charitable' ),
			);

			$args = shortcode_atts( $defaults, $atts, 'charitable_registration' );

			ob_start();

			if ( is_user_logged_in() ) {

				charitable_template( 'shortcodes/logged-in.php', $args );

				return ob_get_clean();

			}

			charitable_template( 'shortcodes/registration.php', array(
				'form' => new Charitable_Registration_Form( $args ),
			) );

			return apply_filters( 'charitable_registration_shortcode', ob_get_clean() );

		}
	}

endif;
