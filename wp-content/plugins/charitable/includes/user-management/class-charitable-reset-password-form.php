<?php
/**
 * Class that manages the display and processing of the reset password form.
 *
 * @package     Charitable/Classes/Charitable_Reset_Password_Form
 * @version     1.4.0
 * @author      Rafe Colton
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Reset_Password_Form' ) ) :

	/**
	 * Charitable_Reset_Password_Form
	 *
	 * @since       1.4.0
	 */
	class Charitable_Reset_Password_Form extends Charitable_Form {

		/**
		 * @var 	string
		 * @access 	protected
		 * @since 	1.4.0
		 */
		protected $nonce_action = 'charitable_reset_password';

		/**
		 * @var 	string
		 * @access 	protected
		 * @since 	1.4.0
		 */
		protected $nonce_name = '_charitable_reset_password_nonce';

		/**
		 * Form action.
		 *
		 * @var 	string
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected $form_action = 'reset_password';

		/**
		 * Reset key.
		 *
		 * @var 	string|null
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected $key;

		/**
		 * Form action.
		 *
		 * @var 	string|null
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected $login;

		/**
		 * Create class object.
		 *
		 * @param   array $args User-defined shortcode attributes.
		 * @access  public
		 * @since   1.4.0
		 */
		public function __construct( $args = array() ) {
			$this->id = uniqid();
			$this->parse_reset_key();
			$this->attach_hooks_and_filters();
		}

		/**
		 * Adds hidden fields to the start of the donation form.
		 *
		 * @param 	Charitable_Form $form
		 * @return 	void
		 * @access  public
		 * @since 	1.0.0
		 */
		public function add_hidden_fields( $form ) {
			$ret = parent::add_hidden_fields( $form );

			if ( false === $ret ) {
				return;
			}

			?>
			<input type="hidden" name="login" value="<?php echo esc_attr( $this->login ) ?>" autocomplete="off" />
			<input type="hidden" name="key" value="<?php echo esc_attr( $this->key ) ?>" />
			<?php
		}

		/**
		 * Reset password fields to be displayed.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_fields() {
			$fields = apply_filters( 'charitable_reset_password_fields', array(
				'pass1' => array(
					'label'    => __( 'New Password', 'charitable' ),
					'type'     => 'password',
					'required' => true,
					'priority' => 10,
					'attrs'    => array(
						'size'         => 20,
						'autocomplete' => 'off',
					),
				),
				'pass2' => array(
					'label'    => __( 'Repeat New Password', 'charitable' ),
					'type'     => 'password',
					'required' => true,
					'priority' => 11,
					'attrs'    => array(
						'size'         => 20,
						'autocomplete' => 'off',
					),
				),
			) );

			uasort( $fields, 'charitable_priority_sort' );

			return $fields;
		}

		/**
		 * Reset the password.
		 *
		 * @return  bool|WP_Error True: when finish. WP_Error on error
		 * @access  public
		 * @static
		 * @since   1.4.0
		 */
		public static function reset_password() {

			$form = new Charitable_Reset_Password_Form();

			if ( ! $form->validate_nonce() || ! $form->validate_honeypot() ) {
				charitable_get_notices()->add_error( __( 'There was an error with processing your form submission. Please reload the page and try again.', 'charitable' ) );
				return;
			}

			/* The key and login must be set. */
			if ( ! isset( $_POST['key'] ) || ! isset( $_POST['login'] ) ) {
				charitable_get_notices()->add_error( '<strong>ERROR:</strong> Invalid reset key.', 'charitable' );
				return;
			}

			$user = check_password_reset_key( $_POST['key'], $_POST['login'] );

			if ( is_wp_error( $user ) ) {
				charitable_get_notices()->add_errors_from_wp_error( $user );
				return;
			}

			/* One of the passwords was not set. */
			if ( ! isset( $_POST['pass1'] ) || ! isset( $_POST['pass2'] ) ) {
				charitable_get_notices()->add_error( '<strong>ERROR:</strong> You must enter both passwords.', 'charitable' );
				return;
			}

			/* The passwords do not match. */
			if ( $_POST['pass1'] != $_POST['pass2'] ) {
				charitable_get_notices()->add_error( __( '<strong>ERROR:</strong> The two passwords you entered don\'t match.', 'charitable' ) );
				return;
			}

			/* Parameter checks OK, reset password */
			reset_password( $user, $_POST['pass1'] );

			charitable_get_notices()->add_success( __( 'Your password was successfully changed.', 'charitable' ) );

			charitable_get_session()->add_notices();

			wp_safe_redirect( charitable_get_permalink( 'login_page' ) );

			exit();

		}

		/**
		 * Get the reset key and login from the cookie.
		 *
		 * @return  void
		 * @access  protected
		 * @since   1.4.0
		 */
		protected function parse_reset_key() {

			$this->key   = null;
			$this->login = null;

			if ( ! isset( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ) ) {
				return;
			}

			$cookie = $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ];

			if ( ! strpos( $cookie, ':' ) ) {
				return;
			}

			$cookie_parts = explode( ':', wp_unslash( $cookie ), 2 );

			list( $login, $key ) = array_map( 'sanitize_text_field', $cookie_parts );

			$user = check_password_reset_key( $key, $login );

			if ( is_wp_error( $user ) ) {

				charitable_get_notices()->add_errors_from_wp_error( $user );
				Charitable_User_Management::get_instance()->set_reset_cookie();
				return;

			}

			/* Reset key / login is correct, display reset password form with hidden key / login values */
			$this->key   = $key;
			$this->login = $login;

		}
	}

endif;
