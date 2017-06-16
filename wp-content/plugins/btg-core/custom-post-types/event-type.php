<?php
/* ==================================================
  Event Post Type Functions
  ================================================== */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
add_action('init', 'imic_event_register', 0);
add_action( 'init', 'event_registrants' );
function event_registrants()
{
global $event_post_registerants;
	$labels = array(
        'name' => $event_post_registerants,
        'singular_name' => __('Registrant','borntogive-core'),
        'add_new' => __('Add New', 'borntogive-core'),
        'add_new_item' => __('Add New', 'borntogive-core'),
        'edit_item' => __('Edit', 'borntogive-core'),
        'new_item' => __('New', 'borntogive-core'),
        'view_item' => __('View', 'borntogive-core'),
        'search_items' => __('Search', 'borntogive-core'),
        'not_found' => __('Nothing found', 'borntogive-core'),
        'not_found_in_trash' => __('Nothing found in Trash', 'borntogive-core'),
        'parent_item_colon' => '',
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=event',
        'show_in_nav_menus' => true,
        'hierarchical' => false,
        'rewrite' => true,
        'supports' => array('title'),
        'has_archive' => true,
		'menu_icon' => 'dashicons-editor-ul',
    );
	$args_d = array(
		"label" => __('Registered Event', "borntogive-core"),
		"singular_label" => __('Registered Event', "borntogive-core"),
		'public' => true,
		'hierarchical' => true,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'args' => array('orderby' => 'term_order'),
		'rewrite' => false,
		'query_var' => true,
		'show_admin_column' => true,
	);
	register_taxonomy('registrant-event', 'event_registrants', $args_d);
    register_post_type('event_registrants', $args);
	register_taxonomy_for_object_type('registrant-event','event_registrants');
}
function imic_event_register() {
global $event_post_slug, $event_post_title, $event_post_all, $event_post_categories, $event_post_tags, $event_category_slug, $event_tag_slug, $event_archive;
		$labels = array(
			'name' => $event_post_title,
			'singular_name' => __('Event', 'borntogive-core'),
			'add_new' => __('Add New', 'borntogive-core'),
			'all_items'=> $event_post_all,
			'add_new_item' => __('Add New', 'borntogive-core'),
			'edit_item' => __('Edit', 'borntogive-core'),
			'new_item' => __('New', 'borntogive-core'),
			'view_item' => __('View', 'borntogive-core'),
			'search_items' => __('Search', 'borntogive-core'),
			'not_found' => __('Nothing found', 'borntogive-core'),
			'not_found_in_trash' => __('Nothing found in Trash', 'borntogive-core'),
			'parent_item_colon' => '',
		);
	$args_d = array(
		"label" => $event_post_categories,
		"singular_label" => __('Event Categroy', "borntogive-core"),
		'public' => true,
		'hierarchical' => true,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'args' => array('orderby' => 'term_order'),
		'rewrite' => array(
			'slug' => $event_category_slug,
			'with_front' => false
		),
		'query_var' => true,
		'show_admin_column' => true,
	);
	register_taxonomy('event-category', 'event', $args_d);
	$tags = array(
		"label" => $event_post_tags,
		"singular_label" => __('Event Tag','borntogive-core'),
		'public' => true,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'args' => array('orderby' => 'term_order'),
		'rewrite' => array(
			'slug' => $event_tag_slug,
			'with_front' => false
		),
	   'query_var' => true,
	   'show_admin_column' => true,
	);
	register_taxonomy('event-tag', 'event',$tags);
	   $args = array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => $event_post_slug,
				'with_front' => false,
		   		'pages' => true
			),
			'supports' => array('title', 'thumbnail','editor','author','excerpt'),
			'has_archive' => $event_archive,
	   		'query_var' => true,
			'menu_icon' => 'dashicons-format-chat',
		   );
		register_post_type('event', $args);
		register_taxonomy_for_object_type('event-category','event');
		register_taxonomy_for_object_type('event-tag','event');
		register_taxonomy_for_object_type('venue','event');
}
?>