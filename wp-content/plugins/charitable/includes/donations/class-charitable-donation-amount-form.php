 <?php
/**
 * Donation amount form model class.
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Donation_Amount_Form
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donation_Amount_Form' ) ) :

	/**
	 * Charitable_Donation_Amount_Form
	 *
	 * @since       1.0.0
	 */
	class Charitable_Donation_Amount_Form extends Charitable_Donation_Form implements Charitable_Donation_Form_Interface {

		/**
		 * @var     Charitable_Campaign
		 */
		protected $campaign;

		/**
		 * @var     array
		 */
		protected $form_fields;

		/**
		 * @var     string
		 */
		protected $nonce_action = 'charitable_donation_amount';

		/**
		 * @var     string
		 */
		protected $nonce_name = '_charitable_donation_amount_nonce';

		/**
		 * Action to be executed upon form submission.
		 *
		 * @var     string
		 * @access  protected
		 */
		protected $form_action = 'make_donation_streamlined';

		/**
		 * Set up callbacks for actions and filters.
		 *
		 * @return  void
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function attach_hooks_and_filters() {
			parent::attach_hooks_and_filters();

			remove_filter( 'charitable_donation_form_gateway_fields', array( $this, 'add_credit_card_fields' ), 10, 2 );
			remove_action( 'charitable_donation_form_after_user_fields', array( $this, 'add_password_field' ) );

			do_action( 'charitable_donation_amount_form_start', $this );
		}

		/**
		 * Return the donation form fields.
		 *
		 * @return  array[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_fields() {
			return $this->get_donation_fields();
		}

		/**
		 * Validate the form submission.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.4.4
		 */
		public function validate_submission() {

			/* If we have already validated the submission, return the value. */
			if ( $this->validated ) {
				return $this->valid;
			}

			$this->validated = true;

			$this->valid = $this->validate_security_check()
				&& $this->check_required_fields( $this->get_merged_fields() )
				&& $this->validate_amount();

			$this->valid = apply_filters( 'charitable_validate_donation_amount_form_submission', $this->valid, $this );

			return $this->valid;

		}

		/**
		 * Return the donation values.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donation_values() {
			$submitted = $this->get_submitted_values();

			$values = array(
				'campaign_id' => $submitted['campaign_id'],
				'amount'      => self::get_donation_amount( $submitted ),
			);

			return apply_filters( 'charitable_donation_amount_form_submission_values', $values, $submitted, $this );
		}

		/**
		 * Redirect to payment form after submission.
		 *
		 * @param   int     $campaign_id
		 * @param   int     $amount
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function redirect_after_submission( $campaign_id, $amount ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return;
			}

			$redirect_url = charitable_get_permalink( 'campaign_donation_page', array( 'campaign_id' => $campaign_id ) );

			if ( 'same_page' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
				$redirect_url .= '#charitable-donation-form';
			}

			$redirect_url = apply_filters( 'charitable_donation_amount_form_redirect', $redirect_url, $campaign_id, $amount );

			wp_redirect( esc_url_raw( $redirect_url ) );

			die();
		}

		/**
		 * Render the donation form.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function render() {

			/* Load the script if it hasn't been loaded yet. */
			if ( ! wp_script_is( 'charitable-script', 'enqueued' ) ) {
				Charitable_Public::get_instance()->enqueue_donation_form_scripts();
			}

			charitable_template( 'donation-form/form-donation.php', array(
				'campaign' => $this->get_campaign(),
				'form'     => $this,
				'form_id'  => 'charitable-donation-amount-form',
			) );
		}
	}

endif; // End class_exists check
