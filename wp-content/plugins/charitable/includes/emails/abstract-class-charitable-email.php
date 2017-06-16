<?php
/**
 * Email model
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Email
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Email' ) ) :

	/**
	 * Charitable_Email
	 *
	 * @abstract
	 * @since       1.0.0
	 */
	abstract class Charitable_Email implements Charitable_Email_Interface {

		/**
		 * @var     string  The email's unique identifier.
		 */
		const ID = '';

		/**
		 * @var     string  Descriptive name of the email.
		 * @access  protected
		 * @since   1.0.0
		 */
		protected $name;

		/**
		 * @var     string[] Array of supported object types (campaigns, donations, donors, etc).
		 * @access  protected
		 * @since   1.0.0
		 */
		protected $object_types = array();

		/**
		 * @var     boolean Whether the email allows you to define the email recipients.
		 * @access  protected
		 * @since   1.0.0
		 */
		protected $has_recipient_field = false;

		/**
		 * @var     boolean Whether the email is required.
		 * @access  protected
		 * @since   1.4.0
		 */
		protected $required = false;

		/**
		 * @var     Charitable_Donation
		 */
		protected $donation;

		/**
		 * @var     Charitable_Campaign
		 */
		protected $campaign;

		/**
		 * @var     string
		 * @access  protected
		 */
		protected $recipients;

		/**
		 * @var     string
		 * @access  protected
		 */
		protected $headers;

		/**
		 * Create a class instance.
		 *
		 * @param   mixed[]  $objects
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct( $objects = array() ) {
			$this->donation = isset( $objects['donation'] ) ? $objects['donation'] : null;
			$this->campaign = isset( $objects['campaign'] ) ? $objects['campaign'] : null;

			add_filter( 'charitable_email_content_fields',         array( $this, 'add_donation_content_fields' ), 10, 2 );
			add_filter( 'charitable_email_preview_content_fields', array( $this, 'add_preview_donation_content_fields' ), 10, 2 );
			add_filter( 'charitable_email_content_fields',         array( $this, 'add_campaign_content_fields' ), 10, 2 );
			add_filter( 'charitable_email_preview_content_fields', array( $this, 'add_preview_campaign_content_fields' ), 10, 2 );
		}

		/**
		 * Return the email name.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_name() {
			return $this->name;
		}

		/**
		 * Return whether the email is required.
		 *
		 * If an email is required, it cannot be disabled/enabled, but it can still be edited.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.4.0
		 */
		public function is_required() {
			return $this->required;
		}

		/**
		 * Return the types of objects.
		 *
		 * @return  string[]
		 * @access  public
		 * @since   1.3.0
		 */
		public function get_object_types() {
			return $this->object_types;
		}

		/**
		 * Return the donation object.
		 *
		 * @return  null|Charitable_Donation
		 * @access  public
		 * @since   1.3.0
		 */
		public function get_donation() {
			return $this->donation;
		}

		/**
		 * Return the campaign object.
		 *
		 * @return  null|Charitable_Campaign
		 * @access  public
		 * @since   1.3.0
		 */
		public function get_campaign() {
			return $this->campaign;
		}

		/**
		 * Get from name for email.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_from_name() {
			return wp_specialchars_decode( charitable_get_option( 'email_from_name', get_option( 'blogname' ) ) );
		}

		/**
		 * Get from address for email.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_from_address() {
			return charitable_get_option( 'email_from_email', get_option( 'admin_email' ) );
		}

		/**
		 * Return the email recipients.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_recipient() {
			return $this->get_option( 'recipient', $this->get_default_recipient() );
		}

		/**
		 * Return the email subject line.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_subject() {
			return $this->get_option( 'subject', $this->get_default_subject() );
		}

		/**
		 * Get the email content type
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_content_type() {
			return apply_filters( 'charitable_email_content_type', 'text/html', $this );
		}

		/**
		 * Get the email headers.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_headers() {
			if ( ! isset( $this->headers ) ) {
				$this->headers  = "From: {$this->get_from_name()} <{$this->get_from_address()}>\r\n";
				$this->headers .= "Reply-To: {$this->get_from_address()}\r\n";
				$this->headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";
			}

			return apply_filters( 'charitable_email_headers', $this->headers, $this );
		}

		/**
		 * Checks whether we are currently previewing the email.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.3.5
		 */
		public function is_preview() {
			return isset( $_GET['charitable_action'] ) && 'preview_email' == $_GET['charitable_action'];
		}

		/**
		 * Return the value of a specific field to be displayed in the email.
		 *
		 * This is used by Charitable_Emails::email_shortcode() to obtain the value of the
		 * particular field that was referenced in the shortcode. The second argument is
		 * an optional array of arguments.
		 *
		 * @param   string $field
		 * @param   array $args Optional. May contain additional arguments.
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_value( $field, $args = array() ) {
			$fields = $this->get_fields();

			if ( $this->is_preview() ) {
				return $this->get_preview_field_content( $field );
			}

			if ( isset( $fields[ $field ] ) ) {
				add_filter( 'charitable_email_content_field_value_' . $field, $fields[ $field ]['callback'], 10, 3 );
			}

			return apply_filters( 'charitable_email_content_field_value_' . $field, '', $args, $this );
		}

		/**
		 * Returns all fields that can be displayed using the [charitable_email] shortcode.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_fields() {
			return apply_filters( 'charitable_email_content_fields', array(
				'site_name' => array(
					'description' => __( 'Your website title', 'charitable' ),
					'callback'    => array( $this, 'get_site_name' ),
				),
				'site_url'  => array(
					'description'   => __( 'Your website URL', 'charitable' ),
					'callback'      => 'home_url',
				),
			), $this );
		}

		/**
		 * Return the site/blog name.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_site_name() {
			return get_option( 'blogname' );
		}

		/**
		 * Register email settings.
		 *
		 * @param   array   $settings
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function email_settings( $settings ) {
			$email_settings = apply_filters( 'charitable_settings_fields_emails_email_' . $this->get_email_id(), array(
				'section_email' => array(
					'type'      => 'heading',
					'title'     => $this->get_name(),
					'priority'  => 2,
				),
				'subject' => array(
					'type'      => 'text',
					'title'     => __( 'Email Subject Line', 'charitable' ),
					'help'      => __( 'The email subject line when it is delivered to recipients.', 'charitable' ),
					'priority'  => 6,
					'class'     => 'wide',
					'default'   => $this->get_default_subject(),
				),
				'headline' => array(
					'type'      => 'text',
					'title'     => __( 'Email Headline', 'charitable' ),
					'help'      => __( 'The headline displayed at the top of the email.', 'charitable' ),
					'priority'  => 10,
					'class'     => 'wide',
					'default'   => $this->get_default_headline(),
				),
				'body' => array(
					'type'      => 'editor',
					'title'     => __( 'Email Body', 'charitable' ),
					'help'      => sprintf( '%s <div class="charitable-shortcode-options">%s</div>',
						__( 'The content of the email that will be delivered to recipients. HTML is accepted.', 'charitable' ),
						$this->get_shortcode_options()
					),
					'priority'  => 14,
					'default'   => $this->get_default_body(),
				),
				'preview' => array(
					'type'      => 'content',
					'title'     => __( 'Preview', 'charitable' ),
					'content'   => sprintf( '<a href="%s" target="_blank" class="button">%s</a>',
						esc_url(
							add_query_arg( array(
								'charitable_action' => 'preview_email',
								'email_id' => $this->get_email_id(),
							), home_url() )
						),
						__( 'Preview email', 'charitable' )
					),
					'priority'  => 18,
					'save'      => false,
				),
			) );

			return wp_parse_args( $settings, $email_settings );
		}

		/**
		 * Add recipient field
		 *
		 * @param   array   $settings
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_recipients_field( $settings ) {
			if ( ! $this->has_recipient_field ) {
				return $settings;
			}

			$settings['recipient'] = array(
				'type'      => 'text',
				'title'     => __( 'Recipients', 'charitable' ),
				'help'      => __( 'A comma-separated list of email address that will receive this email.', 'charitable' ),
				'priority'  => 4,
				'class'     => 'wide',
				'default'   => $this->get_default_recipient(),
			);

			return $settings;
		}

		/**
		 * Add donation content fields.
		 *
		 * @return  array[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_donation_content_fields( $fields, Charitable_Email $email ) {
			if ( ! $this->is_current_email( $email ) ) {
				return $fields;
			}

			if ( ! in_array( 'donation', $this->object_types ) ) {
				return $fields;
			}

			$fields['donor'] = array(
				'description'   => __( 'The full name of the donor', 'charitable' ),
				'callback'      => array( $this, 'get_donor_full_name' ),
			);

			$fields['donor_first_name'] = array(
				'description'   => __( 'The first name of the donor', 'charitable' ),
				'callback'      => array( $this, 'get_donor_first_name' ),
			);

			$fields['donor_email'] = array(
				'description'   => __( 'The email address of the donor', 'charitable' ),
				'callback'      => array( $this, 'get_donor_email' ),
			);

			$fields['donor_address'] = array(
				'description'   => __( 'The donor\'s address', 'charitable' ),
				'callback'      => array( $this, 'get_donor_address' ),
			);

			$fields['donor_phone'] = array(
				'description'   => __( 'The donor\'s phone number', 'charitable' ),
				'callback'      => array( $this, 'get_donor_phone' ),
			);

			$fields['donation_id'] = array(
				'description'   => __( 'The donation ID', 'charitable' ),
				'callback'      => array( $this, 'get_donation_id' ),
			);

			$fields['donation_summary'] = array(
				'description'   => __( 'A summary of the donation', 'charitable' ),
				'callback'      => array( $this, 'get_donation_summary' ),
			);

			$fields['donation_amount'] = array(
				'description'   => __( 'The total amount donated', 'charitable' ),
				'callback' 		=> array( $this, 'get_donation_total' ),
			);

			$fields['donation_date'] = array(
				'description'   => __( 'The date the donation was made', 'charitable' ),
				'callback'      => array( $this, 'get_donation_date' ),
			);

			$fields['donation_status'] = array(
				'description'   => __( 'The status of the donation (pending, paid, etc.)', 'charitable' ),
				'callback'      => array( $this, 'get_donation_status' ),
			);

			$fields['campaigns'] = array(
				'description'   => __( 'The campaigns that were donated to', 'charitable' ),
				'callback'      => array( $this, 'get_campaigns_for_donation' ),
			);

			$fields['campaign_categories'] = array(
				'description'   => __( 'The categories of the campaigns that were donated to', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_categories_for_donation' ),
			);

			return $fields;
		}

		/**
		 * Return the first name of the donor.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donor_first_name() {
			if ( ! $this->has_valid_donation() ) {
				return '';
			}

			return $this->donation->get_donor()->get_donor_meta( 'first_name' );
		}

		/**
		 * Return the full name of the donor.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donor_full_name() {
			if ( ! $this->has_valid_donation() ) {
				return '';
			}

			return $this->donation->get_donor()->get_name();
		}

		/**
		 * Return the email of the donor.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donor_email() {
			if ( ! $this->has_valid_donation() ) {
				return '';
			}

			return $this->donation->get_donor()->get_email();
		}

		/**
		 * Return the address of the donor.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_donor_address() {
			if ( ! $this->has_valid_donation() ) {
				return '';
			}

			return $this->donation->get_donor()->get_address();
		}

		/**
		 * Return the donor's phone number.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_donor_phone() {
			if ( ! $this->has_valid_donation() ) {
				return '';
			}

			return $this->donation->get_donor()->get_donor_meta( 'phone' );
		}

		/**
		 * Returns the donation ID.
		 *
		 * @return  int
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donation_id() {
			if ( ! $this->has_valid_donation() ) {
				return '';
			}

			return $this->donation->get_donation_id();
		}

		/**
		 * Returns a summary of the donation, including all the campaigns that were donated to.
		 *
		 * @param   string $value
		 * @param   mixed[] $args
		 * @param   Charitable_Email $email
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donation_summary( $value, $args, $email ) {
			if ( ! $this->has_valid_donation() ) {
				return $value;
			}

			$output = '';

			foreach ( $this->donation->get_campaign_donations() as $campaign_donation ) {

				$line_item = sprintf( '%s: %s%s',
					$campaign_donation->campaign_name,
					charitable_format_money( $campaign_donation->amount ),
					PHP_EOL
				);

				$output .= apply_filters( 'charitable_donation_summary_line_item_email', $line_item, $campaign_donation, $args, $email );

			}

			return $output;
		}

		/**
		 * Return the total amount donated.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.4.2
		 */
		public function get_donation_total( $value ) {
			if ( ! $this->has_valid_donation() ) {
				return $value;
			}

			return charitable_format_money( $this->donation->get_total_donation_amount() );
		}

		/**
		 * Returns the date the donation was made.
		 *
		 * @param   string $value
		 * @param   mixed[] $args
		 * @return  string
		 * @access  public
		 * @since   1.3.0
		 */
		public function get_donation_date( $value, $args ) {
			if ( ! $this->has_valid_donation() ) {
				return '';
			}

			$format = isset( $args['format'] ) ? $args['format'] : get_option( 'date_format' );

			return $this->donation->get_date( $format );
		}

		/**
		 * Returns the status of the donation.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.3.0
		 */
		public function get_donation_status() {
			if ( ! $this->has_valid_donation() ) {
				return '';
			}

			return $this->donation->get_status( true );
		}

		/**
		 * Return the campaigns donated to.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.4.2
		 */
		public function get_campaigns_for_donation( $value ) {
			if ( ! $this->has_valid_donation() ) {
				return $value;
			}

			return $this->donation->get_campaigns_donated_to();
		}

		/**
		 * Return the categories of the campaigns that were donated to.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.4.2
		 */
		public function get_campaign_categories_for_donation( $value ) {
			if ( ! $this->has_valid_donation() ) {
				return $value;
			}

			$categories = $this->donation->get_campaign_categories_donated_to( 'campaign_category', array(
				'fields' => 'names'
			) );

			return implode( ', ', $categories );
		}

		/**
		 * Add donation content fields' fake data for previews.
		 *
		 * @param   array $fields
		 * @param   Charitable_Email $email
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_preview_donation_content_fields( $fields, Charitable_Email $email ) {

			if ( ! $this->is_current_email( $email ) ) {
				return $fields;
			}

			if ( ! in_array( 'donation', $this->object_types ) ) {
				return $fields;
			}

			$fields['donor']               = 'John Deere';
			$fields['donor_first_name']    = 'John';
			$fields['donor_email']         = 'john@example.com';
			$fields['donor_address']       = charitable_get_location_helper()->get_formatted_address( array(
				'first_name' => 'John',
				'last_name'  => 'Deere',
				'company'    => 'Deere & Company',
				'address'    => 'One John Deere Place',
				'city'       => 'Moline',
				'state'      => 'Illinois',
				'postcode'   => '61265',
				'country'    => 'US',
			) );
			// Yes, this is in fact the address of John Deere headquarters :)
			$fields['donor_phone']         = '1300 000 000';
			$fields['donation_id']         = 164;
			$fields['donation_summary']    = __( 'Fake Campaign: $50.00', 'charitable' ) . PHP_EOL;
			$fields['donation_amount']     = '$50.00';
			$fields['donation_date']       = date_i18n( get_option( 'date_format' ) );
			$fields['donation_status']     = __( 'Paid', 'charitable' );
			$fields['campaigns'] 		   = 'Fake Campaign';
			$fields['campaign_categories'] = 'Fake Category';
			return $fields;
		}

		/**
		 * Add campaign content fields.
		 *
		 * @param   array            $fields
		 * @param   Charitable_Email $email
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_campaign_content_fields( $fields, Charitable_Email $email ) {

			if ( $email->get_email_id() != $this->get_email_id() ) {
				return $fields;
			}

			if ( ! in_array( 'campaign', $this->object_types ) ) {
				return $fields;
			}

			$fields['campaign_title'] = array(
				'description'   => __( 'The title of the campaign', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_title' ),
			);

			$fields['campaign_creator'] = array(
				'description'   => __( 'The name of the campaign creator', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_creator' ),
			);

			$fields['campaign_creator_email'] = array(
				'description'   => __( 'The email address of the campaign creator', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_creator_email' ),
			);

			$fields['campaign_end_date'] = array(
				'description'   => __( 'The end date of the campaign', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_end_date' ),
			);

			$fields['campaign_achieved_goal'] = array(
				'description'   => __( 'Display whether the campaign reached its goal. Add a `success` parameter as the message when the campaign was successful, and a `failure` parameter as the message when the campaign is not successful', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_achieved_goal' ),
			);

			$fields['campaign_donated_amount'] = array(
				'description'   => __( 'Display the total amount donated to the campaign', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_donated_amount' ),
			);

			$fields['campaign_donor_count'] = array(
				'description'   => __( 'Display the number of campaign donors', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_donor_count' ),
			);

			$fields['campaign_goal'] = array(
				'description'   => __( 'Display the campaign\'s fundraising goal', 'charitable' ),
				'callback'      => array( $this, 'get_campaign_goal' ),
			);

			$fields['campaign_url'] = array(
			  'description'     => __( 'Display the campaign\'s URL', 'charitable' ),
			  'callback'        => array( $this, 'get_campaign_url' ),
			);

			return $fields;

		}

		/**
		 * Return the campaign creator's name.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_campaign_title() {

			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			return $this->campaign->post_title;

		}

		/**
		 * Return the campaign creator's name.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_campaign_creator() {

			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			return get_the_author_meta( 'display_name', $this->campaign->get_campaign_creator() );

		}

		/**
		 * Return the campaign creator's email address.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_campaign_creator_email() {

			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			return get_the_author_meta( 'user_email', $this->campaign->get_campaign_creator() );

		}

		/**
		 * Return the campaign end date.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.1.0
		 */
		public function get_campaign_end_date() {

			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			return $this->campaign->get_end_date();

		}

		/**
		 * Display whether the campaign achieved its goal.
		 *
		 * @param   string $value
		 * @param   mixed[] $args
		 * @return  string
		 * @access  public
		 * @since   1.1.0
		 */
		public function get_campaign_achieved_goal( $value, $args ) {

			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			if ( ! $this->campaign->has_goal() ) {
				return '';
			}

			$defaults = array(
				'success' => __( 'The campaign achieved its fundraising goal.', 'charitable' ),
				'failure' => __( 'The campaign did not reach its fundraising goal.', 'charitable' ),
			);

			$args = wp_parse_args( $args, $defaults );

			if ( $this->campaign->has_achieved_goal() ) {
				return $args['success'];
			}

			return $args['failure'];

		}

		/**
		 * Display the total amount donated to the campaign.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.1.0
		 */
		public function get_campaign_donated_amount() {
			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			return charitable_format_money( $this->campaign->get_donated_amount() );
		}

		/**
		 * Display the number of donors to the campaign.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.1.0
		 */
		public function get_campaign_donor_count() {
			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			return $this->campaign->get_donor_count();
		}

		/**
		 * Display the campaign's goal amount.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.1.0
		 */
		public function get_campaign_goal() {
			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			return $this->campaign->get_monetary_goal();
		}

		/**
		 * Display the campaign's URL
		 *
		 * @return  string
		 * @access  public
		 * @since   1.3.0
		 */
		public function get_campaign_url() {
			if ( ! $this->has_valid_campaign() ) {
				return '';
			}

			return get_permalink( $this->campaign->ID );
		}

		/**
		 * Add campaign content fields' fake data for previews.
		 *
		 * @param   array            $fields
		 * @param   Charitable_Email $email
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_preview_campaign_content_fields( $fields, Charitable_Email $email ) {

			if ( $email->get_email_id() != $this->get_email_id() ) {
				return $fields;
			}

			if ( ! in_array( 'campaign', $this->object_types ) ) {
				return $fields;
			}

			$fields['campaign_title']         = 'Fake Campaign';
			$fields['campaign_creator']       = 'Harry Ferguson';
			$fields['campaign_creator_email'] = 'harry@example.com';
			$fields['campaign_end_date']      = date( get_option( 'date_format', 'd/m/Y' ) );
			$fields['campaign_achieved_goal'] = 'The campaign achieved its fundraising goal.';
			$fields['campaign_donated_amount'] = '$16,523';
			$fields['campaign_donor_count']   = 23;
			$fields['campaign_goal']          = '$15,000';
			$fields['campaign_url']           = 'http://www.example.com/campaigns/fake-campaign';

			return $fields;

		}

		/**
		 * Sends the email.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function send() {
			do_action( 'charitable_before_send_email', $this );

			$sent = wp_mail(
				$this->get_recipient(),
				do_shortcode( $this->get_subject() ),
				$this->build_email(),
				$this->get_headers()
			);

			do_action( 'charitable_after_send_email', $this, $sent );

			return $sent;
		}

		/**
		 * Checks whether the email has already been sent.
		 *
		 * @param   int $campaign_id
		 * @return  boolean
		 * @access  public
		 * @since   1.3.2
		 */
		public function is_sent_already( $post_id ) {
			$log = get_post_meta( $post_id, $this->get_log_key(), true );

			if ( is_array( $log ) ) {
				foreach ( $log as $time => $sent ) {
					if ( $sent ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Log that the email was sent.
		 *
		 * @param   int $post_id
		 * @param   boolean $sent
		 * @return  void
		 * @access  public
		 * @since   1.3.2
		 */
		public function log( $post_id, $sent ) {
			$log = get_post_meta( $post_id, $this->get_log_key(), true );

			if ( ! $log ) {
				$log = array();
			}

			$log[ time() ] = $sent;

			update_post_meta( $post_id, $this->get_log_key(), $log );
		}

		/**
		 * Preview the email. This will display a sample email within the browser.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function preview() {
			add_filter( 'charitable_email_shortcode_args', array( $this, 'set_preview_mode' ) );

			do_action( 'charitable_before_preview_email', $this );

			return $this->build_email();
		}

		/**
		 * Set preview mode in the shortcode attributes.
		 *
		 * @param   array   $atts
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function set_preview_mode( $atts ) {
			$atts['preview'] = true;
			return $atts;
		}

		/**
		 * Returns the body content of the email, formatted as HTML.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_body() {
			$body = $this->get_option( 'body', $this->get_default_body() );
			$body = do_shortcode( $body );
			$body = wpautop( $body );
			return apply_filters( 'charitable_email_body', $body, $this );
		}

		/**
		 * Returns the email headline.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_headline() {
			$headline = $this->get_option( 'headline', $this->get_default_headline() );
			$headline = do_shortcode( $headline );
			return apply_filters( 'charitable_email_headline', $headline, $this );
		}

		/**
		 * Checks whether the email has a valid donation object set.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function has_valid_donation() {
			if ( is_null( $this->donation ) || ! is_a( $this->donation, 'Charitable_Donation' ) ) {
				_doing_it_wrong( __METHOD__, __( 'You cannot send this email without a donation!', 'charitable' ), '1.0.0' );
				return false;
			}

			return true;
		}

		/**
		 * Checks whether the email has a valid donation object set.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function has_valid_campaign() {
			if ( is_null( $this->campaign ) || ! is_a( $this->campaign, 'Charitable_Campaign' ) ) {
				_doing_it_wrong( __METHOD__, __( 'You cannot send this email without a campaign!', 'charitable' ), '1.0.0' );
				return false;
			}

			return true;
		}

		/**
		 * Build the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function build_email() {
			ob_start();

			charitable_template( 'emails/header.php', array( 'email' => $this ) );

			charitable_template( 'emails/body.php', array( 'email' => $this ) );

			charitable_template( 'emails/footer.php', array( 'email' => $this ) );

			$message = ob_get_clean();

			return apply_filters( 'charitable_email_message', $message, $this );
		}

		/**
		 * Return the meta key used for the log.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.3.2
		 */
		protected function get_log_key() {
			return '_email_' . $this->get_email_id() . '_log';
		}

		/**
		 * Return the value of an option specific to this email.
		 *
		 * @param   string  $key
		 * @return  mixed
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_option( $key, $default ) {
			return charitable_get_option( array( 'emails_' . $this->get_email_id(), $key ), $default );
		}

		/**
		 * Return the default recipient for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_default_recipient() {
			return '';
		}

		/**
		 * Return the default subject line for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_default_subject() {
			return '';
		}

		/**
		 * Return the default headline for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_default_headline() {
			return '';
		}

		/**
		 * Return the default body for the email.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_default_body() {
			return '';
		}

		/**
		 * Returns the value of a particular field (generally called through the [charitable_email] shortcode).
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_field_content( $field ) {

			$fields = $this->get_fields();

			if ( ! isset( $fields[ $field ] ) ) {
				return '';
			}

			return call_user_func( $fields[ $field ] );

		}

		/**
		 * Return the value of a field for the preview.
		 *
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_preview_field_content( $field ) {

			$values = apply_filters( 'charitable_email_preview_content_fields', array(
				'site_name' => get_option( 'blogname' ),
				'site_url'  => home_url(),
			), $this );

			if ( ! isset( $values[ $field ] ) ) {
				return $field;
			}

			return $values[ $field ];

		}

		/**
		 * Return HTML formatted list of shortcode options that can be used within the body, headline and subject line.
		 *
		 * @return  string
		 * @access  protected
		 * @since   version
		 */
		protected function get_shortcode_options() {

			ob_start();
?>
			<p><?php _e( 'The following options are available with the <code>[charitable_email]</code> shortcode:', 'charitable' ) ?></p>
			<ul>
			<?php foreach ( $this->get_fields() as $key => $field ) : ?>
				<li><strong><?php echo $field['description'] ?></strong>: [charitable_email show=<?php echo $key ?>]</li>
			<?php endforeach ?> 
			</ul>

<?php
			$html = ob_get_clean();

			return apply_filters( 'charitable_email_shortcode_options_text', $html, $this );

		}

		/**
		 * Checks whether the passed email is the same as the current email object.
		 *
		 * @return  boolean
		 * @access  protected
		 * @since   1.3.2
		 */
		protected function is_current_email( Charitable_Email $email ) {

			return $email->get_email_id() == $this->get_email_id();

		}

		/**
		 * @deprecated 1.3.6
		 *
		 * @param   mixed $return
		 * @param   mixed $fallback
		 * @return  mixed $return
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function return_value_if_has_valid_donation( $return, $fallback = '' ) {

			charitable_get_deprecated()->deprecated_function(
				__METHOD__,
				'1.3.6',
				__( 'This function was buggy and has been deprecated.', 'charitable' )
			);

			if ( ! $this->has_valid_donation() ) {
				return $fallback;
			}

			return $return;

		}
	}

endif; // End class_exists check
