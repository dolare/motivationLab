<?php
/**
 * Charitable General Settings UI.
 * 
 * @package     Charitable/Classes/Charitable_General_Settings
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_General_Settings' ) ) : 

/**
 * Charitable_General_Settings
 *
 * @final
 * @since      1.0.0
 */
final class Charitable_General_Settings {

    /**
     * The single instance of this class.  
     *
     * @var     Charitable_General_Settings|null
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
     * @return  Charitable_General_Settings
     * @access  public
     * @since   1.2.0
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Charitable_General_Settings();
        }

        return self::$instance;
    }

    /**
     * Add the general tab settings fields. 
     *
     * @param   array[] $fields
     * @return  array
     * @access  public
     * @since   1.0.0
     */
    public function add_general_fields( $fields = array() ) {
        if ( ! charitable_is_settings_view( 'general' ) ) {
            return array();
        }

        $general_fields = array(
            'section'               => array(
                'title'             => '',
                'type'              => 'hidden',
                'priority'          => 10000,
                'value'             => 'general'
            ),
            'section_locale'        => array(
                'title'             => __( 'Currency & Location', 'charitable' ), 
                'type'              => 'heading', 
                'priority'          => 2
            ),
            'country'               => array(
                'title'             => __( 'Base Country', 'charitable' ), 
                'type'              => 'select', 
                'priority'          => 4, 
                'default'           => 'AU', 
                'options'           => charitable_get_location_helper()->get_countries()
            ), 
            'currency'              => array(
                'title'             => __( 'Currency', 'charitable' ), 
                'type'              => 'select', 
                'priority'          => 10, 
                'default'           => 'AUD',
                'options'           => charitable_get_currency_helper()->get_all_currencies()                        
            ), 
            'currency_format'       => array(
                'title'             => __( 'Currency Format', 'charitable' ), 
                'type'              => 'select', 
                'priority'          => 12, 
                'default'           => 'left',
                'options'           => array(
                    'left'              => '$23.00', 
                    'right'             => '23.00$',
                    'left-with-space'   => '$ 23.00',
                    'right-with-space'  => '23.00 $'
                )
            ),
            'decimal_separator'     => array(
                'title'             => __( 'Decimal Separator', 'charitable' ), 
                'type'              => 'select', 
                'priority'          => 14, 
                'default'           => '.',
                'options'           => array(
                    '.' => 'Period (12.50)',
                    ',' => 'Comma (12,50)'                      
                )
            ), 
            'thousands_separator'   => array(
                'title'             => __( 'Thousands Separator', 'charitable' ), 
                'type'              => 'select', 
                'priority'          => 16, 
                'default'           => ',',
                'options'           => array(
                    ',' => __( 'Comma (10,000)', 'charitable' ), 
                    '.' => __( 'Period (10.000)', 'charitable' ), 
                    ''  => __( 'None', 'charitable' )
                )
            ),
            'decimal_count'         => array(
                'title'             => __( 'Number of Decimals', 'charitable' ), 
                'type'              => 'number', 
                'priority'          => 18, 
                'default'           => 2, 
                'class'             => 'short'
            ),
            'section_donation_form' => array(
                'title'             => __( 'Donation Form', 'charitable' ),
                'type'              => 'heading',
                'priority'          => 20
            ), 
            'donation_form_display' => array(
                'title'             => __( 'Display Options', 'charitable' ), 
                'type'              => 'select', 
                'priority'          => 22, 
                'default'           => 'separate_page',
                'options'           => array(
                    'separate_page' => __( 'Show on a Separate Page', 'charitable' ), 
                    'same_page'     => __( 'Show on the Same Page', 'charitable' ),
                    'modal'         => __( 'Reveal in a Modal', 'charitable' )
                ), 
                'help'              => __( 'Choose how you want a campaign\'s donation form to show.', 'charitable' )
            ),
            'section_pages'         => array(
                'title'             => __( 'Pages', 'charitable' ), 
                'type'              => 'heading', 
                'priority'          => 30
            ),
            'login_page'            => array(
                'title'             => __( 'Login Page', 'charitable' ),
                'type'              => 'select',
                'priority'          => 32,
                'default'           => 'wp',
                'options'           => array(
                    'wp'            => __( 'Use WordPress Login', 'charitable' ),
                    'pages'         => array(
                        'options'   => charitable_get_admin_settings()->get_pages(),
                        'label'     => __( 'Choose a Static Page', 'charitable' )
                    )
                ),
                'help'              => __( 'Allow users to login via the normal WordPress login page or via a static page. The static page should contain the <code>[charitable_login]</code> shortcode.', 'charitable' )
            ),
            'registration_page' => array(
                'title'             => __( 'Registration Page', 'charitable' ),
                'type'              => 'select',
                'priority'          => 34,
                'default'           => 'wp',
                'options'           => array(
                    'wp'            => __( 'Use WordPress Registration Page', 'charitable' ),
                    'pages'         => array(
                        'options'   => charitable_get_admin_settings()->get_pages(),
                        'label'     => __( 'Choose a Static Page', 'charitable' )
                    )
                ),
                'help'              => __( 'Allow users to register via the default WordPress login or via a static page. The static page should contain the <code>[charitable_registration]</code> shortcode.', 'charitable' )
            ),
            'profile_page'          => array(
                'title'             => __( 'Profile Page', 'charitable' ), 
                'type'              => 'select', 
                'priority'          => 36, 
                'options'           => charitable_get_admin_settings()->get_pages(), 
                'help'              => __( 'The static page should contain the <code>[charitable_profile]</code> shortcode.', 'charitable' )
            ), 
            'donation_receipt_page' => array(
                'title'             => __( 'Donation Receipt Page', 'charitable' ), 
                'type'              => 'select', 
                'priority'          => 38, 
                'default'           => 'auto',
                'options'           => array(
                    'auto'          => __( 'Automatic', 'charitable' ),
                    'pages'         => array(
                        'options'   => charitable_get_admin_settings()->get_pages(),
                        'label'     => __( 'Choose a Static Page', 'charitable' )
                    )
                ),
                'help'              => __( 'Choose the page that users will be redirected to after donating. Leave it set to automatic to use the built-in Charitable receipt. If you choose a static page, it should contain the <code>[donation_receipt]</code> shortcode.', 'charitable' )
            )
        );

        $fields = array_merge( $fields, $general_fields );

        return $fields;
    }
}

endif; // End class_exists check