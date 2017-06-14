 <?php
/* ==================================================
  Gallery Post Type Functions
  ================================================== */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
add_action('init', 'imic_gallery_register', 0);
function imic_gallery_register() {
global $gallery_post_slug, $gallery_post_title, $gallery_post_all, $gallery_post_categories, $gallery_category_slug, $gallery_archive;
	$args_c = array(
    "label" => $gallery_post_categories,
    "singular_label" => __('Gallery Category', "borntogive-core"),
    'public' => true,
    'hierarchical' => true,
    'show_ui' => true,
    'show_in_nav_menus' => true,
    'args' => array('orderby' => 'term_order'),
	'rewrite' => array(
		'slug' => $gallery_category_slug,
		'with_front' => false
	),
    'query_var' => true,
	'show_admin_column' => true,
);
register_taxonomy('gallery-category', 'gallery', $args_c);
    $labels = array(
        'name' => $gallery_post_title,
        'singular_name' => __('Gallery Item', 'borntogive-core'),
        'add_new' => __('Add New', 'borntogive-core'),
        'all_items'=> $gallery_post_all,
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
        'show_in_nav_menus' => false,
        'hierarchical' => false,
		'rewrite' => array(
			'slug' => $gallery_post_slug,
			'with_front' => false
		),
        'supports' => array('title', 'thumbnail', 'author', 'post-formats','excerpt'),
        'has_archive' => $gallery_archive,
		'menu_icon' => 'dashicons-format-gallery',
       );
    register_post_type('gallery', $args);
	register_taxonomy_for_object_type('gallery-category','gallery');
}
?>