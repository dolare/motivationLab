<?php
/**
 * This class is responsible for adding the Charitable admin pages.
 *
 * @package     Charitable/Classes/Charitable_Admin_Pages
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Admin_Pages' ) ) :

	/**
	 * Charitable_Admin_Pages
	 *
	 * @since       1.0.0
	 */
	final class Charitable_Admin_Pages {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Admin_Pages|null
		 * @access  private
		 * @static
		 */
		private static $instance = null;

		/**
		 * The page to use when registering sections and fields.
		 *
		 * @var     string
		 * @access  private
		 */
		private $admin_menu_parent_page;

		/**
		 * The capability required to view the admin menu.
		 *
		 * @var     string
		 * @access  private
		 */
		private $admin_menu_capability;

		/**
		 * Create class object.
		 *
		 * @access  private
		 * @since   1.0.0
		 */
		private function __construct() {
			$this->admin_menu_capability  = apply_filters( 'charitable_admin_menu_capability', 'view_charitable_sensitive_data' );
			$this->admin_menu_parent_page = 'charitable';
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @return  Charitable_Admin_Pages
		 * @access  public
		 * @since   1.2.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Admin_Pages();
			}

			return self::$instance;
		}

		/**
		 * Add Settings menu item under the Campaign menu tab.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_menu() {
			add_menu_page(
                'Charitable',
                'Charitable',
                $this->admin_menu_capability,
                $this->admin_menu_parent_page,
                array( $this, 'render_welcome_page' )
            );

			foreach ( $this->get_submenu_pages() as $page ) {
				if ( ! isset( $page['page_title'] )
					|| ! isset( $page['menu_title'] )
					|| ! isset( $page['menu_slug'] ) ) {
					continue;
				}

				$page_title = $page['page_title'];
				$menu_title = $page['menu_title'];
				$capability = isset( $page['capability'] ) ? $page['capability'] : $this->admin_menu_capability;
				$menu_slug  = $page['menu_slug'];
				$function   = isset( $page['function'] ) ? $page['function'] : '';

				add_submenu_page(
					$this->admin_menu_parent_page,
					$page_title,
					$menu_title,
					$capability,
					$menu_slug,
					$function
				);
			}
		}

		/**
		 * Returns an array with all the submenu pages.
		 *
		 * @return  array
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_submenu_pages() {
			$campaign_post_type = get_post_type_object( 'campaign' );
			$donation_post_type = get_post_type_object( 'donation' );

			return apply_filters( 'charitable_submenu_pages', array(
				array(
					'page_title'    => $campaign_post_type->labels->menu_name,
					'menu_title'    => $campaign_post_type->labels->menu_name,
					'menu_slug'     => 'edit.php?post_type=campaign',
				),
				array(
					'page_title'    => $campaign_post_type->labels->add_new,
					'menu_title'    => $campaign_post_type->labels->add_new,
					'menu_slug'     => 'post-new.php?post_type=campaign',
				),
				array(
					'page_title'    => $donation_post_type->labels->menu_name,
					'menu_title'    => $donation_post_type->labels->menu_name,
					'menu_slug'     => 'edit.php?post_type=donation',
				),
				array(
					'page_title'    => __( 'Campaign Categories', 'charitable' ),
					'menu_title'    => __( 'Categories', 'charitable' ),
					'menu_slug'     => 'edit-tags.php?taxonomy=campaign_category&post_type=campaign',
				),
				array(
					'page_title'    => __( 'Campaign Tags', 'charitable' ),
					'menu_title'    => __( 'Tags', 'charitable' ),
					'menu_slug'     => 'edit-tags.php?taxonomy=campaign_tag&post_type=campaign',
				),
				array(
					'page_title'    => __( 'Charitable Settings', 'charitable' ),
					'menu_title'    => __( 'Settings', 'charitable' ),
					'menu_slug'     => 'charitable-settings',
					'function'      => array( $this, 'render_settings_page' ),
					'capability'    => 'manage_charitable_settings',
				),
			) );
		}

		/**
		 * Set up the redirect to the welcome page.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.3.0
		 */
		public function setup_welcome_redirect() {
			add_action( 'admin_init', array( self::get_instance(), 'redirect_to_welcome' ) );
		}

		/**
		 * Redirect to the welcome page.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.3.0
		 */
		public function redirect_to_welcome() {
			wp_safe_redirect( admin_url( 'admin.php?page=charitable&install=true' ) );
			exit;
		}

		/**
		 * Display the Charitable settings page.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function render_settings_page() {
			charitable_admin_view( 'settings/settings' );
		}

		/**
		 * Display the Charitable donations page.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
         *
		 * @deprecated 1.4.0
		 */
		public function render_donations_page() {
			charitable_get_deprecated()->deprecated_function(
                __METHOD__,
                '1.4.0',
                __( 'Donations page now rendered by WordPress default manage_edit-donation_columns', 'charitable' )
            );

			charitable_admin_view( 'donations-page/page' );
		}

		/**
		 * Display the Charitable welcome page.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function render_welcome_page() {
			charitable_admin_view( 'welcome-page/page' );
		}
	}

endif; // End class_exists check
