<?php
/**
 * Charitable Utility Functions.
 *
 * Utility functions.
 *
 * @package 	Charitable/Functions/Utility
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Orders an array by the priority key.
 *
 * @param 	array 	$a
 * @param 	array 	$b
 * @return 	int
 * @since 	1.0.0
 */
function charitable_priority_sort( $a, $b ) {
	if ( $a['priority'] == $b['priority'] ) {
		return 0;
	}

	return $a['priority'] < $b['priority'] ? -1 : 1;
}

/**
 * Checks whether function is disabled.
 *
 * Full credit to Pippin Williamson and the EDD team.
 *
 * @param 	string  $function 	Name of the function.
 * @return 	bool 				Whether or not function is disabled.
 * @since 	1.0.0
 */
function charitable_is_func_disabled( $function ) {
	$disabled = explode( ',',  ini_get( 'disable_functions' ) );

	return in_array( $function, $disabled );
}

/**
 * Verify a nonce. This also just ensures that the nonce is set.
 *
 * @param   string $nonce
 * @param   string $action
 * @param   array  $request_args
 * @return  boolean
 * @since   1.0.0
 */
function charitable_verify_nonce( $nonce, $action, $request_args = array() ) {
	if ( empty( $request_args ) ) {
		$request_args = $_GET;
	}

	return isset( $request_args[ $nonce ] ) && wp_verify_nonce( $request_args[ $nonce ], $action );
}

/**
 * Retrieve the timezone id.
 *
 * Credit: Pippin Williamson & the rest of the EDD team.
 *
 * @return  string
 * @since   1.0.0
 */
function charitable_get_timezone_id() {
	$timezone = get_option( 'timezone_string' );

	/* If site timezone string exists, return it */
	if ( $timezone ) {
		return $timezone;
	}

	$utc_offset = 3600 * get_option( 'gmt_offset', 0 );

	/* Get UTC offset, if it isn't set return UTC */
	if ( ! $utc_offset ) {
		return 'UTC';
	}

	/* Attempt to guess the timezone string from the UTC offset */
	$timezone = timezone_name_from_abbr( '', $utc_offset );

	/* Last try, guess timezone string manually */
	if ( $timezone === false ) {

		$is_dst = date( 'I' );

		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset ) {
					return $city['timezone_id'];
				}
			}
		}
	}

	/* If we still haven't figured out the timezone, fall back to UTC */
	return 'UTC';
}

/**
 * Ensure a number is a positive integer.
 *
 * @return  int|false
 * @since   1.0.0
 */
function charitable_validate_absint( $i ) {
	return filter_var( $i, FILTER_VALIDATE_INT, array( 'min_range' => 1 ) );
}

/**
 * Format an array of strings as a sentence part.
 *
 * If there is one item in the string, this will just return that item.
 * If there are two items, it will return a string like this: "x and y".
 * If there are three or more items, it will return a string like this: "x, y and z".
 *
 * @param   string[] $list
 * @return  string
 * @since   1.3.0
 */
function charitable_list_to_sentence_part( $list ) {
	$list = array_values( $list );

	if ( 1 == count( $list ) ) {
		return $list[0];
	}

	if ( 2 == count( $list ) ) {
		return sprintf( _x( '%s and %s', 'x and y', 'charitable' ), $list[0], $list[1] );
	}

	$last = array_pop( $list );

	return sprintf( _x( '%s and %s', 'x and y', 'charitable' ), implode( ', ', $list ), $last );
}

/**
 * Sanitizes a date passed in the format of January 29, 2009.
 *
 * We use WP_Locale to parse the month that the user has set.
 *
 * @global  WP_Locale $wp_locale
 * @param   string    $date
 * @param   string    $format The date format to return. Default is U (timestamp).
 * @return  string|false
 * @since   1.4.10
 */
function charitable_sanitize_date( $date, $format = 'U' ) {

	global $wp_locale;

	if ( empty( $date ) || ! $date ) {
		return false;
	}

	list( $month, $day, $year ) = explode( ' ', $date );

	$day   = trim( $day, ',' );

	$month = 1 + array_search( $month, array_values( $wp_locale->month ) );

	$time  = mktime( 0, 0, 0, $month, $day, $year );

	if ( 'U' == $format ) {
		return $time;
	}

	return date( $format, $time );

}
