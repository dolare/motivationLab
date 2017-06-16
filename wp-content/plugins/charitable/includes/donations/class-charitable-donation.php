<?php
/**
 * Donation model
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Donation
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donation' ) ) :

	/**
	 * Donation Model
	 *
	 * @since       1.0.0
	 */
	class Charitable_Donation extends Charitable_Abstract_Donation {

		/**
		 * @var     string
		 * @access  public
		 * @since   1.4.0
		 */
		public $donation_type = 'simple';

		/**
		 * Process a refund.
		 *
		 * @param   float $refund_amount
		 * @param   string $message
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function process_refund( $refund_amount, $message = '' ) {
			$campaign_donations = $this->get_campaign_donations();

			$refund_log = get_post_meta( $this->ID, 'donation_refund', true );

			$total_refund = isset( $refund_log['total_refund'] ) ? $refund_log['total_refund'] : 0;
			$refunds_per_campaign = isset( $refund_log['campaign_refunds'] ) ? $refund_log['campaign_refunds'] : array();

			foreach ( $this->get_campaign_donations() as $campaign_donation ) {

				if ( 0 == $refund_amount ) {
					break;
				}

				if ( ! isset( $refunds_per_campaign[ $campaign_donation->campaign_id ] ) ) {
					$refunds_per_campaign[ $campaign_donation->campaign_id ] = array();
				}

				/**
				 * Calculate the amount to be refunded out of this particular campaign's amount.
				 *
				 * This takes into account any amounts that have already been refunded, to find the
				 * amount that remains credited towards to the campaign.
				 */
				$campaign_remaining_amount = $campaign_donation->amount - array_sum( $refunds_per_campaign[ $campaign_donation->campaign_id ] );

				if ( $campaign_remaining_amount > $refund_amount ) {
					$campaign_refund_amount = $refund_amount;
				} else {
					$campaign_refund_amount = $campaign_remaining_amount;
				}

				$refunds_per_campaign[ $campaign_donation->campaign_id ][] = $campaign_refund_amount;

				/* Reduce the remaining amount to refund. */
				$refund_amount -= $campaign_refund_amount;

				/* Increase the total refund amount. */
				$total_refund += $campaign_refund_amount;
			}

			$refund_log = array(
				'time' => time(),
				'message' => $message,
				'campaign_refunds' => $refunds_per_campaign,
				'total_refund' => $total_refund,
			);

			update_post_meta( $this->ID, 'donation_refund', $refund_log );

			$this->update_status( 'charitable-refunded' );
		}
	}

endif; // End class_exists check
