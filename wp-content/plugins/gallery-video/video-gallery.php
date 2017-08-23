<?php

/*
Plugin Name: Gallery - Video Gallery
Plugin URI: https://huge-it.com/wordpress-video-gallery/
Description: Video Gallery plugin was created and specifically designed to show your video files in unusual splendid ways.
Version: 2.2.0
Author: Huge-IT
Author URI: https://huge-it.com/
License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( 'config.php' );

if ( ! class_exists( 'Gallery_Video' ) ) :

    final class Gallery_Video {

        /**
         * Version of plugin
         * @var float
         */
        public $version = '2.2.0';

        /**
         * Instance of Gallery_Video_Admin class to manage admin
         * @var Gallery_Video_Admin instance
         */
        public $admin = null;

        /**
         * Instance of Gallery_Video_Template_Loader class to manage admin
         * @var Gallery_Video_Template_Loader instance
         */
        public $template_loader = null;

        /**
         * The single instance of the class.
         *
         * @var Gallery_Video
         */
        protected static $_instance = null;

        /**
         * Main Gallery_Video Instance.
         *
         * Ensures only one instance of Gallery_Video is loaded or can be loaded.
         *
         * @static
         * @see Gallery_Video()
         * @return Gallery_Video - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        private function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'gallery-video' ), '2.1' );
        }

        /**
         * Unserializing instances of this class is forbidden.
         */
        private function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'gallery-video' ), '2.1' );
        }

        /**
         * Gallery_Video Constructor.
         */
        private function __construct() {
            $this->define_constants();
            $this->includes();
            $this->init_hooks();
            global $Gallery_Video_url,$Gallery_Video_path;
            $Gallery_Video_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
            $Gallery_Video_url = plugins_url('', __FILE__ );
            do_action( 'Gallery_Video_loaded' );
        }

        /**
         * Hook into actions and filters.
         */
        private function init_hooks() {
            register_activation_hook( __FILE__, array( 'Gallery_Video_Install', 'install' ) );
            add_action( 'init', array( $this, 'init' ), 0 );
            add_action( 'plugins_loaded', array($this,'load_plugin_textdomain') );
            add_action( 'widgets_init', array( 'Gallery_Video_Widgets', 'init' ) );
        }

        /**
         * Define Video Gallery Constants.
         */
        private function define_constants() {
            $this->define( 'GALLERY_VIDEO_PLUGIN_URL', plugin_dir_url(__FILE__));
            $this->define( 'GALLERY_VIDEO_PLUGIN_FILE', __FILE__ );
            $this->define( 'GALLERY_VIDEO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( 'GALLERY_VIDEO_VERSION', $this->version );
            $this->define( 'GALLERY_VIDEO_IMAGES_PATH', $this->plugin_path(). DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR );
            $this->define( 'GALLERY_VIDEO_IMAGES_URL', untrailingslashit($this->plugin_url() . '/assets/images/' ));
            $this->define( 'GALLERY_VIDEO_TEMPLATES_PATH', $this->plugin_path() . DIRECTORY_SEPARATOR . 'templates');
            $this->define( 'GALLERY_VIDEO_TEMPLATES_URL', untrailingslashit($this->plugin_url()) . '/templates/');
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * What type of request is this?
         * string $type ajax, frontend or admin.
         *
         * @return bool
         */
        private function is_request( $type ) {
            switch ( $type ) {
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined( 'DOING_AJAX' );
                case 'cron' :
                    return defined( 'DOING_CRON' );
                case 'frontend' :
                    return  ! is_admin() && ! defined( 'DOING_CRON' );
            }
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes() {
            include_once( 'includes/gallery-video-functions.php' );
            include_once( 'includes/gallery-video-video-functions.php' );
            if ( $this->is_request( 'admin' ) ) {
                include_once( 'includes/admin/gallery-video-admin-functions.php' );
            }
        }

        /**
         * Load plugin text domain
         */
        public function load_plugin_textdomain(){
            load_plugin_textdomain( 'gallery-video', false, $this->plugin_path() . '/languages/' );
        }

        /**
         * Init Image gallery when WordPress `initialises.
         */
        public function init() {
            // Before init action.
            do_action( 'before_Gallery_Video_init' );

            $this->template_loader = new Gallery_Video_Template_Loader();

            if ( $this->is_request( 'admin' ) ) {

                $this->admin = new Gallery_Video_Admin();

                new Gallery_Video_Admin_Assets();

            }

            new Gallery_Video_Frontend_Scripts();

            new Gallery_Video_Ajax();

            new Gallery_Video_Shortcode();

            // Init action.
            do_action( 'Gallery_Video_init' );
        }

        /**
         * Get Ajax URL.
         * @return string
         */
        public function ajax_url() {
            return admin_url( 'admin-ajax.php', 'relative' );
        }

        /**
         * Video Gallery Plugin Path.
         *
         * @var string
         * @return string
         */
        public function plugin_path(){
            return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }

        /**
         * Video Gallery Plugin Url.
         * @return string
         */
        public function plugin_url(){
            return plugins_url('', __FILE__ );
        }
    }

endif;

function Gallery_Video(){
    return Gallery_Video::instance();
}

$GLOBALS['Gallery_Video'] = Gallery_Video();