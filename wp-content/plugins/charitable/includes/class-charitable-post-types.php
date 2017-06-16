<?php
/**
 * The class that defines Charitable's custom post types, taxonomies and post statuses.
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Post_Types
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Post_Types' ) ) :

	/**
	 * Charitable_Post_Types
	 *
	 * @since       1.0.0
	 */
	final class Charitable_Post_Types {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Post_Types|null
		 * @access  private
		 * @static
		 */
		private static $instance = null;

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @return  Charitable_Post_Types
		 * @access  public
		 * @since   1.2.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Post_Types();
			}

			return self::$instance;
		}

		/**
		 * Set up the class.
		 *
		 * Note that the only way to instantiate an object is with the on_start method,
		 * which can only be called during the start phase. In other words, don't try
		 * to instantiate this object.
		 *
		 * @access  private
		 * @since   1.0.0
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ), 5 );
			add_action( 'init', array( $this, 'register_post_statuses' ), 5 );
			add_action( 'init', array( $this, 'register_taxonomies' ), 6 );
			add_action( 'init', array( $this, 'add_endpoints' ) );
			add_action( 'init', array( $this, 'add_rewrite_tags' ) );
			add_action( 'init', array( $this, 'add_rewrite_rule' ), 11 );

			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		}

		/**
		 * Register plugin post types.
		 *
		 * @hook    init
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function register_post_types() {
			/**
			 * Campaign post type.
			 *
			 * To change any of the arguments used for the post type, other than the name
			 * of the post type itself, use the 'charitable_campaign_post_type' filter.
		 	*/
			register_post_type( 'campaign',
				apply_filters( 'charitable_campaign_post_type',
					array(
						'labels' => array(
							'name'                  => __( 'Campaigns', 'charitable' ),
							'singular_name'         => __( 'Campaign', 'charitable' ),
							'menu_name'             => _x( 'Campaigns', 'Admin menu name', 'charitable' ),
							'add_new'               => __( 'Add Campaign', 'charitable' ),
							'add_new_item'          => __( 'Add New Campaign', 'charitable' ),
							'edit'                  => __( 'Edit', 'charitable' ),
							'edit_item'             => __( 'Edit Campaign', 'charitable' ),
							'new_item'              => __( 'New Campaign', 'charitable' ),
							'view'                  => __( 'View Campaign', 'charitable' ),
							'view_item'             => __( 'View Campaign', 'charitable' ),
							'search_items'          => __( 'Search Campaigns', 'charitable' ),
							'not_found'             => __( 'No Campaigns found', 'charitable' ),
							'not_found_in_trash'    => __( 'No Campaigns found in trash', 'charitable' ),
							'parent'                => __( 'Parent Campaign', 'charitable' ),
						),
						'description'           => __( 'This is where you can create new campaigns for people to support.', 'charitable' ),
						'public'                => true,
						'show_ui'               => true,
						'capability_type'       => 'campaign',
						'menu_icon'             => '',
						'map_meta_cap'          => true,
						'publicly_queryable'    => true,
						'exclude_from_search'   => false,
						'hierarchical'          => false,
						'rewrite'               => array( 'slug' => 'campaigns', 'with_front' => true ),
						'query_var'             => true,
						'supports'              => array( 'title', 'thumbnail', 'comments' ),
						'has_archive'           => false,
						'show_in_nav_menus'     => true,
						'show_in_menu'          => false,
						'show_in_admin_bar'     => true,
					)
				)
			);

			/**
			 * Donation post type.
			 *
			 * To change any of the arguments used for the post type, other than the name
			 * of the post type itself, use the 'charitable_donation_post_type' filter.
			 */
			register_post_type( 'donation',
				apply_filters( 'charitable_donation_post_type',
					array(
					'labels' => array(
						'name'                  => __( 'Donations', 'charitable' ),
						'singular_name'         => __( 'Donation', 'charitable' ),
						'menu_name'             => _x( 'Donations', 'Admin menu name', 'charitable' ),
						'add_new'               => __( 'Add Donation', 'charitable' ),
						'add_new_item'          => __( 'Add New Donation', 'charitable' ),
						'edit'                  => __( 'Edit', 'charitable' ),
						'edit_item'             => __( 'Donation Details', 'charitable' ),
						'new_item'              => __( 'New Donation', 'charitable' ),
						'view'                  => __( 'View Donation', 'charitable' ),
						'view_item'             => __( 'View Donation', 'charitable' ),
						'search_items'          => __( 'Search Donations', 'charitable' ),
						'not_found'             => __( 'No Donations found', 'charitable' ),
						'not_found_in_trash'    => __( 'No Donations found in trash', 'charitable' ),
						'parent'                => __( 'Parent Donation', 'charitable' ),
					),
					'public'                => false,
					'show_ui'               => true,
					'capability_type'       => 'donation',
					'menu_icon'             => '',
					'map_meta_cap'          => true,
					'publicly_queryable'    => false,
					'exclude_from_search'   => false,
					'hierarchical'          => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'               => false,
					'query_var'             => false,
					'supports'              => array( '' ),
					'has_archive'           => false,
					'show_in_nav_menus'     => false,
					'show_in_menu'          => false,
					)
				)
			);
		}

		/**
		 * Register custom post statuses.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function register_post_statuses() {
			register_post_status( 'charitable-pending', array(
				'label'                     => _x( 'Pending', 'Pending Donation Status', 'charitable' ),
				'label_count'               => _n_noop( 'Pending (%s)',  'Pending (%s)', 'charitable' ),
				'public'                    => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'exclude_from_search'       => true,
			) );

			register_post_status( 'charitable-completed', array(
				'label'                     => _x( 'Paid', 'Paid Donation Status', 'charitable' ),
				'label_count'               => _n_noop( 'Paid (%s)',  'Paid (%s)', 'charitable' ),
				'public'                    => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'exclude_from_search'       => true,
			) );

			register_post_status( 'charitable-failed', array(
				'label'                     => _x( 'Failed', 'Failed Donation Status', 'charitable' ),
				'label_count'               => _n_noop( 'Failed (%s)',  'Failed (%s)', 'charitable' ),
				'public'                    => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'exclude_from_search'       => true,
			) );

			register_post_status( 'charitable-cancelled', array(
				'label'                     => _x( 'Canceled', 'Canceled Donation Status', 'charitable' ),
				'label_count'               => _n_noop( 'Canceled (%s)',  'Canceled (%s)', 'charitable' ),
				'public'                    => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'exclude_from_search'       => true,
			) );

			register_post_status( 'charitable-refunded', array(
				'label'                     => _x( 'Refunded', 'Refunded Donation Status', 'charitable' ),
				'label_count'               => _n_noop( 'Refunded (%s)',  'Refunded (%s)', 'charitable' ),
				'public'                    => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'exclude_from_search'       => true,
			) );

			register_post_status( 'charitable-preapproved', array(
				'label'                     => _x( 'Pre Approved', 'Pre Approved Donation Status', 'charitable' ),
				'label_count'               => _n_noop( 'Pre Approved (%s)',  'Pre Approved (%s)', 'charitable' ),
				'public'                    => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'exclude_from_search'       => true,
			) );
		}

		/**
		 * Register the campaign category taxonomy.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function register_taxonomies() {
			$labels = array(
				'name'                       => _x( 'Campaign Categories', 'Taxonomy General Name', 'charitable' ),
				'singular_name'              => _x( 'Campaign Category', 'Taxonomy Singular Name', 'charitable' ),
				'menu_name'                  => __( 'Categories', 'charitable' ),
				'all_items'                  => __( 'All Campaign Categories', 'charitable' ),
				'parent_item'                => __( 'Parent Campaign Category', 'charitable' ),
				'parent_item_colon'          => __( 'Parent Campaign Category:', 'charitable' ),
				'new_item_name'              => __( 'New Campaign Category Name', 'charitable' ),
				'add_new_item'               => __( 'Add New Campaign Category', 'charitable' ),
				'edit_item'                  => __( 'Edit Campaign Category', 'charitable' ),
				'update_item'                => __( 'Update Campaign Category', 'charitable' ),
				'view_item'                  => __( 'View Campaign Category', 'charitable' ),
				'separate_items_with_commas' => __( 'Separate campaign categories with commas', 'charitable' ),
				'add_or_remove_items'        => __( 'Add or remove campaign categories', 'charitable' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'charitable' ),
				'popular_items'              => __( 'Popular Campaign Categories', 'charitable' ),
				'search_items'               => __( 'Search Campaign Categories', 'charitable' ),
				'not_found'                  => __( 'Not Found', 'charitable' ),
			);

			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => true,
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
			);

			register_taxonomy( 'campaign_category', array( 'campaign' ), $args );

			$labels = array(
				'name'                       => _x( 'Campaign Tags', 'Taxonomy General Name', 'charitable' ),
				'singular_name'              => _x( 'Campaign Tag', 'Taxonomy Singular Name', 'charitable' ),
				'menu_name'                  => __( 'Tags', 'charitable' ),
				'all_items'                  => __( 'All Campaign Tags', 'charitable' ),
				'parent_item'                => __( 'Parent Campaign Tag', 'charitable' ),
				'parent_item_colon'          => __( 'Parent Campaign Tag:', 'charitable' ),
				'new_item_name'              => __( 'New Campaign Tag Name', 'charitable' ),
				'add_new_item'               => __( 'Add New Campaign Tag', 'charitable' ),
				'edit_item'                  => __( 'Edit Campaign Tag', 'charitable' ),
				'update_item'                => __( 'Update Campaign Tag', 'charitable' ),
				'view_item'                  => __( 'View Campaign Tag', 'charitable' ),
				'separate_items_with_commas' => __( 'Separate campaign tags with commas', 'charitable' ),
				'add_or_remove_items'        => __( 'Add or remove campaign tags', 'charitable' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'charitable' ),
				'popular_items'              => __( 'Popular Campaign Tags', 'charitable' ),
				'search_items'               => __( 'Search Campaign Tags', 'charitable' ),
				'not_found'                  => __( 'Not Found', 'charitable' ),
			);

			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => false,
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
			);

			register_taxonomy( 'campaign_tag', array( 'campaign' ), $args );

			register_taxonomy_for_object_type( 'campaign_category', 'campaign' );
			register_taxonomy_for_object_type( 'campaign_tag', 'campaign' );
		}

		/**
		 * Add custom query vars.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.4.0
		 */
		public function add_query_vars( $vars ) {
			$new_vars = array(
				'donation_id',
				'cancel',
			);

			$vars = array_merge( $vars, $new_vars );

			return $vars;
		}

		/**
		 * Add custom endpoints.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_endpoints() {
			add_rewrite_endpoint( 'donate', EP_PERMALINK );
			add_rewrite_endpoint( 'widget', EP_PERMALINK );
			add_rewrite_endpoint( 'reset_password', EP_PERMALINK );
			add_rewrite_endpoint( 'forgot_password', EP_PERMALINK );
			add_rewrite_endpoint( 'donation_receipt', EP_ROOT );
			add_rewrite_endpoint( 'donation_processing', EP_ROOT );
		}

		/**
		 * Add custom rewrite tag.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_rewrite_tags() {
			add_rewrite_tag( '%donation_id%', '([0-9]+)' );
		}

		/**
		 * Add endpoint for editing campaigns.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_rewrite_rule() {
			add_rewrite_rule( 'donation-receipt/([0-9]+)/?$', 'index.php?donation_id=$matches[1]&donation_receipt=1', 'top' );
			add_rewrite_rule( 'donation-processing/([0-9]+)/?$', 'index.php?donation_id=$matches[1]&donation_processing=1', 'top' );
			add_rewrite_rule( '(.?.+?)(?:/([0-9]+))?/forgot-password/?$', 'index.php?pagename=$matches[1]&page=$matches[2]&forgot_password=1', 'top' );
			add_rewrite_rule( '(.?.+?)(?:/([0-9]+))?/reset-password/?$', 'index.php?pagename=$matches[1]&page=$matches[2]&reset_password=1', 'top' );
		}
	}

endif; // End class_exists check.
