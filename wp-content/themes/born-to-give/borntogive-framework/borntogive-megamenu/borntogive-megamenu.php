<?php
/*
 *
 *   BORNTOGIVE MEGA MENU FRAMEWORK
 *   Copyright BORNTOGIVE 2014-16 - www.imithemes.com
 *   ------------------------------------------------
 */
class borntogive_mega_menu {
    /* --------------------------------------------*
     * Constructor
     * -------------------------------------------- */
    /**
     * Initializes the menu by setting localization, filters, and administration functions.
     */
    function __construct() {
        // add custom menu fields to menu
        
        add_filter('wp_setup_nav_menu_item', array($this, 'borntogive_mega_menu_add_custom_nav_fields'));
        // save menu custom fields
        add_action('wp_update_nav_menu_item', array($this, 'borntogive_mega_menu_update_custom_nav_fields'), 10, 3);
        // edit menu walker
        add_filter('wp_edit_nav_menu_walker', array($this, 'borntogive_mega_menu_edit_walker'), 10, 2);
    }
    // end constructor
    /**
     * Add custom fields to $item nav object
     * in order to be used in custom Walker
     *
     * @access      public
     * @since       1.3 
     * @return      void
     */
    function borntogive_mega_menu_add_custom_nav_fields($menu_item) {
        $menu_item->ismega = get_post_meta($menu_item->ID, '_menu_is_mega', true);
        $menu_item->menuposttype = get_post_meta($menu_item->ID, '_menu_post_type', true);
				$menu_item->menusidebars = get_post_meta($menu_item->ID, '_menu_sidebars', true);
        $menu_item->menupost = get_post_meta($menu_item->ID, '_menu_post', true);
        $menu_item->menupostidcomma = get_post_meta($menu_item->ID, '_menu_post_id_comma', true);
        $menu_item->menushortcode = get_post_meta($menu_item->ID, '_menu_shortcode', true);
        return $menu_item;
    }
    /**
     * Save menu custom fields
     *
     * @access      public
     * @since       1.3 
     * @return      void
     */
    function borntogive_mega_menu_update_custom_nav_fields($menu_id, $menu_item_db_id, $args) {
        // Check if element is properly sent
        if (isset($_REQUEST['menu-is-mega'][$menu_item_db_id]) && $_REQUEST['menu-is-mega'][$menu_item_db_id] == 1) {
            update_post_meta($menu_item_db_id, '_menu_is_mega', 1);
        } else {
            update_post_meta($menu_item_db_id, '_menu_is_mega', 0);
        }
        if (isset($_REQUEST['menu-post-type'][$menu_item_db_id]) && !empty($_REQUEST['menu-post-type'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_post_type', $_REQUEST['menu-post-type'][$menu_item_db_id]);
        } 
        if (isset($_REQUEST['menu-post-type'][$menu_item_db_id]) && empty($_REQUEST['menu-post-type'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_post_type', '');
        }
				if (isset($_REQUEST['menu-sidebars'][$menu_item_db_id]) && !empty($_REQUEST['menu-sidebars'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_sidebars', $_REQUEST['menu-sidebars'][$menu_item_db_id]);
        } 
        if (isset($_REQUEST['menu-sidebars'][$menu_item_db_id]) && empty($_REQUEST['menu-sidebars'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_sidebars', '');
        }
        if (isset($_REQUEST['menu-post'][$menu_item_db_id]) && !empty($_REQUEST['menu-post'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_post', $_REQUEST['menu-post'][$menu_item_db_id]);
        }
        if (isset($_REQUEST['menu-post'][$menu_item_db_id]) &&empty($_REQUEST['menu-post'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_post','');
        }
        
        if (isset($_REQUEST['menu-post-id-comma'][$menu_item_db_id]) && !empty($_REQUEST['menu-post-id-comma'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_post_id_comma', $_REQUEST['menu-post-id-comma'][$menu_item_db_id]);
        }
        if (isset($_REQUEST['menu-post-id-comma'][$menu_item_db_id]) &&empty($_REQUEST['menu-post-id-comma'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_post_id_comma','');
        }
        if (isset($_REQUEST['menu-shortcode'][$menu_item_db_id]) && !empty($_REQUEST['menu-shortcode'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_shortcode', $_REQUEST['menu-shortcode'][$menu_item_db_id]);
        }
         if (isset($_REQUEST['menu-shortcode'][$menu_item_db_id]) && empty($_REQUEST['menu-shortcode'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_menu_shortcode','');
        }
    }
    /**
     * Define new Walker edit
     *
     * @access      public
     * @since       1.3 
     * @return      void
     */
    function borntogive_mega_menu_edit_walker($walker, $menu_id) {
        return 'Walker_Nav_Menu_Edit_Custom';
    }
}
// instantiate plugin's class
$GLOBALS['borntogive_mega_menu'] = new borntogive_mega_menu();
include_once( 'edit_custom_walker.php' );
include_once( 'custom_walker.php' );
?>