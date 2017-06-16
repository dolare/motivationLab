<?php
/**
 * Display text field. 
 *
 * @author      Eric Daams
 * @package     Charitable/Admin Views/Metaboxes
 * @copyright   Copyright (c) 2017, Studio 164a 
 * @since       1.2.0
 */

global $post;

if ( ! isset( $view_args[ 'meta_key' ] ) ) {
    return;
}

$key = $view_args[ 'meta_key' ];
$custom_keys = get_post_custom_keys( $post->ID );
$value = $custom_keys && in_array( $key, $custom_keys ) ? get_post_meta( $post->ID, $key, true ) : $view_args[ 'default' ];

$id = ltrim( $key, '_' );
$id = str_replace( '_', '-', $id );
$wrapper_id = 'charitable-' . $id . '-wrap';

?>
<div id="<?php echo $wrapper_id ?>" class="charitable-metabox-wrap charitable-text-field-wrap">
    <?php if ( isset( $view_args['label'] ) ) : ?>
        <label for="<?php echo $id ?>"><?php echo $view_args[ 'label' ]  ?></label>
    <?php endif ?>
    <input type="text" 
        id="<?php echo $id ?>" 
        name="<?php echo esc_attr( $key ) ?>"
        value="<?php echo esc_attr( $value ) ?>" />    
</div>