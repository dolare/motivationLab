<?php
/**
 * A base class to be extended by specific form classes.
 *
 * @package		Charitable/Classes/Charitable_Form
 * @version 	1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Form' ) ) :

	/**
	 * Charitable_Form
	 *
	 * @abstract
	 * @since 		1.0.0
	 */
	abstract class Charitable_Form {

		/**
		 * Temporary, unique ID of this form.
		 *
		 * @var 	string
		 * @access  protected
		 */
		protected $id;

		/**
		 * @var 	string
		 * @access 	protected
		 */
		protected $nonce_action = 'charitable_form';

		/**
		 * @var 	string
		 * @access 	protected
		 */
		protected $nonce_name = '_charitable_form_nonce';

		/**
		 * Form action.
		 *
		 * @var 	string
		 * @access  protected
		 */
		protected $form_action;

		/**
		 * Errors with the form submission.
		 *
		 * @var 	array
		 * @access  protected
		 */
		protected $errors = array();

		/**
		 * Submitted values.
		 *
		 * @var 	array
		 * @access  protected
		 */
		protected $submitted;

		/**
		 * Set up callbacks for actions and filters.
		 *
		 * @return 	void
		 * @access  protected
		 * @since 	1.0.0
		 */
		protected function attach_hooks_and_filters() {
			add_action( 'charitable_form_before_fields', array( $this, 'render_error_notices' ) );
			add_action( 'charitable_form_before_fields', array( $this, 'add_hidden_fields' ) );
			add_action( 'charitable_form_field', array( $this, 'render_field' ), 10, 5 );
			add_filter( 'charitable_form_field_increment', array( $this, 'increment_index' ), 10, 2 );
		}

		/**
		 * Compares the ID of the form passed by the action and the current form object to ensure they're the same.
		 *
		 * @param 	string 		$id
		 * @return 	boolean
		 * @access  public
		 * @since 	1.0.0
		 */
		public function is_current_form( $id ) {
			return $id === $this->id;
		}

		/**
		 * Return the form action.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.3.1
		 */
		public function get_form_action() {
			return $this->form_action;
		}

		/**
		 * Return the form ID.
		 *
		 * @return 	string
		 * @access 	public
		 * @since 	1.0.0
		 */
		public function get_form_identifier() {
			return $this->id;
		}

		/**
		 * Whether the given field type can use the default field template.
		 *
		 * @param 	string 		$field_type
		 * @return 	boolean
		 * @access 	protected
		 * @since 	1.0.0
		 */
		protected function use_default_field_template( $field_type ) {
			$default_field_types = apply_filters( 'charitable_default_template_field_types', array(
				'text',
				'email',
				'password',
				'date',
			) );
			return in_array( $field_type, $default_field_types );
		}

		/**
		 * Display error notices at the start of the form, if there are any.
		 *
		 * @param 	Charitable_Form $form
		 * @return 	void
		 * @access  public
		 * @since 	1.0.0
		 */
		public function render_error_notices( $form ) {
			if ( ! $form->is_current_form( $this->id ) ) {
				return false;
			}

			$errors = charitable_get_notices()->get_errors();

			if ( ! empty( $errors ) ) {

				charitable_template( 'form-fields/errors.php', array(
					'errors' => $errors,
				) );

			}
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
			if ( ! $form->is_current_form( $this->id ) ) {
				return false;
			}

			$this->nonce_field();

			?>			
			<input type="hidden" name="charitable_action" value="<?php echo esc_attr( $this->form_action ) ?>" />
			<input type="hidden" name="charitable_form_id" value="<?php echo esc_attr( $this->id ) ?>" autocomplete="off" />
			<input type="text" name="<?php echo esc_attr( $this->id ) ?>" class="charitable-hidden" value="" autocomplete="off" />			
			<?php
		}

		/**
		 * Set how much the index should be incremented by.
		 *
		 * @param 	int $increment
		 * @param 	array $field
		 * @return  int
		 * @access  public
		 * @since   1.0.0
		 */
		public function increment_index( $increment, $field ) {
			if ( in_array( $field['type'], array(
				'hidden',
				'paragraph',
				'fieldset',
			) )
				|| ( isset( $field['fullwidth'] ) && $field['fullwidth'] ) ) {
				$increment = 0;
			}

			return $increment;
		}

		/**
		 * Render a form field.
		 *
		 * @param 	array 		$field
		 * @param 	string 		$key
		 * @param 	Charitable_Form 	$form
		 * @param 	int 		$index
		 * @return 	boolean 	False if the field was not rendered. True otherwise.
		 * @access  public
		 * @since 	1.0.0
		 */
		public function render_field( $field, $key, $form, $index = 0, $namespace = null ) {
			if ( ! $form->is_current_form( $this->id ) ) {
				return false;
			}

			if ( ! isset( $field['type'] ) ) {
				return false;
			}

			$input_name   = is_null( $namespace ) ? $key : $namespace . '[' . $key . ']';
			$field['key'] = apply_filters( 'charitable_form_field_key', $input_name, $key, $namespace, $form, $index );

			/* Set default attributes array. */
			if ( ! isset( $field['attrs'] ) ) {
				$field['attrs'] = array();
			}

			/* Allows extensions/themes to plug in their own template objects here. */
			$template = apply_filters( 'charitable_form_field_template', false, $field, $form, $index );

			/* Fall back to default Charitable_Template if no template returned or if template was not object of 'Charitable_Template' class. */
			if ( ! $this->is_valid_template( $template ) ) {
				$template = new Charitable_Template( $this->get_template_name( $field ), false );
			}

			if ( ! $template->template_file_exists() ) {
				return false;
			}

			$template->set_view_args( array(
				'form' 		=> $this,
				'field' 	=> $field,
				'classes'	=> $this->get_field_classes( $field, $index ),
			) );

			$template->render();

			return true;
		}

		/**
		 * Return the template name used for this field.
		 *
		 * @param 	array 		$field
		 * @return 	string
		 * @access  public
		 * @since 	1.0.0
		 */
		public function get_template_name( $field ) {
			if ( $this->use_default_field_template( $field['type'] ) ) {
				$template_name = 'form-fields/default.php';
			} else {
				$template_name = 'form-fields/' . $field['type'] . '.php';
			}

			return apply_filters( 'charitable_form_field_template_name', $template_name );
		}

		/**
		 * Return classes that will be applied to the field.
		 *
		 * @param 	array 		$field
		 * @param 	int 		$index
		 * @return 	string
		 * @access  public
		 * @since 	1.0.0
		 */
		public function get_field_classes( $field, $index = 0 ) {
			if ( 'hidden' == $field['type'] ) {
				return;
			}

			$classes = $this->get_field_type_classes( $field['type'] );

			if ( isset( $field['class'] ) ) {
				$classes[] = $field['class'];
			}

			if ( isset( $field['required'] ) && $field['required'] ) {
				$classes[] = 'required-field';
			}

			if ( isset( $field['fullwidth'] ) && $field['fullwidth'] ) {
				$classes[] = 'fullwidth';
			} elseif ( $index > 0 ) {
				$classes[] = $index % 2 ? 'odd' : 'even';
			}

			$classes = apply_filters( 'charitable_form_field_classes', $classes, $field, $index );

			return implode( ' ', $classes );
		}

		/**
		 * Return array of classes based on the field type.
		 *
		 * @param 	string
		 * @return  string[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_field_type_classes( $type ) {
			$classes = array();

			switch ( $type ) {

				case 'paragraph' :
					$classes[] = 'charitable-form-content';
					break;

				case 'fieldset' :
					$classes[] = 'charitable-fieldset';
					break;

				default :
					$classes[] = 'charitable-form-field';
					$classes[] = 'charitable-form-field-' . $type;
			}

			return $classes;
		}

		/**
		 * Output the nonce.
		 *
		 * @return 	void
		 * @access 	public
		 * @since 	1.0.0
		 */
		public function nonce_field() {
			wp_nonce_field( $this->nonce_action, $this->nonce_name );
		}

		/**
		 * Validate nonce data passed by the submitted form.
		 *
		 * @return 	boolean
		 * @access 	public
		 * @since 	1.0.0
		 */
		public function validate_nonce() {
			$submitted = $this->get_submitted_values();
			$validated = isset( $submitted[ $this->nonce_name ] ) && wp_verify_nonce( $submitted[ $this->nonce_name ], $this->nonce_action );

			if ( ! $validated ) {
				charitable_get_notices()->add_error( __( 'Unable to submit form. Please try again.', 'charitable' ) );
			}

			return $validated;
		}

		/**
		 * Make sure that the honeypot field is empty.
		 *
		 * @return 	boolean
		 * @access 	public
		 * @since 	1.4.3
		 */
		public function validate_honeypot() {
			$submitted = $this->get_submitted_values();

			if ( ! isset( $submitted['charitable_form_id'] ) ) {
				return true;
			}

			$form_id = $submitted['charitable_form_id'];

			return array_key_exists( $form_id, $submitted ) && 0 === strlen( $submitted[ $form_id ] );
		}

		/**
		 * Callback method used to filter out non-required fields.
		 *
		 * @return 	array
		 * @access  public
		 * @since 	1.0.0
		 */
		public function filter_required_fields( $field ) {
			return isset( $field['required'] ) && true == $field['required'];
		}

		/**
		 * Filters array returning just the required fields.
		 *
		 * @return 	array[]
		 * @access  public
		 * @since 	1.0.0
		 */
		public function get_required_fields( $fields ) {
			$required_fields = array_filter( $fields, array( $this, 'filter_required_fields' ) );

			return $required_fields;
		}

		/**
		 * Check the passed fields to ensure that all required fields have been submitted.
		 *
		 * @param 	array $fields
		 * @param 	array $submitted
		 * @return 	boolean
		 * @access  public
		 * @since 	1.0.0
		 */
		public function check_required_fields( $fields, $submitted = array() ) {

			if ( empty( $submitted ) ) {
				$submitted = $this->get_submitted_values();
			}

			$ret = true;

			$missing = array();

			foreach ( $this->get_required_fields( $fields ) as $key => $field ) {

				/* We already have a value for this field. */
				if ( ! empty( $field['value'] ) ) {
					continue;
				}

				$exists = isset( $submitted[ $key ] );

				/* Verify that a value was provided. */
				if ( $exists ) {
					$value  = $submitted[ $key ];
					$exists = ! empty( $value ) || ( is_string( $value ) && strlen( $value ) );
				}

				/* If a value was not provided, check if it's in the $_FILES array. */
				if ( ! $exists ) {
					$exists = ( 'picture' == $field['type'] && isset( $_FILES[ $key ] ) && ! empty( $_FILES[ $key ]['name'] ) );
				}

				$exists = apply_filters( 'charitable_required_field_exists', $exists, $key, $field, $submitted, $this );

				if ( ! $exists ) {

					$label = isset( $field['label'] ) ? $field['label'] : $key;

					$missing[] = $label;
				}
			}

			$missing = apply_filters( 'charitable_form_missing_fields', $missing, $this, $fields, $submitted );

			if ( count( $missing ) ) {

				$missing_fields = implode( '</li><li>', $missing );

				charitable_get_notices()->add_error(
					sprintf( '<p>%s</p><ul class="error-list"><li>%s</li></ul>',
						__( 'There were problems with your form submission. The following required fields were not filled out:', 'charitable' ),
						$missing_fields
					)
				);

				$ret = false;
			}

			return $ret;
		}

		/**
		 * Organize fields by data type, also filtering out unused parameters (we just need the key and the type).
		 *
		 * @param 	string 		$key
		 * @param 	array       $field
		 * @param 	array 		$ret
		 * @return  array[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function sort_field_by_data_type( $key, $field, $ret ) {
			/* Filter out paragraphs and fields without a type. */
			if ( ! isset( $field['type'] ) || 'paragraph' == $field['type'] ) {
				return $ret;
			}

			/* Get the data type. Default to meta if no type is set. */
			if ( isset( $field['data_type'] ) ) {
				$ret[ $field['data_type'] ][ $key ] = $field['type'];
			} else {
				$ret[ $key ] = $field['type'];
			}

			return $ret;
		}

		/**
		 * Returns the submitted values.
		 *
		 * Use this method instead of accessing the raw $_POST array to take
		 * advantage of the filter on the values.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_submitted_values() {
			if ( ! isset( $this->submitted ) ) {
				$this->submitted = apply_filters( 'charitable_form_submitted_values', $_POST, $this );
			}

			return $this->submitted;
		}

		/**
		 * Returns the submitted value for a particular field.
		 *
		 * @param 	string $key
		 * @return  mixed Submitted value if set. NULL if value was not set.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_submitted_value( $key ) {
			$submitted = $this->get_submitted_values();
			return isset( $submitted[ $key ] ) ? $submitted[ $key ] : null;
		}

		/**
		 * Uploads a file and attaches it to the given post.
		 *
		 * @param 	string $file_key
		 * @param 	int $post_id
		 * @param 	array $post_data
		 * @param 	array $overrides
		 * @return 	int|WP_Error 	ID of the attachment or a WP_Error object on failure.
		 * @access  public
		 * @since   1.0.0
		 */
		public function upload_post_attachment( $file_key, $post_id, $post_data = array(), $overrides = array() ) {

			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			$overrides = $this->get_file_overrides( $file_key, $overrides );

			return media_handle_upload( $file_key, $post_id, $post_data, $overrides );
		}

		/**
		 * Upload a file.
		 *
		 * @param 	string $file_key
		 * @param 	array  $overrides
		 * @return  array|WP_Error On success, returns an associative array of file attributes. 
		 *                         On failure, returns $overrides['upload_error_handler'](&$file, $message ) 
		 *                         or array( 'error'=>$message ).
		 * @access  public
		 * @since   1.0.0
		 */
		public function upload_file( $file_key, $overrides = array() ) {

			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			$overrides = $this->get_file_overrides( $file_key, $overrides );
			$file      = wp_handle_upload( $_FILES[ $file_key ], $overrides );

			if ( isset( $file['error'] ) ) {
				return new WP_Error( 'upload_error', $file['error'] );
			}

			return $file;
		}

		/**
		 * Return overrides array for use with upload_file() and upload_post_attachment() methods.
		 *
		 * @param 	string $file_key
		 * @param 	array $overrides
		 * @param
		 * @return  array
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_file_overrides( $file_key, $overrides = array() ) {

			$allowed_mimes = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif'          => 'image/gif',
				'png'          => 'image/png',
				'bmp'          => 'image/bmp',
				'tif|tiff'     => 'image/tiff',
				'ico'          => 'image/x-icon',
			);

			$defaults = array(
				'test_form' => false,
				'mimes'     => apply_filters( 'charitable_file_' . $file_key . '_allowed_mimes', $allowed_mimes ),
			);

			$overrides = wp_parse_args( $overrides, $defaults );

			return $overrides;

		}

		/**
		 * Checks whether a template is valid.
		 *
		 * @return  boolean
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function is_valid_template( $template ) {
			return is_object( $template ) && is_a( $template, 'Charitable_Template' );
		}
	}

endif; // End class_exists check
