<?php
/**
 * Profile shortcode class.
 * 
 * @version     1.0.0
 * @package     Charitable/Shortcodes/Profile
 * @category    Class
 * @author      Eric Daams
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Profile_Shortcode' ) ) : 

/**
 * Charitable_Profile_Shortcode class. 
 *
 * @since       1.0.0
 */
class Charitable_Profile_Shortcode {

    /**
     * The callback method for the campaigns shortcode.
     *
     * This receives the user-defined attributes and passes the logic off to the class. 
     *
     * @param   array $atts User-defined shortcode attributes.
     * @return  string
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function display( $atts ) {                
        if ( ! is_user_logged_in() ) {
            return Charitable_Login_Shortcode::display( $atts );
        }

        $args = shortcode_atts( array(), $atts, 'charitable_profile' );

        ob_start();

        charitable_template( 'shortcodes/profile.php', array( 
            'form' => new Charitable_Profile_Form( $args ) 
        ) );

        return apply_filters( 'charitable_profile_shortcode', ob_get_clean() );      
    }
}

endif;