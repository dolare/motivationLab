<?php
/**
 * Check if WordPress is managed (including safemode) or not
 *
 * @return boolean
 */
function oneandone_is_managed() {
	if (
		// installation is managed if .managed file is set
		oneandone_is_managed_file_existing() ||
		// check if WordPress is installed in a safemode path
		oneandone_is_wordpress_root_safemode_path()
	) {
		return true;
	}

	return false;
}

/**
 * Check if the plugins must be used or not, based on its location:
 *
 * * wp-content/plugins -> optional
 * * wp-content/mu-plugins -> required
 *
 * @return boolean
 */
function oneandone_is_must_use_plugin_folder() {
	$plugin_path = One_And_One_Wizard::get_plugin_dir_path();

	if ( strpos( $plugin_path, 'mu-plugins' ) === false ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Open/closed managed contracts have a `.managed` file in the root folder
 *
 * @return boolean
 */
function oneandone_is_managed_file_existing() {
	$abspath = One_And_One_Wizard::get_abspath();

	// open/closed managed contracts has a `.managed` file in the root folder
	if (
		file_exists( $abspath . '/.managed' ) &&
		is_readable( $abspath . '/.managed' )
	) {
		return true;
	} else {
		return false;
	}
}

/**
 * Check if plugin is a safemode installation
 *
 * @return boolean
 */
function oneandone_is_wordpress_root_safemode_path() {
	$abspath = One_And_One_Wizard::get_abspath();

	if (
		preg_match( '/^app\d{5,15}$/', basename( $abspath ) ) ||
		preg_match( '/^trial\d{9,15}$/', basename( $abspath ) )
	) {
		return true;
	} else {
		return false;
	}
}

/**
 * Check file owner of a custom file
 *
 * @return boolean|null
 */
function oneandone_is_file_owner_correct( $file, $owner ) {
	if ( function_exists( 'posix_getpwuid' ) && function_exists( 'fileowner' ) ) {
		$pwuid = posix_getpwuid( fileowner( $file ) );

		if ( isset( $pwuid['name'] ) && $owner === $pwuid['name'] ) {
			return true;
		} else {
			return false;
		}
	}

	return null;
}

/**
 * Check if logging is enabled or not
 *
 * @return boolean
 */
function oneandone_is_logging_enabled() {
	if ( defined( ONEANDONE_MONITOR_ASSISTANT ) && ONEANDONE_MONITOR_ASSISTANT == false ) {
		return false;
	}

	return true;
}
