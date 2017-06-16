<?php
/** Add Colorpicker Field to "Add New Category" Form **/
function imic_category_form_custom_field_add( $taxonomy ) {
?>
<div class="form-field">
    <label for="category_custom_color">Color</label>
    <input name="cat_meta[catBG]" class="colorpicker" type="text" value="" />
    <p class="description">Pick a Category Color</p>
</div>
<?php
}
add_action('event-category_add_form_fields', 'imic_category_form_custom_field_add', 10 );
/** Add New Field To Category **/
function imic_extra_category_fields( $tag ) {
    $t_id = $tag->term_id;
    $cat_meta = get_option( "category_$t_id" );
?>
<tr class="form-field">
    <th scope="row" valign="top"><label for="meta-color"><?php esc_html_e('Category Background Color for Events Calendar','borntogive'); ?></label></th>
    <td>
        <div id="colorpicker">
            <input type="text" name="cat_meta[catBG]" class="colorpicker" size="3" style="width:20%;" value="<?php echo (isset($cat_meta['catBG'])) ? $cat_meta['catBG'] : '#fff'; ?>" />
        </div>
            <br />
        <span class="description"></span>
            <br />
        </td>
</tr>
<?php
}
add_action('event-category_edit_form_fields','imic_extra_category_fields');
/** Save Category Meta **/
function imic_save_extra_category_fileds( $term_id ) {
    if ( isset( $_POST['cat_meta'] ) ) {
        $t_id = $term_id;
        $cat_meta = get_option( "category_$t_id");
        $cat_keys = array_keys($_POST['cat_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['cat_meta'][$key])){
                $cat_meta[$key] = $_POST['cat_meta'][$key];
            }
        }
        //save the option array
        update_option( "category_$t_id", $cat_meta );
    }
}
add_action ('edited_event-category', 'imic_save_extra_category_fileds');
add_action('created_event-category', 'imic_save_extra_category_fileds', 11, 1);
/** Enqueue Color Picker **/
function imic_colorpicker_enqueue() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'colorpicker-js', get_template_directory_uri() . '/js/colorpicker.js', array( 'wp-color-picker' ) );
}
add_action('admin_enqueue_scripts', 'imic_colorpicker_enqueue' );