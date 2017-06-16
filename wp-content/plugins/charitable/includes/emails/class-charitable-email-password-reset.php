<?php
/**
 * Class that models the Password Reset email.
 *
 * @version     1.4.0
 * @package     Charitable/Classes/Charitable_Email_Password_Reset
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Email_Password_Reset' ) ) :

	/**
	 * Password Reset Email
	 *
	 * @since 	1.4.0
	 */
	class Charitable_Email_Password_Reset extends Charitable_Email {

		/**
		 * @var     string
		 */
		const ID = 'password_reset';

		/**
		 * Whether the email allows you to define the email recipients.
		 *
		 * @var     boolean
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected $has_recipient_field = false;

		/**
		 * The Password Reset email is required.
		 *
		 * @var     boolean
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected $required = true;

		/**
		 * The user data.
		 *
		 * @var     WP_User
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected $user;

		/**
		 * The reset link.
		 *
		 * @var 	string|WP_Error
		 * @access 	protected
		 * @since 	1.4.0
		 */
		protected $reset_link;

		/**
		 * Instantiate the email class, defining its key values.
		 *
		 * @param   mixed[] $objects
		 * @access  public
		 * @since 	1.4.0
		 */
		public function __construct( $objects = array() ) {
			parent::__construct( $objects );

			$this->name = apply_filters( 'charitable_email_password_reset_name', __( 'User: Password Reset', 'charitable' ) );
			$this->user = isset( $objects['user'] ) ? $objects['user'] : false;

		}

		/**
		 * Returns the current email's ID.
		 *
		 * @return  string
		 * @access  public
		 * @static
		 * @since 	1.4.0
		 */
		public static function get_email_id() {
			return self::ID;
		}

		/**
		 * Returns all fields that can be displayed using the [charitable_email] shortcode.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_fields() {
			return apply_filters( 'charitable_email_content_fields', array(
				'site_name' => array(
					'description' => __( 'Your website title', 'charitable' ),
					'callback'    => array( $this, 'get_site_name' ),
				),
				'site_url' => array(
					'description' => __( 'Your website URL', 'charitable' ),
					'callback'    => home_url(),
				),
				'user_login' => array(
					'description' => __( 'The user login', 'charitable' ),
					'callback'    => array( $this, 'get_user_login' ),
				),
				'reset_link' => array(
					'description' => __( 'The link the user needs to click to reset their password', 'charitable' ),
					'callback'    => array( $this, 'get_reset_link' ),
				),
			), $this );
		}

		/**
		 * Return the reset link.
		 *
		 * @return  string|WP_Error|false If the reset key could not be generated, an error is returned.
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_reset_link() {
			if ( ! isset( $this->reset_link ) ) {

				if ( ! is_a( $this->user, 'WP_User' ) ) {

					charitable_get_deprecated()->doing_it_wrong(
						__METHOD__,
						__( 'Password reset link cannot be generated without a WP_User object.', 'charitable' ),
						'1.4.0'
					);

					return '';

				}

				$base_url = charitable_get_permalink( 'reset_password_page' );
				$key 	  = get_password_reset_key( $this->user );

				if ( is_wp_error( $key ) ) {
					return $key;
				}

				$this->reset_link = esc_url_raw( add_query_arg( array(
					'key'   => $key,
					'login' => rawurlencode( $this->user->user_login ),
				), $base_url ) );

			}

			return $this->reset_link;
		}

		/**
		 * Return the reset link.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_user_login() {
			if ( ! isset( $this->user ) || ! is_a( $this->user, 'WP_User' ) ) {
				return '';
			}

			return $this->user->user_login;
		}

		/**
		* Return the recipient for the email.
		*
		* @return  string
		* @access  public
		* @since   1.0.0
		*/
		public function get_recipient() {
			if ( ! isset( $this->user ) || ! is_a( $this->user, 'WP_User' ) ) {
				return '';
			}

			return $this->user->user_email;
		}

		/**
		 * Return the default subject line for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected function get_default_subject() {
			return __( 'Password Reset for [charitable_email show=site_name]', 'charitable' );
		}

		/**
		 * Return the default headline for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected function get_default_headline() {
			return apply_filters( 'charitable_email_password_reset_default_headline', __( 'Reset your password', 'charitable' ), $this );
		}

		/**
		 * Return the default body for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since 	1.4.0
		 */
		protected function get_default_body() {
			ob_start();
?>
<p><?php _e( 'Someone requested that the password be reset for the following account:', 'charitable' ) ?></p>
<p><?php _e( 'Username: [charitable_email show=user_login]', 'charitable' ) ?></p>
<p><?php _e( 'If this was a mistake, just ignore this email and nothing will happen.', 'charitable' ) ?></p>
<p><?php _e( 'To reset your password, visit the following address:', 'charitable' ) ?></p>
<p><a href="[charitable_email show=reset_link]">[charitable_email show=reset_link]</a></p>
<?php
		$body = ob_get_clean();

		return apply_filters( 'charitable_email_password_reset_default_body', $body, $this );
		}
	}

endif; // End class_exists check
