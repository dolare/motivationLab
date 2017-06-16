<?php
/** Do not allow direct access! */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Class One_And_One_Setup_Wizard_Dispatcher
 * Computes and shows to the corresponding view of the Assistant in the WP Admin
 */
class One_And_One_Setup_Wizard_Dispatcher {

	/**
	 * Get current action and load corresponding view
	 */
	public function dispatch_wizard_actions() {

		$sitetype_transient = 'oneandone_assistant_process_sitetype_user_'.get_current_user_id();
		$theme_transient    = 'oneandone_assistant_process_theme_user_'.get_current_user_id();

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry, you do not have permission to access the 1&1 WP Assistant.', '1and1-wordpress-wizard' ) );
		}

		wp_enqueue_style( '1and1-wp-wizard' );

		if ( isset( $_GET['reset-assistant-options'] ) ) {
			delete_option( 'oneandone_assistant_completed' );
			delete_option( 'oneandone_assistant_sitetype' );
		}

		/** Manage Themes on step 2 */
		if ( $this->is_action( 'choose_appearance' )
			&& ( isset( $_POST['sitetype'] )
				|| get_option( 'oneandone_assistant_sitetype' )
				|| get_transient( $sitetype_transient )
			)
		) {

			if ( isset( $_POST['sitetype'] ) ) {
				$site_type = sanitize_text_field( key( $_POST['sitetype'] ) );
				set_transient( $sitetype_transient, $site_type, 1200 );

			} else if ( get_transient( $sitetype_transient ) ) {
				$site_type = get_transient( $sitetype_transient );

			} else if ( get_option( 'oneandone_assistant_sitetype' ) ) {
				$site_type = get_option( 'oneandone_assistant_sitetype' );
			}

			include_once( One_And_One_Wizard::get_inc_dir_path().'theme-manager.php' );
			$theme_manager = new One_And_One_Theme_Manager();
			$theme_manager->get_theme_manager( $site_type );

			return;
		}

		/** Manage Plugins on step 3 */
		if ( $this->is_action( 'choose_functionality' )
			&& ( isset( $_GET['site_type'] )
				|| get_option( 'oneandone_assistant_sitetype' )
				|| get_transient( $sitetype_transient )
			)
		) {

			if ( isset( $_GET['site_type'] ) ) {
				$site_type = sanitize_text_field( $_GET['site_type'] );
				set_transient( $sitetype_transient, $site_type, 1200 );

			} else if ( get_transient( $sitetype_transient ) ) {
				$site_type = get_transient( $sitetype_transient );

			} else if ( get_option( 'oneandone_assistant_sitetype' ) ) {
				$site_type = get_option( 'oneandone_assistant_sitetype' );
			}

			if ( isset( $_POST['theme'] ) ) {
				/** Added bc of xss protection */
				$theme_id = sanitize_text_field( key( $_POST['theme'] ) );
				set_transient( $theme_transient, $theme_id, 1200 );

			} elseif ( get_transient( $theme_transient ) ) {
				$theme_id = get_transient( $theme_transient );
			} else {
				$theme_id = '';
			}

			include_once( One_And_One_Wizard::get_inc_dir_path().'plugin-manager.php' );
			$plugin_manager = new One_And_One_Plugin_Manager();
			$plugin_manager->get_plugin_manager( $site_type, $theme_id );

			return;
		}

