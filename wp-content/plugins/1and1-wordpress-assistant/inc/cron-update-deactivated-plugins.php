<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class OneAndOne_Cron_Update_Deactivated_Plugins {

	public function __construct() {
		// Setup cronjob but only in Managed mode
		include_once( One_And_One_Wizard::get_inc_dir_path().'stats-logger.php' );

		if ( oneandone_is_managed() ) {
			add_action( 'wp', array( $this, 'setup_schedule' ) );
			add_action( 'oneandone_cron_update_deactivated_plugins', array( $this, 'update_deactivated_plugins' ) );
			// add new cronjob intervals for development
			//add_filter( 'cron_schedules', array( $this, 'cron_add_debug' ) );
		}
	}

	public function setup_schedule() {
		// $recurrence = 'debug';
		$recurrence = 'hourly';

		if ( ! wp_next_scheduled( 'oneandone_cron_update_deactivated_plugins' ) ) {
			wp_schedule_event( time(), $recurrence, 'oneandone_cron_update_deactivated_plugins' );
		}
	}

	public function update_deactivated_plugins() {
		include_once ABSPATH.'wp-admin/includes/plugin.php';
		include_once ABSPATH.'wp-admin/includes/theme.php';
		include_once ABSPATH.'wp-includes/pluggable.php';
		include_once ABSPATH.'wp-admin/includes/file.php';
		include_once ABSPATH.'wp-admin/includes/misc.php';
		include_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
		
		// Update deactivated themes
		register_theme_directory( WP_CONTENT_DIR.'/themes' );
		$upgrader     = new Theme_Upgrader();
		$themes       = wp_prepare_themes_for_js();
		$active_theme = wp_get_theme();

		wp_update_themes();
		$theme_status = array( 'active' => array(), 'notActive' => array() );

		foreach ( $themes as $theme ) {
			if ( $theme['id'] == $active_theme->get_template() ) {
				$theme_status['active'][$theme['id']] = array( 'v' => $theme['version'] );
				continue;
			}

			$success = $upgrader->upgrade( $theme['id'], array( 'clear_update_cache' => false ) );

			if ( $success ) {
				$theme_status['notActive'][$theme['id']]['upSt'] = 'success';
				$theme_status['notActive'][$theme['id']]['v']    = $theme['version'];
				$this->log( 'theme updated: '.$theme['id'] );
			} elseif ( is_wp_error( $success ) ) {
				$theme_status['notActive'][$theme['id']]['upSt'] = 'failed';
				$theme_status['notActive'][$theme['id']]['v']    = $theme['version'];
				$this->log( 'theme update failed: '.$theme['id'] );
			} else {
				$theme_status['notActive'][$theme['id']]['upSt'] = 'noNeed';
				$theme_status['notActive'][$theme['id']]['v']    = $theme['version'];
				$this->log( 'theme update not needed: '.$theme['id'] );
			}

		}

		wp_update_themes();
		$this->logRemoteThemes( $theme_status );

		// Update deactivated plugins
		$upgrader = new Plugin_Upgrader();
		$plugins  = get_plugins();

		wp_update_plugins();
		$plugin_status = array( 'active' => array(), 'notActive' => array() );

		foreach ( $plugins as $key => $plugin ) {
			if ( is_plugin_active( $key ) ) {
				$plugin_status['active'][$key] = array( 'v' => $plugin['Version'] );
				continue;
			}

			$success = $upgrader->upgrade( $key, array( 'clear_update_cache' => false ) );

			if ( $success ) {
				$plugin_status['notActive'][$key]['upSt'] = 'success';
				$plugin_status['notActive'][$key]['v']    = $plugin['Version'];
				$this->log( 'plugin updated: '.$key );
			} elseif ( is_wp_error( $success ) ) {
				$plugin_status['notActive'][$key]['upSt'] = 'failed';
				$plugin_status['notActive'][$key]['v']    = $plugin['Version'];
				$this->log( 'plugin update failed: '.$key );
			} else {
				$plugin_status['notActive'][$key]['upSt'] = 'noNeed';
				$plugin_status['notActive'][$key]['v']    = $plugin['Version'];
				$this->log( 'plugin update not needed: '.$key );
			}
		}

		wp_update_plugins();
		$this->logRemotePlugins( $plugin_status );
	}

	public function log( $text ) {
		if ( ! file_exists( One_And_One_Wizard::get_plugin_dir_path().'/log' ) ) {
			mkdir( One_And_One_Wizard::get_plugin_dir_path().'/log' );
		}

		file_put_contents( One_And_One_Wizard::get_plugin_dir_path().'/log/cron-update-deactivated-plugins.log', date( '[ d.m.Y H:i:s ] ' ).$text."\n", FILE_APPEND );
	}

	public function cron_add_debug( $schedules ) {
		$schedules['debug'] = array(
			'interval' => 10,
			'display'  => __( 'Once every 10 seconds' )
		);

		return $schedules;
	}

	public function logRemotePlugins( $plugin_status = array() ) {
		$args = array( 'website_type' => 'na', 'method' => 'cron-update-decativated-plugins', 'plugins_selected' => '' );

		if ( ! empty( $plugin_status ) ) {
			$args['plugins_selected'] = json_encode( $plugin_status, true );
		}

		$status = $this->logRemote( $args );

		return $status;
	}

	public function logRemoteThemes( $theme_status = array() ) {
		$args = array( 'website_type' => 'na', 'method' => 'cron-update-decativated-themes', 'plugins_selected' => '' );

		if ( ! empty( $theme_status ) ) {
			$args['theme_selected'] = json_encode( $theme_status, true );
		}

		$status = $this->logRemote( $args );

		return $status;
	}

	public function logRemote( $extra_args = array() ) {
		$args = array( 'website_type' => 'na', 'method' => 'cron-update-decativated-plugins-themes', 'theme_selected' => 'na', 'plugins_selected' => '' );

		if ( ! empty( $extra_args ) ) {
			$args = array_merge( $args, $extra_args );
		}

		$status = One_And_One_StatsLogger::logRemote( $args );

		return $status;
	}
}

new OneAndOne_Cron_Update_Deactivated_Plugins();
