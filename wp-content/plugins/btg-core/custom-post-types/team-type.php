<?php
/* ==================================================
  Team Post Type Functions
  ================================================== */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
add_action('init', 'imic_team_register', 0);
function imic_team_register() {
global $team_post_slug, $team_post_title, $team_post_all, $team_post_categories, $team_category_slug, $team_archive;
	$args_c = array(
		"label" => $team_post_categories,
		"singular_label" => __('Team Categroy', "borntogive-core"),
		'public' => true,
		'hierarchical' => true,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'args' => array('orderby' => 'term_order'),
		'rewrite' => array(
			'slug' => $team_category_slug,
			'with_front' => false
		),
		'query_var' => true,
		'show_admin_column' => true,
	);
	register_taxonomy('team-category', 'team', $args_c);
		$labels = array(
			'name' => $team_post_title,
			'singular_name' => __('Team', 'borntogive-core'),
			'add_new' => __('Add New Member', 'borntogive-core'),
			'all_items'=> $team_post_all,
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
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => $team_post_slug,
				'with_front' => false
			),
			'supports' => array('title', 'thumbnail','editor','author','excerpt'),
			'has_archive' => $team_archive,
			'menu_icon' => 'dashicons-groups',
		   );
		register_post_type('team', $args);
		register_taxonomy_for_object_type('team-category','team');
}
?>