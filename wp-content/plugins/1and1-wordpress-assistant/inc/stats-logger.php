<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class One_And_One_StatsLogger {

	/**
	 * Entering point for logging
	 */
	public static function logRemote( $args, $managed_only = false ) {
		if ( ! empty( $managed_only ) AND ! oneandone_is_managed() ) {
			return false;
		}

		return self::log( $args );
	}

	/**
	 * returns the ONEANDONE APP Id - if readable - otherwise -1
	 * @return int
	 */
	public static function getOneAndOneAppId() {
		if ( defined( 'ONEANDONE_MANAGED_APP_ID' ) ) {
			return ONEANDONE_MANAGED_APP_ID;
		}

		$appId  = - 1;
		$market = 'XX';

		$managedFileName = ABSPATH.'/.managed';

		if ( @is_readable( $managedFileName ) ) {
			$ini_array = @parse_ini_file( $managedFileName );

			if ( isset( $ini_array['install_id'] ) && ( ! empty( $ini_array['install_id'] ) ) ) {
				$appId = $ini_array['install_id'];
			}

			if ( isset( $ini_array['market'] ) && ( ! empty( $ini_array['market'] ) ) ) {
				$market = $ini_array['market'];
			}
		}

		define( 'ONEANDONE_MANAGED_APP_ID', $appId );
		define( 'ONEANDONE_MANAGED_MARKET', $market );

		return ONEANDONE_MANAGED_APP_ID;
	}

	/**
	 * returns the ONEANDONE APP Id - if readable - otherwise -1
	 * @return int
	 */
	public static function getOneAndOneAppMarket() {
		if ( defined( 'ONEANDONE_MANAGED_MARKET' ) ) {
			return ONEANDONE_MANAGED_MARKET;
		}

		self::getOneAndOneAppId();

		return ONEANDONE_MANAGED_MARKET;
	}

	/**
	 * @param $args
	 *
	 * @return array|bool|WP_Error
	 */
	private static function log( $args ) {
		if ( ! oneandone_is_logging_enabled() ) {
			return false;
		}

		$url   = 'https://wp-api.1and1.com/log_assistant.php';
		$appId = self::getOneAndOneAppId();

		$additional_args = array(
			'apiKey'         => 'XXXXXX',
			'domain'         => '',
			'locale'         => get_locale(),
			'wizard_version' => One_And_One_Wizard::VERSION,
			'wp_version'     => $GLOBALS['wp_version'],
			'php_version'    => phpversion(),
			'db_version'     => $GLOBALS['wpdb']->db_version(),
			'appId'          => $appId,
			'market'         => self::getOneAndOneAppMarket()
		);

		try {
			$additional_args['domain'] = get_site_url();
		}
		catch ( Exception $e ) {
			//do nothing
		}

		$post_args = array(
			'method'      => 'POST',
			'timeout'     => 3,
			'redirection' => 5,
			'blocking'    => false, // just send request, don't wait for response
			'user-agent'  => 'agent',
			'body'        => array_merge( $additional_args, $args )
		);

		$response = wp_remote_post( $url, $post_args );

		// no need to check success, because we don't wait for the response
		return $response;
	}
}
