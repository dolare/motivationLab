<?php
/**
 * The template used to display the gateway fields.
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Donation Form
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! isset( $view_args['form'] ) || ! isset( $view_args['field'] ) ) {
	return;
}

$form     = $view_args['form'];
$field    = $view_args['field'];
$classes  = $view_args['classes'];
$gateways = $field['gateways'];
$default  = isset( $field['default'] ) && isset( $gateways[ $field['default'] ] ) ? $field['default'] : key( $gateways );

?>
<fieldset id="charitable-gateway-fields" class="charitable-fieldset">
	<?php
	if ( isset( $field['legend'] ) ) : ?>

		<div class="charitable-form-header"><?php echo $field['legend'] ?></div>

	<?php
	endif;

	if ( count( $gateways ) > 1 ) :
	?>
		<label for="charitable-gateway-selector"><?php _e( 'Choose Your Payment Method', 'charitable' ) ?></label>
		<ul id="charitable-gateway-selector" class="charitable-radio-list charitable-form-field">
			<?php foreach ( $gateways as $gateway_id => $details ) : ?>
				<li><input type="radio" 
						id="gateway-<?php echo $gateway_id ?>"
						name="gateway"
						value="<?php echo esc_attr( $gateway_id ) ?>"
						<?php checked( $default, $gateway_id ) ?> />
					<?php echo $details['label'] ?>
				</li>
			<?php endforeach ?>
		</ul>
	<?php
	endif;

	foreach ( $gateways as $gateway_id => $details ) :

		if ( ! isset( $details['fields'] ) || empty( $details['fields'] ) ) :
			continue;
		endif;

		?>
		<div id="charitable-gateway-fields-<?php echo $gateway_id ?>" class="charitable-gateway-fields charitable-form-fields cf" data-gateway="<?php echo $gateway_id ?>">
			<?php
			$i = 1;

			foreach ( $details['fields'] as $key => $field ) :

				do_action( 'charitable_form_field', $field, $key, $form, $i );

				$i += apply_filters( 'charitable_form_field_increment', 1, $field, $key, $form, $i );

			endforeach; ?>
		</div><!-- #charitable-gateway-fields-<?php echo $gateway_id ?> -->
	
	<?php endforeach ?>
</fieldset><!-- .charitable-fieldset -->
