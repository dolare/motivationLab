<?php
/**
 * Class that sets up the gateways.
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Gateways
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Gateways' ) ) :

	/**
	 * Charitable_Gateways
	 *
	 * @since 		1.0.0
	 */
	class Charitable_Gateways {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Gateways|null
		 * @access  private
		 * @static
		 */
		private static $instance = null;

		/**
		 * All available payment gateways.
		 *
		 * @var 	array
		 * @access  private
		 */
		private $gateways;

		/**
		* Set up the class.
		 *
		 * Note that the only way to instantiate an object is with the charitable_start method,
		 * which can only be called during the start phase. In other words, don't try
		 * to instantiate this object.
		 *
		 * @access 	protected
		 * @since 	1.0.0
		 */
		protected function __construct() {
			add_action( 'init', array( $this, 'register_gateways' ) );
			add_action( 'charitable_make_default_gateway', array( $this, 'handle_gateway_settings_request' ) );
			add_action( 'charitable_enable_gateway', array( $this, 'handle_gateway_settings_request' ) );
			add_action( 'charitable_disable_gateway', array( $this, 'handle_gateway_settings_request' ) );
			add_filter( 'charitable_settings_fields_gateways_gateway', array( $this, 'register_gateway_settings' ), 10, 2 );

			do_action( 'charitable_gateway_start', $this );
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @return  Charitable_Gateways
		 * @access  public
		 * @since   1.2.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Gateways();
			}

			return self::$instance;
		}

		/**
		 * Register gateways.
		 *
		 * To register a new gateway, you need to hook into the `charitable_payment_gateways`
		 * hook and give Charitable the name of your gateway class.
		 *
		 * @return  string[]
		 * @access  public
		 * @since   1.2.0
		 */
		public function register_gateways() {
			$this->gateways = apply_filters( 'charitable_payment_gateways', array(
				'offline' => 'Charitable_Gateway_Offline',
				'paypal'  => 'Charitable_Gateway_Paypal',
			) );
		}

		/**
		 * Receives a request to enable or disable a payment gateway and validates it before passing it off.
		 *
		 * @param 	array
		 * @return 	array
		 * @access 	public
		 * @since 	1.0.0
		 */
		public function handle_gateway_settings_request() {
			if ( ! wp_verify_nonce( $_REQUEST['_nonce'], 'gateway' ) ) {
				wp_die( __( 'Cheatin\' eh?!', 'charitable' ) );
			}

			$gateway = isset( $_REQUEST['gateway_id'] ) ? $_REQUEST['gateway_id'] : false;

			/* Gateway must be set */
			if ( false === $gateway ) {
				wp_die( __( 'Missing gateway.', 'charitable' ) );
			}

			/* Validate gateway. */
			if ( ! isset( $this->gateways[ $gateway ] ) ) {
				wp_die( __( 'Invalid gateway.', 'charitable' ) );
			}

			switch ( $_REQUEST['charitable_action'] ) {
				case 'disable_gateway' :
					$this->disable_gateway( $gateway );
					break;
				case 'enable_gateway' :
					$this->enable_gateway( $gateway );
					break;
				case 'make_default_gateway' :
					$this->set_default_gateway( $gateway );
					break;
				default :
					do_action( 'charitable_gateway_settings_request', $_REQUEST['charitable_action'], $gateway );
			}
		}

		/**
		 * Returns all available payment gateways.
		 *
		 * @return 	string
		 * @access  public
		 * @since 	1.0.0
		 */
		public function get_available_gateways() {
			return $this->gateways;
		}

		/**
		 * Returns the current active gateways.
		 *
		 * @return 	string[]
		 * @access  public
		 * @since 	1.0.0
		 */
		public function get_active_gateways() {
			$active_gateways = charitable_get_option( 'active_gateways', array() );

			foreach ( $active_gateways as $gateway_id => $gateway_class ) {
				if ( ! class_exists( $gateway_class ) ) {
					unset( $active_gateways[ $gateway_id ] );
				}
			}

			uksort( $active_gateways, array( $this, 'sort_by_default' ) );

			return apply_filters( 'charitable_active_gateways', $active_gateways );
		}

		/**
		 * Returns an array of the active gateways, in ID => name format.
		 *
		 * This is useful for select/radio input fields.
		 *
		 * @return  string[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_gateway_choices() {
			$gateways = array();

			foreach ( $this->get_active_gateways() as $id => $class ) {
				$gateway = new $class;
				$gateways[ $id ] = $gateway->get_label();
			}

			return $gateways;
		}

		/**
		 * Returns a text description of the active gateways.
		 *
		 * @return  string[]
		 * @access  public
		 * @since   1.3.0
		 */
		public function get_active_gateways_names() {
			$gateways = array();

			foreach ( $this->get_active_gateways() as $id => $class ) {
				$gateway = new $class;
				$gateways[] = $gateway->get_name();
			}

			return $gateways;
		}

		/**
		 * Return the gateway class name for a given gateway.
		 *
		 * @param 	string 	$gateway
		 * @return  string|false
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_gateway( $gateway ) {
			return isset( $this->gateways[ $gateway ] ) ? $this->gateways[ $gateway ] : false;
		}

		/**
		 * Return the gateway object for a given gateway.
		 *
		 * @param 	string $gateway
		 * @return  Charitable_Gateway|null
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_gateway_object( $gateway ) {
			$class = $this->get_gateway( $gateway );
			return $class ? new $class : null;
		}

		/**
		 * Returns whether the passed gateway is active.
		 *
		 * @param 	string $gateway_id
		 * @return 	boolean
		 * @access  public
		 * @since 	1.0.0
		 */
		public function is_active_gateway( $gateway_id ) {
			return array_key_exists( $gateway_id, $this->get_active_gateways() );
		}

		/**
		 * Checks whether the submitted gateway is valid.
		 *
		 * @param 	string $gateway
		 * @return  boolean
		 * @access  public
		 * @since   1.4.3
		 */
		public function is_valid_gateway( $gateway ) {
			return apply_filters( 'charitable_is_valid_gateway', array_key_exists( $gateway, $this->gateways ), $gateway );
		}

		/**
		 * Returns the default gateway.
		 *
		 * @return 	string
		 * @access  public
		 * @since 	1.0.0
		 */
		public function get_default_gateway() {
			return charitable_get_option( 'default_gateway', '' );
		}

		/**
		 * Provide default gateway settings fields.
		 *
		 * @param 	array $settings
		 * @param 	Charitable_Gateway $gateway 	The gateway's helper object.
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function register_gateway_settings( $settings, Charitable_Gateway $gateway ) {
			add_filter( 'charitable_settings_fields_gateways_gateway_' . $gateway->get_gateway_id(), array( $gateway, 'default_gateway_settings' ), 5 );
			add_filter( 'charitable_settings_fields_gateways_gateway_' . $gateway->get_gateway_id(), array( $gateway, 'gateway_settings' ), 15 );
			return apply_filters( 'charitable_settings_fields_gateways_gateway_' . $gateway->get_gateway_id(), $settings );
		}

		/**
		 * Returns true if test mode is enabled.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function in_test_mode() {
			$enabled = charitable_get_option( 'test_mode', false );
			return apply_filters( 'charitable_in_test_mode', $enabled );
		}

		/**
		 * Checks whether all of the active gateways support a feature.
		 *
		 * If ANY gateway doesn't support the feature, this returns false.
		 *
		 * @param 	string $feature
		 * @return  boolean
		 * @access  public
		 * @since   1.4.0
		 */
		public function all_gateways_support( $feature ) {
			foreach ( $this->get_active_gateways() as $gateway_id => $gateway_class ) {

				$gateway_object = new $gateway_class;

				if ( false === $gateway_object->supports( $feature ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Checks whether any of the active gateways support a feature.
		 *
		 * If any gateway supports the feature, this returns true. Otherwise false.
		 *
		 * @param 	string $feature
		 * @return  boolean
		 * @access  public
		 * @since   1.4.0
		 */
		public function any_gateway_supports( $feature ) {
			foreach ( $this->get_active_gateways() as $gateway_id => $gateway_class ) {

				$gateway_object = new $gateway_class;

				if ( true === $gateway_object->supports( $feature ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Checks whether all of the active gateways support AJAX.
		 *
		 * If ANY gateway doesn't support AJAX, this returns false.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.3.0
		 */
		public function gateways_support_ajax() {
			return $this->all_gateways_support( '1.3.0' );
		}

		/**
		 * Sets the default gateway.
		 *
		 * @param 	string 	$gateway
		 * @return  void
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function set_default_gateway( $gateway ) {
			$settings = get_option( 'charitable_settings' );
			$settings['default_gateway'] = $gateway;

			update_option( 'charitable_settings', $settings );

			charitable_get_admin_notices()->add_success( __( 'Default Gateway Updated', 'charitable' ) );

			do_action( 'charitable_set_gateway_gateway', $gateway );
		}

		/**
		 * Enable a payment gateway.
		 *
		 * @param 	string 	$gateway
		 * @return  void
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function enable_gateway( $gateway ) {
			$settings = get_option( 'charitable_settings' );

			$active_gateways = isset( $settings['active_gateways'] ) ? $settings['active_gateways'] : array();
			$active_gateways[ $gateway ] = $this->gateways[ $gateway ];
			$settings['active_gateways'] = $active_gateways;

			/* If this is the only gateway, make it the default gateway */
			if ( 1 == count( $settings['active_gateways'] ) ) {
				$settings['default_gateway'] = $gateway;
			}

			update_option( 'charitable_settings', $settings );

			Charitable_Settings::get_instance()->add_update_message( __( 'Gateway enabled', 'charitable' ), 'success' );

			do_action( 'charitable_gateway_enable', $gateway );
		}

		/**
		 * Disable a payment gateway.
		 *
		 * @param 	string 	$gateway
		 * @return  void
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function disable_gateway( $gateway ) {
			$settings = get_option( 'charitable_settings' );

			if ( ! isset( $settings['active_gateways'][ $gateway ] ) ) {
				return;
			}

			unset( $settings['active_gateways'][ $gateway ] );

			/* Set a new default gateway */
			if ( $gateway == $this->get_default_gateway() ) {

				$settings['default_gateway'] = count( $settings['active_gateways'] ) ? key( $settings['active_gateways'] ) : '';

			}

			update_option( 'charitable_settings', $settings );

			Charitable_Settings::get_instance()->add_update_message( __( 'Gateway disabled', 'charitable' ), 'success' );

			do_action( 'charitable_gateway_disable', $gateway );
		}

		/**
		 * Sort the active gateways, placing the default gateway first.
		 *
		 * @param 	
		 * @return  int
		 * @access  protected
		 * @since   1.4.0
		 */
		protected function sort_by_default( $a, $b ) {
			$default = $this->get_default_gateway();

			if ( $a == $default ) {
				return -1;
			} 

			if ( $b == $default ) {
				return 1;
			}

			return 0;
		}
	}

endif; // End class_exists check
