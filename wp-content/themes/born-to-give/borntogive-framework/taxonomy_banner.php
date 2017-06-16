<?php
if (!defined('ABSPATH')){
   exit; }// Exit if accessed directly
   /*
    * Add Image Field to category
    */
if (isset($_REQUEST['taxonomy'])):
$taxonomy = $_REQUEST['taxonomy'];
if(!function_exists('vestige_image_category_tax_custom_fields')):
add_action($taxonomy . '_add_form_fields', 'vestige_image_category_tax_custom_fields', 10, 2);
add_action($taxonomy . '_edit_form_fields', 'vestige_image_category_tax_custom_fields', 10, 2);
function vestige_image_category_tax_custom_fields($tag) {
       global $taxonomy;
       if (is_object($tag)) {
           $t_id = $tag->term_id; // Get the ID of the term we're editing
          $term_meta = get_option($taxonomy . $t_id . "_term_banner"); // Do the check
       } else {
           $term_meta = '';
       }
       ?>
       <table class="form-table">
           <tbody><tr class="form-field form-required">
                   <th scope="row"><label for="image"><?php esc_html_e('Term Banner Image', 'borntogive') ?></label></th>
                   <td><?php
                       echo '<div><img id ="banner_image_preview" src ="' . $term_meta . '" width ="150px"/></div>';
                       echo '<input id="upload_term_button" type="button" class="button button-primary" value="'.esc_html__('Upload Image', 'borntogive').'" /> ';
                      if(isset($_REQUEST['tag_ID'])){
                       echo '<input id="upload_term_button_remove" type="button" class="button button-primary" value="'.esc_html__('Remove Image', 'borntogive').'" />';
                      }
                       ?>
                   <p class="description"><?php esc_html_e('Upload an image for the taxonomy .', 'borntogive'); ?></p></td>
                 </tr><input type="hidden" id="term_url" name="banner_term_id" value="<?php echo esc_url($term_meta); ?>" />
           </tbody>
       </table>              
   <?php
} endif;
if(!function_exists('vestige_image_category_save_taxonomy_custom_fields')):
add_action('created_' . $taxonomy, 'vestige_image_category_save_taxonomy_custom_fields');
add_action('edited_' . $taxonomy, 'vestige_image_category_save_taxonomy_custom_fields', 10, 2);
function vestige_image_category_save_taxonomy_custom_fields($term_id) {
       global $taxonomy;
       $t_id = $term_id;
       if (isset($_POST['banner_term_id'])) {
           $term_meta = $_POST['banner_term_id'];
           update_option($taxonomy . $t_id . "_term_banner", $term_meta);
         }
       }
       endif;
endif;
?>