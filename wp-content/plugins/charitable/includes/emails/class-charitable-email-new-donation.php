<?php
/**
 * Class that models the new donation email.
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Email_New_Donation
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Email_New_Donation' ) ) :

	/**
	 * New Donation Email
	 *
	 * @since       1.0.0
	 */
	class Charitable_Email_New_Donation extends Charitable_Email {

		/**
		 * @var     string
		 */
		const ID = 'new_donation';

		/**
		 * @var     boolean Whether the email allows you to define the email recipients.
		 * @access  protected
		 * @since   1.1.0
		 */
		protected $has_recipient_field = true;

		/**
		 * @var     string[] Array of supported object types (campaigns, donations, donors, etc).
		 * @access  protected
		 * @since   1.0.0
		 */
		protected $object_types = array( 'donation' );

		/**
		 * Instantiate the email class, defining its key values.
		 *
		 * @param   mixed[]  $objects
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct( $objects = array() ) {
			parent::__construct( $objects );

			$this->name = apply_filters( 'charitable_email_new_donation_name', __( 'Admin: New Donation Notification', 'charitable' ) );
		}

		/**
		 * Returns the current email's ID.
		 *
		 * @return  string
		 * @access  public
		 * @static
		 * @since   1.0.3
		 */
		public static function get_email_id() {
			return self::ID;
		}

		/**
		 * Static method that is fired right after a donation is completed, sending the donation receipt.
		 *
		 * @param   int     $donation_id
		 * @return  boolean
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function send_with_donation_id( $donation_id ) {
			if ( ! charitable_get_helper( 'emails' )->is_enabled_email( self::get_email_id() ) ) {
				return false;
			}

			if ( ! charitable_is_approved_status( get_post_status( $donation_id ) ) ) {
				return false;
			}

			$donation = new Charitable_Donation( $donation_id );

			if ( ! is_object( $donation ) || 0 == count( $donation->get_campaign_donations() ) ) {
				return false;
			}

			if ( ! apply_filters( 'charitable_send_' . self::get_email_id(), true, $donation ) ) {
				return false;
			}

			$email = new Charitable_Email_New_Donation( array(
				'donation' => $donation,
			) );

			/**
			 * Don't resend the email.
			 */
			if ( $email->is_sent_already( $donation_id ) ) {
				return false;
			}

			$sent = $email->send();

			/**
			 * Log that the email was sent.
			 */
			if ( apply_filters( 'charitable_log_email_send', true, self::get_email_id(), $email ) ) {
				$email->log( $donation_id, $sent );
			}

			return true;
		}

		/**
		 * Return the default recipient for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_default_recipient() {
			return get_option( 'admin_email' );
		}

		/**
		 * Return the default subject line for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_default_subject() {
			return __( 'You have received a new donation', 'charitable' );
		}

		/**
		 * Return the default headline for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_default_headline() {
			return apply_filters( 'charitable_email_donation_receipt_default_headline', __( 'New Donation', 'charitable' ), $this );
		}

		/**
		 * Return the default body for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_default_body() {
			ob_start();
?>
<p><?php _e( '[charitable_email show=donor] has just made a donation!', 'charitable' ) ?></p>
<p><strong>Summary</strong><br />
[charitable_email show=donation_summary]</p>
<?php
			$body = ob_get_clean();

			return apply_filters( 'charitable_email_new_donation_default_body', $body, $this );
		}
	}

endif; // End class_exists check
