<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class One_And_One_PreventPluginAndThemeInstall {

	public function __construct() {
		// Deprecated since 3.2.0
		// hide menu pages
		//add_action( 'admin_menu', array( $this, 'hide_menu_pages' ) );

		// Deprecated since 3.2.0
		// remove capabilites
		//add_filter( 'map_meta_cap', array( $this, 'block_caps' ), 10, 2 );

		// modify plugins help content
		add_action( 'admin_head', array( $this, 'modify_plugins_help_content' ) );

		// hide must-use plugins list
		add_filter( 'show_advanced_plugins', array( $this, 'hide_plugin_list' ), 10, 2 );
	}

	public function hide_plugin_list( $show, $type ) {
		if ( $type == 'mustuse' ) {
			$show = false;
		}

		return $show;
	}

	public function modify_plugins_help_content() {
		global $pagenow;
		
		if ( is_admin() && $pagenow == 'plugins.php' ) {
			?>
			<style type="text/css">
				#tab-panel-overview p:last-child {
					display: none;
				}
			</style>
			<?php
		}
	}

	public function block_caps( $caps, $cap ) {
		$not_allowed_caps = array(
			'install_themes',
			'delete_themes',
			'edit_themes',
			'switch_themes',
			'delete_plugins',
			'edit_plugins',
			'install_plugins',
			'update_core'
		);

		// allow cap "install" for 
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'plugin-information' ) {
			$not_allowed_caps = array(
				'delete_themes',
				'edit_themes',
				'delete_plugins',
				'edit_plugins',
				'update_core'
			);
		}

		if ( in_array( $cap, $not_allowed_caps ) ) {
			$caps[] = 'do_not_allow';
		}

		return $caps;
	}

	public function hide_menu_pages() {
		global $submenu;

		unset( $submenu['plugins.php'][10] );
		unset( $submenu['plugins.php'][15] );
		unset( $submenu['themes.php'][5] );
	}

}

new One_And_One_PreventPluginAndThemeInstall();
