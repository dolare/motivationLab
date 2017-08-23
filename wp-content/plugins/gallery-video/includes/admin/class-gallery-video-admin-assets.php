<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//todo: correct urls
class Gallery_Video_Admin_Assets {

	/**
	 * Gallery_Video_Admin_Assets constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * @param $hook hook of current page
	 */
	public function admin_styles( $hook ) {
		if ( in_array( $hook, Gallery_Video()->admin->pages ) ) {
			wp_enqueue_style( "gallery_video_admin_css", Gallery_Video()->plugin_url() . "/assets/style/admin.style.css", false );
			wp_enqueue_style( "gallery_video_jquery_ui_css", Gallery_Video()->plugin_url() . "/assets/style/jquery-ui.css", false );
			wp_enqueue_style( "gallery_video_simple_slider_css", Gallery_Video()->plugin_url() . "/assets/style/simple-slider_sl.css", false );
			wp_enqueue_style( "gallery_video_featured_plugins_css", Gallery_Video()->plugin_url() . "/assets/style/featured-plugins.css", false );
		}
		$edit_pages = array('post.php','post-new.php');
		if ( in_array( $hook, $edit_pages ) ){
			wp_enqueue_style( "gallery_video_add_shortecode_css", Gallery_Video()->plugin_url() . "/assets/style/add_shortecode.css", false );
		}
	}

	public function admin_scripts( $hook ) {
		$admin_url              = admin_url( "admin-ajax.php" );
		if ( in_array( $hook, Gallery_Video()->admin->pages ) ) {
			wp_enqueue_media();
			wp_enqueue_script( "gallery_video_admin_js", Gallery_Video()->plugin_url() . "/assets/js/admin.js", false );
			wp_enqueue_script( "gallery_video_jquery_ui", Gallery_Video()->plugin_url() . "/assets/js/jquery-ui.js", false );
			wp_enqueue_script( "gallery_video_simple_slider_js", Gallery_Video()->plugin_url() . '/assets/js/simple-slider.js', false );
			wp_enqueue_script( 'gallery_video_js_color', Gallery_Video()->plugin_url() . "/assets/js/jscolor.js" );
			wp_localize_script( 'gallery_video_admin_js', 'ajax_object_admin', $admin_url );
		}
		$edit_pages = array('post.php','post-new.php');
		if ( in_array( $hook, $edit_pages ) ){
			wp_enqueue_script( "gallery_video_add_shortecode", Gallery_Video()->plugin_url() . "/assets/js/add_shortecode.js", false );
			wp_localize_script( 'gallery_video_add_shortecode', 'ajax_object_shortecode', $admin_url );
		}
	}
}
