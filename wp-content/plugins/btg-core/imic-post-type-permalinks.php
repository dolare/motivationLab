<?php
/*
 * 	Copyright IMIC 2014 
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
add_action('admin_init', 'imicPermalinkSettingAtStart');
/**
 * Start  permalink settings
 */
function imicPermalinkSettingAtStart() {
// Add a section to the permalinks page
add_settings_section('imic_setting_section',
__("Permalink Settings for custom post type and their taxonomy",'borntogive-core'),
'setting_section_callback_function',
'permalink'
);
$post_types = get_post_types( array('_builtin' => false,'public' => true), 'names' ); 
foreach ($post_types as $post_type):
if(isset($_POST['submit']) and isset($_POST['_wp_http_referer'])){
if( strpos($_POST['_wp_http_referer'],'options-permalink.php') !== FALSE ) {
$structure = sanitize_text_field($_POST[$post_type.'_structure']);#get setting
#default permalink structure
if( !$structure )
$structure = $post_type;
untrailingslashit($structure);
update_option($post_type.'_structure', $structure );
//texonomy structure
$pt_object = get_post_type_object($post_type);
foreach($pt_object->taxonomies as $term){
 $cat_structure = sanitize_text_field($_POST['structure_'.$term]);#get setting
if( !$cat_structure )
$cat_structure = $cat_structure;
untrailingslashit($cat_structure);
update_option('structure_'.$term, $cat_structure );
//End texonomy structure   
}}}
add_settings_field($post_type.'_structure',
ucfirst($post_type),
'imicSettingStructureCallback',
'permalink',
'imic_setting_section',
$post_type.'_structure'
);
register_setting('permalink',$post_type.'_structure');
endforeach;
}function setting_section_callback_function() {
?>
<p>
<?php _e("If you don't entered permalink structure, permalink is configured as post type",'borntogive-core');?>
</p>
<?php
}
function imicSettingStructureCallback(  $option  ) {
$post_type = str_replace('_structure',"" ,$option);
$pt_object = get_post_type_object($post_type);
$slug = $pt_object->rewrite['slug'];
$with_front = $pt_object->rewrite['with_front'];
$value = get_option($option);
if( !$value )
$value = $post_type;
global $wp_rewrite;
$front = substr( $wp_rewrite->front, 1 );
if( $front and $with_front ) {
$slug = $front.$slug;
}
echo '<p><code>'.home_url().'/'.'</code> <input name="'.$option.'" id="'.$option.'" type="text" class="regular-text code" value="' . $value .'" /></p>';
foreach($pt_object->taxonomies as $term){
    $term_value =  get_option('structure_'.$term);
    if(empty($term_value)){
        $term_value=$term;
    }
echo '<p><code>'.home_url().'/'.'</code> <input name="structure_'.$term.'" id="'.$term.'" type="text" class="regular-text code" value="' . $term_value .'" /></p>';
}}
?>