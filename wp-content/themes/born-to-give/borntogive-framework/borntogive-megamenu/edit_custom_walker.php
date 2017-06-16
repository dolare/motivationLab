<?php
/**
 *  /!\ This is a copy of Walker_Nav_Menu_Edit class in core
 * 
 * Create HTML list of nav menu input items.
 * Copyright BORNTOGIVE 2014 - 2016 - www.imithemes.com
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu {
    /**
     * @see Walker_Nav_Menu::start_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function start_lvl(&$output, $depth = 0, $args = array()) {
        
    }
    /**
     * @see Walker_Nav_Menu::end_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function end_lvl(&$output, $depth = 0, $args = array()) {
        
    }
    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param object $args
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
        global $_wp_nav_menu_max_depth;
        global $borntogive_mega_menu;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';
        ob_start();
        $item_id = esc_attr($item->ID);
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );
        $original_title = '';
        if ('taxonomy' == $item->type) {
            $original_title = get_term_field('name', $item->object_id, $item->object, 'raw');
            if (is_wp_error($original_title))
                $original_title = false;
        } elseif ('post_type' == $item->type) {
            $original_object = get_post($item->object_id);
            $original_title = $original_object->post_title;
        }
        
        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr($item->object),
            'menu-item-edit-' . ( ( isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );
        $title = $item->title;
        if (!empty($item->_invalid)) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf(__('%s (Invalid)', 'borntogive'), $item->title);
        } elseif (isset($item->post_status) && 'draft' == $item->post_status) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf(__('%s (Pending)', 'borntogive'), $item->title);
        }
        $title = empty($item->label) ? $title : $item->label;
        ?>
        <li id="menu-item-<?php echo esc_attr($item_id); ?>" class="<?php echo implode(' ', $classes); ?>">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                <span class="item-title"><?php echo esc_html($title); ?></span>
                <span class="item-controls">
                    <span class="item-type"><?php echo esc_html($item->type_label); ?></span>
                    <span class="item-order hide-if-js">
                        <a href="<?php
                        echo wp_nonce_url(
                                esc_url(add_query_arg(
                                        array(
                            'action' => 'move-up-menu-item',
                            'menu-item' => $item_id,
                                        ), remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                )), 'move-menu_item'
                        );
                        ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up','borntogive'); ?>">&#8593;</abbr></a>
                        |
                        <a href="<?php
                        echo wp_nonce_url(
                                esc_url(add_query_arg(
                                        array(
                            'action' => 'move-down-menu-item',
                            'menu-item' => $item_id,
                                        ), remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                )), 'move-menu_item'
                        );
                        ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down', 'borntogive'); ?>">&#8595;</abbr></a>
                    </span>
                    <a class="item-edit" id="edit-<?php echo esc_attr($item_id); ?>" title="<?php esc_attr_e('Edit Menu Item', 'borntogive'); ?>" href="<?php
                    echo ( isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item'] ) ? admin_url('nav-menus.php') : esc_url(add_query_arg('edit-menu-item', $item_id, remove_query_arg($removed_args, admin_url('nav-menus.php#menu-item-settings-' . $item_id))));
                    ?>"><?php esc_html_e('Edit Menu Item', 'borntogive'); ?></a>
                </span>
                </dt>
            </dl>
            <div class="menu-item-settings" id="menu-item-settings-<?php echo esc_attr($item_id); ?>">
                <?php if ('custom' == $item->type) : ?>
                    <p class="field-url description description-wide">
                        <label for="edit-menu-item-url-<?php echo esc_attr($item_id); ?>">
                            <?php esc_html_e('URL', 'borntogive'); ?><br />
                            <input type="text" id="edit-menu-item-url-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->url); ?>" />
                        </label>
                    </p>
                <?php endif; ?>
                <p class="description description-thin">
                    <label for="edit-menu-item-title-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e('Navigation Label', 'borntogive'); ?><br />
                        <input type="text" id="edit-menu-item-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->title); ?>" />
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e('Title Attribute', 'borntogive'); ?><br />
                        <input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->post_excerpt); ?>" />
                    </label>
                </p>
                <p class="field-link-target description">
                    <label for="edit-menu-item-target-<?php echo esc_attr($item_id); ?>">
                        <input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr($item_id); ?>" value="_blank" name="menu-item-target[<?php echo esc_attr($item_id); ?>]"<?php checked($item->target, '_blank'); ?> />
                        <?php esc_html_e('Open link in a new window/tab', 'borntogive'); ?>
                    </label>
                </p>
                <p class="field-css-classes description description-thin">
                    <label for="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e('CSS Classes (optional)', 'borntogive'); ?><br />
                        <input type="text" id="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr(implode(' ', $item->classes)); ?>" />
                    </label>
                </p>
                <p class="field-xfn description description-thin">
                    <label for="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e('Link Relationship (XFN)', 'borntogive'); ?><br />
                        <input type="text" id="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->xfn); ?>" />
                    </label>
                </p>
                <p class="field-description description description-wide">
                    <label for="edit-menu-item-description-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e('Description', 'borntogive'); ?><br />
                        <textarea id="edit-menu-item-description-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo esc_attr($item_id); ?>]"><?php echo esc_html($item->description); // textarea_escaped     ?></textarea>
                        <span class="description"><?php esc_html_e('The description will be displayed in the menu if the current theme supports it.', 'borntogive'); ?></span>
                    </label>
                </p>
                <p class="field-move hide-if-no-js description description-wide">
					<label>
						<span><?php esc_html_e('Move','borntogive'); ?></span>
						<a href="#" class="menus-move-up"><?php esc_html_e( 'Up one','borntogive'); ?></a>
						<a href="#" class="menus-move-down"><?php esc_html_e( 'Down one','borntogive'); ?></a>
						<a href="#" class="menus-move-left"></a>
						<a href="#" class="menus-move-right"></a>
						<a href="#" class="menus-move-top"><?php esc_html_e( 'To the top','borntogive' ); ?></a>
					</label>
				</p>
                <?php
               
                  /* New fields insertion starts here */
                if(empty($item->type)){
              ?>
                
                <div class ="custom_menu_data">
                <p class="field-custom description description-wide">
                    <label for="edit-menu-is-mega-<?php echo esc_attr($item_id); ?>">
                        <input type="checkbox" id="edit-menu-is-mega-<?php echo esc_attr($item_id); ?>" class="edit-menu-item-custom megamenu"   name="menu-is-mega[<?php echo esc_attr($item_id); ?>]" value="1"<?php echo checked(!empty($item->ismega), 1, false); ?> /> <?php esc_html_e('Enable Mega Menu', 'borntogive'); ?>
                    </label>
                </p>
                <div class ="enabled_mega_data">
                <p class="field-custom description description-wide">
                    
                    <label for="edit-menu-post-type-<?php echo esc_attr($item_id); ?>">
                        <select name ="menu-post-type[<?php echo esc_attr($item_id); ?>]" class="menu-post-type edit-menu-item-custom" id="edit-menu-post-type-<?php echo esc_attr($item_id); ?>" >
                            <?php
                            
                            $post_types = borntogive_get_all_types();
                            if(($key = array_search('attachment', $post_types)) !== false){
					unset($post_types[$key]);
				}
                            echo '<option value ="">' . esc_html__('Select Post Type', 'borntogive') . '</option>';
                            
                            foreach ($post_types as $post_type) {
                                $activePost = ($post_type == $item->menuposttype)? 'selected' : '';
                                echo '<option value="'.trim($post_type).'"'.$activePost.'>'.$post_type.'</option>';
                            }
                            ?>
                        </select>
                    </label>
                </p>
                <p class="field-custom description description-wide">
                    
                    <label for="edit-menu-sidebars-<?php echo esc_attr($item_id); ?>">
                        <select name ="menu-sidebars[<?php echo esc_attr($item_id); ?>]" class="menu-sidebars edit-menu-item-custom" id="edit-menu-sidebars-<?php echo esc_attr($item_id); ?>" >
                            <?php
                            
                            $sidebars = borntogive_get_all_sidebars();
														
                            echo '<option value ="">' . esc_html__('Select Sidebar', 'borntogive') . '</option>';
                            
                            foreach ($sidebars as $key=>$value) {
                                $activePost = ($key == $item->menusidebars)? 'selected' : '';
                                echo '<option value="'.esc_attr($key).'"'.$activePost.'>'.esc_attr($value).'</option>';
                            }
                            ?>
                        </select>
                    </label>
                </p>
                <p class="field-custom description description-wide">
                    <label for="edit-menu-post-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e('Enter Number of Post Ex-3','borntogive'); ?><input type="text" id="edit-menu-post-<?php echo esc_attr($item_id); ?>" class="menu-post edit-menu-item-custom"   name="menu-post[<?php echo esc_attr($item_id); ?>]" value="<?php echo ($item->menupost); ?>" /> 
                    </label>
                </p>
                <p class="field-custom description description-wide">
                    <label for="edit-menu-post-id-comma-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e('Enter Comma seperated value Ex-1,2,3(for  menu use M like M10)','borntogive'); ?><input type="text" id="edit-menu-post-id-comma-<?php echo esc_attr($item_id); ?>" class="menu-post-id-comma edit-menu-item-custom"   name="menu-post-id-comma[<?php echo esc_attr($item_id); ?>]" value="<?php echo ($item->menupostidcomma); ?>" /> 
                    </label>
                </p>
                <p class="field-custom description description-wide">
                    <label for="edit-menu-shortcode-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e('Textarea may be used as Text Editor','borntogive'); ?>
                        <textarea id="edit-menu-shortcode-<?php echo esc_attr($item_id); ?>" class="edit-menu-item-custom" style="margin: 2px; width: 396px; height: 92px;"  name="menu-shortcode[<?php echo esc_attr($item_id); ?>]" /><?php echo ($item->menushortcode); ?></textarea> 
                    
                    </label>
                </p>
                 </div>
                </div>
        <?php
                }
                
        /* New fields insertion ends here */
        ?>
                <div class="menu-item-actions description-wide submitbox">
                <?php if ('custom' != $item->type && $original_title !== false) : ?>
                        <p class="link-to-original">
                        <?php printf(__('Original: %s', 'borntogive'), '<a href="' . esc_attr($item->url) . '">' . esc_html($original_title) . '</a>'); ?>
                        </p>
                        <?php endif; ?>
                    <a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr($item_id); ?>" href="<?php
                    echo wp_nonce_url(
                            esc_url(add_query_arg(
                                    array(
                        'action' => 'delete-menu-item',
                        'menu-item' => $item_id,
                                    ), remove_query_arg($removed_args, admin_url('nav-menus.php'))
                            )), 'delete-menu_item_' . $item_id
                    );
                    ?>"><?php esc_html_e('Remove', 'borntogive'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo esc_attr($item_id); ?>" href="<?php echo esc_url(add_query_arg(array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg($removed_args, admin_url('nav-menus.php'))));
                    ?>#menu-item-settings-<?php echo esc_attr($item_id); ?>"><?php esc_html_e('Cancel', 'borntogive'); ?></a>
                </div>
                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item_id); ?>" />
                <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->object_id); ?>" />
                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->object); ?>" />
                <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->menu_item_parent); ?>" />
                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->menu_order); ?>" />
                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item->type); ?>" />
            </div><!-- .menu-item-settings-->
            <ul class="menu-item-transport"></ul>
        <?php
        $output .= ob_get_clean();
    }
}
?>