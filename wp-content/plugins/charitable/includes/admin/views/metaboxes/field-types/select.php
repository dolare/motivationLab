<?php
/**
 * Display checkbox field.
 *
 * @author      Eric Daams
 * @package     Charitable/Admin Views/Metaboxes
 * @copyright   Copyright (c) 2017, Studio 164a
 * @since       1.4.6
 */

global $post;

if ( ! isset( $view_args['meta_key'] ) ) {
	return;
}

if ( ! isset( $view_args['options'] ) ) {
	return;
}

$key         = $view_args['meta_key'];
$custom_keys = get_post_custom_keys( $post->ID );
$checked     = $custom_keys && in_array( $key, $custom_keys ) ? get_post_meta( $post->ID, $key, true ) : $view_args['default'];

$id          = ltrim( $key, '_' );
$id          = str_replace( '_', '-', $id );
$wrapper_id  = 'charitable-' . $id . '-wrap';

?>
<div id="<?php echo esc_attr( $wrapper_id ) ?>" class="charitable-metabox-wrap charitable-select-wrap">
	<?php if ( isset( $view_args['label'] ) ) : ?>
		<label for="<?php echo esc_attr( $id ) ?>"><?php echo $view_args['label']  ?></label>
	<?php endif ?>
	<select id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $key ) ?>">
	<?php
	foreach ( $view_args['options'] as $key => $option ) :

		if ( is_array( $option ) ) :

			$label = isset( $option['label'] ) ? $option['label'] : '' ?>

			<optgroup label="<?php echo $label ?>">

			<?php foreach ( $option['options'] as $k => $opt ) : ?>

				<option value="<?php echo esc_attr( $k ) ?>" <?php selected( $k, $checked ) ?>><?php echo $opt ?></option>

			<?php endforeach ?>

			</optgroup>

		<?php else : ?>
			
			<option value="<?php echo esc_attr( $key ) ?>" <?php selected( $key, $checked ) ?>><?php echo $option ?></option>

		<?php

		endif;
	endforeach;
	?>
	</select>    
</div>
