 <?php
/**
 * Offline Payment Gateway
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Gateway_Offline
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Gateway_Offline' ) ) :

	/**
	 * Offline Payment Gateway
	 *
	 * @since		1.0.0
	 */
	class Charitable_Gateway_Offline extends Charitable_Gateway {

		/**
		 * @var     string
		 */
		const ID = 'offline';

		/**
		 * Whether the gateway supports Charitable 1.3.0.
		 *
		 * @var     boolean
		 * @access  protected
		 * @since   1.3.0
		 */
		protected $supports_130 = false;

		/**
		 * Instantiate the gateway class, defining its key values.
		 *
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->name = apply_filters( 'charitable_gateway_offline_name', __( 'Offline', 'charitable' ) );

			$this->defaults = array(
				'label' => __( 'Offline Donation', 'charitable' ),
				'instructions' => __( 'Thank you for your donation. We will contact you shortly for payment.', 'charitable' ),
			);

			$this->supports = array(
				'recurring',
				'1.3.0',				
			);
		}

		/**
		 * Register gateway settings.
		 *
		 * @param   array $settings
		 * @return  array
		 * @access  public
		 * @since   1.0.0
	 	*/
		public function gateway_settings( $settings ) {
			$settings['instructions'] = array(
				'type'      => 'textarea',
				'title'     => __( 'Instructions', 'charitable' ),
				'help'      => __( 'These are the instructions you provide to donors after they make a donation.', 'charitable' ),
				'priority'  => 6,
				'default'   => $this->defaults['instructions'],
			);

			return $settings;
		}

		/**
		 * Returns the current gateway's ID.
		 *
		 * @return  string
		 * @access  public
		 * @static
		 * @since   1.0.3
		 */
		public static function get_gateway_id() {
			return self::ID;
		}
	}

endif; // End class_exists check
