 <?php
/* ==================================================
  Testimonial Post Type Functions
  ================================================== */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
add_action('init', 'testimonial_register', 0);
function testimonial_register() {
global $testimonial_post_slug, $testimonial_post_title, $testimonial_post_all, $testimonial_post_categories, $testimonial_category_slug, $testimonial_archive;
	$args_c = array(
		"label" => $testimonial_post_categories,
		"singular_label" => __('Testimonial Category', "borntogive-core"),
		'public' => true,
		'hierarchical' => true,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'args' => array('orderby' => 'term_order'),
		'rewrite' => array(
			'slug' => $testimonial_category_slug,
			'with_front' => false
		),
		'query_var' => true,
		'show_admin_column' => true,
	);
	register_taxonomy('testimonial-category', 'testimonial', $args_c);
    $labels = array(
        'name' => $testimonial_post_title,
        'singular_name' => __('Testimonial', 'borntogive-core'),
        'add_new' => __('Add New', 'borntogive-core'),
		'all_items'=> $testimonial_post_all,
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
			'slug' => $testimonial_post_slug,
			'with_front' => false
		),
        'supports' => array('title', 'thumbnail', 'editor','excerpt'),
        'has_archive' => $testimonial_archive,
		'menu_icon' => 'dashicons-editor-quote',
	
    );
    register_post_type('testimonial', $args);
	register_taxonomy_for_object_type('testimonial-category','testimonial');
}
?>