<?php
/**
 * Gateway abstract model
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Gateway
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Gateway' ) ) :

	/**
	 * Charitable_Gateway
	 *
	 * @abstract
	 * @since		1.0.0
	 */
	abstract class Charitable_Gateway implements Charitable_Gateway_Interface {

		/**
		 * @var     string The gateway's unique identifier.
		 */
		const ID = '';

		/**
		 * @var     string Name of the payment gateway.
		 * @access  protected
		 * @since   1.0.0
		 */
		protected $name;

		/**
		 * @var     array The default values for all settings added by the gateway.
		 * @access  protected
		 * @since   1.0.0
		 */
		protected $defaults;

		/**
		 * Supported features such as 'credit-card', and 'recurring' donations
		 *
		 * @var     string[]
		 * @access  protected
		 * @since   1.3.0
		 */
		protected $supports = array();

		/**
		 * Return the gateway name.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_name() {
			return $this->name;
		}

		/**
		 * Returns the default gateway label to be displayed to donors.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_default_label() {
			return isset( $this->defaults['label'] ) ? $this->defaults['label'] : $this->get_name();
		}

		/**
		 * Provide default gateway settings fields.
		 *
		 * @param   array   $settings
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function default_gateway_settings( $settings ) {
			return array(
				'section_gateway' => array(
					'type'      => 'heading',
					'title'     => $this->get_name(),
					'priority'  => 2,
				),
				'label' => array(
					'type'      => 'text',
					'title'     => __( 'Gateway Label', 'charitable' ),
					'help'      => __( 'The label that will be shown to donors on the donation form.', 'charitable' ),
					'priority'  => 4,
					'default'   => $this->get_default_label(),
				),
			);
		}

		/**
		 * Return the settings for this gateway.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_settings() {
			return charitable_get_option( 'gateways_' . $this->get_gateway_id(), array() );
		}

		/**
		 * Retrieve the gateway label.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_label() {
			return charitable_get_option( 'label', $this->get_default_label(), $this->get_settings() );
		}

		/**
		 * Return the value for a particular gateway setting.
		 *
		 * @param   string $setting
		 * @return  mixed
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_value( $setting ) {
			$default = isset( $this->defaults[ $setting ] ) ? $this->defaults[ $setting ] : '';
			return charitable_get_option( $setting, $default, $this->get_settings() );
		}

		/**
		 * Check if a gateway supports a given feature.
		 *
		 * Gateways should override this to declare support (or lack of support) for a feature.
		 *
		 * @param   string $feature string The name of a feature to test support for.
		 * @return  bool True if the gateway supports the feature, false otherwise.
		 * @since   1.3.0
		 */
		public function supports( $feature ) {
			$supported = in_array( $feature, $this->supports ) ? true : false;

			/* Provide backwards compatibility for gateways that have not been updated. */
			if ( ! $supported && 'credit-card' == $feature && isset( $this->credit_card_form ) ) {
				$supported = $this->credit_card_form;
			}

			return apply_filters( 'charitable_payment_gateway_supports', $supported, $feature, $this );
		}

		/**
		 * Returns an array of credit card fields.
		 *
		 * If the gateway requires different fields, this can simply be redefined
		 * in the child class.
		 *
		 * @return  array[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_credit_card_fields() {
			return apply_filters( 'charitable_credit_card_fields', array(
				'cc_name' => array(
					'label'     => __( 'Name on Card', 'charitable' ),
					'type'      => 'text',
					'required'  => true,
					'priority'  => 2,
					'data_type' => 'gateway',
				),
				'cc_number' => array(
					'label'     => __( 'Card Number', 'charitable' ),
					'type'      => 'text',
					'required'  => true,
					'priority'  => 4,
					'data_type' => 'gateway',
				),
				'cc_cvc' => array(
					'label'     => __( 'CVV Number', 'charitable' ),
					'type'      => 'text',
					'required'  => true,
					'priority'  => 6,
					'data_type' => 'gateway',
				),
				'cc_expiration' => array(
					'label'     => __( 'Expiration', 'charitable' ),
					'type'      => 'cc-expiration',
					'required'  => true,
					'priority'  => 8,
					'data_type' => 'gateway',
				),
			), $this );
		}

		/**
		 * Redirect the donation to the processing page.
		 *
		 * @param   mixed $result
		 * @param   int $donation_id
		 * @return  array
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function redirect_to_processing( $result, $donation_id ) {
			return array(
				'redirect' => charitable_get_permalink( 'donation_processing_page', array(
					'donation_id' => $donation_id,
					)
				),
				'safe' => true,
			);
		}

		/**
		 * Returns whether a credit card form is required for this gateway.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 *
		 * @deprecated
		 */
		public function requires_credit_card_form() {
			charitable_get_deprecated()->deprecated_function( __METHOD__, '1.3.0', 'Charitable_Gateway::supports( \'credit-card\' )' );
			return $this->supports( 'credit-card' );
		}

		/**
		 * Register gateway settings.
		 *
		 * @param   array   $settings
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		abstract public function gateway_settings( $settings );
	}

endif; // End class_exists check