		/** Installation and activation processes */
		if ( $this->is_action( 'activate' ) && isset( $_POST['site_type'] ) && isset( $_POST['theme'] ) ) {

			/** Increase PHP limits to avoid timeouts and memory limits */
			@ini_set( 'error_reporting', 0 );
			@ini_set( 'memory_limit', '256M' );
			@set_time_limit( 300 );

			include_once( One_And_One_Wizard::get_inc_dir_path().'plugin-manager.php' );
			include_once( One_And_One_Wizard::get_inc_dir_path().'plugin-adapter.php' );
			$plugin_manager = new One_And_One_Plugin_Manager();
			$plugin_adapter = new One_And_One_Plugin_Adapter();

			$site_type = sanitize_text_field( $_POST['site_type'] );
			$theme_id  = sanitize_text_field( $_POST['theme'] );
			$messages  = array();

			/** Check nonce */
			check_admin_referer( 'activate' );

			/** Process Theme */
			if ( ! empty( $theme_id ) ) {
				$installed_themes = wp_get_themes();
				$themes_meta      = $plugin_manager->get_theme_meta( $site_type );

				if ( ! array_key_exists( $theme_id, $installed_themes ) ) {
					include_once( One_And_One_Wizard::get_inc_dir_path().'installer.php' );
					One_And_One_Installer::install_theme( $themes_meta[$theme_id] );
				}

				switch_theme( $theme_id );
				update_option( 'oneandone_assistant_theme', $theme_id );

				$theme_name = One_And_One_Sitetype_Filter::get_active_theme_name();

				if ( ! empty( $themes_meta[$theme_id]['name'] ) ) {
					$theme_name = ucwords( $themes_meta[$theme_id]['name'] );
				}

				$messages[] = sprintf( __( 'Theme activated: <strong>%s</strong>', '1and1-wordpress-wizard' ), $theme_name );
			} else {
				$messages[] = __( 'There was no theme selected, so the current theme is still active.', '1and1-wordpress-wizard' );
			}

			/** Process Plugins */
			$plugins_to_activate = array();

			if ( $_POST['plugins'] ) {
				if ( is_array( $_POST['plugins'] ) ) {
					foreach ( $_POST['plugins'] as $item ) {
						$plugins_to_activate[] = sanitize_text_field( $item );
					}
				}
			}

			/** Get all installed plugins */
			$installed_plugins = $plugin_manager->get_installed_plugin_slugs();
			$plugins_meta      = $plugin_manager->get_plugin_meta( $site_type );

			/** Download and install missing plugins first */
			foreach ( $plugins_to_activate as $plugin_to_activate ) {
				if ( ! in_array( $plugin_to_activate, $installed_plugins ) ) {
					if ( ! empty( $plugins_meta[$plugin_to_activate] ) ) {
						include_once( One_And_One_Wizard::get_inc_dir_path().'installer.php' );
						if ( $plugin_installed = One_And_One_Installer::install_plugin( $plugins_meta[$plugin_to_activate] ) ) {
							$installed_plugins = array_merge( $installed_plugins, $plugin_installed );
						}
					}
				}
			}


			foreach ( $installed_plugins as $plugin_path => $plugin_slug ) {
				try {
					$is_active_plugin = $plugin_manager->is_active_plugin( $plugin_slug );

					if ( $is_active_plugin && ! in_array( $plugin_slug, $plugins_to_activate ) ) {
						deactivate_plugins( $plugin_path );
						$messages[] = sprintf( __( 'Plugin deactivated: <strong>%s</strong>', '1and1-wordpress-wizard' ), ucwords( $plugins_meta[$plugin_slug]->name ) );

					} else if ( ! $is_active_plugin && in_array( $plugin_slug, $plugins_to_activate ) ) {
						//fix for woocommerce stuff
						if (in_array('woocommerce-germanized', $plugins_to_activate)
							&& in_array('woocommerce', $plugins_to_activate) &&
							$plugin_slug == 'woocommerce-germanized') {
							WC()->init();
						}

						if (in_array('woocommerce', $plugins_to_activate)) {
							if (!function_exists('wc_get_screen_ids')) {
								function wc_get_screen_ids() {
									return array();
								}
							}
						}

						$result = activate_plugin( plugin_basename( $plugin_path ) );


						if ( is_wp_error( $result ) ) {
							if ( ! empty( $result->errors['plugin_not_found'][0] ) ) {
								error_log( $result->errors['plugin_not_found'][0] );
							}
						}
						$messages[] = sprintf( __( 'Plugin activated: <strong>%s</strong>', '1and1-wordpress-wizard' ), ucwords( $plugins_meta[$plugin_slug]->name ) );
                        $is_active_plugin = true;
					}

					if ( $is_active_plugin ) {
						if ( method_exists( $plugin_adapter, 'adapt_'.$plugin_slug ) ) {
							call_user_func( array( $plugin_adapter, 'adapt_'.$plugin_slug ) );
						}
					}
				}
				catch ( Exception $e ) {
					error_log( $e->getMessage() );
				}
			}

			?>

			<div class="wrap">
				<h2><?php esc_html_e( 'setup_assistant_header', '1and1-wordpress-wizard' ) ?></h2>

				<h2><?php esc_html_e( 'Assistant completed.', '1and1-wordpress-wizard' ); ?></h2>
				<p><?php printf( __( 'You are now ready to use your WordPress installation. You can continue customizing the selected theme or write your first blog post.', '1and1-wordpress-wizard' ), 'plugins.php', 'themes.php' ) ?></p>

				<p>
					<a href="<?php echo admin_url( 'post-new.php' ); ?>" class="button" target="_parent"><?php esc_html_e( 'Write a post', '1and1-wordpress-wizard' ); ?></a>
					&nbsp; &nbsp;
					<a href="<?php echo admin_url( 'index.php' ); ?>" class="button" title="<?php _e( 'Go to Dashboard' ); ?>" target="_parent"><?php _e( 'Go to Dashboard' ); ?></a>
					&nbsp; &nbsp;

					<?php do_action( 'oneandone_post_setup_custom_buttons' ); ?>

					<a href="<?php echo home_url( '/' ); ?>" class="button button-primary" title="<?php _e( 'View your site' ); ?>" target="_parent"><?php _e( 'View your site' ); ?></a>
				</p>

                <hr />

                <h3><?php esc_html_e( 'Processing setup:', '1and1-wordpress-wizard' ); ?></h3>

                <?php foreach ( $messages as $message ): ?>
                    <div class="oneandone-update-notice">
                        <p><?php echo $message; ?></p>
                    </div>
                <?php endforeach; ?>

                <script type="text/javascript">
					/* <![CDATA[ */
					jQuery(function ($) {
						$('.update-nag').hide();
						$('.is-dismissible').hide();
					});
					/* ]]> */
				</script>
			</div>

			<?php

			/** store assistant is completed */
			update_option( 'oneandone_assistant_completed', true );

			/** store website type in db */
			update_option( 'oneandone_assistant_sitetype', $site_type );

			$pluginsImploded = implode( ',', $plugins_to_activate );
			/** store plugins */
			update_option( 'oneandone_assistant_plugins', $pluginsImploded );

			delete_transient( $sitetype_transient );
			delete_transient( $theme_transient );

			/** Log the installation process */
			$this->log( array( 'website_type' => $site_type, 'method' => 'finished', 'theme_selected' => $theme_id, 'plugins_selected' => $pluginsImploded ) );

			return;
		}

		/** If something is missing show the start of the wizard */
		include( One_And_One_Wizard::get_views_dir_path().'setup-wizard-site-select-step.php' );
		One_And_One_Site_Selection_Step::get_site_selection();
	}

	/**
	 * Check what the current action is
	 *
	 * @param  string $action
	 * @return bool
	 */
	private function is_action( $action ) {
		if ( isset( $_GET['setup_action'] ) && $action == $_GET['setup_action'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Log errors
	 *
	 * @param  array $args
	 * @return bool
	 */
	private function log( $args ) {
		if ( ! oneandone_is_logging_enabled() ) {
			return false;
		}

		include_once( One_And_One_Wizard::get_inc_dir_path().'stats-logger.php' );
		One_And_One_StatsLogger::logRemote( $args );

		return true;
	}

}
