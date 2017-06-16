<?php
/** Do not allow direct access! */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Class One_And_One_Sitetype_Filter
 * Retrieves Use Cases data from the sitetype-config.json
 */
class One_And_One_Sitetype_Filter {

	/**
	 * Get the list of Use Cases,
	 * each one with an array of associated data if $with_data is set to true.
	 * Data includes Use Case's:
	 * - title,
	 * - description,
	 * - image path.
	 *
	 * @param  bool $with_data
	 * @return array | bool
	 */
	public static function get_sitetypes($with_data = true) {
		$config = self::get_config();

		if ( empty( $config ) ) {
			return false;
		}

		$sitetypes = array();
		$data_format = array(
			"headline"    => "",
			"description" => "",
			"image"       => ""
		);

		foreach ( $config->sitetypes as $key => $data ) {
			if ( $key !== 'any' ) {

				if ( $with_data ) {
					$sitetypes[$key] =  array_intersect_key(
						( array ) $data,
						$data_format
					);

				} else {
					$sitetypes[] = $key;
				}
			}
		}

		return $sitetypes;
	}

	/**
	 * Filter the associated themes for a Use Case,
	 * among the list of selected themes
	 *
	 * @param  array $themes
	 * @param  string $sitetype
	 * @return array
	 */
	public static function get_filtered_themes( $themes, $sitetype ) {
		$config = self::get_config();

		if ( empty ( $config ) ) {
			return $themes;
		}

		$filtered_themes = array();
		$included_themes = array();
		$request_information_themes = array();

		if ( isset( $config->sitetypes->$sitetype->themes ) ) {
			foreach ( $config->sitetypes->$sitetype->themes as $theme_slug ) {
				foreach ( $themes as $theme ) {
					if ( $theme['id'] == $theme_slug ) {
						$filtered_themes[$theme_slug] = $theme;
						$included_themes[]            = $theme_slug;

						break;
					}
				}

				if ( ! in_array( $theme_slug, $included_themes ) ) {
					$request_information_themes[] = $theme_slug;
				}
			}
		}

		/** Get all information of provided themes from the official WP API but just if not already cached */
		$themes_cached = array();
		$theme_meta_filename = One_And_One_Wizard::get_plugin_dir_path() . 'cache/theme-' . $sitetype . '-meta.txt';
		$updated = false;

		if ( file_exists( $theme_meta_filename ) ) {
			$themes_cached = unserialize( file_get_contents( $theme_meta_filename ) );
		}

		if ( ! empty( $request_information_themes ) ) {
			foreach ( $request_information_themes as $request_information_theme ) {
				if ( ! empty( $themes_cached[$request_information_theme] ) ) {

					/** Handle special case: Theme was installed previously and uninstalled again but cache files were not refreshed */
					if ( ! file_exists( WP_CONTENT_DIR . '/themes/' . $request_information_theme . '/screenshot.png' ) ) {
						if ( ! empty( $themes_cached[$request_information_theme]['screenshot_url'] ) ) {
							$themes_cached[$request_information_theme]['screenshot'][0] = $themes_cached[$request_information_theme]['screenshot_url'];

							$filtered_themes[$request_information_theme] = $themes_cached[$request_information_theme];
							$included_themes[]                           = $request_information_theme;

							continue;
						}

						self::load_theme( $request_information_theme, $filtered_themes, $included_themes );
						$updated = true;

						continue;
					}

					$filtered_themes[$request_information_theme] = $themes_cached[$request_information_theme];
					$included_themes[]                           = $request_information_theme;

					continue;
				}

				self::load_theme( $request_information_theme, $filtered_themes, $included_themes );
				$updated = true;
			}
		}

		/** Save information to cache file to speed up loading performance */
		if ( ! empty( $updated ) ) {
			include_once One_And_One_Wizard::get_plugin_dir_path() . 'inc/cron-update-plugin-meta.php';
			$cron_class = new OneAndOne_Cron_Update_Plugin_Meta();
			$cron_class->update_theme_meta( $filtered_themes, array( $sitetype ) );
		}

		/** If active theme is not in list, then add it */
		$theme_active = wp_get_theme();
		$theme_active_stylesheet = $theme_active->get_stylesheet();

		if ( ! in_array( $theme_active_stylesheet, $included_themes ) ) {
			self::load_theme( $theme_active_stylesheet, $filtered_themes, $included_themes );
		}

		/** Sort arrays - first entry must be the active theme */
		self::sort_theme_array( $filtered_themes, $theme_active_stylesheet, $filtered_themes[$theme_active_stylesheet] );

		return $filtered_themes;
	}

	/**
	 * Get the active theme's name
	 *
	 * @return string
	 */
	public static function get_active_theme_name() {
		$theme_name = ucwords( str_replace( array( '-', '_' ), ' ', get_template() ) );

		return $theme_name;
	}

	/**
	 * Filter the associated plugins for a Use Case, in a given Plugin category
	 *
	 * @param  array  $plugins
	 * @param  string $sitetype
	 * @param  string $configkey
	 * @return array
	 */
	public static function get_filtered_plugins_by_configkey( $plugins, $sitetype, $configkey ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );

		$config = self::get_config();
		$filtered_plugins = array();

		if ( empty( $config ) || empty ( $configkey ) ) {
			return array();
		}

		self::load_plugins(
			self::get_plugins_slugs( $sitetype, ( array ) $configkey ),
			$filtered_plugins,
			$plugins
		);

