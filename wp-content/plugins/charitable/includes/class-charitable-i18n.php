<?php
/**
 * Sets up translations for Charitable.
 *
 * @package     Charitable/Classes/Charitable_i18n
 * @version     1.1.2
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_i18n' ) ) : 

/**
 * Charitable_i18n
 *
 * @since       1.1.2
 */
class Charitable_i18n {
    
    /**
     * The single instance of this class.  
     *
     * @var     Charitable_i18n|null
     * @access  private
     * @static
     */
    private static $instance = null;    

    /**
     * @var     string
     */
    protected $textdomain = 'charitable';

    /**
     * The path to the languages directory. 
     *
     * @var     string
     * @access  protected
     */
    protected $languages_directory;

    /**
     * The site locale.
     *
     * @var     string
     * @access  protected
     */
    protected $locale;

    /**
     * The MO filename.
     *
     * @var     string
     * @access  protected
     */
    protected $mofile;

    /**
     * Set up the class. 
     *
     * @access  private
     * @since   1.1.2
     */
    private function __construct() {
        $this->languages_directory = apply_filters( 'charitable_languages_directory', 'charitable/i18n/languages' );
        $this->locale = apply_filters( 'plugin_locale', get_locale(), $this->textdomain );
        $this->mofile = sprintf( '%1$s-%2$s.mo', $this->textdomain, $this->locale );

        $this->load_textdomain();
    }

    /**
     * Returns and/or create the single instance of this class.  
     *
     * @return  Charitable_i18n
     * @access  public
     * @since   1.2.0
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Charitable_i18n();
        }

        return self::$instance;
    }    

    /**
     * Create class object.
     * 
     * @return  void
     * @access  public
     * @since   1.1.2
     */
    public function load_textdomain() {
        foreach ( array( 'global', 'local' ) as $source ) {
            
            $mofile_path = $this->get_mofile_path( $source );

            if ( ! file_exists( $mofile_path ) ) {
                continue;
            }
         
            load_textdomain( $this->textdomain, $mofile_path );
        }        

        load_plugin_textdomain( $this->textdomain, false, $this->languages_directory );
    }

    /**
     * Get the path to the MO file.
     *
     * @param   string $source Either 'local' or 'global'. 
     * @return  string
     * @access  private
     * @since   1.1.2
     */
    private function get_mofile_path( $source = 'local' ) {
        if ( 'global' == $source ) {
            return WP_LANG_DIR . '/' . $this->textdomain . '/' . $this->mofile;
        }

        return trailingslashit( $this->languages_directory ) . $this->mofile;
    }

    /**
     * Instantiate the class, but only during the start phase.
     *
     * This method is officially deprecated as of 1.2.0 since we are removing
     * the need for Charitable_Start_Object. It has been left intact for extensions
     * that have not been updated yet.
     *
     * Expect full removal in Charitable 1.3 or after.
     *
     * @deprecated     
     */
    public static function charitable_start( Charitable $charitable ) {
        if ( ! $charitable->is_start() ) {
            return;
        }

        $class = get_called_class();
        $charitable->register_object( new $class );
    }    
}

endif; // End class_exists check