<?php
/**
 * The template used to display the registration form.
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Account
 * @since   1.0.0
 * @version 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

$form = $view_args['form'];

/**
 * @hook    charitable_user_registration_before
 */
do_action( 'charitable_user_registration_before', $form );

?>
<form method="post" id="charitable-registration-form" class="charitable-form">
	<?php
	/**
	 * @hook    charitable_form_before_fields
	 */
	do_action( 'charitable_form_before_fields', $form ) ?>
	
	<div class="charitable-form-fields cf">

	<?php

	$i = 1;

	foreach ( $form->get_fields() as $key => $field ) :

		do_action( 'charitable_form_field', $field, $key, $form, $i );

		$i += apply_filters( 'charitable_form_field_increment', 1, $field, $key, $form, $i );

	endforeach;

	?>
	
	</div>

	<?php
	/**
	 * @hook    charitable_form_after_fields
	 */
	do_action( 'charitable_form_after_fields', $form );

	?>
	<div class="charitable-form-field charitable-submit-field">
		<button class="button button-primary" type="submit" name="register"><?php esc_attr_e( 'Register', 'charitable' ) ?></button>
	</div>
</form>
<?php

/**
 * @hook    charitable_user_registration_after
 */
do_action( 'charitable_user_registration_after', $form );
