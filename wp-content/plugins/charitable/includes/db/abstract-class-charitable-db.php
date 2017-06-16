<?php
/**
 * Charitable DB base class.
 *
 * Credit: This is based on Easy Digital Downloads' EDD_DB class. Major props to Pippin Williamson.
 * https://github.com/easydigitaldownloads/Easy-Digital-Downloads/blob/master/includes/class-edd-db.php
 *
 * @package     Charitable/Classes/Charitable_DB
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @version 	1.0.0
*/
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_DB' ) ) :

	/**
	 * Charitable_DB
	 *
	 * @abstract
	 * @since 		1.0.0
	 */
	abstract class Charitable_DB {

		/**
		 * The name of our database table
		 *
		 * @access  public
		 * @since   1.0.0
		 */
		public $table_name;

		/**
		 * The version of our database table
		 *
		 * @access  public
		 * @since   1.0.0
		 */
		public $version;

		/**
		 * The name of the primary column
		 *
		 * @access  public
		 * @since   1.0.0
		 */
		public $primary_key;

		/**
		 * Get things started
		 *
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct() {}

		/**
		 * Whitelist of columns
		 *
		 * @return  array
		 * @abstract
		 * @access  public
		 * @since   1.0.0
		 */
		abstract public function get_columns();

		/**
		 * Default column values
		 *
		 * @return 	array
		 * @abstract
		 * @access  public
		 * @since   1.0.0
		 */
		abstract public function get_column_defaults();

		/**
		 * Return the format for the given column.
		 *
		 * @param 	string $column
		 * @return 	%s, %d or %f
		 * @access 	public
		 * @since 	1.0.0
		 */
		public function get_column_format( $column ) {
			$columns = $this->get_columns();
			$format = isset( $columns[ $column ] ) ? $columns[ $column ] : false;

			// If the column isn't found, throw an exception.
			if ( false === $format ) {
				throw new Exception( sprintf( 'Invalid column passed %s', $column ) );
			}

			// If the column format isn't valid, throw an exception.
			if ( ! in_array( $format, array( '%s', '%d', '%f' ) ) ) {
				throw new Exception( sprintf( 'Invalid column format for column %s. Format returned %s', $column, $format ) );
			}

			return $format;
		}

		/**
		 * Retrieve a row by the primary key
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  object
		 */
		public function get( $row_id ) {
			global $wpdb;
			return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $this->primary_key = %d LIMIT 1;", $row_id ) );
		}

		/**
		 * Retrieve a row by a specific column / value
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  object
		 */
		public function get_by( $column, $row_id ) {
			global $wpdb;
			return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $column = {$this->get_column_format($column)} LIMIT 1;", $row_id ) );
		}

		/**
		 * Retrieve a specific column's value by the primary key
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  string
		 */
		public function get_column( $column, $row_id ) {
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $this->primary_key = %d LIMIT 1;", $row_id ) );
		}

		/**
		 * Retrieve a specific column's value by the the specified column / value
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  string
		 */
		public function get_column_by( $column, $column_where, $column_value ) {
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $column_where = {$this->get_column_format($column_where)} LIMIT 1;", $column_value ) );
		}

		/**
		 * Count all rows.
		 *
		 * @return 	int
		 * @access  public
		 * @since 	1.0.0
		 */
		public function count_all() {
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT( * ) FROM $this->table_name;" );
		}

		/**
		 * Count all rows that certain criteria.
		 *
		 * @return 	int
		 * @access  public
		 * @since 	1.0.0
		 */
		public function count_by( $column, $column_value ) {
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM $this->table_name WHERE $column = {$this->get_column_format($column)};", $column_value ) );
		}

		/**
		 * Insert a new row
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  int
		 */
		public function insert( $data, $type = '' ) {
			global $wpdb;

			/* Set default values */
			$data = wp_parse_args( $data, $this->get_column_defaults() );

			do_action( 'charitable_pre_insert_' . $type, $data );

			/* Initialise column format array */
			$column_formats = $this->get_columns();

			/* Force fields to lower case */
			$data = array_change_key_case( $data );

			/* White list columns */
			$data = array_intersect_key( $data, $column_formats );

			/* Reorder $column_formats to match the order of columns given in $data */
			$data_keys = array_keys( $data );
			$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

			$inserted = $wpdb->insert( $this->table_name, $data, $column_formats );

			/* If the insert failed, return 0 */
			if ( false === $inserted ) {
				return 0;
			}

			do_action( 'charitable_post_insert_' . $type, $wpdb->insert_id, $data );

			return $wpdb->insert_id;
		}

		/**
		 * Update a row
		 *
		 * @global 	WPDB $wpdb
		 * @param 	int 	$row_id
		 * @param 	array 	$data
		 * @param 	string 	$where 	Column used in where argument.
		 * @access  public
		 * @since   1.0.0
		 * @return  bool
		 */
		public function update( $row_id, $data = array(), $where = '' ) {
			global $wpdb;

			// Row ID must be positive integer
			$row_id = absint( $row_id );

			if ( empty( $row_id ) ) {
				return false;
			}

			if ( empty( $where ) ) {
				$where = $this->primary_key;
			}

			// Initialise column format array
			$column_formats = $this->get_columns();

			// Force fields to lower case
			$data = array_change_key_case( $data );

			// White list columns
			$data = array_intersect_key( $data, $column_formats );

			// Reorder $column_formats to match the order of columns given in $data
			$data_keys = array_keys( $data );
			$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

			if ( false === $wpdb->update( $this->table_name, $data, array( $where => $row_id ), $column_formats ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Delete a row identified by the primary key
		 *
		 * @param 	int 	$row_id
		 * @access  public
		 * @since   1.0.0
		 * @return  bool
		 */
		public function delete( $row_id = 0 ) {
			// Row ID must be positive integer
			$row_id = absint( $row_id );

			if ( empty( $row_id ) ) {
				return false;
			}

			return $this->delete_by( $this->primary_key, $row_id );
		}

		/**
		 * Delete a row identified by a specific column.
		 *
		 * @global 	WPDB $wpdb
		 * @param 	string $column
		 * @param 	mixed $row_id
		 * @access  public
		 * @since   1.2.0
		 * @return  bool
		 */
		public function delete_by( $column, $row_id = 0 ) {
			global $wpdb;

			$result = $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE $column = {$this->get_column_format($column)}", $row_id ) );

			return false !== $result;
		}

	    /**
	     * Create the table.
	     *
	     * @global  $wpdb
	     * @param 	string 	$sql
	     * @access  protected
	     * @since   1.0.0
	     */
	    protected function _create_table( $sql ) {
	        global $wpdb;

	        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	        dbDelta( $sql );

	        update_option( $this->table_name . '_db_version', $this->version );
	    }
	}

endif;
