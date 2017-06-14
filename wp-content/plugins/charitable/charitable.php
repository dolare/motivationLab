 <?php
/**
 * Plugin Name:         Charitable
 * Plugin URI:          https://www.wpcharitable.com
 * Description:         The WordPress fundraising alternative for non-profits, created to help non-profits raise money on their own website.
 * Version:             1.4.16
 * Author:              WP Charitable
 * Author URI:          https://wpcharitable.com
 * Requires at least:   4.1
 * Tested up to:        4.7.4
 *
 * Text Domain:         charitable
 * Domain Path:         /i18n/languages/
 *
 * @package             Charitable
 * @author              Eric Daams
 * @copyright           Copyright (c) 2017, Studio 164a
 * @license             http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable' ) ) :

	/**
	 * Main Charitable class
	 *
	 * @class       Charitable
	 * @version     1.4.0
	 */
	class Charitable {

		/**
		 * Current plugin version.
		 *
		 * @var     string
		 */
		const VERSION = '1.4.16';

		/**
		 * Database version.
		 *
		 * @var     string A date in the format: YYYYMMDD
		 */
		const DB_VERSION = '20150615';

		/**
		 * The Campaign post type.
		 *
		 * @var     string
		 */
		const CAMPAIGN_POST_TYPE = 'campaign';

		/**
		 * The Donation post type.
		 *
		 * @var     string
		 */
		const DONATION_POST_TYPE = 'donation';

		/**
		 * Single class object.
		 *
		 * @var     Charitable
		 * @access  private
		 */
		private static $instance = null;

		/**
		 * Directory path for the plugin.
		 *
		 * @var     string
		 * @access  private
		 */
		private $directory_path;

		/**
		 * Directory url for the plugin.
		 *
		 * @var     string
		 * @access  private
		 */
		private $directory_url;

		/**
		 * Directory path for the includes folder of the plugin.
		 *
		 * @var     string
		 * @access  private
		 */
		private $includes_path;

		/**
		 * Store of registered objects.
		 *
		 * @var     array
		 * @access  private
		 */
		private $registry;

		/**
		 * Donation factory instance.
		 *
		 * @var Charitable_Donation_Factory
		 */
		public $donation_factory = null;

		/**
		 * Create class instance.
		 *
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->directory_path = plugin_dir_path( __FILE__ );
			$this->directory_url  = plugin_dir_url( __FILE__ );
			$this->includes_path  = $this->directory_path . 'includes/';

			$this->load_dependencies();

			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

			add_action( 'plugins_loaded', array( $this, 'start' ), 1 );
		}

		/**
		 * Returns the original instance of this class.
		 *
		 * @return  Charitable
		 * @since   1.0.0
		 */
		public static function get_instance() {
			return self::$instance;
		}

		/**
		 * Run the startup sequence.
		 *
		 * This is only ever executed once.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function start() {
			// If we've already started (i.e. run this function once before), do not pass go.
			if ( $this->started() ) {
				return;
			}

			// Set static instance.
			self::$instance = $this;

			// Factory to create new donation instances.
			$this->donation_factory = new Charitable_Donation_Factory();

			$this->maybe_start_ajax();

			$this->attach_hooks_and_filters();

			$this->maybe_start_admin();

			$this->maybe_start_public();

			Charitable_Addons::load( $this );
		}

		/**
		 * Include necessary files.
		 *
		 * @return  void
		 * @access  private
		 * @since   1.0.0
		 */
		private function load_dependencies() {
			$includes_path = $this->get_path( 'includes' );

			/* Abstracts */
			require_once( $includes_path . 'abstracts/class-charitable-form.php' );
			require_once( $includes_path . 'abstracts/class-charitable-query.php' );
			require_once( $includes_path . 'abstracts/class-charitable-start-object.php' );

			/* Functions & Core Classes */
			require_once( $includes_path . 'charitable-core-functions.php' );
			require_once( $includes_path . 'charitable-utility-functions.php' );
			require_once( $includes_path . 'class-charitable-locations.php' );
			require_once( $includes_path . 'class-charitable-notices.php' );
			require_once( $includes_path . 'class-charitable-post-types.php' );
			require_once( $includes_path . 'class-charitable-request.php' );
			require_once( $includes_path . 'class-charitable-cron.php' );
			require_once( $includes_path . 'class-charitable-i18n.php' );

			/* Addons */
			require_once( $includes_path . 'addons/class-charitable-addons.php' );

			/* Campaigns */
			require_once( $includes_path . 'campaigns/charitable-campaign-functions.php' );
			require_once( $includes_path . 'campaigns/class-charitable-campaign.php' );
			require_once( $includes_path . 'campaigns/class-charitable-campaigns.php' );
			require_once( $includes_path . 'campaigns/charitable-campaign-hooks.php' );

			/* Currency */
			require_once( $includes_path . 'currency/charitable-currency-functions.php' );
			require_once( $includes_path . 'currency/class-charitable-currency.php' );

			/* Donations */
			require_once( $includes_path . 'donations/abstract-charitable-donation.php' );
			require_once( $includes_path . 'donations/interface-charitable-donation-form.php' );
			require_once( $includes_path . 'donations/class-charitable-donation-processor.php' );
			require_once( $includes_path . 'donations/class-charitable-donation.php' );
			require_once( $includes_path . 'donations/class-charitable-donation-factory.php' );
			require_once( $includes_path . 'donations/class-charitable-donations.php' );
			require_once( $includes_path . 'donations/class-charitable-donations-query.php' );
			require_once( $includes_path . 'donations/class-charitable-donation-form.php' );
			require_once( $includes_path . 'donations/class-charitable-donation-amount-form.php' );
			require_once( $includes_path . 'donations/charitable-donation-hooks.php' );
			require_once( $includes_path . 'donations/charitable-donation-functions.php' );

			/* Users */
			require_once( $includes_path . 'users/charitable-user-functions.php' );
			require_once( $includes_path . 'users/class-charitable-user.php' );
			require_once( $includes_path . 'users/class-charitable-roles.php' );
			require_once( $includes_path . 'users/class-charitable-donor.php' );
			require_once( $includes_path . 'users/class-charitable-donor-query.php' );

			/* Gateways */
			require_once( $includes_path . 'gateways/interface-charitable-gateway.php' );
			require_once( $includes_path . 'gateways/class-charitable-gateways.php' );
			require_once( $includes_path . 'gateways/abstract-class-charitable-gateway.php' );
			require_once( $includes_path . 'gateways/class-charitable-gateway-offline.php' );
			require_once( $includes_path . 'gateways/class-charitable-gateway-paypal.php' );

			/* Emails */
			require_once( $includes_path . 'emails/interface-charitable-email.php' );
			require_once( $includes_path . 'emails/class-charitable-emails.php' );
			require_once( $includes_path . 'emails/abstract-class-charitable-email.php' );
			require_once( $includes_path . 'emails/class-charitable-email-new-donation.php' );
			require_once( $includes_path . 'emails/class-charitable-email-donation-receipt.php' );
			require_once( $includes_path . 'emails/class-charitable-email-campaign-end.php' );
			require_once( $includes_path . 'emails/class-charitable-email-password-reset.php' );
			require_once( $includes_path . 'emails/charitable-email-hooks.php' );

			/* Database */
			require_once( $includes_path . 'db/abstract-class-charitable-db.php' );
			require_once( $includes_path . 'db/class-charitable-campaign-donations-db.php' );
			require_once( $includes_path . 'db/class-charitable-donors-db.php' );

			/* Licensing */
			require_once( $includes_path . 'licensing/class-charitable-licenses.php' );
			require_once( $includes_path . 'licensing/class-charitable-plugin-updater.php' );

			/* Public */
			require_once( $includes_path . 'public/charitable-page-functions.php' );
			require_once( $includes_path . 'public/charitable-template-helpers.php' );
			require_once( $includes_path . 'public/class-charitable-session.php' );
			require_once( $includes_path . 'public/class-charitable-template.php' );
			require_once( $includes_path . 'public/class-charitable-template-part.php' );
			require_once( $includes_path . 'public/class-charitable-templates.php' );
			require_once( $includes_path . 'public/class-charitable-ghost-page.php' );
			require_once( $includes_path . 'public/class-charitable-user-dashboard.php' );

			/* Shortcodes */
			require_once( $includes_path . 'shortcodes/class-charitable-campaigns-shortcode.php' );
			require_once( $includes_path . 'shortcodes/class-charitable-my-donations-shortcode.php' );
			require_once( $includes_path . 'shortcodes/class-charitable-donation-receipt-shortcode.php' );
			require_once( $includes_path . 'shortcodes/class-charitable-login-shortcode.php' );
			require_once( $includes_path . 'shortcodes/class-charitable-registration-shortcode.php' );
			require_once( $includes_path . 'shortcodes/class-charitable-profile-shortcode.php' );			
			require_once( $includes_path . 'shortcodes/charitable-shortcodes-hooks.php' );

			/* Widgets */
			require_once( $includes_path . 'widgets/class-charitable-widgets.php' );
			require_once( $includes_path . 'widgets/class-charitable-campaign-terms-widget.php' );
			require_once( $includes_path . 'widgets/class-charitable-campaigns-widget.php' );
			require_once( $includes_path . 'widgets/class-charitable-donors-widget.php' );
			require_once( $includes_path . 'widgets/class-charitable-donate-widget.php' );
			require_once( $includes_path . 'widgets/class-charitable-donation-stats-widget.php' );

			/* User Management */
			require_once( $includes_path . 'user-management/class-charitable-registration-form.php' );
			require_once( $includes_path . 'user-management/class-charitable-profile-form.php' );
			require_once( $includes_path . 'user-management/class-charitable-forgot-password-form.php' );
			require_once( $includes_path . 'user-management/class-charitable-reset-password-form.php' );
			require_once( $includes_path . 'user-management/class-charitable-user-management.php' );
			require_once( $includes_path . 'user-management/charitable-user-management-hooks.php' );

			/* Customizer */
			require_once( $includes_path . 'admin/customizer/class-charitable-customizer.php' );

			/* Deprecated */
			require_once( $includes_path . 'deprecated/charitable-deprecated-functions.php' );

			/**
			 * We are registering this object only for backwards compatibility. It
			 * will be removed in or after Charitable 1.3.
			 *
			 * @deprecated
			 */
			$this->register_object( Charitable_Emails::get_instance() );
			$this->register_object( Charitable_Request::get_instance() );
			$this->register_object( Charitable_Gateways::get_instance() );
			$this->register_object( Charitable_i18n::get_instance() );
			$this->register_object( Charitable_Post_Types::get_instance() );
			$this->register_object( Charitable_Cron::get_instance() );
			$this->register_object( Charitable_Widgets::get_instance() );
			$this->register_object( Charitable_Licenses::get_instance() );
			$this->register_object( Charitable_User_Dashboard::get_instance() );
		}

		/**
		 * Set up hook and filter callback functions.
		 *
		 * @return  void
		 * @access  private
		 * @since   1.0.0
		 */
		private function attach_hooks_and_filters() {
			add_action( 'wpmu_new_blog', array( $this, 'maybe_activate_charitable_on_new_site' ) );
			add_action( 'plugins_loaded', array( $this, 'charitable_install' ), 100 );
			add_action( 'plugins_loaded', array( $this, 'charitable_start' ), 100 );
			add_action( 'setup_theme', array( 'Charitable_Customizer', 'start' ) );

			/**
			 * We do this on priority 20 so that any functionality that is loaded on init (such
			 * as addons) has a chance to run before the event.
			 */
			add_action( 'init', array( $this, 'do_charitable_actions' ), 20 );
			add_filter( 'charitable_sanitize_donation_meta', 'charitable_sanitize_donation_meta', 10, 2 );
		}

		/**
		 * Checks whether we're in the admin area and if so, loads the admin-only functionality.
		 *
		 * @return  void
		 * @access  private
		 * @since   1.0.0
		 */
		private function maybe_start_admin() {
			if ( ! is_admin() ) {
				return;
			}

			require_once( $this->get_path( 'admin' ) . 'class-charitable-admin.php' );
			require_once( $this->get_path( 'admin' ) . 'charitable-admin-hooks.php' );

			/**
			 * We are registering this object only for backwards compatibility. It
			 * will be removed in or after Charitable 1.3.
			 *
			 * @deprecated
			 */
			$this->register_object( Charitable_Admin::get_instance() );
		}

		/**
		 * Checks whether we're on the public-facing side and if so, loads the public-facing functionality.
		 *
		 * @return  void
		 * @access  private
		 * @since   1.0.0
		 */
		private function maybe_start_public() {
			if ( is_admin() ) {
				return;
			}

			require_once( $this->get_path( 'public' ) . 'class-charitable-public.php' );

			/**
			 * We are registering this object only for backwards compatibility. It
			 * will be removed in or after Charitable 1.3.
			 *
			 * @deprecated
			 */
			$this->register_object( Charitable_Public::get_instance() );
		}

		/**
		 * Checks whether we're executing an AJAX hook and if so, loads some AJAX functionality.
		 *
		 * @return  void
		 * @access  private
		 * @since   1.0.0
		 */
		private function maybe_start_ajax() {
			if ( false === ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				return;
			}

			require_once( $this->get_path( 'includes' ) . 'ajax/charitable-ajax-functions.php' );
			require_once( $this->get_path( 'includes' ) . 'ajax/charitable-ajax-hooks.php' );

			/**
			 * We are registering this object only for backwards compatibility. It
			 * will be removed in or after Charitable 1.3.
			 *
			 * @deprecated
			 */
			$this->register_object( Charitable_Session::get_instance() );
		}

		/**
		 * This method is fired after all plugins are loaded and simply fires the charitable_start hook.
		 *
		 * Extensions can use the charitable_start event to load their own functionality.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function charitable_start() {
			do_action( 'charitable_start', $this );
		}

		/**
		 * Fires off an action right after Charitable is installed, allowing other
		 * plugins/themes to do something at this point.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.1
		 */
		public function charitable_install() {
			$install = get_transient( 'charitable_install' );

			if ( ! $install ) {
				return;
			}

			require_once( $this->get_path( 'includes' ) . 'class-charitable-install.php' );

			Charitable_Install::finish_installing();

			do_action( 'charitable_install' );

			delete_transient( 'charitable_install' );
		}

		/**
		 * Returns whether we are currently in the start phase of the plugin.
		 *
		 * @return  bool
		 * @access  public
		 * @since   1.0.0
		 */
		public function is_start() {
			return current_filter() == 'charitable_start';
		}

		/**
		 * Returns whether the plugin has already started.
		 *
		 * @return  bool
		 * @access  public
		 * @since   1.0.0
		 */
		public function started() {
			return did_action( 'charitable_start' ) || current_filter() == 'charitable_start';
		}

		/**
		 * Returns whether the plugin is being activated.
		 *
		 * @return  bool
		 * @access  public
		 * @since   1.0.0
		 */
		public function is_activation() {
			return current_filter() == 'activate_charitable/charitable.php';
		}

		/**
		 * Returns whether the plugin is being deactivated.
		 *
		 * @return  bool
		 * @access  public
		 * @since   1.0.0
		 */
		public function is_deactivation() {
			return current_filter() == 'deactivate_charitable/charitable.php';
		}

		/**
		 * Stores an object in the plugin's registry.
		 *
		 * @param   mixed $object
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function register_object( $object ) {
			if ( ! is_object( $object ) ) {
				return;
			}

			$class = get_class( $object );

			$this->registry[ $class ] = $object;
		}

		/**
		 * Returns a registered object.
		 *
		 * @param   string $class   The type of class you want to retrieve.
		 * @return  mixed           The object if its registered. Otherwise false.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_registered_object( $class ) {
			return isset( $this->registry[ $class ] ) ? $this->registry[ $class ] : false;
		}

		/**
		 * Returns plugin paths.
		 *
		 * @param   string $type        If empty, returns the path to the plugin.
		 * @param   bool $absolute_path If true, returns the file system path. If false, returns it as a URL.
		 * @return  string
		 * @since   1.0.0
		 */
		public function get_path( $type = '', $absolute_path = true ) {
			$base = $absolute_path ? $this->directory_path : $this->directory_url;

			switch ( $type ) {
				case 'includes' :
					$path = $base . 'includes/';
					break;

				case 'admin' :
					$path = $base . 'includes/admin/';
					break;

				case 'public' :
					$path = $base . 'includes/public/';
					break;

				case 'assets' :
					$path = $base . 'assets/';
					break;

				case 'templates' :
					$path = $base . 'templates/';
					break;

				case 'directory' :
					$path = $base;
					break;

				default :
					$path = __FILE__;
			}

			return $path;
		}

		/**
		 * Returns the plugin's version number.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_version() {
			$version = self::VERSION;

			if ( false !== strpos( $version, '-' ) ) {
				$parts   = explode( '-', $version );
				$version = $parts[0];
			}

			return $version;
		}

		/**
		 * Returns the public class.
		 *
		 * @return  Charitable_Public
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_public() {
			return $this->get_registered_object( 'Charitable_Public' );
		}

		/**
		 * Returns the admin class.
		 *
		 * @return  Charitable_Admin
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_admin() {
			return $this->get_registered_object( 'Charitable_Admin' );
		}

		/**
		 * Return the current request object.
		 *
		 * @return  Charitable_Request
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_request() {
			$request = $this->get_registered_object( 'Charitable_Request' );

			if ( false === $request ) {
				$request = new Charitable_Request();
				$this->register_object( $request );
			}

			return $request;
		}

		/**
		 * Returns the model for one of Charitable's database tables.
		 *
		 * @param   string $table
		 * @return  Charitable_DB
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_db_table( $table ) {
			$tables = $this->get_tables();

			if ( ! isset( $tables[ $table ] ) ) {
				_doing_it_wrong( __METHOD__, sprintf( 'Invalid table %s passed', $table ), '1.0.0' );
				return null;
			}

			$class_name = $tables[ $table ];

			$db_table = $this->get_registered_object( $class_name );

			if ( false === $db_table ) {
				$db_table = new $class_name;
				$this->register_object( $db_table );
			}

			return $db_table;
		}

		/**
		 * Return the filtered list of registered tables.
		 *
		 * @return  string[]
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_tables() {
			$default_tables = array(
				'campaign_donations' => 'Charitable_Campaign_Donations_DB',
				'donors'             => 'Charitable_Donors_DB',
			);

			return apply_filters( 'charitable_db_tables', $default_tables );
		}

		/**
		 * Maybe activate Charitable when a new site is added in a multisite network.
		 *
		 * @param 	int $blog_id
		 * @return  boolean
		 * @access  public
		 * @since   1.4.6
		 */
		public function maybe_activate_charitable_on_new_site( $blog_id ) {

			if ( is_plugin_active_for_network( basename( $this->directory_path ) . '/charitable.php' ) ) {

				switch_to_blog( $blog_id );

				$this->activate( false );

				restore_current_blog();
			}
		}

		/**
		 * Runs on plugin activation.
		 *
		 * @see 	register_activation_hook
		 *
		 * @param 	boolean $network_wide Whether to enable the plugin for all sites in the network
		 *                           	  or just the current site. Multisite only. Default is false.
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function activate( $network_wide = false ) {

			require_once( $this->get_path( 'includes' ) . 'class-charitable-install.php' );

			if ( is_multisite() && $network_wide ) {

				global $wpdb;

		        foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {

		            switch_to_blog( $blog_id );

		            new Charitable_Install();

		            restore_current_blog();
		        }
			} else {

				new Charitable_Install();

			}
		}

		/**
		 * Runs on plugin deactivation.
		 *
		 * @see     register_deactivation_hook
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function deactivate() {
			require_once( $this->get_path( 'includes' ) . 'class-charitable-uninstall.php' );
			new Charitable_Uninstall();
		}

		/**
		 * If a charitable_action event is triggered, delegate the event using do_action.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function do_charitable_actions() {
			if ( isset( $_REQUEST['charitable_action'] ) ) {

				$action = $_REQUEST['charitable_action'];

				do_action( 'charitable_' . $action, 20 );
			}
		}

		/**
		 * Throw error on object clone.
		 *
		 * This class is specifically designed to be instantiated once. You can retrieve the instance using charitable()
		 *
		 * @since   1.0.0
		 * @access  public
		 * @return  void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'charitable' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since   1.0.0
		 * @access  public
		 * @return  void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'charitable' ), '1.0.0' );
		}

		/**
		 * DEPRECATED METHODS
		 */

		/**
		 * @deprecated
		 */
		public function get_currency_helper() {
			charitable_get_deprecated()->deprecated_function( __METHOD__, '1.4.0', 'charitable_get_currency_helper' );
			return charitable_get_currency_helper();
		}

		/**
		 * @deprecated
		 */
		public function get_location_helper() {
			charitable_get_deprecated()->deprecated_function( __METHOD__, '1.2.0', 'charitable_get_location_helper' );
			return charitable_get_location_helper();
		}
	}

	$charitable = new Charitable();

endif;
