<?php
/**
 * Add a hidden field in settings area.
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

$value = $view_args[ 'value' ];

?>
<input type="hidden"  
    id="<?php printf( 'charitable_settings_%s', implode( '_', $view_args[ 'key' ] ) ) ?>" 
    name="<?php printf( 'charitable_settings[%s]', $view_args[ 'name' ] ) ?>"
    value="<?php echo esc_attr( $value ) ?>"  />