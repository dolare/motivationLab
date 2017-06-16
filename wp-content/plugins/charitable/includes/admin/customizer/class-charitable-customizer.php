<?php
/**
 * Class that sets up the WordPress Customizer integration.
 *
 * @package     Charitable/Classes/Charitable_Customizer
 * @version     1.2.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Customizer' ) ) :

	/**
	 * Sets up the Wordpress customizer
	 *
	 * @since 1.2.0
	 */
	class Charitable_Customizer {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Customizer|null
		 * @access  private
		 * @static
		 */
		private static $instance = null;

		/**
		 * Create object instance.
		 *
		 * @access  private
		 * @since   1.2.0
		 */
		private function __construct() {
			add_action( 'customize_save_after', array( $this, 'customize_save_after' ) );
			add_action( 'customize_register', array( $this, 'register_customizer_fields' ) );
			add_action( 'customize_preview_init', array( $this, 'load_customizer_script' ) );
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @global  WP_Customize_Manager $wp_customize
		 * @return  Charitable_Customizer
		 * @access  public
		 * @since   1.2.0
		 */
		public static function start() {
			global $wp_customize;

			if ( ! $wp_customize ) {
				return;
			}

			if ( is_null( self::$instance ) ) {
				self::$instance = new Charitable_Customizer();
			}

			return self::$instance;
		}

		/**
		 * After the customizer has finished saving each of the fields, delete the transient.
		 *
		 * @see     customize_save_after hook
		 * @return  void
		 * @access  public
		 * @since   1.2.0
		 */
		public function customize_save_after() {
			delete_transient( 'charitable_custom_styles' );
		}

		/**
		 * Theme customization.
		 *
		 * @param   WP_Customize_Manager $wp_customize
		 * @return  void
		 */
		public function register_customizer_fields( $wp_customize ) {
			$fields = array(
				'title'     => __( 'Charitable', 'charitable' ),
				'priority'  => 1000,
				'capability' => 'manage_charitable_settings',
				'sections'  => array(
					'charitable_donation_form' => array(
						'title'     => __( 'Donation Form', 'charitable' ),
						'priority'  => 1020,
						'settings'  => array(
							'donation_form_display' => array(
								'setting' => array(
									'transport'         => 'refresh',
									'default'           => 'separate_page',
									'sanitize_callback' => array( $this, 'sanitize_donation_form_display_option' ),
								),
								'control' => array(
									'type'              => 'select',
									'label'             => __( 'How do you want a campaign\'s donation form to show?', 'charitable' ),
									'priority'          => 1022,
									'choices'           => array(
										'separate_page' => __( 'Show on a Separate Page', 'charitable' ),
										'same_page'     => __( 'Show on the Same Page', 'charitable' ),
										'modal'         => __( 'Reveal in a Modal', 'charitable' ),
									),
								),
							),
							'donation_form_minimal_fields' => array(
								'setting' => array(
									'transport'         => 'refresh',
									'default'           => 0,
								),
								'control' => array(
									'type'              => 'radio',
									'label'             => __( 'Only show required fields', 'charitable' ),
									'priority'          => 1024,
									'choices'           => array(
										1 => __( 'Yes', 'charitable' ),
										0 => __( 'No', 'charitable' ),
									),
								),
							),
						),
					),
				),
			);

			if ( apply_filters( 'charitable_add_custom_styles', true ) ) {
				$highlight_colour = apply_filters( 'charitable_default_highlight_colour', '#f89d35' );

				$fields['sections']['charitable_design'] = array(
					'title'     => __( 'Design Options', 'charitable' ),
					'priority'  => 1010,
					'settings'  => array(
						'highlight_colour' => array(
							'setting'   => array(
								'transport'         => 'postMessage',
								'default'           => $highlight_colour,
								'sanitize_callback' => 'sanitize_hex_color',
							),
							'control'   => array(
								'control_type'      => 'WP_Customize_Color_Control',
								'priority'          => 1110,
								'label'             => __( 'Highlight Color', 'charitable' ),
							),
						),
					),
				);
			}

			$fields = apply_filters( 'charitable_customizer_fields', $fields );

			$this->add_panel( 'charitable', $fields );
		}

		/**
		 * Make sure the donation form display option is valid.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.2.0
		 */
		public function sanitize_donation_form_display_option( $option ) {
			if ( ! in_array( $option, array( 'separate_page', 'same_page', 'modal' ) ) ) {
				$option = 'separate_page';
			}

			return $option;
		}

		/**
		 * Adds a panel.
		 *
		 * @param   string  $panel_id
		 * @param   array   $panel
		 * @return  void
		 * @access  private
		 * @since   1.2.0
		 */
		private function add_panel( $panel_id, $panel ) {
			global $wp_customize;

			if ( empty( $panel ) ) {
				return;
			}

			$wp_customize->add_panel( $panel_id, array(
				'title'    => $panel['title'],
				'priority' => $panel['priority'],
			) );

			$this->add_panel_sections( $panel_id, $panel['sections'] );
		}

		/**
		 * Adds sections to a panel.
		 *
		 * @param   string  $panel_id
		 * @param   array   $sections
		 * @return  void
		 * @access  private
		 * @since   1.2.0
		 */
		private function add_panel_sections( $panel_id, $sections ) {
			global $wp_customize;

			if ( empty( $sections ) ) {
				return;
			}

			foreach ( $sections as $section_id => $section ) {
				$this->add_section( $section_id, $section, $panel_id );
			}
		}

		/**
		 * Adds section & settings
		 *
		 * @param   string $section_id
		 * @param   array $section
		 * @param   string $panel
		 * @return  void
		 * @access  private
		 * @since   1.2.0
		 */
		private function add_section( $section_id, $section, $panel ) {
			global $wp_customize;

			if ( empty( $section ) ) {
				return;
			}

			$settings = $section['settings'];

			unset( $section['settings'] );

			$section['panel'] = $panel;

			$wp_customize->add_section( $section_id, $section );

			$this->add_section_settings( $section_id, $settings );
		}


		/**
		 * Adds settings to a given section.
		 *
		 * @param   string $section_id
		 * @param   array $settings
		 * @return  void
		 * @access  private
		 * @since   1.2.0
		 */
		private function add_section_settings( $section_id, $settings ) {
			global $wp_customize;

			if ( empty( $settings ) ) {
				return;
			}

			foreach ( $settings as $setting_id => $setting ) {
				if ( ! isset( $setting['setting']['type'] ) ) {
					$setting['setting']['type'] = 'option';
				}

				$setting_id = "charitable_settings[$setting_id]";

				$wp_customize->add_setting( $setting_id, $setting['setting'] );

				$setting_control = $setting['control'];
				$setting_control['section'] = $section_id;

				if ( isset( $setting_control['control_type'] ) ) {

					$setting_control_type = $setting_control['control_type'];

					unset( $setting_control['control_type'] );

					$wp_customize->add_control( new $setting_control_type( $wp_customize, $setting_id, $setting_control ) );

				} else {

					$wp_customize->add_control( $setting_id, $setting_control );

				}
			}
		}

		/**
		 * Load the theme-customizer.js file.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.2.0
		 */
		public function load_customizer_script() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_register_script(
				'charitable-customizer',
				charitable()->get_path( 'assets', false ) . 'js/charitable-customizer' . $suffix . '.js',
				array( 'jquery-core', 'customize-preview' ),
				'1.2.0-beta5',
				true
			);

			wp_enqueue_script( 'charitable-customizer' );

		}
	}

endif; // End class_exists check
