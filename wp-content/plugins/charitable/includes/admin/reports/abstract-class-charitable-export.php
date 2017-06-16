<?php
/**
 * Abstract class defining Export model.
 *
 * @package     Charitable/Classes/Charitable_Export
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Export' ) ) :

	/**
	 * Charitable_Export
	 *
	 * @abstract
	 * @since       1.0.0
	 */
	abstract class Charitable_Export {

		/**
		 * @var     string  The type of export.
		 */
		const EXPORT_TYPE = '';

		/**
		 * @var     string[] The CSV's columns.
		 * @access  protected
		 */
		protected $columns;

		/**
		 * @var     mixed[] Optional array of arguments.
		 * @access  protected
		 */
		protected $args;

		/**
		 * @var     mixed[] Array of default arguments.
		 * @access  protected
		 */
		protected $defaults = array();

		/**
		 * Create class object.
		 *
		 * @param   mixed[] $args
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct( $args = array() ) {
			$this->columns = $this->get_csv_columns();
			$this->args = wp_parse_args( $args, $this->defaults );

			$this->export();
		}

		/**
		 * Returns whether the current user can export data.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function can_export() {
			return (bool) apply_filters( 'charitable_export_capability', current_user_can( 'export_charitable_reports' ), $this );
		}

		/**
		 * Export the CSV file.
		 *
		 * @return  void
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function export() {

			$data = array_map( array( $this, 'map_data' ), $this->get_data() );

			$this->print_headers();

			/* Create a file pointer connected to the output stream */
			$output = fopen( 'php://output', 'w' );

			/* Print first row headers. */
			fputcsv( $output, array_values( $this->columns ) );

			/* Print the data */
			foreach ( $data as $row ) {
				fputcsv( $output, $row );
			}

			fclose( $output );

			exit();
		}

		/**
		 * Receives a row of data and maps it to the keys defined in the columns.
		 *
		 * @param   object|array $data
		 * @return  mixed
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function map_data( $data ) {
			/* Cast the data to array */
			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			$row = array();

			foreach ( $this->columns as $key => $label ) {
				$value = isset( $data[ $key ] ) ? $data[ $key ] : '';
				$value = apply_filters( 'charitable_export_data_key_value', $value, $key, $data );
				$row[] = $value;
			}

			return $row;
		}

		/**
		 * Print the CSV document headers.
		 *
		 * @return  void
		 * @access  protected
		 * @since   1.0.0
		 */
		protected function print_headers() {
			ignore_user_abort( true );

			if ( ! charitable_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
				set_time_limit( 0 );
			}

			/* Check for PHP 5.3+ */
			if ( function_exists( 'get_called_class' ) ) {
				$class  = get_called_class();
				$export = $class::EXPORT_TYPE;
			} else {
				$export = '';
			}

			nocache_headers();
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=charitable-export-' . $export . '-' . date( 'm-d-Y' ) . '.csv' );
			header( 'Expires: 0' );
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
		abstract protected function get_csv_columns();

		/**
		 * Get the data to be exported.
		 *
		 * @return  array
		 * @access  protected
		 * @since   1.0.0
		 */
		abstract protected function get_data();
	}

endif; // End class_exists check