		/** Return the filtered list with plugins and their data */
		return $filtered_plugins;
	}

	/**
	 * Get all categorized plugins for a Use Case
	 *
	 * @param  array $plugins
	 * @param  string $sitetype
	 * @return array
	 */
	public static function get_filtered_category_plugins( $plugins, $sitetype ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );

		$config = self::get_config();

		if ( empty( $config ) ) {
			return array();
		}

		$filtered_plugins = array();

		if ( ! empty( $config->sitetypes->$sitetype->plugin_categories )
		     && is_array( $config->sitetypes->$sitetype->plugin_categories )) {

			foreach ( $config->sitetypes->$sitetype->plugin_categories as $category ) {

				if ( $category != 'recommended' ) {
					$filtered_plugins[$category] = array();

					self::load_plugins(
						self::get_plugins_slugs( $sitetype, array( $category ) ),
						$filtered_plugins[$category],
						$plugins
					);
				}
			}
		}

		return $filtered_plugins;
	}

	/**
	 * Get plugins (as slugs) for a Use Case, in a given config section
	 *
	 * @param  string $sitetype
	 * @param  array  $include_categories
	 * @return array
	 */
	public static function get_plugins_slugs( $sitetype, $include_categories = array() ) {
		$config = self::get_config();

		if ( empty( $config->plugins ) || empty( $sitetype ) ) {
			return array();
		}

		$plugins_slugs = array();
		$include_categories = ( array ) $include_categories;

		foreach ( $config->plugins as $plugin_slug => $plugin_config ) {

			if ( ! in_array( $plugin_slug, $plugins_slugs ) ) {

				$is_plugin_available_for_sitetype =
					( ! empty( $plugin_config->category->any )
					  || ! empty( $plugin_config->category->$sitetype ) );

				$is_plugin_available_for_language =
					( $plugin_config->languages == 'any'
					  || ( is_array( $plugin_config->languages )
					       && in_array( get_locale(), $plugin_config->languages ) ) );

				$is_plugin_available_for_category =
					( $is_plugin_available_for_sitetype
					  && ( empty( $include_categories )
					     || ( ! empty( $plugin_config->category->any )
					          && in_array( $plugin_config->category->any, $include_categories ) )
					     || ( ! empty( $plugin_config->category->$sitetype )
					          && in_array( $plugin_config->category->$sitetype, $include_categories ) ) ) );

				if ( $is_plugin_available_for_category && $is_plugin_available_for_language ) {
					$plugins_slugs[] = $plugin_slug;
				}
			}
		}

		return $plugins_slugs;
	}

	/**
	 * Retrieve the configuration from the WP transient
	 * https://codex.wordpress.org/Transients_API
	 *
	 * @return mixed
	 */
	public static function get_config() {
		$config = get_transient( 'one_and_one_sitetype_config' );

		if ( ! $config || isset( $_GET['refresh_sitetype_config'] ) ) {
			$json = file_get_contents( One_And_One_Wizard::get_plugin_dir_path() . 'sitetype-config.json' );

			if ( $json ) {
				$config = json_decode( $json );
				set_transient( 'one_and_one_sitetype_config', $config, 300 );

				return $config;
			}

			return false;
		}

		return $config;
	}

	/**
	 * Sort the array of themes, active theme first
	 *
	 * @param array  $theme_array
	 * @param string $theme_active_stylesheet
	 * @param object $theme_object
	 */
	private static function sort_theme_array( &$theme_array, $theme_active_stylesheet, $theme_object ) {
		if ( ! empty( $theme_array[$theme_active_stylesheet] ) ) {
			unset( $theme_array[$theme_active_stylesheet] );
		}

		$theme_array                           = array_reverse( $theme_array, true );
		$theme_array[$theme_active_stylesheet] = $theme_object;

		/** Set active parameter if not set yet, needed in the output for the correct CSS class */
		if ( empty( $theme_array[$theme_active_stylesheet]['active'] ) ) {
			$theme_array[$theme_active_stylesheet]['active'] = true;
		}

		$theme_array = array_reverse( $theme_array, true );
	}

	/**
	 * Retrieve a theme through the WP API and save it in the $f.iltered_themes
	 *
	 * @param string $theme_slugs
	 * @param array  $filtered_themes
	 * @param array  $included_themes
	 */
	private static function load_theme( $theme_slugs, &$filtered_themes, &$included_themes ) {
		$api_request = wp_remote_get( 'https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=' . $theme_slugs );

		if ( $api_request['response']['code'] == 200 AND ! empty( $api_request['body'] ) ) {

			$theme_api_request = json_decode( $api_request['body'], true );
			$theme_api_request['id'] = $theme_api_request['slug'];

			if ( ! empty( $theme_api_request['screenshot_url'] ) ) {
				$theme_api_request['screenshot'] = array( $theme_api_request['screenshot_url'] );
			}

			if ( ! empty( $theme_api_request['sections']['description'] ) ) {
				$theme_api_request['description'] = $theme_api_request['sections']['description'];
			}

			$filtered_themes[$theme_api_request['slug']] = $theme_api_request;
			$included_themes[] = $theme_api_request['slug'];
		}
	}

	/**
	 * Retrieve plugins data using the existing plugins data + the API
	 *
	 * @param  array $plugin_slugs
	 * @param  array $filtered_plugins
	 * @param  array $included_plugins
	 */
	public static function load_plugins( $plugin_slugs, &$filtered_plugins, $included_plugins ) {

		foreach ( $plugin_slugs as $slug ) {
			$plugin_selected = false;

			foreach ( $included_plugins as $plugin ) {
				if ( $plugin->slug == $slug ) {
					$filtered_plugins[] = $plugin;
					$plugin_selected = true;
					break;
				}
			}
			if ( empty( $plugin_selected ) ) {
				$filtered_plugins[] = plugins_api(
					'plugin_information',
					array(
						'slug'   => $slug,
						'fields' => array(
							'icons'             => true,
							'short_description' => true
						)
					)
				);
			}
		}
	}
}
