<?php
/**
 * Custom Walker
 * Copyright BORNTOGIVE 2014-16 - www.imithemes.com
 * @access      public
 * @since       1.0 
 * @return      void
 */
class borntogive_mega_menu_walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
        global $wp_query;
        global $borntogive_mega_menu;
        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';
        $class_names = $value = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $menu_is_mega = get_post_meta($item->ID, '_menu_is_mega', true);
        if ($menu_is_mega == 1) {
            $mega_class = 'megamenu';
        } else {
            $mega_class = '';
            $class_names = ' class="menu-item-' . $item->ID . ' ' . esc_attr($class_names) . ' ' . $mega_class . '"';
            $output .= $indent . '<li ' . $value . $class_names . '>';
        }
        $prepend = '';
        $append = '<span class="nav-line"></span>';
        $description = !empty($item->description) ? '<span>' . esc_attr($item->description) . '</span>' : '';
        if ($depth != 0) {
            $description = $append = $prepend = "";
        }
        //Menu Is Mega
        if (get_post_meta($item->ID, '_menu_is_mega', true) == 1) {
            $item_output = '';
            $menuposttype = get_post_meta($item->ID, '_menu_post_type', true);
						$menu_sidebars = get_post_meta($item->ID, '_menu_sidebars', true);
            $menupost = get_post_meta($item->ID, '_menu_post', true);
            $menu_post_id_comma = get_post_meta($item->ID, '_menu_post_id_comma', true);
            $menushortcode = get_post_meta($item->ID, '_menu_shortcode', true);
						$menushortcode = ($menushortcode!='')?$menushortcode:'[sidebar_megamenu id="'.$menu_sidebars.'"]';
						$hide_item_subtitle = $menu_sidebars!='' ? " megamenu-sub-title-hide" : "";
            $sidebar_container = $menu_sidebars!= '' ? " class='".$menu_sidebars."'" : "";
					 if (!empty($menuposttype) || !empty($menu_post_id_comma)) {
						$item_output.='<li'. $sidebar_container.'><div class="col-md-3"> <span class="megamenu-sub-title'.$hide_item_subtitle.'">' . $item->title . '</span>';
					} elseif (!empty($menushortcode)) {
						$item_output.='<li'. $sidebar_container.'><div class="col-md-3"> <span class="megamenu-sub-title'.$hide_item_subtitle.'">' . $item->title . '</span>';
					}
            if (!empty($menuposttype)) {
                if ($menuposttype == 'event') {
                    $item_output.='<ul class="sub-menu"><l';
                    //$item_output.=borntogive_get_recursive_event_data($menuposttype, $menupost);
                    $item_output.='</ul>';
                } else {
                    $posts = get_posts(array('post_type' => $menuposttype, 'post_status' => 'publish', 'suppress_filters' => false, 'posts_per_page' => $menupost));
                }
                if (!empty($posts)) {
                    $item_output.='<ul class="sub-menu">';
                    foreach ($posts as $posts_data) {
                        $item_output.='<li><a href="' . get_permalink($posts_data->ID) . '">' . $posts_data->post_title . '</a>';
                        $item_output.='</li>';
                    }
                    $item_output.='</ul>';
                }
            } else {
                if (!empty($menu_post_id_comma)) {
                    $data_in_post = explode(',', $menu_post_id_comma);
                    $item_output.='<ul class="sub-menu">';
                    foreach ($data_in_post as $posts_data) {
                        if (strpos($posts_data, 'M') !== false) {
                            $menuId = substr($posts_data, 1);
                            $args = array(
                                'output' => ARRAY_A,
                                'update_post_term_cache' => false);
                            $menu_items = wp_get_nav_menu_items($menuId, $args);
                            foreach ($menu_items as $items) {
                                $item_output.='<li><a href="' . $items->url . '">' . $items->title . '</a>';
                                $item_output.='</li>';
                            }
                        } else {
                            $posts_data = get_post($posts_data);
                            if (get_post_type($posts_data->ID) != 'event') {
                                $item_output.='<li>';
                                $item_output.='<a href="' . get_permalink($posts_data->ID) . '">' . $posts_data->post_title . '</a>';
                                $item_output.='</li>';
                            } else {
                                $item_output.=borntogive_get_recursive_event_data('event', 1, $posts_data->ID);
                            }
                        }
                    }
                    $item_output.='</ul>';
                }
            }
						if($menu_sidebars!='')
						{
							global $sidebars_widgets;
							$count = count ($sidebars_widgets[$menu_sidebars]);
							echo '<style>';
							echo '.'.$menu_sidebars.' .widget {';
							echo 'width:'.(100/$count-4).'%;';
							echo 'float:left;';
							echo '}';
							echo ' .megamenu-sub-title-hide{';
							echo 'display:none !important;visibility:hidden !important';
							echo '}';
							echo '</style>';
						}
						else
						{
							
						}
            if (!empty($menushortcode)) {
                $item_output.= do_shortcode($menushortcode);
            }
            if (!empty($menuposttype) || !empty($menu_post_id_comma) || !empty($menushortcode)) {
                $item_output.='</div></li>';
            }
        } else {
            $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
            $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
            $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
            $attributes .=!empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
            $item_output = $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . $prepend . apply_filters('the_title', $item->title, $item->ID) . $append;
            $item_output .= $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;
        }
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
function end_el(&$output, $item, $depth = 0, $args = array()){ if($item->object=='question' ) : $output .= "</li>\n"; endif; }
}
?>