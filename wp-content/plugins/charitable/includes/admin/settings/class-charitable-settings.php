<?php
/**
 * Charitable Settings UI.
 *
 * @package     Charitable/Classes/Charitable_Settings
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Settings' ) ) :

	/**
	 * Charitable_Settings
	 *
	 * @final
	 * @since      1.0.0
	 */
	final class Charitable_Settings {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Settings|null
		 * @access  private
		 * @static
		 */
		private static $instance = null;

		/**
		 * Current field. Used to access field args from the views.
		 *
		 * @var     array
		 * @access  private
		 */
		private $current_field;

		/**
		 * Create object instance.
		 *
		 * @access  private
		 * @since   1.0.0
		 */
		private function __construct() {
			do_action( 'charitable_admin_settings_start', $this );
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @return  Charitable_Settings
		 * @access  public
		 * @since   1.2.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Settings();
			}

			return self::$instance;
		}

		/**
		 * Return the array of tabs used on the settings page.
		 *
		 * @return  string[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_sections() {

			return apply_filters( 'charitable_settings_tabs', array(
				'general'  => __( 'General', 'charitable' ),
				'gateways' => __( 'Payment Gateways', 'charitable' ),
				'emails'   => __( 'Emails', 'charitable' ),
				'advanced' => __( 'Advanced', 'charitable' ),
			) );

		}

		/**
		 * Optionally add the extensions tab.
		 *
		 * @param   string[] $tabs
		 * @return  string[]
		 * @access  public
		 * @since   1.3.0
		 */
		public function maybe_add_extensions_tab( $tabs ) {
			$actual_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

			/* Set the tab to 'extensions' */
			$_GET['tab'] = 'extensions';

			$settings = apply_filters( 'charitable_settings_tab_fields_extensions', array() );

			/* Set the tab back to whatever it actually is */
			$_GET['tab'] = $actual_tab;

			if ( ! empty( $settings ) ) {
				$tabs = charitable_add_settings_tab(
					$tabs,
					'extensions',
					__( 'Extensions', 'charitable' ),
					array(
						'index' => 3,
					)
				);
			}

			return $tabs;
		}

		/**
		 * Register setting.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function register_settings() {
			if ( ! charitable_is_settings_view() ) {
				return;
			}

			register_setting( 'charitable_settings', 'charitable_settings', array( $this, 'sanitize_settings' ) );

			$fields = $this->get_fields();

			if ( empty( $fields ) ) {
				return;
			}

			$sections = array_merge( $this->get_sections(), $this->get_dynamic_groups() );

			/* Register each section */
			foreach ( $sections as $section_key => $section ) {
				$section_id = 'charitable_settings_' . $section_key;

				add_settings_section(
					$section_id,
					__return_null(),
					'__return_false',
					$section_id
				);

				if ( ! isset( $fields[ $section_key ] ) || empty( $fields[ $section_key ] ) ) {
					continue;
				}

				/* Sort by priority */
				$section_fields = $fields[ $section_key ];
				uasort( $section_fields, 'charitable_priority_sort' );

				/* Add the individual fields within the section */
				foreach ( $section_fields as $key => $field ) {

					$this->register_field( $field, array( $section_key, $key ) );

				}
			}
		}

		/**
		 * Sanitize submitted settings before saving to the database.
		 *
		 * @param   array $values
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function sanitize_settings( $values ) {

			$old_values = get_option( 'charitable_settings', array() );
			$new_values = array();

			if ( ! is_array( $values ) ) {
				$values = array();
			}

			/* Loop through all fields, merging the submitted values into the master array */
			foreach ( $values as $section => $submitted ) {
				$new_values = array_merge( $new_values, $this->get_section_submitted_values( $section, $submitted ) );
			}

			$values = wp_parse_args( $new_values, $old_values );
			$values = apply_filters( 'charitable_save_settings', $values, $new_values, $old_values );

			$this->add_update_message( __( 'Settings saved', 'charitable' ), 'success' );

			return $values;
		}

		/**
		 * Checkbox settings should always be either 1 or 0.
		 *
		 * @param   mixed       $value
		 * @param   array       $field
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function sanitize_checkbox_value( $value, $field ) {
			if ( isset( $field['type'] ) && 'checkbox' == $field['type'] ) {
				$value = intval( $value && 'on' == $value );
			}

			return $value;
		}

		/**
		 * Render field. This is the default callback used for all fields, unless an alternative callback has been specified.
		 *
		 * @param   array       $args
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function render_field( $args ) {
			$field_type = isset( $args['type'] ) ? $args['type'] : 'text';

			charitable_admin_view( 'settings/' . $field_type, $args );
		}


		/**
		 * Returns an array of all pages in the id=>title format.
		 *
		 * @return  string[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_pages() {
			$pages = wp_cache_get( 'filtered_static_pages', 'charitable' );

			if ( false === $pages ) {

				$all_pages = get_pages();

				if ( ! $all_pages ) {
					$all_pages = array();
				}

				$pages = array();

				foreach ( $all_pages as $page ) {

					$pages[ $page->ID ] = $page->post_title;

				}

				wp_cache_set( 'filtered_static_pages', $pages, 'charitable' );
			}

			return $pages;
		}

		/**
		 * Add an update message.
		 *
		 * @param 	string  $message
		 * @param 	string  $type
		 * @param 	boolean $dismissible
		 * @return  string[]
		 * @access  public
		 * @since   1.4.6
		 */
		public function add_update_message( $message, $type = 'error', $dismissible = true ) {
			if ( ! in_array( $type, array( 'error', 'success', 'warning', 'info' ) ) ) {
				$type = 'error';
			}

			charitable_get_admin_notices()->add_notice( $message, $type, false, $dismissible );
		}

		/**
		 * Recursively add settings fields, given an array.
		 *
		 * @param   array   $fields
		 * @param   string  $section_key
		 * @return  void
		 * @access  private
		 * @since   1.0.0
		 */
		private function register_field( $field, $keys ) {
			$section_id = 'charitable_settings_' . $keys[0];

			if ( isset( $field['render'] ) && ! $field['render'] ) {
				return;
			}

			/* Drop the first key, which is the section identifier */
			$field['name'] = implode( '][', $keys );

			if ( ! $this->is_dynamic_group( $keys[0] ) ) {
				array_shift( $keys );
			}

			$field['key']     = $keys;
			$field['classes'] = $this->get_field_classes( $field );
			$callback         = isset( $field['callback'] ) ? $field['callback'] : array( $this, 'render_field' );
			$label            = $this->get_field_label( $field, end( $keys ) );

			add_settings_field(
				sprintf( 'charitable_settings_%s', implode( '_', $keys ) ),
				$label,
				$callback,
				$section_id,
				$section_id,
				$field
			);
		}

		/**
		 * Return the label for the given field.
		 *
		 * @param   array   $field
		 * @param   string  $key
		 * @return  string
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_field_label( $field, $key ) {
			$label = '';

			if ( isset( $field['label_for'] ) ) {
				$label = $field['label_for'];
			}

			if ( isset( $field['title'] ) ) {
				$label = $field['title'];
			}

			return $label;
		}

		/**
		 * Return a space separated string of classes for the given field.
		 *
		 * @param   array   $field
		 * @return  string
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_field_classes( $field ) {
			$classes = array( 'charitable-settings-field' );

			if ( isset( $field['class'] ) ) {
				$classes[] = $field['class'];
			}

			$classes = apply_filters( 'charitable_settings_field_classes', $classes, $field );

			return implode( ' ', $classes );
		}

		/**
		 * Return an array with all the fields & sections to be displayed.
		 *
		 * @uses    charitable_settings_fields
		 * @see     Charitable_Settings::register_setting()
		 * @return  array
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_fields() {
			/**
			 * Use the charitable_settings_tab_fields to include the fields for new tabs.
			 * DO NOT use it to add individual fields. That should be done with the
			 * filters within each of the methods.
			 */
			$fields = array();

			foreach ( $this->get_sections() as $section_key => $section ) {
				$fields[ $section_key ] = apply_filters( 'charitable_settings_tab_fields_' . $section_key, array() );
			}

			return apply_filters( 'charitable_settings_tab_fields', $fields );
		}

		/**
		 * Get the submitted value for a particular setting.
		 *
		 * @param   string      $key
		 * @param   array       $field
		 * @param   array       $submitted
		 * @return  mixed|null  Returns null if the value was not submitted or is not applicable.
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_setting_submitted_value( $key, $field, $submitted ) {
			$value = null;

			if ( isset( $field['save'] ) && ! $field['save'] ) {
				return $value;
			}

			$field_type = isset( $field['type'] ) ? $field['type'] : '';

			switch ( $field_type ) {
				case '' :
				case 'heading' :
					return $value;
					break;

				case 'checkbox' :
					$value = isset( $submitted[ $key ] );
					break;

				case 'multi-checkbox' :
					$value = isset( $submitted[ $key ] ) ? $submitted[ $key ] : array();
					break;

				default :
					$value = $submitted[ $key ];
			}

			return apply_filters( 'charitable_sanitize_value', $value, $field, $submitted, $key );
		}

		/**
		 * Return the submitted values for the given section.
		 *
		 * @param   string $section
		 * @param   array  $submitted
		 * @return  array
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_section_submitted_values( $section, $submitted ) {
			$values = array();
			$form_fields = $this->get_fields();

			if ( ! isset( $form_fields[ $section ] ) ) {
				return $values;
			}

			foreach ( $form_fields[ $section ] as $key => $field ) {

				$value = $this->get_setting_submitted_value( $key, $field, $submitted );

				if ( is_null( $value ) ) {
					continue;
				}

				if ( $this->is_dynamic_group( $section ) ) {
					$values[ $section ][ $key ] = $value;
					continue;
				}

				$values[ $key ] = $value;
			}

			return $values;
		}

		/**
		 * Return list of dynamic groups.
		 *
		 * @return  string[]
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_dynamic_groups() {
			return apply_filters( 'charitable_dynamic_groups', array() );
		}

		/**
		 * Returns a composite key, given a section and submitted values.
		 *
		 * @param   string  $section
		 * @param   array   $submitted
		 * @return  string
		 * @access  private
		 * @since   1.0.0
		 */
		private function get_composite_key( $section, $submitted ) {
			if ( ! is_array( current( $submitted ) ) ) {
				return false;
			}

			return sprintf( '%s_%s', $section, key( $submitted ) );
		}

		/**
		 * Returns whether the given key indicates the start of a new section of the settings.
		 *
		 * @param   string  $section
		 * @param   array   $submitted
		 * @return  boolean
		 * @access  private
		 * @since   1.0.0
		 */
		private function is_dynamic_group( $composite_key ) {
			return array_key_exists( $composite_key, $this->get_dynamic_groups() );
		}
		
		/**
		 * @deprecated 1.4.13
		 */
		public function get_update_messages() {
			
			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.4.13',
				'Charitable_Admin_Notices::get_notices()'
			);
			
			return charitable_get_admin_notices()->get_notices();
		}
	}

endif; // End class_exists check
