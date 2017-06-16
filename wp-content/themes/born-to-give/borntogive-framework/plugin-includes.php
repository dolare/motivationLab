<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1
 * @author     Thomas Griffin <thomasgriffinmedia.com>
 * @author     Gary Jones <gamajo.com>
 * @copyright  Copyright (c) 2014, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/thomasgriffin/TGM-Plugin-Activation
 */
/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/borntogive-framework//tgm/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', 'my_theme_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function my_theme_register_required_plugins() {
    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin pre-packaged with a theme.
        
       
        // This is an example of how to include a plugin from the WordPress Plugin Repository.
		array(
			'name' => esc_html__('Pojo Sidebars', 'borntogive'),
			'slug' => 'pojo-sidebars',
			'required' 	=> false,
		),
		array(
			'name' => esc_html__('Simple Twitter Tweets', 'borntogive'),
		    'slug' => 'simple-twitter-tweets',
			'required' 	=> false,
		),
       	array(
			'name' => esc_html__('WooCommerce - excelling eCommerce', 'borntogive'),
		    'slug' => 'woocommerce',
			'required' 	=> false,
		),
		array(
			'name' => esc_html__('Charitable', 'borntogive'),
		    'slug' => 'charitable',
			'required' 	=> true,
		),
		array(
			'name' => esc_html__('Contact Form 7', 'borntogive'),
		    'slug' => 'contact-form-7',
			'required' 	=> false,
		),
		array(
			'name' => esc_html__('TinyMCE Advanced', 'borntogive'),
		    'slug' => 'tinymce-advanced',
			'required' 	=> false,
		),
		 array(
            'name'               => esc_html__('Revolution Slider','borntogive'), // The plugin name.
            'slug'               => 'revslider', // The plugin slug (typically the folder name).
            'source'             => BORNTOGIVE_FILEPATH . '/includes'. '/plugins/revslider.zip', // The plugin source.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'version' 			 => '5.4.3.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url'       => '', // If set, overrides default API URL and points to an external URL.
        ),
			array(
            'name'               => esc_html__('Born to Give Core','borntogive'), // The plugin name.
            'slug'               => 'btg-core', // The plugin slug (typically the folder name).
            'source'             => BORNTOGIVE_FILEPATH . '/includes'. '/plugins/btg-core.zip', // The plugin source.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'version'            => '1.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url'       => '', // If set, overrides default API URL and points to an external URL.
        ),
		array(
            'name'               => esc_html__('Redux Framework','borntogive'), // The plugin name.
            'slug'               => 'redux-framework', // The plugin slug (typically the folder name).
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
        ),
		array(
            'name'               => esc_html__('Meta Box Show Hide','borntogive'), // The plugin name.
            'slug'               => 'meta-box-show-hide', // The plugin slug (typically the folder name).
			'source'             => get_template_directory() . '/includes'. '/plugins/meta-box-show-hide.zip', // The plugin source.
			'version' 			 => '1.0.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
        ),
		array(
            'name'               => esc_html__('imic Shortcodes','borntogive'), // The plugin name.
            'slug'               => 'imic-shortcodes', // The plugin slug (typically the folder name).
			'source'  			 => get_template_directory() . '/includes'. '/plugins/imic-shortcodes.zip', // The plugin source.
			'version' 			 => '1.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
        ),
		array(
            'name'               => esc_html__('visual composer','borntogive'), // The plugin name.
            'slug'               => 'js_composer', // The plugin slug (typically the folder name).
			'source'             => get_template_directory() . '/includes'. '/plugins/js_composer.zip', // The plugin source.
			'version' 			 => '5.1.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
        ),
		array(
            'name'               => esc_html__('borntogive vc elements','borntogive'), // The plugin name.
            'slug'               => 'borntogive-vc-elements', // The plugin slug (typically the folder name).
			'source'             => get_template_directory() . '/includes'. '/plugins/borntogive-vc-elements.zip', // The plugin source.
            'version'            => '1.6.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
        ),
		array(
			'name' 				=> esc_html__('Meta Box', 'borntogive'),
		   	'slug' 				=> 'meta-box',
			'required' 			=> true,
            'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
		),
            
    );
    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => esc_html__( 'Install Required Plugins', 'borntogive' ),
            'menu_title'                      => esc_html__( 'Install Plugins', 'borntogive' ),
            'installing'                      => esc_html__( 'Installing Plugin: %s', 'borntogive' ), // %s = plugin name.
            'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'borntogive' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'borntogive' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'borntogive' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'borntogive' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'borntogive' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'borntogive' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'borntogive' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'borntogive' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'borntogive' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'borntogive' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'borntogive' ),
            'return'                          => esc_html__( 'Return to Required Plugins Installer', 'borntogive' ),
            'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'borntogive' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'borntogive' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );
    tgmpa( $plugins, $config );
}