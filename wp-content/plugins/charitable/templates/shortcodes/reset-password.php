<?php
/**
 * The template used to display the reset password form. Provided here
 * primarily as a way to make it easier to override using theme templates.
 *
 * @author  Rafe Colton
 * @package Charitable/Templates/Account
 * @since   1.4.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$form = $view_args[ 'form' ];

?>
<div class="charitable-reset-password-form">
  <?php
  /**
   * @hook charitable_reset_password_before
   */
  do_action( 'charitable_reset_password_before' );

  ?>
  <form id="resetpassform" class="charitable-form" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off">

    <?php do_action( 'charitable_form_before_fields', $form ); ?>

    <div class="charitable-form-fields cf">
      <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $view_args['login'] ); ?>" autocomplete="off" >
      <input type="hidden" name="rp_key" value="<?php echo esc_attr( $view_args['key'] ); ?>" >

      <?php
      $i = 1;

      foreach ( $form->get_fields() as $key => $field ) :
        do_action( 'charitable_form_field', $field, $key, $form, $i );
        $i += apply_filters( 'charitable_form_field_increment', 1, $field, $key, $form, $i );
      endforeach;

      ?>
      <p class="description"><?php echo wp_get_password_hint(); ?></p>

    </div>
    <?php do_action( 'charitable_form_after_fields', $form ); ?>
    <div class="charitable-form-field charitable-submit-field resetpass-submit">
      <input type="submit" name="submit" class="lostpassword-button" id="resetpass-button"
         value="<?php _e( 'Reset Password', 'charitable' ); ?>">
    </div>
  </form>

  <?php
  /**
   * @hook charitable_reset_password_after
   */
  do_action( 'charitable_reset_password_after' );
  ?>
</div>
