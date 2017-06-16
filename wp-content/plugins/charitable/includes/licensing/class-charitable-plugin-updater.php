<?php

// uncomment this line for testing
//set_site_transient( 'update_plugins', null );

/**
 * Allows plugins to use their own update API.
 *
 * This is part of the EDD Software Licensing suite. Just renamed here for Charitable. 
 *
 * @author Pippin Williamson
 * @version 1.6
 */
class Charitable_Plugin_Updater {
	private $api_url   = '';
	private $api_data  = array();
	private $name      = '';
	private $slug      = '';

	/**
	 * Class constructor.
	 *
	 * @uses    plugin_basename()
	 * @uses    hook()
	 *
	 * @param   string $api_url The URL pointing to the custom API endpoint.
	 * @param   string $plugin_file Path to the plugin file.
	 * @param   array $api_data Optional data to send with API calls.
	 * @return  void
	 */
	public function __construct( $api_url, $plugin_file, $api_data = null ) {
		$this->api_url  = trailingslashit( $api_url );
		$this->api_data = $api_data;
		$this->name     = plugin_basename( $plugin_file );
		$this->slug     = basename( $plugin_file, '.php' );
		$this->version  = $api_data['version'];

		// Set up hooks.
		$this->init();
	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses    add_filter()
	 *
	 * @return  void
	 * @access  public
	 * @since   1.0.0
	 */
	public function init() {
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'show_changelog' ) );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
	}

	/**
	 * Return the update information for the plugin. 
	 *
	 * @return  false|object
	 * @access  public
	 * @since   1.4.0
	 */
	public function get_version_info() {

		if ( ! isset( $this->version_info ) ) {

			$update_transient = get_site_transient( 'update_plugins' );

			if ( ! isset( $update_transient->response[ $this->name ] ) ) {

				$this->version_info = false; 
			}

			else {

				$this->version_info = $update_transient->response[ $this->name ];

			}

		}

		return $this->version_info;
	}   

	/**
	 * Show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string  $file
	 * @param array   $plugin
	 */
	public function show_update_notification( $file, $plugin ) {

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( ! is_multisite() ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( Charitable_Licenses::get_instance(), 'check_for_updates' ) );

		$update_cache = get_site_transient( 'update_plugins' );

		if ( ! is_object( $update_cache ) || empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$cache_key    = md5( 'edd_plugin_' .sanitize_key( $this->name ) . '_version_info' );
			$version_info = get_transient( $cache_key );

			if( false === $version_info ) {

				$version_info = $this->api_request( 'plugin_latest_version', array( 'slug' => $this->slug ) );

				set_transient( $cache_key, $version_info, 3600 );
			}


			if ( ! is_object( $version_info ) ) {
				return;
			}

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			}

			$update_cache->last_checked = time();
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );

		} else {

			$version_info = $update_cache->response[ $this->name ];

		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( Charitable_Licenses::get_instance(), 'check_for_updates' ) );

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';

			$changelog_link = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );

			if ( empty( $version_info->download_link ) ) {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>.', 'charitable' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version )
				);
			} else {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a> or <a href="%4$s">update now</a>.', 'charitable' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version ),
					esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) )
				);
			}

			echo '</div></td></tr>';
		}
	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @uses    api_request()
	 *
	 * @param   mixed $data
	 * @param   string $action
	 * @param   object $args
	 * @return  object $data
	 * @access  public
	 * @since   1.0.0
	 */
	public function plugins_api_filter( $data, $action = '', $args = null ) {

		if ( $action != 'plugin_information' ) {

			return $data;

		}

		if ( ! isset( $args->slug ) || ( $args->slug != $this->slug ) ) {

			return $data;

		}        

		$version_info = $this->get_version_info();

		if ( $version_info ) {

			$data = $version_info;

		}

		return $data;
	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses    get_bloginfo()
	 * @uses    wp_remote_post()
	 * @uses    is_wp_error()
	 *
	 * @param   string $action The requested action.
	 * @param   array $data Parameters for the API action.
	 * @return  false||object
	 * @access  private
	 * @since   1.0.0
	 */
	private function api_request( $action, $data ) {

		$data = array_merge( $this->api_data, $data );

		if ( $data['slug'] != $this->slug ) {
			return;
		}

		if ( empty( $data['license'] ) ) {
			return;
		}

		if ( home_url() == $this->api_url ) {
			return false; // Don't allow a plugin to ping itself
		}

		$api_params = array(
			'edd_action' => 'get_version',
			'license'    => $data['license'],
			'item_name'  => isset( $data['item_name'] ) ? $data['item_name'] : false,
			'item_id'    => isset( $data['item_id'] ) ? $data['item_id'] : false,
			'slug'       => $data['slug'],
			'author'     => $data['author'],
			'url'        => home_url(),
		);

		$request = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
		}

		if ( $request && isset( $request->sections ) ) {
			$request->sections = maybe_unserialize( $request->sections );
		} else {
			$request = false;
		}

		return $request;
	}

	/**
	 * Display the changelog.
	 *
	 * @return  void
	 * @access  public
	 * @since   1.0.0
	 */
	public function show_changelog() {

		if ( empty( $_REQUEST['edd_sl_action'] ) || 'view_plugin_changelog' != $_REQUEST['edd_sl_action'] ) {
			return;
		}

		if ( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['slug'] ) ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_die( __( 'You do not have permission to install plugin updates', 'charitable' ), __( 'Error', 'charitable' ), array( 'response' => 403 ) );
		}

		$response = $this->get_version_info();

		if ( $response && isset( $response->sections['changelog'] ) ) {
			echo '<div style="background:#fff;padding:10px;">' . $response->sections['changelog'] . '</div>';
		}

		exit;
	}

}
