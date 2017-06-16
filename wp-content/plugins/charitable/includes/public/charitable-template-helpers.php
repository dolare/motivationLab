<?php 
/**
 * Charitable Template Helpers. 
 *
 * Functions used to assist with rendering templates.
 * 
 * @package     Charitable/Functions/Templates
 * @version     1.2.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Simple CSS compression. 
 *
 * Removes all comments, removes spaces after colons and strips out all the whitespace. 
 *
 * Based on http://manas.tungare.name/software/css-compression-in-php/
 *
 * @param   string $css The block of CSS to be compressed. 
 * @return  string The compressed CSS
 * @since   1.2.0
 */
function charitable_compress_css( $css ) {
	/* 1. Remove comments */
	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

	/* 2. Remove space after colons */
	$css = str_replace( ': ', ':', $css );

	/* 3. Remove whitespace */
	$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

	return $css;
}

/**
 * Provides arguments passed to campaigns within the loop.
 *
 * @param   mixed[] $view_args Optional. If called by the shortcode, this will contain the arguments passed to the shortcode.
 * @return  mixed[]
 * @since   1.2.3
 */
function charitable_campaign_loop_args( $view_args = array() ) {
	$defaults = array(
		'button' => 'donate',
	);

	$args = wp_parse_args( $view_args, $defaults );

	return apply_filters( 'charitable_campaign_loop_args', $args );
}

/**
 * Processes arbitrary form attributes into HTML-safe key/value pairs
 *
 * @param   array $field Array defining the form field attributes
 * @return  string       The formatted HTML-safe attributes
 * @since   1.3.0
 * @see     Charitable_Form::render_field()
 */
function charitable_get_arbitrary_attributes( $field ) {
	if ( ! isset( $field['attrs'] ) ) {
		$field['attrs'] = array();
	}

	/* Add backwards compatibility support for placeholder, min, max, step, pattern and rows. */
	foreach ( array( 'placeholder', 'min', 'max', 'step', 'pattern', 'rows' ) as $attr ) {
		if ( isset( $field[ $attr ] ) && ! isset( $field['attrs'][ $attr ] ) ) {
			$field['attrs'][ $attr ] = $field[ $attr ];
		}
	}

	$output = '';

	foreach ( $field['attrs'] as $key => $value ) {
		$escaped_value = esc_attr( $value );
		$output .= " $key=\"$escaped_value\" ";
	}

	return apply_filters( 'charitable_arbitrary_field_attributes', $output );
}

/**
 * Checks whether we are currently in the main loop on a singular page.
 *
 * This should be used in any functions run on the_content hook, to prevent
 * Charitable's filters touching other the_content instances outside the main
 * loop.
 *
 * @return 	boolean
 * @since 	1.4.11
 */
function charitable_is_main_loop() {
	return is_single() && in_the_loop() && is_main_query();
}
