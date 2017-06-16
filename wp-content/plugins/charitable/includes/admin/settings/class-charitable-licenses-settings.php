<?php
/**
 * Charitable Licenses Settings UI.
 *
 * @package     Charitable/Classes/Charitable_Licenses_Settings
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Licenses_Settings' ) ) :

	/**
	 * Charitable_Licenses_Settings
	 *
	 * @final
	 * @since      1.0.0
	 */
	final class Charitable_Licenses_Settings {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Licenses_Settings|null
		 * @access  private
		 * @static
		 */
		private static $instance = null;

		/**
		 * Create object instance.
		 *
		 * @access  private
		 * @since   1.0.0
		 */
		private function __construct() {
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @return  Charitable_Licenses_Settings
		 * @access  public
		 * @since   1.2.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Licenses_Settings();
			}

			return self::$instance;
		}

		/**
		 * Optionally add the licenses tab.
		 *
		 * @param   string[] $tabs
		 * @return  string[]
		 * @access  public
		 * @since   1.4.7
		 */
		public function maybe_add_licenses_tab( $tabs ) {

			$products = charitable_get_helper( 'licenses' )->get_products();

			if ( empty( $products ) ) {
				return $tabs;
			}

			$tabs = charitable_add_settings_tab(
				$tabs,
				'licenses',
				__( 'Licenses', 'charitable' ),
				array(
					'index' => 3,
				)
			);

			return $tabs;

		}

		/**
		 * Add the licenses tab settings fields.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_licenses_fields() {
			if ( ! charitable_is_settings_view( 'licenses' ) ) {
				return array();
			}

			$fields = array(
				'section' => array(
					'title'     => '',
					'type'      => 'hidden',
					'priority'  => 10000,
					'value'     => 'licenses',
					'save'      => false,
				),
				'licenses' => array(
					'title'     => false,
					'callback'  => array( $this, 'render_licenses_table' ),
					'priority'  => 4,
				),
			);

			foreach ( charitable_get_helper( 'licenses' )->get_products() as $key => $product ) {
				$fields[ $key ] = array(
					'type'      => 'text',
					'render'    => false,
					'priority'  => 6,
				);
			}

			return $fields;
		}

		/**
		 * Add the licenses group.
		 *
		 * @param   string[] $groups
		 * @return  string[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_licenses_group( $groups ) {
			$groups['licenses'] = array();
			return $groups;
		}

		/**
		 * Render the licenses table.
		 *
		 * @param   mixed[] $args
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function render_licenses_table( $args ) {
			charitable_admin_view( 'settings/licenses', $args );
		}

		/**
		 * Checks for updated license and invalidates status field if not set.
		 *
		 * @param   mixed[] $values The parsed values combining old values & new values.
		 * @param   mixed[] $new_values The newly submitted values.
		 * @return  mixed[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function save_license( $values, $new_values ) {
			/* If we didn't just submit licenses, stop here. */
			if ( ! isset( $new_values['licenses'] ) ) {
				return $values;
			}

			$licenses = $new_values['licenses'];

			foreach ( $licenses as $product_key => $license ) {
				$license = trim( $license );

				if ( empty( $license ) ) {
					$values['licenses'][ $product_key ] = '';
					continue;
				}

				$license_data = charitable_get_helper( 'licenses' )->verify_license( $product_key, $license );

				if ( empty( $license_data ) ) {
					continue;
				}

				$values['licenses'][ $product_key ] = $license_data;
			}

			return $values;
		}
	}

endif; // End class_exists check
