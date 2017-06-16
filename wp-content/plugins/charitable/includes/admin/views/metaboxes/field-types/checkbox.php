<?php
/**
 * Display checkbox field.
 *
 * @author      Eric Daams
 * @package     Charitable/Admin Views/Metaboxes
 * @copyright   Copyright (c) 2017, Studio 164a
 * @since       1.0.0
 */

global $post;

if ( ! isset( $view_args['meta_key'] ) ) {
	return;
}

$key         = $view_args['meta_key'];
$custom_keys = get_post_custom_keys( $post->ID );
$checked     = $custom_keys && in_array( $key, $custom_keys ) ? get_post_meta( $post->ID, $key, true ) : $view_args['default'];

$id          = ltrim( $key, '_' );
$id          = str_replace( '_', '-', $id );
$wrapper_id  = 'charitable-' . $id . '-wrap';

?>
<div id="<?php echo $wrapper_id ?>" class="charitable-metabox-wrap charitable-checkbox-wrap">
	<input type="checkbox" 
		id="<?php echo $id ?>" 
		name="<?php echo esc_attr( $key ) ?>"
		value="1"
		<?php checked( $checked ) ?> />
	<?php if ( isset( $view_args['label'] ) ) : ?>
		<label for="<?php echo $id ?>"><?php echo $view_args['label']  ?></label>
	<?php endif ?>
</div>
