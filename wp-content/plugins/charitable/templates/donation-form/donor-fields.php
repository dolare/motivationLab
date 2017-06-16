<?php
/**
 * The template used to display the user fields.
 *
 * @author 	Studio 164a
 * @package Charitable/Templates/Donation Form
 * @since 	1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! isset( $view_args['form'] ) || ! isset( $view_args['field'] ) ) {
	return;
}

$form   = $view_args['form'];
$field  = $view_args['field'];
$fields = isset( $field['fields'] ) ? $field['fields'] : array();

if ( empty( $fields ) ) {
	return;
}

?>
<fieldset id="charitable-donor-fields" class="charitable-fieldset">
	<?php
	if ( isset( $field['legend'] ) ) : ?>

		<div class="charitable-form-header"><?php echo $field['legend'] ?></div>

	<?php
	endif;

	/**
	 * @hook 	charitable_donation_form_donor_fields_before
	 */
	do_action( 'charitable_donation_form_donor_fields_before', $form );

	?>
	<div class="charitable-form-fields cf">

		<?php
		$i = 1;

		foreach ( $fields as $key => $field ) :

			do_action( 'charitable_form_field', $field, $key, $form, $i );

			$i += apply_filters( 'charitable_form_field_increment', 1, $field, $key, $form, $i );

		endforeach; ?>

	</div>
	<?php

	/**
	 * @hook 	charitable_donation_form_donor_fields_after
	 */
	do_action( 'charitable_donation_form_donor_fields_after', $form );

	?>
</fieldset><!-- .charitable-fieldset -->
