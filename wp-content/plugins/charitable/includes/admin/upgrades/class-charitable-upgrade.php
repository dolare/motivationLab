<?php
/**
 * Charitable Upgrade class.
 *
 * The responsibility of this class is to manage migrations between versions of Charitable.
 *
 * @package		Charitable
 * @subpackage	Charitable/Charitable Upgrade
 * @copyright 	Copyright (c) 2017, Eric Daams
 * @license     http://opensource.org/licenses/gpl-1.0.0.php GNU Public License
 * @since 		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Upgrade' ) ) :

	/**
	 * Charitable_EDD_Upgrade
	 *
	 * @since 		1.0.0
	 */
	class Charitable_Upgrade {

	    /**
	     * @var     Charitable_Upgrade
	     * @access  private
	     * @static
	     * @since   1.3.0
	     */
	    private static $instance = null;

		/**
		 * Current database version.
		 * @var 	false|string
		 * @access 	protected
		 */
		protected $db_version;

		/**
		 * Edge version.
		 * @var 	string
		 * @access 	protected
		 */
		protected $edge_version;

		/**
		 * Array of methods to perform when upgrading to specific versions.
		 * @var 	array
		 * @access 	protected
		 */
		protected $upgrade_actions;

		/**
		 * Option key for upgrade log.
		 * @var 	string
		 * @access 	protected
		 */
		protected $upgrade_log_key = 'charitable_upgrade_log';

		/**
		 * Option key for plugin version.
		 * @var 	string
		 * @access 	protected
		 */
		protected $version_key = 'charitable_version';

	    /**
	     * Create and return the class object.
	     *
	     * @access  public
	     * @static
	     * @since   1.3.0
	     */
	    public static function get_instance() {
	        if ( is_null( self::$instance ) ) {
	            self::$instance = new Charitable_Upgrade();
	        }

	        return self::$instance;
	    }

		/**
		 * Manages the upgrade process.
		 *
		 * @param 	false|string 	$db_version
		 * @param 	string 			$edge_version
		 * @access 	protected
		 * @since 	1.0.0
		 */
		protected function __construct( $db_version = '', $edge_version = '' ) {
			/**
			 * We are keeping this for the sake of backwards compatibility for
			 * extensions that extend Charitable_Upgrade.
			 */
			if ( strlen( $db_version ) && strlen( $edge_version ) ) {

				$this->db_version = $db_version;
				$this->edge_version = $edge_version;

				/**
				 * Perform version upgrades.
				 */
				$this->do_upgrades();

				/**
				 * Log the upgrade and update the database version.
				 */
				$this->save_upgrade_log();
				$this->update_db_version();

			} else {

				$this->upgrade_actions = array(
					'update_upgrade_system' => array(
						'version' => '1.3.0',
						'message' => __( 'Charitable needs to update the database.', 'charitable' ),
						'prompt' => true,
					),
					'fix_donation_dates' => array(
						'version' => '1.3.0',
						'message' => __( 'Charitable needs to fix incorrect donation dates.', 'charitable' ),
						'prompt' => true,
					),
					'trigger_cron' => array(
						'version' => '1.3.4',
						'message' => '',
						'prompt' => false,
						'callback' => array( 'Charitable_Cron', 'schedule_events' ),
					),
					'flush_permalinks_140' => array(
						'version' => '1.4.0',
						'message' => '',
						'prompt' => false,
						'callback' => array( $this, 'flush_permalinks' ),
					),
					'show_release_140_upgrade_notice' => array(
						'version' => '1.4.0',
						'notice' => 'release-140',
					),
					'show_release_142_upgrade_notice' => array(
						'version' => '1.4.2',
						'notice' => 'release-142',
					),
					'show_release_143_paypal_notice' => array(
						'version' => '1.4.3',
						'notice' => 'release-143-paypal',
					),
					'remove_campaign_manager_cap' => array(
						'version'  => '1.4.5',
						'message'  => '',
						'prompt'   => false,
						'callback' => array( $this, 'remove_campaign_manager_cap' ),
					),
					'show_recurring_donations_notice' => array(
						'version'  => '1.4.10',
						'notice'   => 'release-1410-recurring-donations',
					),
					'fix_empty_campaign_end_date_meta' => array(
						'version'  => '1.4.11',
						'message'  => '',
						'prompt'   => false,
						'callback' => array( $this, 'fix_empty_campaign_end_date_meta' ),
					),
				);

			}
		}

		/**
		 * Populate the upgrade log when first installing the plugin.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.3.0
		 */
		public function populate_upgrade_log_on_install() {
			/**
			 * If the log already exists, don't change it.
			 */
			if ( get_option( $this->upgrade_log_key ) ) {
				return;
			}

			$log = array(
				'install' => array(
					'version' => Charitable::VERSION,
					'message' => __( 'Charitable was installed.', 'charitable' ),
				),
			);

			foreach ( $this->upgrade_actions as $key => $notes ) {
				$notes['install'] = true;
				$log[ $key ] = $notes;
			}

			add_option( $this->upgrade_log_key, $log );
		}

		/**
		 * Check if there is an upgrade that needs to happen and if so, displays a notice to begin upgrading.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.3.0
		 */
		public function add_upgrade_notice() {
			if ( isset( $_GET['page'] ) && 'charitable-upgrades' == $_GET['page'] ) {
				return;
			}

			if ( ! current_user_can( 'manage_charitable_settings' ) ) {
				return;
			}

			/**
			 * If an upgrade is still in progress, continue it until it's done.
			 */
			$upgrade_progress = $this->upgrade_is_in_progress();

			if ( false !== $upgrade_progress ) {

				/* Fixes a bug that incorrectly set the page as charitable-upgrade */
				if ( isset( $upgrade_progress['page'] ) && 'charitable-upgrade' == $upgrade_progress['page'] ) {
					$upgrade_progress['page'] = 'charitable-upgrades';
				}
	?>		
				<div class="error">
					<p><?php printf( __( 'Charitable needs to complete an upgrade that was started earlier. Click <a href="%s">here</a> to continue the upgrade.', 'charitable' ), esc_url( add_query_arg( $upgrade_progress, admin_url( 'index.php' ) ) ) ) ?>
					</p>
				</div>
	<?php
			} else {
				foreach ( $this->upgrade_actions as $action => $upgrade ) {

					/**
					 * If we've already done this upgrade, continue.
					 */
					if ( $this->upgrade_has_been_completed( $action ) ) {
						continue;
					}

					/**
					 * Check if we're just setting a transient to display a notice.
					 */
					if ( array_key_exists( 'notice', $upgrade ) ) {
						$this->set_update_notice_transient( $upgrade, $action );
						continue;
					}

					/**
					 * If the upgrade does not need a prompt, just do it straight away.
					 */
					if ( $this->do_upgrade_immediately( $upgrade ) ) {
						call_user_func( $upgrade['callback'] );
						continue;
					}
		?>
					<div class="updated">
						<p><?php printf( '%s %s', $upgrade['message'], sprintf( __( 'Click <a href="%s">here</a> to start the upgrade.', 'charitable' ), esc_url( admin_url( 'index.php?page=charitable-upgrades&charitable-upgrade=' . $action ) ) ) ) ?>
						</p>
					</div>
		<?php
				}
			}
		}

		/**
		 * Evaluates two version numbers and determines whether an upgrade is
		 * required for version A to get to version B.
		 *
		 * @param 	false|string $version_a
		 * @param 	string $version_b
		 * @return 	bool
		 * @static
		 * @access 	public
		 * @since 	1.0.0
		 */
		public static function requires_upgrade( $version_a, $version_b ) {
			return false === $version_a || version_compare( $version_a, $version_b, '<' );
		}

		/**
		 * This just flushes the permalinks on the `init` hook.
		 *
		 * Called by 1.0.1 and 1.1.3 update scripts.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.1.3
		 */
		public function flush_permalinks() {
			add_action( 'init', 'flush_rewrite_rules' );

			if ( version_compare( $this->edge_version, '1.4.0', '>=' ) ) {
				$this->update_upgrade_log( 'flush_permalinks_140' );
			}
		}

		/**
		 * Upgrade to version 1.1.0.
		 *
		 * This sets up the daily scheduled event.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.1.0
		 */
		public function upgrade_1_1_0() {
			Charitable_Cron::schedule_events();
		}

		/**
		 * Update the upgrade system.
		 *
		 * Also updates the campaign donations table to start storing amounts as DECIMAL, instead of FLOAT.
		 *
		 * This upgrade routine was added in 1.3.0.
		 *
		 * @see 	https://github.com/Charitable/Charitable/issues/56
		 *
		 * @return  void
		 * @access  public
		 * @since   1.3.0
		 */
		public function update_upgrade_system() {
			if ( ! current_user_can( 'manage_charitable_settings' ) ) {
				wp_die( __( 'You do not have permission to do Charitable upgrades', 'charitable' ), __( 'Error', 'charitable' ), array( 'response' => 403 ) );
			}

			ignore_user_abort( true );

			if ( ! charitable_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
				@set_time_limit( 0 );
			}

			/**
			 * Update the campaign donations table to use DECIMAL for amounts.
			 *
			 * @see 	https://github.com/Charitable/Charitable/issues/56
			 */
			$table = new Charitable_Campaign_Donations_DB();
			$table->create_table();

			$this->upgrade_logs();

			$this->finish_upgrade( 'update_upgrade_system' );
		}

		/**
		 * Fix the donation dates.
		 *
		 * This upgrade routine was added in 1.3.0
		 *
		 * @see 	https://github.com/Charitable/Charitable/issues/58
		 *
		 * @return  void
		 * @access  public
		 * @since   1.3.0
		 */
		public function fix_donation_dates() {
			if ( ! current_user_can( 'manage_charitable_settings' ) ) {
				wp_die( __( 'You do not have permission to do Charitable upgrades', 'charitable' ), __( 'Error', 'charitable' ), array( 'response' => 403 ) );
			}

			ignore_user_abort( true );

			if ( ! charitable_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
				@set_time_limit( 0 );
			}

			$step   = isset( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
			$number = 20;

			$total  = Charitable_Donations::count_all();

			/**
			 * If there are no donations to update, go ahead and wrap it up right now.
			 */
			if ( ! $total ) {
				$this->finish_upgrade( 'fix_donation_dates' );
			}

			$donations = get_posts( array(
				'post_type' => Charitable::DONATION_POST_TYPE,
				'posts_per_page' => $number,
				'paged' => $step,
				'post_status' => array_keys( Charitable_Donation::get_valid_donation_statuses() ),
			) );

			if ( count( $donations ) ) {

				/**
				 * Prevent donation receipt & admin notifications from getting resent.
				 */
				remove_action( 'save_post_' . Charitable::DONATION_POST_TYPE, array( 'Charitable_Email_Donation_Receipt', 'send_with_donation_id' ) );
				remove_action( 'save_post_' . Charitable::DONATION_POST_TYPE, array( 'Charitable_Email_New_Donation', 'send_with_donation_id' ) );

				foreach ( $donations as $donation ) {

					/**
					 * Thankfully, we store the timestamp of the donation in the log,
					 * so we can use that to correct any incorrect post_date/post_date_gmt
					 * values.
					 */
					$donation_log = get_post_meta( $donation->ID, '_donation_log', true );

					if ( empty( $donation_log ) ) {
						continue;
					}

					$time = $donation_log[0]['time'];

					$date_gmt = gmdate( 'Y-m-d H:i:s', $time );

					if ( $date_gmt == $donation->post_date_gmt ) {
						continue;
					}

					$date = get_date_from_gmt( $date_gmt );

					wp_update_post( array(
						'ID' => $donation->ID,
						'post_date' => $date,
						'post_date_gmt' => $date_gmt,
					) );
				}

				$step++;

				$redirect = add_query_arg( array(
					'page' => 'charitable-upgrades',
					'charitable-upgrade' => 'fix_donation_dates',
					'step' => $step,
					'number' => $number,
					'total' => $total,
				), admin_url( 'index.php' ) );

				wp_redirect( $redirect );

				exit;
			}

			$this->upgrade_logs();

			$this->finish_upgrade( 'fix_donation_dates' );
		}

		/**
		 * Upgrade the logs structure.
		 *
		 * This upgrade routine was added in 1.3.0.
		 *
		 * @see  	Charitable_Upgrade::update_upgrade_system()
		 * @see 	Charitable_upgrade::fix_donation_dates()
		 *
		 * @return  void
		 * @access  public
		 * @since   1.3.0
		 */
		public function upgrade_logs() {
			/**
			 * Deal with old upgrades.
			 */
			$log = get_option( $this->upgrade_log_key, false );

			/**
			 * Both of the 1.3 upgrades call this, so we need to make sure it hasn't run yet.
			 */
			if ( is_array( $log ) && isset( $log['legacy_logs'] ) ) {
				return;
			}

			$last_log = ! is_array( $log ) ? false : end( $log );

			/**
			 * If we're upgrading from prior to 1.1.0, we'll schedule events and flush rewrite rules.
			 */
			if ( false === $last_log || version_compare( $last_log['to'], '1.1.0', '<' ) ) {
				Charitable_Cron::schedule_events(); // 1.1.0 upgrade
				flush_rewrite_rules(); // 1.2.0 upgrade
			} /**
			 * If we're upgrade from prior to 1.2.0, we'll just flush the rewrite rules.
			 */
			elseif ( version_compare( $last_log['to'], '1.2.0', '<' ) ) {
				flush_rewrite_rules(); // 1.2.0 upgrade
			}

			/**
			 * Update the upgrade log and save all old logs as 'legacy_logs'.
			 */
			if ( is_array( $log ) ) {
				$new_log = array(
					'legacy_logs' => array(
						'time'    => time(),
						'version' => charitable()->get_version(),
						'logs'    => $log,
					),
				);

				update_option( $this->upgrade_log_key, $new_log );
			}
		}

		/**
		 * Remove the 'manage_charitable_settings' cap from the Campaign Manager role.
		 *
		 * @global 	WP_Roles
		 * @return  void
		 * @access  public
		 * @since   1.4.5
		 */
		public function remove_campaign_manager_cap() {
			global $wp_roles;

			if ( class_exists( 'WP_Roles' ) ) {
				if ( ! isset( $wp_roles ) ) {
					$wp_roles = new WP_Roles();
				}
			}

			if ( is_object( $wp_roles ) ) {
				$wp_roles->remove_cap( 'campaign_manager', 'manage_charitable_settings' );
			}

			$this->update_upgrade_log( 'remove_campaign_manager_cap' );
		}

		/**
		 * Convert the campaign end date meta to 0 for any campaigns where it is currently blank.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.4.11
		 */
		public function fix_empty_campaign_end_date_meta() {
			global $wpdb;

			$sql = "UPDATE $wpdb->postmeta
					INNER JOIN $wpdb->posts
					ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					SET $wpdb->postmeta.meta_value = 0
					WHERE $wpdb->postmeta.meta_key = '_campaign_end_date'
					AND $wpdb->postmeta.meta_value = ''
					AND $wpdb->posts.post_type = 'campaign';";

			$wpdb->query( $sql );
		}

		/**
		 * Set a transient to display an update notice.
		 *
		 * @param 	array  $upgrade
		 * @param 	string $action
		 * @return  void
		 * @access  public
		 * @since   1.4.0
		 */
		public function set_update_notice_transient( $upgrade, $action ) {
			set_transient( 'charitable_' . $upgrade['notice'] . '_notice', 1 );

			$this->update_upgrade_log( $action );
		}

		/**
		 * Checks whether an upgrade has been completed.
		 *
		 * @param 	string $action
		 * @return  boolean
		 * @access  protected
		 * @since   1.3.0
		 */
		protected function upgrade_has_been_completed( $action ) {
			$log = get_option( $this->upgrade_log_key );

			return is_array( $log ) && array_key_exists( $action, $log );
		}

		/**
		 * Checks whether an upgrade should be completed immediately, without a prompt.
		 *
		 * @param 	array $upgrade
		 * @return  boolean
		 * @access  protected
		 * @since   1.3.4
		 */
		protected function do_upgrade_immediately( $upgrade ) {

			/* If a prompt is required, return false. */
			if ( ! isset( $upgrade['prompt'] ) || $upgrade['prompt'] ) {
				return false;
			}

			/* If the callback is set and it's callable, return true. */
			return isset( $upgrade['callback'] ) && is_callable( $upgrade['callback'] );
		}

		/**
		 * Checks whether an upgrade is in progress.
		 *
		 * @return  false|array False if the upgrade is not in progress.
		 * @access  protected
		 * @since   1.3.0
		 */
		protected function upgrade_is_in_progress() {
			$doing_upgrade = get_option( 'charitable_doing_upgrade', false );

			if ( empty( $doing_upgrade ) ) {
				return false;
			}

			return $doing_upgrade;
		}

		/**
		 * Finish an upgrade. This clears the charitable_doing_upgrade setting and updates the log.
		 *
		 * @param 	string $upgrade
		 * @param 	string $redirect_url Optional URL to redirect to after the upgrade.
		 * @return  void
		 * @access  protected
		 * @since   1.3.0
		 */
		protected function finish_upgrade( $upgrade, $redirect_url = '' ) {
			delete_option( 'charitable_doing_upgrade' );

			$this->update_upgrade_log( $upgrade );

			if ( empty( $redirect_url ) ) {
				$redirect_url = admin_url( 'index.php' );
			}

			wp_redirect( $redirect_url );

			exit();
		}

		/**
		 * Add a completed upgrade to the upgrade log.
		 *
		 * @param 	string $upgrade
		 * @return  False if value was not updated and true if value was updated.
		 * @access  protected
		 * @since   1.3.0
		 */
		protected function update_upgrade_log( $upgrade ) {
			$log = get_option( $this->upgrade_log_key );

			$log[ $upgrade ] = array(
				'time' => time(),
				'version' => charitable()->get_version(),
			);

			return update_option( $this->upgrade_log_key, $log );
		}

		/*---------------------------------------------------------------------------*
		 *
		 * HERE BE DEPRECATED FUNCTIONS...
		 *
		 * We're keeping these functions since Charitable add-ons extend this
		 * class and don't know whether those have upgraded.
		 *
		 ---------------------------------------------------------------------------*/

		/**
		 * Upgrade from the current version stored in the database to the live version.
		 *
		 * @param 	false|string $db_version
		 * @param 	string $edge_version
	 	 * @return 	void
		 * @static
		 * @access 	public
		 * @since 	1.0.0
		 */
		public static function upgrade_from( $db_version, $edge_version ) {
			if ( self::requires_upgrade( $db_version, $edge_version ) ) {
				new Charitable_Upgrade( $db_version, $edge_version );
			}
		}

		/**
		 * Perform version upgrades.
		 *
		 * @return 	void
		 * @access 	protected
		 * @since 	1.0.0
		 */
		protected function do_upgrades() {
			/**
			 * Before Charitable 1.3, upgrades were in a simple key=>value
			 * array format.
			 *
			 * $upgrade_actions = array(
			 * 	'1.0.1' => 'flush_permalinks',
			 * 	'1.1.0' => 'upgrade_1_1_0',
			 * 	'1.1.3' => 'flush_permalinks',
			 * 	'1.2.0' => 'flush_permalinks',
			 * );
			 */

			if ( empty( $this->upgrade_actions ) || ! is_array( $this->upgrade_actions ) ) {
				return;
			}

			foreach ( $this->upgrade_actions as $version => $method ) {

				/**
				 * do_upgrades was called, but the $upgrade_actions data structure has changed.
				 *
				 * This should never happen.
				 */
				if ( is_array( $method ) ) {
					return;
				}

				if ( self::requires_upgrade( $this->db_version, $version ) ) {

					call_user_func( array( $this, $method ) );

				}
			}
		}

		/**
		 * Saves a log of the version to version upgrades made.
		 *
		 * @return 	void
		 * @access 	protected
		 * @since 	1.0.0
		 */
		protected function save_upgrade_log() {
			$log = get_option( $this->upgrade_log_key );

			if ( false === $log || ! is_array( $log ) ) {
				$log = array();
			}

			$log[] = array(
				'timestamp'		=> time(),
				'from'			=> $this->db_version,
				'to'			=> $this->edge_version,
			);

			update_option( $this->upgrade_log_key, $log );
		}

		/**
		 * Upgrade complete. This saves the new version to the database.
		 *
		 * @return 	void
		 * @access 	protected
		 * @since 	1.0.0
		 */
		protected function update_db_version() {
			update_option( $this->version_key, $this->edge_version );
		}
	}

endif; // End class_exists check
