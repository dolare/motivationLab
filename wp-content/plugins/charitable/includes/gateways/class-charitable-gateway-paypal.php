 <?php
/**
 * Paypal Payment Gateway class
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Gateway_Paypal
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Gateway_Paypal' ) ) :

	/**
	 * Paypal Payment Gateway
	 *
	 * @since		1.0.0
	 */
	class Charitable_Gateway_Paypal extends Charitable_Gateway {

		/**
		 * @var     string
		 */
		const ID = 'paypal';

		/**
		 * Instantiate the gateway class, defining its key values.
		 *
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->name = apply_filters( 'charitable_gateway_paypal_name', __( 'PayPal', 'charitable' ) );

			$this->defaults = array(
				'label' => __( 'PayPal', 'charitable' ),
			);

			$this->supports = array(
				'recurring',
				'1.3.0',
			);
		}

		/**
		 * Register gateway settings.
		 *
		 * @param   array   $settings
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function gateway_settings( $settings ) {
			$settings['paypal_email'] = array(
				'type'      => 'email',
				'title'     => __( 'PayPal Email Address', 'charitable' ),
				'priority'  => 6,
				'help'      => __( 'Enter the email address for the PayPal account that should receive donations.', 'charitable' ),
			);

			$settings['transaction_mode'] = array(
				'type'      => 'radio',
				'title'     => __( 'PayPal Transaction Type', 'charitable' ),
				'priority'  => 8,
				'options'   => array(
					'donations' => __( 'Donations', 'charitable' ),
					'standard'  => __( 'Standard Transaction', 'charitable' ),
				),
				'default'   => 'donations',
				'help'      => sprintf( '%s<br /><a href="%s" target="_blank">%s</a>',
					__( 'PayPal offers discounted fees to registered non-profit organizations. You must create a PayPal Business account to apply.', 'charitable' ),
					'https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=merchant%2Fdonations',
					__( 'Find out more.', 'charitable' )
				),
			);

			$settings['disable_ipn_verification'] = array(
				'type' 	   => 'checkbox',
				'title'	   => __( 'Disable IPN Verification', 'charitable' ),
				'priority' => 10,
				'default'  => 0,
				'help' 	   => __( 'If you are having problems with donations not getting marked as Paid, disabling IPN verification might fix the problem. However, it is important to be aware that this is a <strong>less secure</strong> method for verifying donations.', 'charitable' ),
			);

			return $settings;
		}

		/**
		 * Validate the submitted credit card details.
		 *
		 * @param   boolean $valid
		 * @param   string $gateway
		 * @param   mixed[] $values
		 * @return  boolean
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function validate_donation( $valid, $gateway, $values ) {
			if ( 'paypal' != $gateway ) {
				return $valid;
			}

			$settings = charitable_get_option( 'gateways_paypal', array() );
			$email = trim( $settings['paypal_email'] );

			/* Make sure that the keys are set. */
			if ( empty( $email ) ) {

				charitable_get_notices()->add_error( __( 'Missing PayPal email address. Unable to proceed with payment.', 'charitable' ) );
				return false;

			}

			return $valid;
		}

		/**
		 * Process the donation with PayPal.
		 *
		 * @param   boolean|array $return
		 * @param   int $donation_id
		 * @param   Charitable_Donation_Processor $processor
		 * @return  array
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function process_donation( $return, $donation_id, $processor ) {

			$gateway          = new Charitable_Gateway_Paypal();
			$user_data 		  = $processor->get_donation_data_value( 'user' );
			$donation 		  = charitable_get_donation( $donation_id );
			$transaction_mode = $gateway->get_value( 'transaction_mode' );
			$donation_key 	  = $processor->get_donation_data_value( 'donation_key' );

			$paypal_args = apply_filters( 'charitable_paypal_redirect_args', array(
				'business'      => $gateway->get_value( 'paypal_email' ),
				'email'         => isset( $user_data['email'] ) ? $user_data['email'] : '',
				'first_name'    => isset( $user_data['first_name'] ) ? $user_data['first_name'] : '',
				'last_name'     => isset( $user_data['last_name'] ) ? $user_data['last_name'] : '',
				'address1'      => isset( $user_data['address'] ) ? $user_data['address'] : '',
				'address2'      => isset( $user_data['address_2'] ) ? $user_data['address_2'] : '',
				'city'          => isset( $user_data['city'] ) ? $user_data['city'] : '',
				'country'       => isset( $user_data['country'] ) ? $user_data['country'] : '',
				'zip'           => isset( $user_data['postcode'] ) ? $user_data['postcode'] : '',
				'invoice'       => $donation_key,
				'amount'        => $donation->get_total_donation_amount( true ),
				'item_name'     => html_entity_decode( $donation->get_campaigns_donated_to(), ENT_COMPAT, 'UTF-8' ),
				'no_shipping'   => '1',
				'shipping'      => '0',
				'no_note'       => '1',
				'currency_code' => charitable_get_currency(),
				'charset'       => get_bloginfo( 'charset' ),
				// 'custom'        => json_encode( array( 'donation_id' => $donation_id, 'donation_key' => $donation_key ) ),
				'custom'        => $donation_id,
				'rm'            => '2',
				'return'        => charitable_get_permalink( 'donation_receipt_page', array( 'donation_id' => $donation_id ) ),
				'cancel_return' => charitable_get_permalink( 'donation_cancel_page', array( 'donation_id' => $donation_id ) ),
				'notify_url'    => charitable_get_ipn_url( Charitable_Gateway_Paypal::ID ),
				'cbt'           => get_bloginfo( 'name' ),
				'bn'            => 'Charitable_SP',
				'cmd'           => 'donations' == $transaction_mode ? '_donations' : '_xclick',
			), $donation_id, $processor );

			/* Set up the PayPal redirect URL. */
			$paypal_redirect = trailingslashit( $gateway->get_redirect_url() ) . '?';
			$paypal_redirect .= http_build_query( $paypal_args );
			$paypal_redirect = str_replace( '&amp;', '&', $paypal_redirect );

			/* Redirect to PayPal */
			return array(
				'redirect' => $paypal_redirect,
				'safe' => false,
			);

		}

		/**
		 * Handle a call to our IPN listener.
		 *
		 * @return  void
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function process_ipn() {

			/* We only accept POST requests */
			if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' != $_SERVER['REQUEST_METHOD'] ) {
				die( __( 'Invalid Request', 'charitable' ) );
			}

			$gateway = new Charitable_Gateway_Paypal();
			$data    = $gateway->get_encoded_ipn_data();

			if ( defined( 'CHARITABLE_DEBUG' ) && CHARITABLE_DEBUG ) {
			    error_log( json_encode( $data ) );
			}

			if ( empty( $data ) ) {
				die( __( 'Empty Data', 'charitable' ) );
			}

			if ( ! $gateway->get_value( 'disable_ipn_verification' ) && ! $gateway->paypal_ipn_verification( $data ) ) {
				die( __( 'IPN Verification Failure', 'charitable' ) );
			}

			$defaults = array(
				'txn_type'       => '',
				'payment_status' => '',
				'custom' 		 => 0,
			);

			$data        = wp_parse_args( $data, $defaults );
			$custom      = json_decode( $data['custom'], true );
			$donation_id = is_array( $custom ) && array_key_exists( 'donation_id', $custom )
				? absint( $custom['donation_id'] )
				: absint( $custom );

			if ( ! $donation_id ) {
				die( __( 'Missing Donation ID', 'charitable' ) );
			}

			/**
			 * By default, all transactions are handled by the web_accept handler.
			 * To handle other transaction types in a different way, use the
			 * 'charitable_paypal_{transaction_type}' hook.
			 *
			 * @see Charitable_Gateway_Paypal::process_web_accept()
			 */
			$txn_type = strlen( $data['txn_type'] ) ? $data['txn_type'] : 'web_accept';

			if ( has_action( 'charitable_paypal_' . $txn_type ) ) {

				do_action( 'charitable_paypal_' . $txn_type, $data, $donation_id );

			} else {

				do_action( 'charitable_paypal_web_accept', $data, $donation_id );

			}

			exit;
		}

		/**
		 * Receives verified IPN data from PayPal and processes the donation.
		 *
		 * @param 	array $data        The data received in the IPN from PayPal.
		 * @param 	int   $donation_id The donation ID received from PayPal.
		 * @return  void
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function process_web_accept( $data, $donation_id ) {

			$gateway        = new Charitable_Gateway_Paypal();
			$donation       = charitable_get_donation( $donation_id );

			if ( 'paypal' != $donation->get_gateway() ) {
				die( __( 'Incorrect Gateway', 'charitable' ) );
			}

			$custom         = json_decode( $data['custom'], true );

			if ( array_key_exists( 'invoice', $data ) ) {
				$donation_key = $data['invoice'];
			} elseif( is_array( $custom ) && array_key_exists( 'donation_key', $custom ) ) {
				$donation_key = $custom['donation_key'];
			} else {
				die( __( 'Missing Donation Key', 'charitable' ) );
			}

			$amount         = $data['mc_gross'];
			$payment_status = strtolower( $data['payment_status'] );
			$currency_code  = strtoupper( $data['mc_currency'] );
			$business_email = isset( $data['business'] ) && is_email( $data['business'] ) ? trim( $data['business'] ) : trim( $data['receiver_email'] );

			/* Verify that the business email matches the PayPal email in the settings */
			if ( strcasecmp( $business_email, trim( $gateway->get_value( 'paypal_email' ) ) ) != 0 ) {

				$message = sprintf( '%s %s', __( 'Invalid Business email in the IPN response. IPN data:', 'charitable' ), json_encode( $data ) );
				$donation->update_donation_log( $message );
				$donation->update_status( 'charitable-failed' );
				die( __( 'Incorrect Business Email', 'charitable' ) );

			}

			/* Verify that the currency matches. */
			if ( charitable_get_currency() != $currency_code ) {

				$message = sprintf( '%s %s', __( 'The currency in the IPN response does not match the site currency. IPN data:', 'charitable' ), json_encode( $data ) );
				$donation->update_donation_log( $message );
				$donation->update_status( 'charitable-failed' );

				die( __( 'Incorrect Currency', 'charitable' ) );

			}

			/* Process a refunded donation. */
			if ( in_array( $payment_status, array( 'refunded', 'reversed' ) ) ) {

				/* It's a partial refund. */
				if ( $amount < $donation->get_total_donation_amount( true ) ) {
					$message = sprintf( '%s: #%s',
						__( 'Partial PayPal refund processed', 'charitable' ),
						isset( $data['parent_txn_id'] ) ? $data['parent_txn_id'] : ''
					);
				} else {
					$message = sprintf( '%s #%s %s: %s',
						__( 'PayPal Payment', 'charitable' ),
						isset( $data['parent_txn_id'] ) ? $data['parent_txn_id'] : '',
						__( 'refunded with reason', 'charitable' ),
						isset( $data['reason_code'] ) ? $data['reason_code'] : ''
					);
				}

				$donation->process_refund( $amount, $message );

				die( __( 'Refund Processed', 'charitable' ) );

			}

			/* Mark a payment as failed. */
			if ( in_array( $payment_status, array( 'declined', 'failed', 'denied', 'expired', 'voided' ) ) ) {

				$message = sprintf( '%s: %s', __( 'The donation has failed with the following status', 'charitable' ), $payment_status );
				$donation->update_donation_log( $message );
				$donation->update_status( 'charitable-failed' );

				die( __( 'Payment Failed', 'charitable' ) );

			}

			/* If we have already processed this donation, stop here. */
			if ( 'charitable-completed' == get_post_status( $donation_id ) ) {
				die( __( 'Donation Processed Already', 'charitable' ) );
			}

			/* Verify that the donation key matches the one stored for the donation. */
			if ( $donation_key != $donation->get_donation_key() ) {

				$message = sprintf( '%s %s', __( 'Donation key in the IPN response does not match the donation. IPN data:', 'charitable' ), json_encode( $data ) );
				$donation->update_donation_log( $message );
				$donation->update_status( 'charitable-failed' );

				die( __( 'Invalid Donation Key', 'charitable' ) );

			}

			/* Verify that the amount in the IPN matches the amount we expected. */
			if ( $amount < $donation->get_total_donation_amount( true ) ) {

				$message = sprintf( '%s %s', __( 'The amount in the IPN response does not match the expected donation amount. IPN data:', 'charitable' ), json_encode( $data ) );
				$donation->update_donation_log( $message );
				$donation->update_status( 'charitable-failed' );

				die( __( 'Incorrect Amount', 'charitable' ) );

			}

			/* Save the transation ID */
			$donation->set_gateway_transaction_id( $data['txn_id'] );

			/* Process a completed donation. */
			if ( 'completed' == $payment_status ) {

				$message = sprintf( '%s: %s', __( 'PayPal Transaction ID', 'charitable' ), $data['txn_id'] );
				$donation->update_donation_log( $message );
				$donation->update_status( 'charitable-completed' );

				die( __( 'Donation Completed', 'charitable' ) );

			}

			/* If the donation is set to pending but has a pending_reason provided, save that to the log. */
			if ( 'pending' == $payment_status ) {

				if ( array_key_exists( 'pending_reason', $data ) ) {

					$message = $gateway->get_pending_reason_note( strtolower( $data['pending_reason'] ) );
					$donation->update_donation_log( $message );

				}

				$donation->update_status( 'charitable-pending' );

				die( __( 'Donation Pending', 'charitable' ) );

			}

			die( __( 'Unknown Response', 'charitable' ) );
		}

		/**
		 * Return the posted IPN data.
		 *
		 * @return  mixed[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_encoded_ipn_data() {
			$post_data = '';

			/* Fallback just in case post_max_size is lower than needed. */
			if ( ini_get( 'allow_url_fopen' ) ) {
				$post_data = file_get_contents( 'php://input' );
			} else {
				ini_set( 'post_max_size', '12M' );
			}

			if ( strlen( $post_data ) ) {
				$arg_separator = ini_get( 'arg_separator.output' );
				$data_string = 'cmd=_notify-validate' . $arg_separator . $post_data;

				/* Convert collected post data to an array */
				parse_str( $data_string, $data );

				return $data;
			}

			/* Return an empty array if there are no POST variables. */
			if ( empty( $_POST ) ) {
				return array();
			}

			$data = array(
				'cmd' => '_notify-validate',
			);
			
			return array_merge( $data, $_POST );

		}

		/**
		 * Validates an IPN request with PayPal.
		 *
		 * @param   mixed[] $data
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function paypal_ipn_verification( $data ) {

			$remote_post_vars = array(
				'method'           => 'POST',
				'timeout'          => 45,
				'redirection'      => 5,
				'httpversion'      => '1.1',
				'blocking'         => true,
				'headers'          => array(
					'host'         => 'www.paypal.com',
					'connection'   => 'close',
					'content-type' => 'application/x-www-form-urlencoded',
					'post'         => '/cgi-bin/webscr HTTP/1.1',

				),
				'sslverify'        => false,
				'body'             => $data,
			);

			/* Get response */
			$api_response = wp_remote_post( $this->get_redirect_url(), $remote_post_vars );

			$is_valid = ! is_wp_error( $api_response ) && 'VERIFIED' == $api_response['body'];

			return apply_filters( 'charitable_paypal_ipn_verification', $is_valid, $api_response );
		}

		/**
		 * Return a note to log for a pending payment.
		 *
		 * @param   string $reason_code
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_pending_reason_note( $reason_code ) {
			switch ( $reason_code ) {
				case 'echeck' :
					$note = __( 'Payment made via eCheck and will clear automatically in 5-8 days', 'charitable' );
					break;

				case 'address' :
					$note = __( 'Payment requires a confirmed customer address and must be accepted manually through PayPal', 'charitable' );
					break;

				case 'intl' :
					$note = __( 'Payment must be accepted manually through PayPal due to international account regulations', 'charitable' );
					break;

				case 'multi-currency' :
					$note = __( 'Payment received in non-shop currency and must be accepted manually through PayPal', 'charitable' );
					break;

				case 'paymentreview' :
				case 'regulatory_review' :
					$note = __( 'Payment is being reviewed by PayPal staff as high-risk or in possible violation of government regulations', 'charitable' );
					break;

				case 'unilateral' :
					$note = __( 'Payment was sent to non-confirmed or non-registered email address.', 'charitable' );
					break;

				case 'upgrade' :
					$note = __( 'PayPal account must be upgraded before this payment can be accepted', 'charitable' );
					break;

				case 'verify' :
					$note = __( 'PayPal account is not verified. Verify account in order to accept this payment', 'charitable' );
					break;

				default :
					$note = sprintf( __( 'Payment is pending for unknown reasons. Contact PayPal support for assistance. Reason code: %s', 'charitable' ), $reason_code );
			}

			return apply_filters( 'charitable_paypal_gateway_pending_reason_note', $note, $reason_code );
		}

		/**
		 * Return the base of the PayPal
		 *
		 * @param   bool $ssl_check
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_redirect_url( $ssl_check = false ) {
			$protocol = is_ssl() || ! $ssl_check ? 'https://' : 'http://';

			if ( charitable_get_option( 'test_mode' ) ) {

				$paypal_uri = $protocol . 'www.sandbox.paypal.com/cgi-bin/webscr';

			} else {

				$paypal_uri = $protocol . 'www.paypal.com/cgi-bin/webscr';

			}

			return apply_filters( 'charitable_paypal_uri', $paypal_uri );
		}

		/**
		 * Returns the current gateway's ID.
		 *
		 * @return  string
		 * @access  public
		 * @static
		 * @since   1.0.3
		 */
		public static function get_gateway_id() {
			return self::ID;
		}

		/**
		 * Receives the IPN from PayPal after the sandbox test and attempts to verify the result.
		 *
		 * @return  void
		 * @access  public
		 * @static
		 * @since   1.4.3
		 */
		public static function process_sandbox_test_ipn() {

			$gateway = new Charitable_Gateway_Paypal();
			$data    = $gateway->get_encoded_ipn_data();

			/* If any of these checks fail, we conclude that this is not a proper IPN from PayPal. */
			if ( empty( $data ) || ! is_array( $data ) ) {
				die("empty data");
			}

			/* Compare the token with the one we generated. */
			$token = get_option( 'charitable_paypal_sandbox_test_token' );

			if ( ! array_key_exists( 'custom', $data ) || $token !== $data['custom'] ) {
				die("missing or mismatched custom data");
			}

			$remote_post_vars = array(
				'method'           => 'POST',
				'timeout'          => 45,
				'redirection'      => 5,
				'httpversion'      => '1.1',
				'blocking'         => true,
				'headers'          => array(
					'host'         => 'www.paypal.com',
					'connection'   => 'close',
					'content-type' => 'application/x-www-form-urlencoded',
					'post'         => '/cgi-bin/webscr HTTP/1.1',
				),
				'sslverify'        => false,
				'body'             => $data,
			);

			/* Call the PayPal API to verify the IPN. */
			$protocol     = is_ssl() ? 'https://' : 'http://';
			$remote_url   = $protocol . 'www.sandbox.paypal.com/cgi-bin/webscr';
			$api_response = wp_remote_post( $remote_url, $remote_post_vars );
			$succeeded 	  = ! is_wp_error( $api_response );
			$message      = '';

			if ( $succeeded ) {

				$result  = 'succeeded';
				$subject = __( 'Your PayPal integration is working', 'charitable' );
				$message = __( '<p>Good news! We successfuly received the Instant Payment Notification from PayPal and were able to verify it with them.</p>', 'charitable' );
				$message .= __( '<p>This means that your website is all set to continue receiving donations through PayPal. You should not experience any issues when PayPal upgrades its SSL certificates.</p>', 'charitable' );
				$message .= __( '<p>Cheers<br />Eric & Wes', 'charitable' );

			} else {

				$result  = 'failed';
				$subject = __( 'Your PayPal test failed', 'charitable' );
				$message .= __( '<p>We received the Instant Payment Notification from PayPal but were not able to verify its authenticity.', 'charitable' );
				$message .= __( '<p>Our communicaton with PayPal failed with the following errors:</p>', 'charitable' );
				$message .= '<ul>';

				foreach ( $api_response->get_error_messages() as $error ) {
					$message .= sprintf( '<li>%s</li>', $error );
				}

				$message .= '</ul>';
				$message .= __( '<p>Unfortunately, this means that you are likely to face problems with your PayPal donations from October 2016 onwards. Your donors will still be able to proceed to PayPal and make their donation, but their donations will not be automatically marked as Paid in your WordPress dashboard.</p>', 'charitable' );
				$message .= __( '<h3>Short-term fix</h3>', 'charitable' );
				$message .= __( '<p><strong>Disable IPN verification</strong>. This makes your donation verification process less secure, but it will allow your donations to continue getting marked as Paid. To set this up, log into your WordPress dashboard and go to <em>Charitable</em> > <em>Settings</em> > <em>Payment Gateways</em>, select your PayPal settings and enable the "Disable IPN Verification" setting.', 'charitable' );
				$message .= __( '<h3>Long-term solution</h3>', 'charitable' );
				$message .= __( '<p><strong>Get in touch with your web host</strong>. Please refer them to <a href="https://www.paypal-knowledge.com/infocenter/index?page=content&widgetview=true&id=FAQ1766&viewlocale=en_US">the upgrade information provided by PayPal</a>. You should also provide them with the error message you received from PayPal above.</p>', 'charitable' );
				$message .= __( '<p>If your web host is unable to upgrade the software on your server, we strongly recommend switching to a hosting platform that provides a more modern, and secure service.</p>', 'charitable' );
				$message .= __( '<p>Cheers<br />Eric & Wes', 'charitable' );

			}

			/* Store the result. */
			update_option( 'charitable_paypal_sandbox_test', $result );

			/* Clear the token. */
			delete_option( 'charitable_paypal_sandbox_test_token' );

			/* Set a transient to display the success/failure of the test. */
			set_transient( 'charitable_paypal-sandbox-test_notice', 1 );

			/* Remove the transient about the PayPal upgrade. */
			delete_transient( 'charitable_release-143-paypal_notice' );

			/* Send an email to the site admin. */
			ob_start();

			charitable_template( 'emails/header.php', array( 'email' => null, 'headline' => $subject ) );

			echo $message;

			charitable_template( 'emails/footer.php' );

			$message = ob_get_clean();

			$headers  = "From: Charitable <support@wpcharitable.com>\r\n";
			$headers .= "Reply-To: support@wpcharitable.com\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";

			/* Send an email to the site administrator letting them know. */
			$sent = wp_mail(
				get_option( 'admin_email' ),
				$subject,
				$message,
				$headers
			);

		}
	}

endif; // End class_exists check
