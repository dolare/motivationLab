<?php
/**
 * Charitable Install class.
 * 
 * The responsibility of this class is to manage the events that need to happen 
 * when the plugin is activated.
 *
 * @package		Charitable
 * @subpackage	Charitable/Charitable Install
 * @copyright 	Copyright (c) 2017, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 		1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Install' ) ) : 

	/**
	 * Charitable_Install
	 *
	 * @since 		1.0.0
	 */
	class Charitable_Install {

		/**
		 * Install the plugin. 
		 *
		 * @access 	public
		 * @since 	1.0.0
		 */
		public function __construct() {
			$this->setup_roles();
			$this->create_tables();		
			$this->setup_upgrade_log();		

			set_transient( 'charitable_install', 1, 0 );
		}	

		/**
		 * Finish the plugin installation. 
		 *
		 * @return  void
		 * @access  public
		 * @static
		 * @since   1.3.4
		 */
		public static function finish_installing() {		
			Charitable_Cron::schedule_events();

			add_action( 'init', 'flush_rewrite_rules' );
		}

		/**
		 * Create wp roles and assign capabilities
		 *
		 * @return 	void
		 * @access 	protected
		 * @since 	1.0.0
		 */
		protected function setup_roles() {
			require_once( 'users/class-charitable-roles.php' );
			$roles = new Charitable_Roles();
			$roles->add_roles();
			$roles->add_caps();
		}

		/**
		 * Create database tables. 
		 *
		 * @return 	void
		 * @access 	protected
		 * @since 	1.0.0
		 */
		protected function create_tables() {
			require_once( 'db/abstract-class-charitable-db.php' );

			require_once( 'db/class-charitable-campaign-donations-db.php' );
			$table_helper = new Charitable_Campaign_Donations_DB();
			$table_helper->create_table();

			require_once( 'db/class-charitable-donors-db.php' );
			$table_helper = new Charitable_Donors_DB();
			$table_helper->create_table();
		}

		/**
		 * Set up the upgrade log. 
		 *
		 * @return  void
		 * @access  protected
		 * @since   1.3.0
		 */
		protected function setup_upgrade_log() {
			require_once( 'admin/upgrades/class-charitable-upgrade.php' );
			Charitable_Upgrade::get_instance()->populate_upgrade_log_on_install();
		}
	}

endif;
