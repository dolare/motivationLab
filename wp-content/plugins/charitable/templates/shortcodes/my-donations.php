<?php
/**
 * Displays a table of the user's donations, with links to the donation receipts.
 *
 * Override this template by copying it to yourtheme/charitable/shortcodes/my-donations.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Account
 * @since   1.4.0
 * @version 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

$donations = $view_args['donations'];

/**
 * @hook    charitable_my_donations_before
 */
do_action( 'charitable_my_donations_before', $donations );

if ( empty( $donations ) ) : ?>

	<p><?php _e( 'You have not made any donations yet.', 'charitable' ) ?></p>

<?php else : ?>

	<table class="charitable-creator-donations">
		<thead>
			<tr>
				<th scope="col"><?php _e( 'Date', 'charitable' ) ?></th>
				<th scope="col"><?php _e( 'Campaign', 'charitable' ) ?></th>
				<th scope="col"><?php _e( 'Amount', 'charitable' ) ?></th>
				<th scope="col"><?php _e( 'Receipt', 'charitable' ) ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $donations as $donation ) : ?>
			<tr>
				<td><?php echo mysql2date( 'F j, Y', get_post_field( 'post_date', $donation->ID ) )?></td>
				<td><?php echo $donation->campaigns ?></td>
				<td><?php echo charitable_format_money( $donation->amount ) ?></td>
				<td><a href="<?php echo esc_url( charitable_get_permalink( 'donation_receipt_page', array( 'donation_id' => $donation->ID ) ) ) ?>"><?php _e( 'View Receipt', 'charitable' ) ?></a></td>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>

<?php endif;

/**
 * @hook    charitable_my_donations_after
 */
do_action( 'charitable_my_donations_after', $donations );
