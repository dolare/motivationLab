<?php
/**
 * Display the PayPal sandbox test form.
 *
 * @author  Studio 164a
 * @package Charitable/Admin Views/Settings
 * @since   1.4.3
 */

$submit_url = add_query_arg( array(
	'charitable_action' => 'do_paypal_sandbox_test',
), admin_url( 'admin.php' ) );

$return_url = add_query_arg( array(
	'charitable_action' => 'paypal_sandbox_test_return',
), admin_url( 'admin.php' ) );

$notify_url = charitable_get_ipn_url( 'paypal_sandbox_test' );
$result     = get_option( 'charitable_paypal_sandbox_test', false );
$mode       = 'donations' == charitable_get_option( array( 'gateways_paypal', 'transaction_mode' ), 'standard' ) ? '_donations' : '_xclick';

?>
<div id="paypal-sandbox-test">
<?php if ( isset( $_GET['sandbox_test'] ) && ! $result ) : ?>

	<div class="notice notice-warning charitable-notice" data-notice="paypal-sandbox-test">
		<p><?php _e( 'We haven\'t received a notification from PayPal yet. Refresh this page in a few minutes. We will also send you an email to notify you when we receive the notification from PayPal.', 'charitable' ) ?></p>
	</div>

<?php elseif ( $result && get_transient( 'charitable_paypal-sandbox-test_notice' ) ) :

	if ( ! wp_script_is( 'charitable-admin-notice' ) ) :
		wp_enqueue_script( 'charitable-admin-notice' );
	endif;

	if ( 'succeeded' === $result ) : ?>
		<div class="notice notice-success charitable-notice" data-notice="paypal-sandbox-test">
			<p><?php _e( '<strong>Success!</strong> We received a notification from PayPal and were able to perform the security check. Looks like you\'re all set!', 'charitable' ) ?></p>
		</div>

	<?php elseif ( 'failed' === $result ) : ?>

		<div class="notice notice-error charitable-notice" data-notice="paypal-sandbox-test">
			<p><?php _e( '<strong>Failed</strong> We received a notification from PayPal but were not able to perform the security check.', 'charitable' ) ?></p>
		</div>

	<?php endif;

endif ?>
	<form method="post" action="<?php echo esc_url( $submit_url ) ?>">
		<input type="hidden" name="amount" value="5.00" />
		<input type="hidden" name="item_name" value="<?php esc_attr_e( 'Sandbox Test Donation', 'charitable' ) ?>" />
		<input type="hidden" name="no_shipping" value="1" />
		<input type="hidden" name="shipping" value="0" />
		<input type="hidden" name="no_note" value="1" />
		<input type="hidden" name="currency_code" value="<?php echo esc_attr( charitable_get_currency() ) ?>" />
		<input type="hidden" name="charset" value="<?php echo esc_attr( get_bloginfo( 'charset' ) ) ?>" />
		<input type="hidden" name="rm" value="2" />
		<input type="hidden" name="return" value="<?php echo esc_url( $return_url ) ?>" />
		<input type="hidden" name="notify_url" value="<?php echo esc_url( $notify_url ) ?>" />
		<input type="hidden" name="cbt" value="<?php echo esc_attr( get_bloginfo( 'name' ) ) ?>" />
		<input type="hidden" name="bn" value="Charitable_SP" />
		<input type="hidden" name="cmd" value="<?php echo $mode ?>" />
		<input type="hidden" name="country" value="<?php echo esc_attr( charitable_get_option( 'country', 'AU' ) ) ?>" />        
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><h3><?php _e( 'Run a Test Donation', 'charitable' ) ?></h3></th>
					<td><hr /></td>
				</tr>
				<tr>
					<td colspan="2" style="padding: 0 0 15px 0;">
						<p><?php printf( __( "In October 2016, PayPal is upgrading the SSL certificates used to secure its web sites and API endpoints. <a href='%s'>Read about how these changes will impact you.</a>", 'charitable' ),
							'https://www.wpcharitable.com/how-paypals-ssl-certificate-upgrade-will-affect-you-and-how-you-can-prepare-for-it/?utm_source=notice&utm_medium=wordpress-dashboard&utm_campaign=paypal-ssl-upgrade&utm_content=blog-post'
						) ?></p>						
						<p><?php printf(
							__( 'To prepare for this update, you can
								we strongly recommend creating a <a href="%s">PayPal sandbox account</a> and testing a donation in the sandbox using the form below.', 'charitable' ),
							'https://www.wpcharitable.com/documentation/how-do-i-create-a-paypal-sandbox-account/?utm_source=notice&utm_medium=wordpress-dashboard&utm_campaign=paypal-ssl-upgrade&utm_content=sandbox-docs'
						) ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Sandbox Seller Email Address', 'charitable' ) ?></th>
					<td><input type="email" name="business" value="" class="charitable-settings-field"></td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Make a Test Donation', 'charitable' ) ?>"></p>
	</form>
</div>
