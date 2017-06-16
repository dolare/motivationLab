<?php
/**
 * The template used to display the forgot password form. Provided here primarily as a way to make it easier to override using theme templates.
 *
 * Override this template by copying it to yourtheme/charitable/account/forgot-password.php
 *
 * @author  Rafe Colton
 * @package Charitable/Templates/Account
 * @since   1.4.0
 * @version 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * @var 	Charitable_Forgot_Password_Form
 */
$form = $view_args['form'];

?>
<div class="charitable-forgot-password-form">
	<?php
	/**
	* @hook charitable_forgot_password_before
	*/
	do_action( 'charitable_forgot_password_before' );

	?>
	<form id="lostpasswordform" class="charitable-form" method="post">

		<?php do_action( 'charitable_form_before_fields', $form ); ?>

		<div class="charitable-form-fields cf">
			<?php
			$i = 1;

			foreach ( $form->get_fields() as $key => $field ) :

				do_action( 'charitable_form_field', $field, $key, $form, $i );

				$i += apply_filters( 'charitable_form_field_increment', 1, $field, $key, $form, $i );

			endforeach;

			?>
		</div>
		
		<?php do_action( 'charitable_form_after_fields', $form ); ?>
		
		<div class="charitable-form-field charitable-submit-field">
			<button class="button button-primary lostpassword-button" type="submit"><?php esc_attr_e( 'Reset Password', 'charitable' ) ?></button>
		</div>

	</form>
	<?php

	/**
	* @hook charitable_forgot_password_after
	*/
	do_action( 'charitable_forgot_password_after' );
	?>
</div>
