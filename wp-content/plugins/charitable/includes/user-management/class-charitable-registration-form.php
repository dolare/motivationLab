<?php
/**
 * Class that manages the display and processing of the registration form.
 *
 * @package     Charitable/Classes/Charitable_Registration_Form
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Registration_Form' ) ) :

	/**
	 * Charitable_Registration_Form
	 *
	 * @since       1.0.0
	 */
	class Charitable_Registration_Form extends Charitable_Form {

		/**
		 * Shortcode parameters.
		 *
		 * @var     array
		 * @access  protected
		 */
		protected $shortcode_args;

		/**
		 * @var     string
		 */
		protected $nonce_action = 'charitable_user_registration';

		/**
		 * @var     string
		 */
		protected $nonce_name = '_charitable_user_registration_nonce';

		/**
		 * Action to be executed upon form submission.
		 *
		 * @var     string
		 * @access  protected
		 */
		protected $form_action = 'save_registration';

		/**
		 * The current donor.
		 *
		 * @var     Charitable_Donor
		 * @access  protected
		 */
		protected $donor;

		/**
		 * Create class object.
		 *
		 * @param   array       $args       User-defined shortcode attributes.
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct( $args = array() ) {
			$this->id = uniqid();
			$this->shortcode_args = $args;
			$this->attach_hooks_and_filters();
		}

		/**
		 * Return the arguments passed to the shortcode.
		 *
		 * @return  mixed[]
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_shortcode_args() {
			return $this->shortcode_args;
		}

		/**
		 * Profile fields to be displayed.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_fields() {
			$fields = apply_filters( 'charitable_user_registration_fields', array(
				'user_email' => array(
					'label'     => __( 'Email', 'charitable' ),
					'type'      => 'email',
					'required'  => true,
					'priority'  => 4,
					'value'     => isset( $_POST['user_email'] ) ? $_POST['user_email'] : '',
				),
				'user_login' => array(
					'label'     => __( 'Username', 'charitable' ),
					'type'      => 'text',
					'priority'  => 6,
					'required'  => true,
					'value'     => isset( $_POST['user_login'] ) ? $_POST['user_login'] : '',
				),
				'user_pass' => array(
					'label'     => __( 'Password', 'charitable' ),
					'type'      => 'password',
					'priority'  => 8,
					'required'  => true,
					'value'     => isset( $_POST['user_pass'] ) ? $_POST['user_pass'] : '',
				),
			) );

			uasort( $fields, 'charitable_priority_sort' );

			return $fields;
		}

		/**
		 * Adds hidden fields to the start of the registration
		 *
		 * @param 	Charitable_Form 	$form
		 * @return 	void
		 * @access  public
		 * @since 	1.0.0
		 */
		public function add_hidden_fields( $form ) {
			$ret = parent::add_hidden_fields( $form );

			if ( false === $ret ) {
				return;
			}

			$redirect = false;

			if ( isset( $_GET['redirect_to'] ) && strlen( $_GET['redirect_to'] ) ) {

				$redirect = $_GET['redirect_to'];

			} elseif ( isset( $this->shortcode_args['redirect'] ) && strlen( $this->shortcode_args['redirect'] ) ) {

				$redirect = $this->shortcode_args['redirect'];

			}

			if ( ! $redirect ) {
				return;
			}

			?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect ) ?>" />
			<?php
		}

		/**
		 * Update registration after form submission.
		 *
		 * @return  void
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function save_registration() {
			$form = new Charitable_Registration_Form();

			if ( ! $form->validate_nonce() || ! $form->validate_honeypot() ) {

				charitable_get_notices()->add_error( __( 'There was an error with processing your form submission. Please reload the page and try again.', 'charitable' ) );
				return;

			}

			$fields = $form->get_fields();
			$valid  = $form->check_required_fields( $fields );

			if ( ! $valid ) {
				return;
			}

			$submitted = apply_filters( 'charitable_registration_values', $_POST, $fields, $form );

			if ( ! isset( $submitted['user_email'] ) || ! is_email( $submitted['user_email'] ) ) {

				charitable_get_notices()->add_error( sprintf(
					__( '%s is not a valid email address.', 'charitable' ),
					$submitted['user_email']
				) );

				return false;
			}

			$user    = new Charitable_User();
			$user_id = $user->update_profile( $submitted, array_keys( $fields ) );

			/**
			 * If the user was successfully created, redirect to the login redirect URL.
			 * If there was a problem, this simply falls through and keeps the user on the
			 * registration page.
			 */
			if ( $user_id ) {
				wp_safe_redirect( charitable_get_login_redirect_url() );
				exit();
			}
		}

		/**
		 * Return the link to the login page, or false if we are not going to display it.
		 *
		 * @return  false|string
		 * @access  public
		 * @since   1.4.2
		 */
		public function get_login_link() {

			if ( false == $this->shortcode_args['login_link_text'] || 'false' == $this->shortcode_args['login_link_text'] ) {
				return false;
			}

			$login_link = charitable_get_permalink( 'login_page' );

			if ( charitable_get_permalink( 'registration_page' ) === $login_link ) {
				return false;
			}

			if ( isset( $_GET['redirect_to'] ) ) {
				$login_link = add_query_arg( 'redirect_to', $_GET['redirect_to'], $login_link );
			}

			return sprintf( '<a href="%1$s">%2$s</a>',
				esc_url( $login_link ),
				$this->shortcode_args['login_link_text']
			);
		}
	}

endif; // End class_exists check
