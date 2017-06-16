<?php 
/**
 * Renders the extended description field for the Campaign post type.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $post;

$textarea_name = 'content';
$textarea_rows = apply_filters( 'charitable_extended_description_rows', 40 );
$textarea_placeholder = __( 'Enter description...', 'charitable' );
$textarea_tab_index = isset( $view_args['tab_index'] ) ? $view_args['tab_index'] : 0;

if ( $GLOBALS['wp_version'] >= 3.3 && function_exists( 'wp_editor' ) ) : 

	wp_editor( $post->post_content, 'charitable-extended-description', array( 
		'textarea_name' => 'post_content', 
		'textarea_rows' => $textarea_rows, 
		'tabindex' 		=> $textarea_tab_index 
	) );

else : 
	?>
	<textarea name="<?php esc_attr_e( $textarea_name ); ?>" id="<?php esc_attr_e( $textarea_id ); ?>" tabindex="<?php esc_attr_e( $textarea_tab_index ); ?>" rows="<?php esc_attr_e( $textarea_rows ); ?>" placeholder="<?php esc_attr_e( $textarea_placeholder ); ?>"><?php echo esc_html( htmlspecialchars( $textarea_content ) ); ?></textarea>
	<?php 
endif;