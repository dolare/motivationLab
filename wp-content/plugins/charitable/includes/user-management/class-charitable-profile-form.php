<?php
/**
 * Class that manages the display and processing of the profile form.
 *
 * @package     Charitable/Classes/Charitable_Profile_Form
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Profile_Form' ) ) :

	/**
	 * Charitable_Profile_Form
	 *
	 * @since       1.0.0
	 */
	class Charitable_Profile_Form extends Charitable_Form {

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
		protected $nonce_action = 'charitable_user_profile';

		/**
		 * @var     string
		 */
		protected $nonce_name = '_charitable_user_profile_nonce';

		/**
		 * Action to be executed upon form submission.
		 *
		 * @var     string
		 * @access  protected
		 */
		protected $form_action = 'update_profile';

		/**
		 * The current user.
		 *
		 * @var     Charitable_User
		 * @access  protected
		 */
		protected $user;

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
		 * Return the current user's Charitable_User object.
		 *
		 * @return  Charitable_User
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_user() {
			if ( ! isset( $this->user ) ) {
				$this->user = new Charitable_User( wp_get_current_user() );
			}

			return $this->user;
		}

		/**
		 * Returns the value of a particular key.
		 *
		 * @param   string $key
		 * @param   string $default     Optional. The value that will be used if none is set.
		 * @return  mixed
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_user_value( $key, $default = '' ) {
			if ( isset( $_POST[ $key ] ) ) {
				return $_POST[ $key ];
			}

			$user = $this->get_user();
			$value = $default;

			if ( $user ) {
				switch ( $key ) {
					case 'user_description' :
						$value = $user->description;
						break;

					default :
						if ( $user->has_prop( $key ) ) {
							$value = $user->get( $key );
						}
				}
			}

			return apply_filters( 'charitable_campaign_submission_user_value', $value, $key, $user );
		}

		/**
		 * Return the core user fields.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_user_fields() {
			$user_fields = apply_filters( 'charitable_user_fields', array(
				'first_name' => array(
					'label'     => __( 'First name', 'charitable' ),
					'type'      => 'text',
					'priority'  => 4,
					'required'  => true,
					'value'     => $this->get_user_value( 'first_name' ),
				),
				'last_name' => array(
					'label'     => __( 'Last name', 'charitable' ),
					'type'      => 'text',
					'priority'  => 6,
					'required'  => true,
					'value'     => $this->get_user_value( 'last_name' ),
				),
				'user_email' => array(
					'label'     => __( 'Email', 'charitable' ),
					'type'      => 'email',
					'required'  => true,
					'priority'  => 8,
					'value'     => $this->get_user_value( 'user_email' ),
				),
				'organisation' => array(
					'label'     => __( 'Organization', 'charitable' ),
					'type'      => 'text',
					'priority'  => 10,
					'required'  => false,
					'value'     => $this->get_user_value( 'organisation' ),
				),
				'description' => array(
					'label'     => __( 'Bio', 'charitable' ),
					'type'      => 'textarea',
					'required'  => false,
					'priority'  => 12,
					'value'     => $this->get_user_value( 'description' ),
				),
			), $this );

			uasort( $user_fields, 'charitable_priority_sort' );

			return $user_fields;
		}

		/**
		 * Return the user's address fields.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_address_fields() {
			$address_fields = apply_filters( 'charitable_user_address_fields', array(
				'address' => array(
					'label'     => __( 'Address', 'charitable' ),
					'type'      => 'text',
					'priority'  => 22,
					'required'  => false,
					'value'     => $this->get_user_value( 'donor_address' ),
				),
				'address_2' => array(
					'label'     => __( 'Address 2', 'charitable' ),
					'type'      => 'text',
					'priority'  => 24,
					'required'  => false,
					'value'     => $this->get_user_value( 'donor_address_2' ),
				),
				'city' => array(
					'label'     => __( 'City', 'charitable' ),
					'type'      => 'text',
					'priority'  => 26,
					'required'  => false,
					'value'     => $this->get_user_value( 'donor_city' ),
				),
				'state' => array(
					'label'     => __( 'State', 'charitable' ),
					'type'      => 'text',
					'priority'  => 28,
					'required'  => false,
					'value'     => $this->get_user_value( 'donor_state' ),
				),
				'postcode' => array(
					'label'     => __( 'Postcode / ZIP code', 'charitable' ),
					'type'      => 'text',
					'priority'  => 30,
					'required'  => false,
					'value'     => $this->get_user_value( 'donor_postcode' ),
				),
				'country' => array(
					'label'     => __( 'Country', 'charitable' ),
					'type'      => 'select',
					'options'   => charitable_get_location_helper()->get_countries(),
					'priority'  => 32,
					'required'  => false,
					'value'     => $this->get_user_value( 'donor_country', charitable_get_option( 'country' ) ),
				),
				'phone' => array(
					'label'     => __( 'Phone', 'charitable' ),
					'type'      => 'text',
					'priority'  => 34,
					'required'  => false,
					'value'     => $this->get_user_value( 'donor_phone' ),
				),
			), $this );

			uasort( $address_fields, 'charitable_priority_sort' );

			return $address_fields;
		}

		/**
		 * Return the social fields.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_social_fields() {
			$social_fields = apply_filters( 'charitable_user_social_fields', array(
				'user_url' => array(
					'label'     => __( 'Your Website', 'charitable' ),
					'type'      => 'url',
					'priority'  => 42,
					'required'  => false,
					'value'     => $this->get_user_value( 'user_url' ),
				),
				'twitter' => array(
					'label'     => __( 'Twitter', 'charitable' ),
					'type'      => 'text',
					'priority'  => 44,
					'required'  => false,
					'value'     => $this->get_user_value( 'twitter' ),
				),
				'facebook' => array(
					'label'     => __( 'Facebook', 'charitable' ),
					'type'      => 'text',
					'priority'  => 46,
					'required'  => false,
					'value'     => $this->get_user_value( 'facebook' ),
				),
			), $this );

			uasort( $social_fields, 'charitable_priority_sort' );

			return $social_fields;
		}

		/**
		 * Profile fields to be displayed.
		 *
		 * @return  array[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_fields() {
			$fields = apply_filters( 'charitable_user_profile_fields', array(
				'user_fields' => array(
					'legend'    => __( 'Your Details', 'charitable' ),
					'type'      => 'fieldset',
					'fields'    => $this->get_user_fields(),
					'priority'  => 0,
				),
				'password_fields' => array(
					'legend'    => __( 'Change Your Password', 'charitable' ),
					'type'      => 'fieldset',
					'fields'    => $this->get_password_fields(),
					'priority'  => 10,
				),
				'address_fields' => array(
					'legend'    => __( 'Your Address', 'charitable' ),
					'type'      => 'fieldset',
					'fields'    => $this->get_address_fields(),
					'priority'  => 20,
				),
				'social_fields' => array(
					'legend'    => __( 'Your Social Profiles', 'charitable' ),
					'type'      => 'fieldset',
					'fields'    => $this->get_social_fields(),
					'priority'  => 40,
				),
			), $this );

			uasort( $fields, 'charitable_priority_sort' );

			return $fields;
		}

		/**
		 * The fields displayed on the password form.
		 *
		 * @return  array[]
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_password_fields() {
			$password_fields = apply_filters( 'charitable_user_profile_password_fields', array(
				'current_pass' => array(
					'priority'  => 2,
					'type'      => 'password',
					'label'     => __( 'Current Password (leave blank to leave unchanged)', 'charitable' ),
					'value'     => '',
					'required'  => false,
				),
				'user_pass' => array(
					'priority'  => 4,
					'type'      => 'password',
					'label'     => __( 'New Password (leave blank to leave unchanged)', 'charitable' ),
					'required'  => false,
				),
				'user_pass_repeat' => array(
					'priority'  => 6,
					'type'      => 'password',
					'label'     => __( 'New Password (again)', 'charitable' ),
					'required'  => false,
				),
			), $this );

			uasort( $password_fields, 'charitable_priority_sort' );

			return $password_fields;
		}

		/**
		 * Returns all fields as a merged array.
		 *
		 * @return  array[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_merged_fields() {
			$fields = array();

			foreach ( $this->get_fields() as $key => $section ) {

				if ( isset( $section['fields'] ) ) {
					$fields = array_merge( $fields, $section['fields'] );
				} else {
					$fields[ $key ] = $section;
				}
			}

			return $fields;
		}

		/**
		 * Update profile after form submission.
		 *
		 * @return  void
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function update_profile() {
			$form = new Charitable_Profile_Form();

			if ( ! $form->validate_nonce() || ! $form->validate_honeypot() ) {

				charitable_get_notices()->add_error( __( 'There was an error with processing your form submission. Please reload the page and try again.', 'charitable' ) );
				return;

			}

			$user = $form->get_user();

			/* Verify that the user is logged in. */
			if ( 0 == $user->ID ) {
				return;
			}

			$fields = $form->get_merged_fields();

			$submitted = apply_filters( 'charitable_profile_update_values', $_POST, $fields, $form );

			/* Remove the current_pass and user_pass_repeat fields, if set. */
			unset(
				$submitted['current_pass'],
				$submitted['user_pass_repeat']
			);

			$valid = $form->check_required_fields( $fields );

			if ( $valid && $form->is_changing_password() ) {

				$valid = $form->validate_password_change();

			}

			if ( $valid ) {

				$user->update_profile( $submitted, array_keys( $fields ) );

				do_action( 'charitable_profile_updated', $submitted, $fields, $form );

			}
		}

		/**
		 * Check whether the password is being changed.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.4.0
		 */
		public function is_changing_password() {
			if ( ! isset( $_POST['user_pass'] ) || empty( $_POST['user_pass'] ) ) {
				return false;
			}

			if ( ! isset( $_POST['current_pass'] ) || empty( $_POST['current_pass'] ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Changes a password if the current password is correct and the repeat matches the new password.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.4.0
		 */
		public function validate_password_change() {

			/* The current password must be correct. */
			if ( false == wp_check_password( $_POST['current_pass'], $this->get_user()->user_pass ) ) {

				charitable_get_notices()->add_error( 'Current password is incorrect.', 'charitable' );

				return false;

			}

			/* The new password must match the repeat (if set). */
			if ( isset( $_POST['user_pass_repeat'] ) && $_POST['user_pass_repeat'] != $_POST['user_pass'] ) {

				charitable_get_notices()->add_error( 'New passwords did not match.', 'charitable' );

				return false;

			}

			return true;
		}

		/**
		 * Add the charitable_user_profile_after_fields hook but fire off a deprecated notice.
		 *
		 * @deprecated 1.4.0
		 * @return  void
		 * @access  public
		 * @static
		 * @since   1.4.0
		 */
		public static function add_deprecated_charitable_user_profile_after_fields_hook( $form ) {
			if ( ! has_action( 'charitable_user_profile_after_fields' ) ) {
				return;
			}

			charitable_get_deprecated()->doing_it_wrong( __METHOD__, __( 'charitable_user_profile_after_fields hook has been removed. Use charitable_form_after_fields instead.', 'charitable' ), '1.4.0' );

			if ( 'Charitable_Profile_Form' == get_class( $form ) ) {
				do_action( 'charitable_user_profile_after_fields', $form );
			}
		}
	}

endif; // End class_exists check
