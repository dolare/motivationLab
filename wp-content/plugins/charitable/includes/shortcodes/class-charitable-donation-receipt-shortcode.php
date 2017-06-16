<?php
/**
 * Donation Receipt shortcode class.
 *
 * @version     1.2.0
 * @package     Charitable/Shortcodes/Donation Receipt
 * @category    Class
 * @author      Eric Daams
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donation_Receipt_Shortcode' ) ) :

	/**
	 * Charitable_Donation_Receipt_Shortcode class.
	 *
	 * @since       1.2.0
	 */
	class Charitable_Donation_Receipt_Shortcode {

		/**
		 * The callback method for the campaigns shortcode.
		 *
		 * This receives the user-defined attributes and passes the logic off to the class.
		 *
		 * @param   array   $atts   User-defined shortcode attributes.
		 * @return  string
		 * @access  public
		 * @static
		 * @since   1.2.0
		 */
		public static function display( $atts ) {
			return apply_filters( 'charitable_donation_receipt_shortcode', charitable_template_donation_receipt_output( '' ) );
		}
	}

endif;
