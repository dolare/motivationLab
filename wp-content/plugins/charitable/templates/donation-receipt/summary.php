<?php
/**
 * Displays the donation summary.
 *
 * Override this template by copying it to yourtheme/charitable/donation-receipt/summary.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Donation Receipt
 * @since   1.0.0
 * @version 1.4.7
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * @var     Charitable_Donation
 */
$donation = $view_args['donation'];

?>
<dl class="donation-summary">
	<dt class="donation-id"><?php _e( 'Donation Number:', 'charitable' ) ?></dt>
	<dd class="donation-summary-value"><?php echo $donation->get_number() ?></dd>
	<dt class="donation-date"><?php _e( 'Date:', 'charitable' ) ?></dt>
	<dd class="donation-summary-value"><?php echo $donation->get_date() ?></dd>
	<dt class="donation-total"> <?php _e( 'Total:', 'charitable' ) ?></dt>
	<dd class="donation-summary-value"><?php echo charitable_format_money( $donation->get_total_donation_amount() ) ?></dd>
	<dt class="donation-method"><?php _e( 'Payment Method:', 'charitable' ) ?></dt>
	<dd class="donation-summary-value"><?php echo $donation->get_gateway_label() ?></dd>
</dl>
