<?php
if (!defined('ABSPATH'))
exit; // Exit if accessed directly 
/*
* Here you include files which is required by theme
*/
require_once(get_template_directory().'/borntogive-framework' . '/borntogive-theme-functions.php');
/* META BOX FRAMEWORK
================================================== */
require_once(get_template_directory().'/borntogive-framework' . '/meta-boxes.php');
require_once(get_template_directory().'/borntogive-framework' . '/term_color_picker.php');
/* MEGA MENU
	================================================== */  
require_once(get_template_directory() . '/borntogive-framework' . '/borntogive-megamenu/borntogive-megamenu.php');
/* EVENT REGISTRATION
====================================================*/
require_once(get_template_directory().'/borntogive-framework' . '/registrant_tickets_field.php');
require_once(get_template_directory().'/borntogive-framework' . '/tickets_clone_fields.php');
require_once(get_template_directory().'/borntogive-framework' . '/taxonomy_banner.php');
/* PLUGIN INCLUDES
================================================== */
require_once(get_template_directory().'/borntogive-framework' . '/plugin-includes.php');
/* WIDGETS INCLUDES
================================================== */
require_once(get_template_directory().'/borntogive-framework' . '/widgets/custom_category.php');
require_once(get_template_directory().'/borntogive-framework' . '/widgets/recent_posts.php');
require_once(get_template_directory().'/borntogive-framework' . '/widgets/tabs_widget.php');
/* LOAD STYLESHEETS
================================================== */
if (!function_exists('borntogive_enqueue_styles')) {
function borntogive_enqueue_styles() {
global $borntogive_options;
$theme_info = wp_get_theme();
$theme_color_scheme = (isset($borntogive_options['theme_color_scheme']))?$borntogive_options['theme_color_scheme']:'color1.css';
			wp_enqueue_style('bootstrap', BORNTOGIVE_THEME_PATH . '/css/bootstrap.css', array(), $theme_info->get( 'Version' ), 'all');
			wp_enqueue_style('line-icons', BORNTOGIVE_THEME_PATH . '/css/line-icons.css', array(), $theme_info->get( 'Version' ), 'all');
			wp_enqueue_style('font-awesome', BORNTOGIVE_THEME_PATH . '/css/font-awesome.css', array(), $theme_info->get( 'Version' ), 'all');
			wp_enqueue_style('animations', BORNTOGIVE_THEME_PATH . '/css/animations.css', array(), $theme_info->get( 'Version' ), 'all');
			wp_enqueue_style('bootstrap_theme', BORNTOGIVE_THEME_PATH . '/css/bootstrap-theme.css', array(), $theme_info->get( 'Version' ), 'all');
			wp_enqueue_style('borntogive_main', get_stylesheet_uri(), array(), $theme_info->get( 'Version' ), 'all');
        wp_enqueue_style('magnific_popup', BORNTOGIVE_THEME_PATH . '/vendor/magnific/magnific-popup.css', array(), $theme_info->get( 'Version' ), 'all');
			wp_enqueue_style('owl-carousel1', BORNTOGIVE_THEME_PATH . '/vendor/owl-carousel/css/owl.carousel.css', array(), $theme_info->get( 'Version' ), 'all');
			wp_enqueue_style('owl-carousel2', BORNTOGIVE_THEME_PATH . '/vendor/owl-carousel/css/owl.theme.css', array(), $theme_info->get( 'Version' ), 'all');
			if (isset($borntogive_options['theme_color_type'][0])&&$borntogive_options['theme_color_type'][0] == 0) {
				wp_enqueue_style('borntogive_colors', BORNTOGIVE_THEME_PATH . '/colors/' . $theme_color_scheme, array(), $theme_info->get( 'Version' ), 'all');
			}
			wp_enqueue_style('borntogive_fullcalendar', BORNTOGIVE_THEME_PATH . '/vendor/fullcalendar/fullcalendar.css', array(), $theme_info->get( 'Version' ), 'all');
			wp_enqueue_style('borntogive_fullcalendar_print', BORNTOGIVE_THEME_PATH . '/vendor/fullcalendar/fullcalendar.print.css', array(), $theme_info->get( 'Version' ), 'print');
			//**End Enqueue STYLESHEETPATH**//
		}
		add_action('wp_enqueue_scripts', 'borntogive_enqueue_styles', 99);
}
if (!function_exists('borntogive_enqueue_scripts')) {
    function borntogive_enqueue_scripts() {
        $borntogive_options = get_option('borntogive_options');
		$theme_info = wp_get_theme();
        //**register script**//
		wp_enqueue_script('modernizr', BORNTOGIVE_THEME_PATH . '/js/modernizr.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('magnific', BORNTOGIVE_THEME_PATH . '/vendor/magnific/jquery.magnific-popup.min.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('borntogive_ui_plugins', BORNTOGIVE_THEME_PATH . '/js/ui-plugins.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('borntogive_helper_plugins', BORNTOGIVE_THEME_PATH . '/js/helper-plugins.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('owl_carousel', BORNTOGIVE_THEME_PATH . '/vendor/owl-carousel/js/owl.carousel.min.js', array(), $theme_info->get( 'Version' ), true);
		
		wp_enqueue_script('bootstrap', BORNTOGIVE_THEME_PATH . '/js/bootstrap.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('borntogive_init', BORNTOGIVE_THEME_PATH . '/js/init.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('borntogive_flexslider', BORNTOGIVE_THEME_PATH . '/vendor/flexslider/js/jquery.flexslider.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('borntogive_circle_progress', BORNTOGIVE_THEME_PATH . '/js/circle-progress.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('borntogive_fullcalendar_moments', BORNTOGIVE_THEME_PATH . '/vendor/fullcalendar/lib/moment.min.js', array(), $theme_info->get( 'Version' ), false);
		wp_enqueue_script('borntogive_fullcalendar', BORNTOGIVE_THEME_PATH . '/vendor/fullcalendar/fullcalendar.min.js', array('jquery'), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('borntogive_gcal', BORNTOGIVE_THEME_PATH . '/vendor/fullcalendar/gcal.js', array(), $theme_info->get( 'Version' ), true);
		wp_enqueue_script('borntogive_fullcalendar_init', BORNTOGIVE_THEME_PATH . '/js/calender_events.js', array('jquery'), $theme_info->get( 'Version' ), true);
		
		
		$calendar_today = (isset($borntogive_options['calendar_today']))?$borntogive_options['calendar_today']:'Today';
		$calendar_month = (isset($borntogive_options['calendar_month']))?$borntogive_options['calendar_month']:'Month';
		$calendar_week = (isset($borntogive_options['calendar_week']))?$borntogive_options['calendar_week']:'Week';
		$calendar_day = (isset($borntogive_options['calendar_day']))?$borntogive_options['calendar_day']:'Day';
		$calendar_header_view = (isset($borntogive_options['calendar_header_view']))?$borntogive_options['calendar_header_view']:1;
		$calendar_event_limit = (isset($borntogive_options['calendar_event_limit']))?$borntogive_options['calendar_event_limit']:4;
		$google_api_key = (isset($borntogive_options['google_feed_key']))?$borntogive_options['google_feed_key']:'';
		$google_calendar_id = (isset($borntogive_options['google_feed_id']))?$borntogive_options['google_feed_id']:'';
        $monthNamesValue = $borntogive_options['calendar_month_name'];
        $monthNames = (empty($monthNamesValue)) ? array() : explode(',', trim($monthNamesValue));
        $monthNamesShortValue = $borntogive_options['calendar_month_name_short'];
        $monthNamesShort = (empty($monthNamesShortValue)) ? array() : explode(',', trim($monthNamesShortValue));
        $dayNamesValue = $borntogive_options['calendar_day_name'];
        $dayNames = (empty($dayNamesValue)) ? array() : explode(',', trim($dayNamesValue));
        $dayNamesShortValue = $borntogive_options['calendar_day_name_short'];
        $dayNamesShort = (empty($dayNamesShortValue)) ? array() : explode(',', trim($dayNamesShortValue));
		$view = (isset($borntogive_options['default_calendar_view']))?$borntogive_options['default_calendar_view']:'month';
		$format=BornToGiveConvertDate(get_option('time_format'));
		wp_localize_script('borntogive_fullcalendar_init', 'calenderEvents', array('homeurl' => get_template_directory_uri(), 'monthNames' => $monthNames, 'monthNamesShort' => $monthNamesShort, 'dayNames' => $dayNames, 'dayNamesShort' => $dayNamesShort,'time_format'=>$format,'start_of_week'=>get_option('start_of_week'),'googlekey'=>$google_api_key,'googlecalid'=>$google_calendar_id,'ajaxurl' => admin_url('admin-ajax.php'),'calheadview'=> $calendar_header_view,'eventLimit'=>$calendar_event_limit,'today'=>$calendar_today,'month'=>$calendar_month,'week'=>$calendar_week,'day'=>$calendar_day,'view'=>$view));
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
    add_action('wp_enqueue_scripts', 'borntogive_enqueue_scripts');
}
/* LOAD BACKEND SCRIPTS
  ================================================== */
function borntogive_admin_scripts() 
{
 	wp_register_script('borntogive-admin-functions', BORNTOGIVE_THEME_PATH . '/js/admin_scripts.js', 'jquery', NULL, TRUE);
	global $pagenow;
	if(($pagenow=='user-edit.php')||($pagenow=='profile.php'))
	{
		wp_enqueue_media();
	}
  wp_enqueue_script('borntogive-admin-functions');
	if(isset($_REQUEST['taxonomy'])){
      wp_enqueue_script('borntogive-upload', BORNTOGIVE_THEME_PATH . '/js/imic-upload.js', 'jquery', NULL, TRUE);
      wp_enqueue_media();
  }
}
add_action('admin_init', 'borntogive_admin_scripts');
/* LOAD BACKEND STYLE
  ================================================== */
function borntogive_admin_styles() {
    add_editor_style(BORNTOGIVE_THEME_PATH . '/css/editor-style.css');
}
add_action('admin_head', 'borntogive_admin_styles');
?>