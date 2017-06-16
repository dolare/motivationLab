<?php
/**
 * Class to assist with the setup of extension licenses.
 *
 * @package     Charitable/Classes/Charitable_Licenses
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Licenses' ) ) :

	/**
	 * Charitable_Licenses
	 *
	 * @since       1.0.0
	 */
	class Charitable_Licenses {

		/**
		 * The base URL used for updates.
		 *
		 * @var string
		 */
		const UPDATE_URL = 'https://www.wpcharitable.com';

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Licenses|null
		 * @access  private
		 * @static
		 */
		private static $instance = null;

		/**
		 * All the registered products requiring licensing.
		 *
		 * @var     array
		 * @access  private
		 */
		private $products;

		/**
		 * All the stored licenses.
		 *
		 * @var     array
		 * @access  private
		 */
		private $licenses;

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @return  Charitable_Licenses
		 * @access  public
		 * @since   1.2.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Licenses();
			}

			return self::$instance;
		}

		/**
		 * Create class object.
		 *
		 * @access  private
		 * @since   1.0.0
		 */
		private function __construct() {
			$this->products = array();

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );
			add_action( 'charitable_deactivate_license', array( $this, 'deactivate_license' ) );
		}

		/**
		 * Checks for any Charitable extensions with updates.
		 *
		 * @param  	array $_transient_data The plugin updates data.
		 * @return  array
		 * @access  public
		 * @since   1.4.0
		 */
		public function check_for_updates( $_transient_data ) {
			global $pagenow;

			if ( ! is_object( $_transient_data ) ) {
				$_transient_data = new stdClass;
			}

			if ( 'plugins.php' === $pagenow && is_multisite() ) {
				return $_transient_data;
			}

			/* Loop over our licensed products and check whether any are missing transient data. */
			$missing_data = array();

			foreach ( $this->get_licenses() as $product_key => $license_details ) {

				if ( ! is_array( $license_details ) ) {
					continue;
				}

				$product = $this->get_product_license_details( $product_key );

				$plugin_file = plugin_basename( $product['file'] );

				if ( empty( $_transient_data->response ) || empty( $_transient_data->response[ $plugin_file ] ) ) {

					$missing_data[ $plugin_file ] = $product;

				}
			}

			/* If we are missing data for any of our products, check whether any have an update. */
			if ( ! empty( $missing_data ) ) {

				$versions = $this->get_versions();

				if ( ! empty( $versions ) ) {

					foreach ( $missing_data as $plugin_file => $product ) {

						if ( ! isset( $versions[ $product['name'] ] ) ) {
							continue;
						}

						$version_info = $versions[ $product['name'] ];

						if ( version_compare( $product['version'], $version_info['new_version'], '<' ) ) {

							if ( isset( $version_info['sections'] ) ) {
								$version_info['sections'] = maybe_unserialize( $version_info['sections'] );
							}

							$_transient_data->response[ $plugin_file ] = (object) $version_info;

						}

						$_transient_data->last_checked = time();
						$_transient_data->checked[ $plugin_file ] = $product['version'];

					}
				}
			}

			return $_transient_data;
		}

		/**
		 * Register a product that requires licensing.
		 *
		 * @param   string $item_name The title of the product.
		 * @param   string $author The author of the product.
		 * @param   string $version The current product version we have installed.
		 * @param 	string $file The path to the plugin file.
		 * @param   string $url The base URL where the plugin is licensed. Defaults to Charitable_Licenses::UPDATE_URL.
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function register_licensed_product( $item_name, $author, $version, $file, $url = false ) {
			if ( ! $url ) {
				$url = Charitable_Licenses::UPDATE_URL;
			}

			$product_key = $this->get_item_key( $item_name );

			$this->products[ $product_key ] = array(
				'name'      => $item_name,
				'author'    => $author,
				'version'   => $version,
				'url'       => $url,
				'file'      => $file,
			);

			$licenses = $this->get_licenses();
			$license = isset( $licenses[ $product_key ]['license'] ) ? trim( $licenses[ $product_key ]['license'] ) : '';

			new Charitable_Plugin_Updater( $url, $file, array(
				'version'   => $version,
				'license'   => $license,
				'item_name' => $item_name,
				'author'    => $author,
			) );
		}

		/**
		 * Return the list of products requiring licensing.
		 *
		 * @return  array[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_products() {
			return $this->products;
		}

		/**
		 * Return a specific product's details.
		 *
		 * @param 	string $item The item for which we are getting product details.
		 * @return  string[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_product_license_details( $item ) {
			return isset( $this->products[ $item ] ) ? $this->products[ $item ] : false;
		}

		/**
		 * Returns whether the given product has a valid license.
		 *
		 * @param   string $item The item to check.
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function has_valid_license( $item ) {
			$license = $this->get_license_details( $item );

			if ( ! $license || ! isset( $license['valid'] ) ) {
				return false;
			}

			return $license['valid'];
		}

		/**
		 * Returns the license details for the given product.
		 *
		 * @param   string $item The item to get the license for.
		 * @return  mixed[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_license( $item ) {
			$license = $this->get_license_details( $item );

			if ( ! $license || ! is_array( $license )  ) {
				return false;
			}

			return $license['license'];
		}

		/**
		 * Returns the active license details for the given product.
		 *
		 * @param   string $item The item to get active licensing details for.
		 * @return  mixed[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_license_details( $item ) {
			$licenses = $this->get_licenses();

			if ( ! isset( $licenses[ $item ] ) ) {
				return false;
			}

			return $licenses[ $item ];
		}

		/**
		 * Return the list of licenses.
		 *
		 * Note: The licenses are not necessarily valid. If a user enters an invalid
		 * license, the license will be stored but it will be flagged as invalid.
		 *
		 * @return  array[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_licenses() {
			if ( ! isset( $this->licenses ) ) {
				$this->licenses = charitable_get_option( 'licenses', array() );
			}

			return $this->licenses;
		}

		/**
		 * Verify a license.
		 *
		 * @param   string $item The item to verify.
		 * @param   string $license The license key for the item.
		 * @return  mixed[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function verify_license( $item, $license ) {
			$license = trim( $license );

			if ( $license === $this->get_license( $item ) ) {
				return $this->get_license_details( $item );
			}

			$product_details = $this->get_product_license_details( $item );

			/* This product was not correctly registered. */
			if ( ! $product_details ) {
				return;
			}

			/* Data to send in our API request */
			$api_params = array(
				'edd_action' => 'activate_license',
				'license' => $license,
				'item_name' => urlencode( $product_details['name'] ),
				'url' => home_url(),
			);

			/* Call the custom API */
			$response = wp_remote_post( $product_details['url'], array(
				'timeout' => 15,
				'sslverify' => false,
				'body' => $api_params,
			) );

			/* Make sure the response came back okay */
			if ( is_wp_error( $response ) ) {
				return;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			return array(
				'license' => $license,
				'expiration_date' => $license_data->expires,
				'valid' => ( 'valid' === $license_data->license ),
			);
		}

		/**
		 * Return the URL to deactivate a specific license. 
		 *
		 * @param   string $item The item to deactivate.
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_license_deactivation_url( $item ) {
			return esc_url( add_query_arg( array(
				'charitable_action' => 'deactivate_license',
				'product_key'       => $item,
				'_nonce'            => wp_create_nonce( 'license' ),
			), admin_url( 'admin.php?page=charitable-settings&tab=licenses' ) ) );
		}

		/**
		 * Deactivate a license.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function deactivate_license() {
			if ( ! wp_verify_nonce( $_REQUEST['_nonce'], 'license' ) ) {
				wp_die( esc_attr__( 'Cheatin\' eh?!', 'charitable' ) );
			}

			$product_key = isset( $_REQUEST['product_key'] ) ? $_REQUEST['product_key'] : false;

			/* Product key must be set */
			if ( false === $product_key ) {
				wp_die( esc_attr__( 'Missing product key', 'charitable' ) );
			}

			$product = $this->get_product_license_details( $product_key );

			/* Make sure we have a valid product with a valid license. */
			if ( ! $product || ! $this->has_valid_license( $product_key ) ) {
				wp_die( esc_attr__( 'This product is not valid or does not have a valid license key.', 'charitable' ) );
			}

			$license = $this->get_license( $product_key );

			/* Data to send to wpcharitable.com to deactivate the license. */
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license' => $license,
				'item_name' => urlencode( $product['name'] ),
				'url' => home_url(),
			);

			/* Call the custom API. */
			$response = wp_remote_post( $product['url'], array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			/* Make sure the response came back okay */
			if ( is_wp_error( $response ) ) {
				return;
			}

			/* Decode the license data */
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			$settings = get_option( 'charitable_settings' );

			unset( $settings['licenses'][ $product_key ] );

			update_option( 'charitable_settings', $settings );
		}

		/**
		 * Return a key for the item, based on the item name.
		 *
		 * @param   string $item_name
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_item_key( $item_name ) {
			return strtolower( str_replace( ' ', '_', $item_name ) );
		}

		/**
		 * Return the latest versions of Charitable plugins.
		 *
		 * @return  array
		 * @access  protected
		 * @since   1.4.0
		 */
		protected function get_versions() {
			$versions = wp_cache_get( 'plugin_versions', 'charitable' );

			if ( false === $versions ) {

				$licenses = array();

				foreach ( $this->get_licenses() as $license ) {

					if ( isset( $license['license'] ) ) {
						$licenses[] = $license['license'];
					}
				}

				$response = wp_remote_post(
					Charitable_Licenses::UPDATE_URL . '/edd-api/versions/',
					array(
						'sslverify' => false,
						'timeout' => 15,
						'body' => array(
							'licenses' => $licenses,
							'url'      => home_url(),
						),
					)
				);

				$versions = wp_remote_retrieve_body( $response );

				$versions = json_decode( $versions, true );

				wp_cache_set( 'plugin_versions', $versions, 'charitable' );
			}

			return $versions;
		}
	}

endif; // End class_exists check.
