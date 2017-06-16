<?php
/**
 * Donor model.
 *
 * @package     Charitable/Classes/Charitable_Donor
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donor' ) ) :

	/**
	 * Charitable_Donor
	 *
	 * @since       1.0.0
	 */
	class Charitable_Donor {

		/**
		 * The donor ID.
		 *
		 * @var     int
		 * @access  protected
		 */
		protected $donor_id;

		/**
		 * The donor data from charitable_donors table.
		 *
		 * @var     Object
		 * @access  protected
		 */
		protected $data;

		/**
		 * The donation ID.
		 *
		 * @var     int
		 * @access  protected
		 */
		protected $donation_id;

		/**
		 * User object.
		 *
		 * @var     Charitable_User
		 * @access  protected
		 */
		protected $user;

		/**
		 * Donation object.
		 *
		 * @var     Charitable_Donation|null
		 * @access  protected
		 */
		protected $donation = null;

		/**
		 * Donor meta.
		 *
		 * @var     mixed[]
		 * @access  protected
		 */
		protected $donor_meta;

		/**
		 * A mapping of user keys.
		 *
		 * @var 	string[]
		 * @access 	protected
		 * @since 	1.4.0
		 */
		protected $mapped_keys;

		/**
		 * Create class object.
		 *
		 * @param   int $donor_id
		 * @param   int $donation_id
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct( $donor_id, $donation_id = false ) {
			$this->donor_id    = $donor_id;
			$this->data        = charitable_get_table( 'donors' )->get( $donor_id );
			$this->donation_id = $donation_id;
		}

		/**
		 * Magic getter method. Looks for the specified key in as a property before using Charitable_User's __get method.
		 *
		 * @return  mixed
		 * @access  public
		 * @since   1.0.0
		 */
		public function __get( $key ) {
			if ( isset( $this->$key ) ) {
				return $this->$key;
			}

			if ( isset( $this->data->$key ) ) {
				return $this->data->$key;
			}

			return $this->get_user()->$key;
		}

		/**
		 * Display the donor name when echoing object.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.4.0
		 */
		public function __toString() {
			return $this->get_name();
		}

		/**
		 * A thin wrapper around the Charitable_User::get() method.
		 *
		 * @param   string $key
		 * @return  mixed
		 * @access  public
		 * @since   1.2.4
		 */
		public function get( $key ) {
			return $this->get_user()->get( $key );
		}

		/**
		 * Return the Charitable_User object for this donor.
		 *
		 * @return  Charitable_User
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_user() {
			if ( ! isset( $this->user ) ) {
				$this->user = $this->user = Charitable_User::init_with_donor( $this->donor_id );
			}

			return $this->user;
		}

		/**
		 * Return the Charitable_Donation object associated with this object.
		 *
		 * @return  Charitable_Donation|false
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donation() {
			if ( ! isset( $this->donation ) ) {
				$this->donation = $this->donation_id ? charitable_get_donation( $this->donation_id ) : false;
			}

			return $this->donation;
		}

		/**
		 * Return the Charitable_Donation object associated with this object.
		 *
		 * @return  object[]
		 * @access  public
		 * @since   1.3.5
		 */
		public function get_donations() {
			return $this->get_user()->get_donations();
		}

		/**
		 * Return the donor meta stored for the particular donation.
		 *
		 * @param   string $key Optional key passed to return a particular meta field.
		 * @return  array|false
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donor_meta( $key = '' ) {
			if ( ! $this->get_donation() ) {
				return false;
			}

			if ( ! isset( $this->donor_meta ) ) {
				$this->donor_meta = get_post_meta( $this->donation_id, 'donor', true );
			}

			if ( empty( $key ) ) {
				return $this->donor_meta;
			}

			if ( isset( $this->donor_meta[ $key ] ) ) {
				return $this->donor_meta[ $key ];
			}

			$mapped_keys = $this->get_mapped_keys();

			if ( ! in_array( $key, $mapped_keys ) ) {
				return '';
			}

			$key = array_search( $key, $mapped_keys );

			if ( isset( $this->donor_meta[ $key ] ) ) {
				return $this->donor_meta[ $key ];
			}
		}

		/**
		 * Return the donor's name stored for the particular donation.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_name() {
			$meta = $this->get_donor_meta();

			if ( $meta ) {

				$first_name = isset( $meta['first_name'] ) ? $meta['first_name'] : '';
				$last_name = isset( $meta['last_name'] ) ? $meta['last_name'] : '';

			} else {

				$first_name = $this->data->first_name;
				$last_name = $this->data->last_name;

			}

			$name = trim( sprintf( '%s %s', $first_name, $last_name ) );

			return apply_filters( 'charitable_donor_name', $name, $this );
		}

		/**
		 * Return the donor's email address.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.2.4
		 */
		public function get_email() {
			$email = $this->get_donor_meta( 'email' );

			return $email ? $email : $this->data->email;
		}

		/**
		 * Return the donor's address.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.2.4
		 */
		public function get_address() {
			return $this->get_user()->get_address( $this->donation_id );
		}

		/**
		 * Return the donor avatar.
		 *
		 * @param   int $size
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_avatar( $size = 100 ) {
			return apply_filters( 'charitable_donor_avatar', $this->get_user()->get_avatar(), $this );
		}

		/**
		 * Return the donor location.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_location() {
			if ( ! $this->get_donor_meta() ) {
				return $this->get_user()->get_location();
			}

			$meta = $this->get_donor_meta();
			$city = isset( $meta['city'] ) ? $meta['city'] : '';
			$state = isset( $meta['state'] ) ? $meta['state'] : '';
			$country = isset( $meta['country'] ) ? $meta['country'] : '';

			$region = strlen( $city ) ? $city : $state;

			if ( strlen( $country ) ) {

				if ( strlen( $region ) ) {
					$location = sprintf( '%s, %s', $region, $country );
				} else {
					$location = $country;
				}
			} else {
				$location = $region;
			}

			return apply_filters( 'charitable_donor_location', $location, $this );
		}

		/**
		 * Return the donation amount.
		 *
		 * If a donation ID was passed to the object constructor, this will return
		 * the total donated with this particular donation. Otherwise, this will
		 * return the total amount ever donated by the donor.
		 *
		 * @param   int $campaign_id Optional. If set, returns total donated to this particular campaign.
		 * @return  decimal
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_amount( $campaign_id = false ) {
			if ( $this->get_donation() ) {
				return $this->get_donation_amount( $campaign_id );
			}

			return $this->get_user()->get_total_donated( $campaign_id );
		}

		/**
		 * Return the amount of the donation.
		 *
		 * @param   int $campaign_id Optional. If set, returns the amount donated to the campaign.
		 * @return  decimal
		 * @access  public
		 * @since   1.2.0
		 */
		public function get_donation_amount( $campaign_id = '' ) {
			return apply_filters( 'charitable_donor_donation_amount', charitable_get_table( 'campaign_donations' )->get_donation_amount( $this->donation_id, $campaign_id ), $this, $campaign_id );
		}

		/**
		 * Return the array of mapped keys, where the key is mapped to a meta_key in the user meta table.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_mapped_keys() {
			if ( ! isset( $this->mapped_keys ) ) {
				$this->mapped_keys = charitable_get_user_mapped_keys();
			}

			return $this->mapped_keys;
		}

		/**
		 * Return a value from the donor meta.
		 *
		 * @deprecated
		 *
		 * @param   string $key
		 * @return  mixed
		 * @access  public
		 * @since   1.2.4
		 */
		public function get_value( $key ) {
			charitable_get_deprecated()->deprecated_function( __METHOD__, '1.4.0', 'Charitable_Donor::get_donor_meta()' );
			return $this->get_donor_meta( $key );
		}
	}

endif; // End class_exists check