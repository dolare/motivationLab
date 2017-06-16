<?php
/**
 * Charitable Advanced Settings UI.
 * 
 * @package     Charitable/Classes/Charitable_Advanced_Settings
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Advanced_Settings' ) ) : 

/**
 * Charitable_Advanced_Settings
 *
 * @final
 * @since      1.0.0
 */
final class Charitable_Advanced_Settings {

    /**
     * The single instance of this class.  
     *
     * @var     Charitable_Advanced_Settings|null
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
     * @return  Charitable_Advanced_Settings
     * @access  public
     * @since   1.2.0
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Charitable_Advanced_Settings();
        }

        return self::$instance;
    }

    /**
     * Add the advanced tab settings fields. 
     *
     * @return  array<string,array>
     * @access  public
     * @since   1.0.0
     */
    public function add_advanced_fields() {
        if ( ! charitable_is_settings_view( 'advanced' ) ) {
            return array();
        }

        return array(
            'section'               => array(
                'title'             => '',
                'type'              => 'hidden',
                'priority'          => 10000,
                'value'             => 'advanced'
            ),            
            'section_dangerous'     => array(
                'title'             => __( 'Dangerous Settings', 'charitable' ), 
                'type'              => 'heading', 
                'priority'          => 100
            ),
            'delete_data_on_uninstall'  => array(
                'label_for'         => __( 'Reset Data', 'charitable' ), 
                'type'              => 'checkbox', 
                'help'              => __( 'DELETE ALL DATA when uninstalling the plugin.', 'charitable' ), 
                'priority'          => 105
            )
        );
    }
}

endif; // End class_exists check