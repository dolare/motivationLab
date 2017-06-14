 <?php
/**
 * Charitable Session class.
 *
 * The responsibility of this class is to manager the user sessions.
 *
 * @package		Charitable
 * @subpackage	Charitable/Charitable Session
 * @copyright 	Copyright (c) 2017, Eric Daams
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 		1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Session' ) ) :

	/**
	 * Charitable_Session
	 *
	 * @since 		1.0.0
	 */
	class Charitable_Session {

		/**
	     * The single instance of this class.
	     *
	     * @var     Charitable_Session|null
	     * @access  private
	     * @static
	     */
	    private static $instance = null;

		/**
		 * Holds our session data
		 *
		 * @var 	array
		 * @access 	private
		 * @since 	1.0.0
		 */
		private $session;

		/**
		 * Instantiate session object. Private constructor.
		 *
		 * @access 	private
		 * @since 	1.0.0
		 */
		private function __construct() {
			if ( ! defined( 'WP_SESSION_COOKIE' ) ) {
				define( 'WP_SESSION_COOKIE', 'charitable_session' );
			}

			if ( ! class_exists( 'Recursive_ArrayAccess' ) ) {
				require_once( charitable()->get_path( 'includes' ) . 'libraries/wp-session/class-recursive-arrayaccess.php' );
			}

			if ( ! class_exists( 'WP_Session_Utils' ) ) {
				require_once( charitable()->get_path( 'includes' ) . 'libraries/wp-session/class-wp-session-utils.php' );
			}

			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				require_once( charitable()->get_path( 'includes' ) . 'libraries/wp-session/wp-cli.php' );
			}

			if ( ! class_exists( 'WP_Session' ) ) {
				require_once( charitable()->get_path( 'includes' ) . 'libraries/wp-session/class-wp-session.php' );
				require_once( charitable()->get_path( 'includes' ) . 'libraries/wp-session/wp-session.php' );
			}

			/* Set the expiration length & variant of the session */
			add_filter( 'wp_session_expiration', array( $this, 'set_session_length' ), 99999 );
			add_filter( 'wp_session_expiration_variant', array( $this, 'set_session_expiration_variant_length' ), 99999 );

			$this->session = WP_Session::get_instance();
		}

		/**
	     * Returns and/or create the single instance of this class.
	     *
	     * @return  Charitable_Session
	     * @access  public
	     * @since   1.2.0
	     */
	    public static function get_instance() {
	        if ( is_null( self::$instance ) ) {
	            self::$instance = new Charitable_Session();
	        }

	        return self::$instance;
	    }

		/**
		 * Return a session variable.
		 *
		 * @param 	string $key Session variable key.
		 * @return 	mixed Session variable
		 * @access  public
		 * @since 	1.0.0
		 */
		public function get( $key ) {
			$key = sanitize_key( $key );
			return isset( $this->session[ $key ] ) ? maybe_unserialize( $this->session[ $key ] ) : false;
		}

		/**
		 * Set a session variable.
		 *
		 * @param 	string $key   Session variable key.
		 * @param 	mixed  $value The value of the session variable.
		 * @return 	mixed The session variable value.
		 * @access  public
		 * @since 	1.0.0
		 */
		public function set( $key, $value ) {
			$key = sanitize_key( $key );

			if ( is_array( $value ) ) {
				$this->session[ $key ] = serialize( $value );
			} else {
				$this->session[ $key ] = $value;
			}

			return $this->session[ $key ];
		}

		/**
		 * Set the length of the cookie session to 24 hours.
		 *
		 * @return 	int
		 * @access  public
		 * @since 	1.0.0
		 */
		public function set_session_length() {
			if ( ! defined( 'DAY_IN_SECONDS' ) ) {
				define( 'DAY_IN_SECONDS', 86400 );
			}

			return DAY_IN_SECONDS;
		}

		/**
		 * Set the cookie expiration variant time to 23 hours.
		 *
		 * @return 	void
		 * @access  public
		 * @since 	1.0.0
		 */
		public function set_session_expiration_variant_length() {
			if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
				define( 'HOUR_IN_SECONDS', 3600 );
			}

			return HOUR_IN_SECONDS * 23;
		}

		/**
		 * Add a donation to a campaign to the session.
		 *
		 * @param 	int 	$campaign_id
		 * @param 	int 	$amount
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_donation( $campaign_id, $amount ) {
			$donations = $this->get( 'donations' );

			$campaign_donation = isset( $donations[ $campaign_id ] ) ? $donations[ $campaign_id ] : array();
			$campaign_donation['amount'] = floatval( $amount );

			$donations[ $campaign_id ] = $campaign_donation;

			$this->set( 'donations', $donations );
		}

		/**
		 * Remove a donation from the session.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function remove_donation( $campaign_id ) {
			$donations = $this->get( 'donations' );

			if ( isset( $donations[ $campaign_id ] ) ) {
				unset( $donations[ $campaign_id ] );
			}

			$this->set( 'donations', $donations );
		}

		/**
		 * Return the donation in session for a campaign.
		 *
		 * @param 	int $campaign_id The campaign ID.
		 * @return  false|array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donation_by_campaign( $campaign_id ) {
			$donations = $this->get( 'donations' );
			return isset( $donations[ $campaign_id ] ) ? $donations[ $campaign_id ] : false;
		}

		/**
		 * Store the donation key in the session, to ensure the user can access their receipt.
		 *
		 * @param 	string $donation_key The transaction key for the donation.
		 * @return  void
		 * @access  public
		 * @since   1.1.2
		 */
		public function add_donation_key( $donation_key ) {
			$keys = $this->get( 'donation-keys' );

			if ( ! $keys ) {
				$keys = array();
			}

			$keys[] = $donation_key;

			$this->set( 'donation-keys', $keys );
		}

		/**
		 * Checks whether the donation key is stored in the session.
		 *
		 * @param 	string $donation_key The transaction key for the donation.
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function has_donation_key( $donation_key ) {
			$keys = $this->get( 'donation-keys' );

			if ( ! $keys ) {
				return false;
			}

			return in_array( $donation_key, $keys );
		}

		/**
		 * Add the all notices to the session.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_notices() {
			$this->set( 'notices', charitable_get_notices()->get_notices() );
		}

		/**
		 * Return any notices set in the session.
		 *
		 * @return 	array Session variable
		 * @access  public
		 * @since 	1.4.0
		 */
		public function get_notices() {
			$notices = $this->get( 'notices' );

			if ( $notices ) {
				return $notices;
			}

			return array(
				'error'		=> array(),
				'warning'	=> array(),
				'success'	=> array(),
				'info'		=> array(),
			);
		}

		/**
		 * Returns the session ID.
		 *
		 * @return 	string Session ID
		 * @access 	public
		 * @since 	1.3.5
		 */
		public function get_session_id() {
			return $this->session->session_id;
		}
	}

endif;
