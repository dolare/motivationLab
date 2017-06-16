<?php
/**
 * A helper class to retrieve Donations.
 *
 * @package     Charitable/Classes/Charitable_Donations_Query
 * @version     1.4.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Donations_Query' ) ) :

	/**
	 * Charitable_Donations_Query
	 *
	 * @since 	1.4.0
	 */
	class Charitable_Donations_Query extends Charitable_Query {

		/**
		 * Create class object.
		 *
		 * @access  public
		 * @since   1.4.0
		 */
		public function __construct( $args = array() ) {

			$defaults = array(
				'output'   => 'donations', // Use 'posts' to get standard post objects.
				'status'   => false, // Set to an array with statuses to only show certain statuses.
				'orderby'  => 'date', // Currently only supports 'date'.
				'order'    => 'DESC',
				'number'   => 20,
				'paged'    => 1,
				'campaign' => 0,
				'donor_id' => 0,
			);

			$this->args = wp_parse_args( $args, $defaults );

			$this->position = 0;
			$this->prepare_query();
			$this->results = $this->get_donations();

		}

		/**
		 * Return list of donation IDs together with the number of donations they have made.
		 *
		 * @return  object[]
		 * @access  public
		 * @since   1.4.0
		 */
		public function get_donations() {

			$records = $this->query();

			/**
			 * Return Donations objects.
			 */
			if ( 'donations' == $this->get( 'output' ) ) {

				return array_map( 'charitable_get_donation', $records );

			}

			$currency_helper = charitable_get_currency_helper();

			/**
			 * When the currency uses commas for decimals and periods for thousands,
			 * the amount returned from the database needs to be sanitized.
			 */
			if ( $currency_helper->is_comma_decimal() ) {

				foreach ( $records as $i => $row ) {

					$records[ $i ]->amount = $currency_helper->sanitize_database_amount( $row->amount );

				}
			}

			return $records;

		}

		/**
		 * Set up fields query argument.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.4.0
		 */
		public function setup_fields() {

			/* If we are returning Donation objects, we only need to return the donation IDs. */
			if ( 'donations' == $this->get( 'output' ) ) {
				return;
			}

			add_filter( 'charitable_query_fields', array( $this, 'donation_fields' ), 4 );
			add_filter( 'charitable_query_fields', array( $this, 'donation_calc_fields' ), 5 );

		}

		/**
		 * Set up orderby query argument.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.4.0
		 */
		public function setup_orderby() {

			$orderby = $this->get( 'orderby', false );

			if ( ! $orderby ) {
				return;
			}

			switch ( $orderby ) {
				case 'date' :
					add_filter( 'charitable_query_orderby', array( $this, 'orderby_date' ) );
					break;

				case 'amount' :
					add_filter( 'charitable_query_orderby', array( $this, 'orderby_donation_amount' ) );
					break;
			}

		}

		/**
		 * Remove any hooks that have been attached by the class to prevent contaminating other queries.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.4.0
		 */
		public function unhook_callbacks() {

			remove_action( 'charitable_pre_query',     array( $this, 'setup_fields' ) );
			remove_filter( 'charitable_query_fields',  array( $this, 'donation_fields' ), 4 );
			remove_filter( 'charitable_query_fields',  array( $this, 'donation_calc_fields' ), 5 );
			remove_filter( 'charitable_query_join',    array( $this, 'join_campaign_donations_table_on_donation' ), 5 );
			remove_filter( 'charitable_query_where',   array( $this, 'where_status_is_in' ), 5 );
			remove_filter( 'charitable_query_where',   array( $this, 'where_campaign_is_in' ), 6 );
			remove_filter( 'charitable_query_where',   array( $this, 'where_donor_id_is_in' ), 7 );
			remove_filter( 'charitable_query_orderby', array( $this, 'orderby_date' ) );
			remove_filter( 'charitable_query_orderby', array( $this, 'orderby_donation_amount' ) );
			remove_action( 'charitable_post_query',    array( $this, 'unhook_callbacks' ) );

		}

		/**
		 * Set up callbacks for WP_Query filters.
		 *
		 * @return  void
		 * @access  protected
		 * @since   1.4.0
		 */
		protected function prepare_query() {

			add_action( 'charitable_pre_query',     array( $this, 'setup_fields' ) );
			add_action( 'charitable_pre_query',     array( $this, 'setup_orderby' ) );
			add_filter( 'charitable_query_join',    array( $this, 'join_campaign_donations_table_on_donation' ), 5 );
			add_filter( 'charitable_query_where',   array( $this, 'where_status_is_in' ), 5 );
			add_filter( 'charitable_query_where',   array( $this, 'where_campaign_is_in' ), 6 );
			add_filter( 'charitable_query_where',   array( $this, 'where_donor_id_is_in' ), 7 );
			add_filter( 'charitable_query_groupby', array( $this, 'groupby_donation_id' ) );
			add_action( 'charitable_post_query',    array( $this, 'unhook_callbacks' ) );

		}
	}

endif; // End class_exists check
