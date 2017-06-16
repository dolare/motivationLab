<?php
/**
 * An abstract base class defining common methods used by Charitable queries.
 *
 * @package     Charitable/Classes/Charitable_Query
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Query' ) ) :

	/**
	 * Charitable_Query
	 *
	 * @since       1.0.0
	 */
	abstract class Charitable_Query implements Iterator {

		/**
		 * User-defined arguments.
		 *
		 * @var     array
		 * @access  protected
		 */
		protected $args;

		/**
		 * Internal iterator position.
		 *
		 * @var     int
		 * @access  protected
		 */
		protected $position = 0;

		/**
		 * The WP_Query object that is actually querying the data.
		 *
		 * @var     WP_Query
		 * @access  protected
		 */
		protected $query;

		/**
		 * Result set.
		 *
		 * @var     object[]
		 * @access  protected
		 */
		protected $results;

		/**
		 * Parameters to pass to the query.
		 *
		 * @var     mixed[]
		 * @access  protected
		 */
		protected $parameters = array();

		/**
		 * Return the query argument value for the given key.
		 *
		 * @param   string $key
		 * @return  mixed|false Returns fallback if the argument is not found.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get( $key, $fallback = false ) {
			return isset( $this->args[ $key ] ) ? $this->args[ $key ] : $fallback;
		}

		/**
		 * Set the query argument for the given key.
		 *
		 * @param   string $key
		 * @param   mixed $value
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function set( $key, $value ) {
			$this->args[ $key ] = apply_filters( 'charitable_query_sanitize_argument_' . $key, $value, $this );
		}

		/**
		 * Remove the given query argument.
		 *
		 * @param   string $key
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function remove( $key ) {
			unset( $this->args[ $key ] );
		}

		/**
		 * Return the results of the query.
		 *
		 * @global  WPDB $wpdb
		 * @return  object[]
		 * @access  public
		 * @since   1.0.0
		 */
		public function query() {
			if ( ! isset( $this->query ) ) {
				global $wpdb;

				do_action( 'charitable_pre_query', $this );

				$this->parameters = array();

				$sql = "SELECT {$this->fields()} {$this->from()} {$this->join()} {$this->where()} {$this->groupby()} {$this->orderby()} {$this->order()} {$this->limit()} {$this->offset()};";

				$this->query = $wpdb->get_results( $wpdb->prepare( $sql, $this->parameters ) );

				do_action( 'charitable_post_query', $this );
			}

			return $this->query;
		}

		/**
		 * Return the fields right after the SELECT part of the query.
		 *
		 * @global  WPBD $wpdb
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function fields() {
			global $wpdb;
			return apply_filters( 'charitable_query_fields', "{$wpdb->posts}.ID", $this );
		}

		/**
		 * Return the FROM part of the query.
		 *
		 * @global  WPBD $wpdb
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function from() {
			global $wpdb;
			return apply_filters( 'charitable_query_from', "FROM $wpdb->posts", $this );
		}

		/**
		 * Return the JOIN part of the query.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function join() {
			return apply_filters( 'charitable_query_join', '', $this );
		}

		/**
		 * Return the WHERE part of the query.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function where() {
			return apply_filters( 'charitable_query_where', 'WHERE 1=1 ', $this );
		}

		/**
		 * Return the GROUPBY part of the query.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function groupby() {
			return apply_filters( 'charitable_query_groupby', '', $this );
		}

		/**
		 * Return the ORDERBY part of the query.
		 *
		 * @global  WPBD $wpdb
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function orderby() {
			global $wpdb;
			return apply_filters( 'charitable_query_orderby', "ORDER BY {$wpdb->posts}.ID", $this );
		}

		/**
		 * Return the ORDER part of the query.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function order() {
			return apply_filters( 'charitable_query_order', $this->get( 'order', 'DESC' ), $this );
		}

		/**
		 * Return the LIMIT part of the query.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function limit() {
			if ( $this->show_all() ) {
				return '';
			}

			return apply_filters( 'charitable_query_limit', "LIMIT {$this->get( 'number', 20 )}", $this );
		}

		/**
		 * Return the OFFSET part of the query.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function offset() {
			if ( $this->show_all() ) {
				return '';
			}

			$offset = $this->get( 'number' ) * ( $this->get( 'paged', 1 ) - 1 );
			return apply_filters( 'charitable_query_offset', "OFFSET $offset" , $this );
		}

		/**
		 * Select donor-specific fields.
		 *
		 * @return  string $select_statement
		 * @access  public
		 * @since   1.0.0
		 */
		public function donor_fields( $select_statement ) {
			$select_statement .= ', d.donor_id, d.user_id, d.first_name, d.last_name, d.email, d.date_joined';
			return $select_statement;
		}

		/**
		 * Retrieve the donation ID and campaigns.
		 *
		 * @return  string $select_statement
		 * @access  public
		 * @since   1.0.0
		 */
		public function donation_fields( $select_statement ) {
			$select_statement .= ", cd.donation_id, GROUP_CONCAT(cd.campaign_name SEPARATOR ', ') AS campaigns, GROUP_CONCAT(cd.campaign_id SEPARATOR ',') AS campaign_ids";
			return $select_statement;
		}

		/**
		 * Select donation-specific fields.
		 *
		 * @return  string $select_statement
		 * @access  public
		 * @since   1.0.0
		 */
		public function donation_calc_fields( $select_statement ) {
			$select_statement .= ', COUNT(cd.campaign_donation_id) AS donations, SUM(cd.amount) AS amount';
			return $select_statement;
		}

		/**
		 * Select total amount field.
		 *
		 * @return  string $select_statement
		 * @access  public
		 * @since   1.2.0
		 */
		public function donation_amount_sum_field( $select_statement ) {
			$select_statement .= ', SUM(cd.amount) AS amount';
			return $select_statement;
		}

		/**
		 * Filter query by campaign receiving the donation.
		 *
		 * @param   string $where_statement
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function where_campaign_is_in( $where_statement ) {
			$campaign = $this->get( 'campaign', 0 );

			if ( ! $campaign ) {
				return $where_statement;
			}

			if ( ! is_array( $campaign ) ) {
				$campaign = array( $campaign );
			}

			$campaign = array_filter( $campaign, 'charitable_validate_absint' );

			$placeholders = $this->get_placeholders( count( $campaign ), '%d' );

			$this->add_parameters( $campaign );

			$where_statement .= " AND cd.campaign_id IN ({$placeholders})";
			return $where_statement;
		}

		/**
		 * Filter query by status of the post.
		 *
		 * @global  WPBD $wpdb
		 * @param   string $where_statement
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function where_status_is_in( $where_statement ) {
			global $wpdb;

			$status = $this->get( 'status', false );

			if ( ! $status ) {
				return $where_statement;
			}

			if ( ! is_array( $status ) ) {
				$status = array( $status );
			}

			$status = array_filter( $status, 'charitable_is_valid_donation_status' );

			$placeholders = $this->get_placeholders( count( $status ), '%s' );

			$this->add_parameters( $status );

			$where_statement .= " AND {$wpdb->posts}.post_status IN ({$placeholders})";
			return $where_statement;
		}

		/**
		 * Filter query by donor ID.
		 *
		 * @global  WPBD $wpdb
		 * @param   string $where_statement
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function where_donor_id_is_in( $where_statement ) {
			global $wpdb;

			$donor_id = $this->get( 'donor_id', false );

			if ( ! $donor_id ) {
				return $where_statement;
			}

			if ( ! is_array( $donor_id ) ) {
				$donor_id = array( $donor_id );
			}

			$donor_id = array_filter( $donor_id, 'charitable_validate_absint' );

			$placeholders = $this->get_placeholders( count( $donor_id ), '%d' );

			$this->add_parameters( $donor_id );

			$where_statement .= " AND cd.donor_id IN ({$placeholders})";

			return $where_statement;
		}

		/**
		 * A method used to join the campaign donations table on the campaigns query.
		 *
		 * @global  WPBD $wpdb
		 * @param   string $join_statement
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function join_campaign_donations_table_on_campaign( $join_statement ) {
			global $wpdb;
			$join_statement .= " INNER JOIN {$wpdb->prefix}charitable_campaign_donations cd ON cd.campaign_id = $wpdb->posts.ID ";
			return $join_statement;
		}

		/**
		 * A method used to join the campaign donations table on the campaigns query.
		 *
		 * @global  WPBD $wpdb
		 * @param   string $join_statement
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function join_campaign_donations_table_on_donation( $join_statement ) {
			global $wpdb;
			$join_statement .= " INNER JOIN {$wpdb->prefix}charitable_campaign_donations cd ON cd.donation_id = $wpdb->posts.ID ";
			return $join_statement;
		}

		/**
		 * A method used to join the donors table on the query.
		 *
		 * @global  WPBD $wpdb
		 * @param   string $join_statement
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function join_donors_table( $join_statement ) {
			global $wpdb;
			$join_statement .= " INNER JOIN {$wpdb->prefix}charitable_donors d ON d.donor_id = cd.donor_id ";
			return $join_statement;
		}

		/**
		 * Group results by the ID.
		 *
		 * @global  WPBD $wpdb
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function groupby_ID() {
			global $wpdb;
			return "GROUP BY {$wpdb->posts}.ID";
		}

		/**
		 * Group a query by the donor ID.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function groupby_donor_id() {
			return 'GROUP BY cd.donor_id';
		}

		/**
		 * Group a query by the donation ID.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.4.0
		 */
		public function groupby_donation_id() {
			return 'GROUP BY cd.donation_id';
		}

		/**
		 * Order by the date of the post.
		 *
		 * @global  WPBD $wpdb
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function orderby_date() {
			global $wpdb;
			return "ORDER BY {$wpdb->posts}.post_date";
		}

		/**
		 * Order by the results count of the ID column.
		 *
		 * This is useful when used in combination with a group statement.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function orderby_count() {
			return 'ORDER BY COUNT(*)';
		}

		/**
		 * A method used to change the ordering of the campaigns query, to order by the amount donated.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function orderby_donation_amount() {
			return 'ORDER BY COALESCE(SUM(cd.amount), 0)';
		}

		/**
		 * Return number of results.
		 *
		 * @return  int
		 * @access  public
		 * @since   1.0.0
		 */
		public function count() {
			return count( $this->results );
		}

		/**
		 * Rewind to first result.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function rewind() {
			$this->position = 0;
		}

		/**
		 * Return current element.
		 *
		 * @return  object
		 * @access  public
		 * @since   1.0.0
		 */
		public function current() {
			return $this->results[ $this->position ];
		}

		/**
		 * Return current key.
		 *
		 * @return  int
		 * @access  public
		 * @since   1.0.0
		 */
		public function key() {
			return $this->position;
		}

		/**
		 * Advance to next item.
		 *
		 * @return  int
		 * @access  public
		 * @since   1.0.0
		 */
		public function next() {
			++$this->position;
		}

		/**
		 * Ensure that current position is valid.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function valid() {
			return isset( $this->results[ $this->position ] );
		}

		/**
		 * Add parameters to pass to the prepared query.
		 *
		 * @param   mixed $parameters
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function add_parameters( $parameters ) {
			$this->parameters = array_merge( $this->parameters, $parameters );
		}

		/**
		 * Whether to show all results.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.1.0
		 */
		public function show_all() {
			return -1 == $this->get( 'number' );
		}

		/**
		 * Return the correct number of placeholders given a symbol and count.
		 *
		 * @param   int $count
		 * @param   string $placeholder
		 * @return  string
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function get_placeholders( $count = 1, $placeholder = '%s' ) {
			$placeholders = array_fill( 0, $count, $placeholder );
			return implode( ', ', $placeholders );
		}
	}

endif; // End class_exists check
