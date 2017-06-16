<?php
/**
 * Class that is responsible for generating a CSV export of donations.
 *
 * @package     Charitable/Classes/Charitable_Export_Donations
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Export_Donations' ) ) :

	/* Include Charitable_Export base class. */
	if ( ! class_exists( 'Charitable_Export' ) ) {
		require_once( 'abstract-class-charitable-export.php' );
	}

	/**
	 * Charitable_Export_Donations
	 *
	 * @since       1.0.0
	 */
	class Charitable_Export_Donations extends Charitable_Export {

		/**
		 * @var     string  The type of export.
		 */
		const EXPORT_TYPE = 'donations';

		/**
		 * @var     mixed[] Array of default arguments.
		 * @access  protected
		 */
		protected $defaults = array(
			'start_date'    => '',
			'end_date'      => '',
			'campaign_id'   => 'all',
			'status'        => 'all',
		);

		/**
		 * @var     string[] List of donation statuses.
		 * @access  protected
		 */
		protected $statuses;

		/**
		 * Create class object.
		 *
		 * @param   mixed[] $args
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct( $args ) {
			$this->statuses = charitable_get_valid_donation_statuses();

			add_filter( 'charitable_export_data_key_value', array( $this, 'set_custom_field_data' ), 10, 3 );

			parent::__construct( $args );
		}

		/**
		 * Filter the date and time fields.
		 *
		 * @param   mixed   $value
		 * @param   string  $key
		 * @param   array   $data
		 * @return  mixed
		 * @access  public
		 * @since   1.0.0
		 */
		public function set_custom_field_data( $value, $key, $data ) {
			switch ( $key ) {
				case 'date' :
					if ( isset( $data['post_date'] ) ) {
						$value = mysql2date( 'l, F j, Y', $data['post_date'] );
					}
					break;

				case 'time' :
					if ( isset( $data['post_date'] ) ) {
						$value = mysql2date( 'H:i A', $data['post_date'] );
					}
					break;

				case 'status' :
					if ( isset( $data['post_status'] ) ) {
						$value = $this->statuses[ $data['post_status'] ];
					}
					break;

				case 'address' :
					$value = charitable_get_donation( $data['donation_id'] )->get_donor()->get_donor_meta( 'address' );
					break;

				case 'address_2' :
					$value = charitable_get_donation( $data['donation_id'] )->get_donor()->get_donor_meta( 'address_2' );
					break;

				case 'city' :
					$value = charitable_get_donation( $data['donation_id'] )->get_donor()->get_donor_meta( 'city' );
					break;

				case 'state' :
					$value = charitable_get_donation( $data['donation_id'] )->get_donor()->get_donor_meta( 'state' );
					break;

				case 'postcode' :
					$value = charitable_get_donation( $data['donation_id'] )->get_donor()->get_donor_meta( 'postcode' );
					break;

				case 'country':
					$value = charitable_get_donation( $data['donation_id'] )->get_donor()->get_donor_meta( 'country' );
					break;

				case 'phone' :
					$value = charitable_get_donation( $data['donation_id'] )->get_donor()->get_donor_meta( 'phone' );
					break;

				case 'address_formatted':
					$value = str_replace( '<br/>', PHP_EOL, charitable_get_donation( $data['donation_id'] )->get_donor_address() );
					break;

				case 'donation_gateway' :
					$gateway = charitable_get_donation( $data['donation_id'] )->get_gateway_object();
					$value   = is_a( $gateway, 'Charitable_Gateway' ) ? $gateway->get_name() : '';
					break;
			}

			return $value;
		}

		/**
		 * Return the CSV column headers.
		 *
		 * The columns are set as a key=>label array, where the key is used to retrieve the data for that column.
		 *
		 * @return  string[]
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_csv_columns() {
			$columns = array(
				'donation_id'       => __( 'Donation ID', 'charitable' ),
				'campaign_id'       => __( 'Campaign ID', 'charitable' ),
				'campaign_name'     => __( 'Campaign Title', 'charitable' ),
				'first_name'        => __( 'Donor First Name', 'charitable' ),
				'last_name'         => __( 'Donor Last Name', 'charitable' ),
				'email'             => __( 'Email', 'charitable' ),
				'address'           => __( 'Address', 'charitable' ),
				'address_2'         => __( 'Address 2', 'charitable' ),
				'city'			    => __( 'City', 'charitable' ),
				'state'			    => __( 'State', 'charitable' ),
				'postcode'		    => __( 'Postcode', 'charitable' ),
				'country' 		    => __( 'Country', 'charitable' ),
				'phone'             => __( 'Phone Number', 'charitable' ),
				'address_formatted' => __( 'Address Formatted', 'charitable' ),
				'amount'            => __( 'Donation Amount', 'charitable' ),
				'date'              => __( 'Date of Donation', 'charitable' ),
				'time'              => __( 'Time of Donation', 'charitable' ),
				'status'            => __( 'Donation Status', 'charitable' ),
				'donation_gateway'  => __( 'Donation Gateway', 'charitable' ),
			);

			return apply_filters( 'charitable_export_donations_columns', $columns, $this->args );
		}

		/**
		 * Get the data to be exported.
		 *
		 * @return  array
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_data() {
			$query_args = array();

			if ( strlen( $this->args['start_date'] ) ) {
				$query_args['start_date'] = charitable_sanitize_date( $this->args['start_date'], 'Y-m-d 00:00:00' );
			}

			if ( strlen( $this->args['end_date'] ) ) {
				$query_args['end_date'] = charitable_sanitize_date( $this->args['end_date'], 'Y-m-d 00:00:00' );
			}

			if ( 'all' != $this->args['campaign_id'] ) {
				$query_args['campaign_id'] = $this->args['campaign_id'];
			}

			if ( 'all' != $this->args['status'] ) {
				$query_args['status'] = $this->args['status'];
			}

			/** @deprecated filter name with misspelling */
			$query_args = apply_filters( 'chairtable_export_donations_query_args', $query_args, $this->args );
			$query_args = apply_filters( 'charitable_export_donations_query_args', $query_args, $this->args );

			return charitable_get_table( 'campaign_donations' )->get_donations_report( $query_args );
		}
	}

endif; // End class_exists check
