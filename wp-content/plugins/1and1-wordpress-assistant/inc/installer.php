<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

include_once( ABSPATH.'/wp-admin/includes/plugin.php' );

class One_And_One_Installer {
	static public function install_plugin( $plugin_meta ) {
		include_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
		include_once 'automatic-installer-skin.php';

		$upgrader = new Plugin_Upgrader( new One_And_One_Automatic_Installer_Skin() );

		if ( $upgrader->install( $plugin_meta->download_link ) ) {
			return self::update_plugin_list( $plugin_meta->slug );
		}

		return false;
	}

	static public function update_plugin_list( $plugin_slug ) {
		wp_clean_plugins_cache( true );
		$plugins = get_plugins();

		foreach ( $plugins as $key => $plugin ) {
			$parts = explode( '/', $key );

			if ( $parts[0] == $plugin_slug ) {
				return array( $key => $plugin_slug );
			}
		}

		return false;
	}

	static public function install_theme( $theme_meta ) {
		include_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
		include_once 'automatic-installer-skin.php';

		$installer = new Theme_Upgrader( new One_And_One_Automatic_Installer_Skin() );

		return $installer->install( $theme_meta['download_link'] );
	}
}