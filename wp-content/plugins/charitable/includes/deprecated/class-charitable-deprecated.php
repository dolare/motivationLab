<?php
/**
 * A helper class for logging deprecated arguments, functions and methods.
 *
 * @package     Charitable/Classes/Charitable_Deprecated
 * @version     1.4.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Deprecated' ) ) :

	/**
	 * Charitable_Deprecated
	 *
	 * @since       1.4.0
	 */
	class Charitable_Deprecated {

		/**
		 * @var     Charitable_Deprecated
		 * @access  private
		 * @static
		 * @since   1.4.0
		 */
		private static $instance = null;

		/**
		 * @var     $logging
		 * @access  private
		 * @static
		 * @since   1.4.0
		 */
		private static $logging;

		/**
		 * Create class object. Private constructor.
		 *
		 * @access  private
		 * @since   1.4.0
		 */
		private function __construct() {
		}

		/**
		 * Create and return the class object.
		 *
		 * @access  public
		 * @static
		 * @since   1.4.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Deprecated();
			}

			return self::$instance;
		}

		/**
		 * Log a deprecated argument.
		 *
		 * @param   string $function
		 * @param   string $version
		 * @param   string|null $extra_message
		 * @return  boolean Whether the notice was logged.
		 * @access  public
		 * @since   1.4.0
		 */
		public function deprecated_argument( $function, $version, $extra_message = null ) {
			if ( ! $this->is_logging_enabled() ) {
				return false;
			}

			if ( ! is_null( $extra_message ) ) {
				$message = sprintf( __( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s of Charitable! %3$s', 'charitable' ), $function, $version, $extra_message );
			} else {
				$message = sprintf( __( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s of Charitable with no alternatives available.', 'charitable' ), $function, $version );
			}

			trigger_error( $message );

			return true;
		}

		/**
		 * Log a deprecated function.
		 *
		 * @param   string      $function The function that has been deprecated
		 * @param   string      $version The version of Charitable where the function was deprecated.
		 * @param   string|null $replacement Optional. The function to use instead.
		 * @return  boolean Whether the notice was logged.
		 * @access  public
		 * @since   1.4.0
		 */
		public function deprecated_function( $function, $version, $replacement = null ) {
			if ( ! $this->is_logging_enabled() ) {
				return false;
			}

			if ( ! is_null( $replacement ) ) {
				$message = sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s of Charitable! Use %3$s instead.', 'charitable' ), $function, $version, $replacement );
			} else {
				$message = sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s of Charitable with no alternatives available.', 'charitable' ), $function, $version );
			}

			trigger_error( $message );

			return true;
		}

		/**
		 * Log a general "doing it wrong" notice.
		 *
		 * @param   string $function
		 * @param   string $message
		 * @param   string $version
		 * @return  boolean Whether the notice was logged.
		 * @access  public
		 * @since   1.4.0
		 */
		public function doing_it_wrong( $function, $message, $version ) {
			if ( ! $this->is_logging_enabled() ) {
				return false;
			}

			$version = is_null( $version ) ? '' : sprintf( __( '(This message was added in Charitable version %s.)', 'charitable' ), $version );

			$message = sprintf( __( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s', 'charitable' ), $function, $message, $version );

			trigger_error( $message );

			return true;
		}

		/**
		 * Returns whether logging is enabled.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.4.0
		 */
		private function is_logging_enabled() {
			if ( ! isset( self::$logging ) ) {
				self::$logging = WP_DEBUG && apply_filters( 'charitable_log_deprecated_notices', true );
			}

			return self::$logging;
		}
	}

endif; // End class_exists check
