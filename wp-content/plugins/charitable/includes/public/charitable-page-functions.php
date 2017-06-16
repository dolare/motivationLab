<?php
/**
 * Charitable Page Functions.
 *
 * @package 	Charitable/Functions/Page
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Displays a template.
 *
 * @param 	string|string[] $template_name A single template name or an ordered array of template.
 * @param 	mixed[] $args 				   Optional array of arguments to pass to the view.
 * @return 	Charitable_Template
 * @since 	1.0.0
 */
function charitable_template( $template_name, array $args = array() ) {
	if ( empty( $args ) ) {
		$template = new Charitable_Template( $template_name );
	} else {
		$template = new Charitable_Template( $template_name, false );
		$template->set_view_args( $args );
		$template->render();
	}

	return $template;
}

/**
 * Return the template path if the template exists. Otherwise, return default.
 *
 * @param 	string|string[] $template
 * @return  string The template path if the template exists. Otherwise, return default.
 * @since   1.0.0
 */
function charitable_get_template_path( $template, $default = '' ) {
	$t = new Charitable_Template( $template, false );
	$path = $t->locate_template();

	if ( ! file_exists( $path ) ) {
		$path = $default;
	}

	return $path;
}

/**
 * Return the URL for a given page.
 *
 * Example usage:
 *
 * - charitable_get_permalink( 'campaign_donation_page' );
 * - charitable_get_permalink( 'login_page' );
 * - charitable_get_permalink( 'registration_page' );
 * - charitable_get_permalink( 'profile_page' );
 * - charitable_get_permalink( 'donation_receipt_page' );
 * - charitable_get_permalink( 'donation_cancel_page' );
 *
 * @param 	string 	$page
 * @param   array   $args       Optional array of arguments.
 * @return  string|false        String if page is found. False if none found.
 * @since   1.0.0
 */
function charitable_get_permalink( $page, $args = array() ) {
	return apply_filters( 'charitable_permalink_' . $page, false, $args );
}

/**
 * Checks whether we are currently looking at the given page.
 *
 * Example usage:
 *
 * - charitable_is_page( 'campaign_donation_page' );
 * - charitable_is_page( 'login_page' );
 * - charitable_is_page( 'registration_page' );
 * - charitable_is_page( 'profile_page' );
 * - charitable_is_page( 'donation_receipt_page' );
 * - charitable_is_page( 'donation_cancel_page' );
 *
 * @param   string  $page
 * @param 	array 	$args 		Optional array of arguments.
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_page( $page, $args = array() ) {
	return apply_filters( 'charitable_is_page_' . $page, false, $args );
}

/**
 * Returns the URL for the campaign donation page.
 *
 * This is used when you call charitable_get_permalink( 'campaign_donation_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @global 	WP_Rewrite $wp_rewrite
 * @param 	string $url
 * @param 	array $args
 * @return 	string
 * @since 	1.0.0
 */
function charitable_get_campaign_donation_page_permalink( $url, $args = array() ) {
	global $wp_rewrite;

	$campaign_id = isset( $args['campaign_id'] ) ? $args['campaign_id'] : get_the_ID();
	$campaign_url = get_permalink( $campaign_id );

	if ( 'same_page' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
		return $campaign_url;
	}

	if ( $wp_rewrite->using_permalinks()
		&& ! in_array( get_post_status( $campaign_id ), array( 'pending', 'draft' ) )
		&& ! isset( $_GET['preview'] ) ) {
		return trailingslashit( $campaign_url ) . 'donate/';
	}

	return esc_url_raw( add_query_arg( array( 'donate' => 1 ), $campaign_url ) );
}

add_filter( 'charitable_permalink_campaign_donation_page', 'charitable_get_campaign_donation_page_permalink', 2, 2 );

/**
 * Returns the URL for the campaign donation page.
 *
 * This is used when you call charitable_get_permalink( 'donation_receipt_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @global  WP_Rewrite $wp_rewrite
 * @param   string     $url
 * @param   array      $args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_donation_receipt_page_permalink( $url, $args = array() ) {
	global $wp_rewrite;

	$receipt_page = charitable_get_option( 'donation_receipt_page', 'auto' );

	$donation_id = isset( $args['donation_id'] ) ? $args['donation_id'] : get_the_ID();

	if ( 'auto' != $receipt_page ) {
		return esc_url_raw( add_query_arg( array( 'donation_id' => $donation_id ), get_permalink( $receipt_page ) ) );
	}

	if ( $wp_rewrite->using_permalinks() ) {
		$url = sprintf( '%s/donation-receipt/%d', untrailingslashit( home_url() ), $donation_id );
	} else {
		$url = esc_url_raw( add_query_arg( array( 'donation_receipt' => 1, 'donation_id' => $donation_id ), home_url() ) );
	}

	return $url;
}

add_filter( 'charitable_permalink_donation_receipt_page', 'charitable_get_donation_receipt_page_permalink', 2, 2 );

/**
 * Returns the URL for the campaign donation page.
 *
 * This is used when you call charitable_get_permalink( 'donation_processing_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @global  WP_Rewrite $wp_rewrite
 * @param   string $url
 * @param   array $args
 * @return  string
 * @since   1.2.0
 */
function charitable_get_donation_processing_page_permalink( $url, $args = array() ) {
	global $wp_rewrite;

	$donation_id = isset( $args['donation_id'] ) ? $args['donation_id'] : get_the_ID();

	if ( $wp_rewrite->using_permalinks() ) {
		$url = sprintf( '%s/donation-processing/%d', untrailingslashit( home_url() ), $donation_id );
	} else {
		$url = esc_url_raw( add_query_arg( array(
			'donation_processing' => 1,
			'donation_id' => $donation_id,
		), home_url() ) );
	}

	return $url;
}

add_filter( 'charitable_permalink_donation_processing_page', 'charitable_get_donation_processing_page_permalink', 2, 2 );

/**
 * Returns the url of the widget page.
 *
 * This is used when you call charitable_get_permalink( 'campaign_widget_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @param 	string 		$url
 * @param 	array 		$args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_campaign_widget_page_permalink( $url, $args = array() ) {
	global $wp_rewrite;

	$campaign_id = isset( $args['campaign_id'] ) ? $args['campaign_id'] : get_the_ID();

	if ( $wp_rewrite->using_permalinks() && ! isset( $_GET['preview'] ) ) {
		$url = trailingslashit( get_permalink( $campaign_id ) ) . 'widget/';
	} else {
		$url = esc_url_raw( add_query_arg( array( 'widget' => 1 ), get_permalink( $campaign_id ) ) );
	}

	return $url;
}

add_filter( 'charitable_permalink_campaign_widget_page', 'charitable_get_campaign_widget_page_permalink', 2, 2 );

/**
 * Returns the URL for the campaign donation page.
 *
 * This is used when you call charitable_get_permalink( 'donation_cancel_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @global 	WP_Rewrite $wp_rewrite
 *
 * @param 	string $url The default URL.
 * @param 	array  $args An array of arguments.
 * @return 	string
 * @since 	1.4.0
 */
function charitable_get_donation_cancel_page_permalink( $url, $args = array() ) {
	global $wp_rewrite;

	/* A donation ID must be provided. */
	if ( ! isset( $args['donation_id'] ) ) {
		return $url;
	}

	/* Grab the first campaign donation. */
	$campaign_donation = current( charitable_get_donation( $args['donation_id'] )->get_campaign_donations() );

	$donation_page = charitable_get_permalink( 'campaign_donation_page', array(
		'campaign_id' => $campaign_donation->campaign_id,
	) );

	return esc_url_raw( add_query_arg( array(
		'donation_id' => $args['donation_id'],
		'cancel' => true,
	), $donation_page ) );
}

add_filter( 'charitable_permalink_donation_cancel_page', 'charitable_get_donation_cancel_page_permalink', 2, 2 );

/**
 * Checks whether the current request is for the given page.
 *
 * This is used when you call charitable_is_page( 'campaign_donation_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * By default, this will return true when viewing a campaign with the `donate`
 * query var set, or when the donation form is shown on the campaign page or
 * in a modal.
 *
 * Pass `'strict' => true` in `$args` to only return true when the `donate`
 * query var is set.
 *
 * @global 	WP_Query $wp_query
 * @param 	boolean  $ret
 * @param 	array    $args
 * @return 	boolean
 * @since 	1.0.0
 */
function charitable_is_campaign_donation_page( $ret, $args = array() ) {
	global $wp_query;

	if ( ! $wp_query->is_singular( Charitable::CAMPAIGN_POST_TYPE ) ) {
		return false;
	}

	if ( isset( $wp_query->query_vars['donate'] ) ) {
		return true;
	}

	/* If 'strict' is set to `true`, this will only return true if this has the /donate/ endpoint. */
	if ( isset( $args['strict'] ) && $args['strict'] ) {
		return false;
	}

	return 'separate_page' != charitable_get_option( 'donation_form_display', 'separate_page' );
}

add_filter( 'charitable_is_page_campaign_donation_page', 'charitable_is_campaign_donation_page', 2, 2 );

/**
 * Checks whether the current request is for the campaign widget page.
 *
 * This is used when you call charitable_is_page( 'campaign_widget_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @global  WP_Query    $wp_query
 *
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_campaign_widget_page() {
	global $wp_query;

	return $wp_query->is_main_query()
		&& isset( $wp_query->query_vars['widget'] )
		&& $wp_query->is_singular( Charitable::CAMPAIGN_POST_TYPE );
}

add_filter( 'charitable_is_page_campaign_widget_page', 'charitable_is_campaign_widget_page', 2 );

/**
 * Checks whether the current request is for the donation receipt page.
 *
 * This is used when you call charitable_is_page( 'donation_receipt_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @global 	WP_Query 	$wp_query
 *
 * @return 	boolean
 * @since 	1.0.0
 */
function charitable_is_donation_receipt_page() {
	global $wp_query;

	$receipt_page = charitable_get_option( 'donation_receipt_page', 'auto' );

	if ( 'auto' != $receipt_page ) {
		return is_page( $receipt_page );
	}

	return is_main_query()
		&& isset( $wp_query->query_vars['donation_receipt'] )
		&& isset( $wp_query->query_vars['donation_id'] );
}

add_filter( 'charitable_is_page_donation_receipt_page', 'charitable_is_donation_receipt_page', 2 );

/**
 * Checks whether the current request is for the donation receipt page.
 *
 * This is used when you call charitable_is_page( 'donation_processing_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @global  WP_Query    $wp_query
 *
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_donation_processing_page() {
	global $wp_query;

	return is_main_query()
		&& isset( $wp_query->query_vars['donation_processing'] )
		&& isset( $wp_query->query_vars['donation_id'] );
}

add_filter( 'charitable_is_page_donation_processing_page', 'charitable_is_donation_processing_page', 2 );

/**
 * Checks whether the current request is for the donation cancel page.
 *
 * This is used when you call charitable_is_page( 'donation_cancel_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @global 	WP_Query $wp_query
 *
 * @return 	boolean
 * @since 	1.4.0
 */
function charitable_is_donation_cancel_page() {
	global $wp_query;

	return charitable_is_page( 'campaign_donation_page' )
		&& isset( $wp_query->query_vars['donation_id'] )
		&& isset( $wp_query->query_vars['cancel'] )
		&& $wp_query->query_vars['cancel'];
}

add_filter( 'charitable_is_page_donation_cancel_page', 'charitable_is_donation_cancel_page', 2 );

/**
 * Checks whether the current request is for an email preview.
 *
 * This is used when you call charitable_is_page( 'email_preview' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_email_preview() {
	return isset( $_GET['charitable_action'] ) && 'preview_email' == $_GET['charitable_action'];
}

add_filter( 'charitable_is_page_email_preview', 'charitable_is_email_preview', 2 );

/**
 * Checks whether the current request is for a single campaign.
 *
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_campaign_page() {
	return is_singular() && Charitable::CAMPAIGN_POST_TYPE == get_post_type();
}

/**
 * Returns the URL for the user login page.
 *
 * This is used when you call charitable_get_permalink( 'login_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @see     charitable_get_permalink
 *
 * @global  WP_Rewrite  $wp_rewrite
 * @param   string      $url
 * @param   array       $args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_login_page_permalink( $url, $args = array() ) {
	$page = charitable_get_option( 'login_page', 'wp' );
	$url  = 'wp' == $page ? wp_login_url() : get_permalink( $page );
	return $url;
}

add_filter( 'charitable_permalink_login_page', 'charitable_get_login_page_permalink', 2, 2 );


/**
 * Returns the URL for the forgot password page
 *
 * This is used when you call charitable_get_permalink( 'forgot_password_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @see     charitable_get_permalink
 *
 * @global  WP_Rewrite  $wp_rewrite
 * @param   string      $url
 * @param   array       $args
 * @return  string
 * @since   1.4.0
 */
function charitable_get_forgot_password_page_permalink( $url, $args = array() ) {
	global $wp_rewrite;

	$login_page = charitable_get_permalink( 'login_page' );

	/* If we are using the default WordPress login process,
	 * return the lostpassword URL. */
	if ( wp_login_url() == $login_page ) {
		return wp_lostpassword_url();
	}

	if ( $wp_rewrite->using_permalinks() ) {
		return trailingslashit( $login_page ) . 'forgot-password/';
	}

	return esc_url_raw( add_query_arg( array( 'forgot_password' => 1 ), $login_page ) );
}

add_filter( 'charitable_permalink_forgot_password_page', 'charitable_get_forgot_password_page_permalink', 2, 2 );

/**
 * Returns the URL for the reset password page
 *
 * This is used when you call charitable_get_permalink( 'reset_password_page' ). In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @see     charitable_get_permalink
 *
 * @global  WP_Rewrite $wp_rewrite
 * @param   string     $url
 * @param   array      $args This must include a user object.
 * @return  string|false
 * @since   1.4.0
 */
function charitable_get_reset_password_page_permalink( $url, $args = array() ) {
	global $wp_rewrite;

	$login_page = charitable_get_permalink( 'login_page' );

	/* If we are using the default WordPress login process, return false. */
	if ( wp_login_url() == $login_page ) {

		charitable_get_deprecated()->doing_it_wrong(
			__FUNCTION__,
			__( 'Password reset link should not be called when using the default WordPress login.', 'charitable' ),
			'1.4.0'
		);

		return false;

	}

	/* Get the base URL. */
	if ( $wp_rewrite->using_permalinks() ) {
		return trailingslashit( $login_page ) . 'reset-password/';
	}

	return esc_url_raw( add_query_arg( array( 'reset_password' => 1 ), $login_page ) );
}

add_filter( 'charitable_permalink_reset_password_page', 'charitable_get_reset_password_page_permalink', 2, 2 );

/**
 * Checks whether the current request is for the campaign editing page.
 *
 * This is used when you call charitable_is_page( 'login_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @see     charitable_is_page
 *
 * @global 	WP_Post $post
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_login_page( $ret = false ) {
	global $post;

	$page = charitable_get_option( 'login_page', 'wp' );

	if ( 'wp' == $page ) {
		$ret = wp_login_url() == charitable_get_current_url();
	} elseif ( is_object( $post ) ) {
		$ret = $page == $post->ID;
	}

	return $ret;
}

add_filter( 'charitable_is_page_login_page', 'charitable_is_login_page', 2 );

/**
 * Checks whether the current request is for the campaign editing page.
 *
 * This is used when you call charitable_is_page( 'forgot_password_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @see     charitable_is_page
 *
 * @global  WP_Query       $wp_query
 * @param 	boolean|string $ret The value to be filtered and returned.
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_forgot_password_page( $ret = false ) {
	global $wp_query;

	$login_page = charitable_get_option( 'login_page', 'wp' );

	if ( 'wp' == $login_page ) {
		return wp_lostpassword_url() == charitable_get_current_url();
	}

	return $wp_query->is_main_query()
		&& isset( $wp_query->query_vars['forgot_password'] );
}

add_filter( 'charitable_is_page_forgot_password_page', 'charitable_is_forgot_password_page', 2 );

/**
 * Checks whether the current request is for the campaign editing page.
 *
 * This is used when you call charitable_is_page( 'login_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @see     charitable_is_page
 *
 * @global  WP_Query       $wp_query
 * @param 	boolean|string $ret The value to be filtered and returned.
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_reset_password_page( $ret = false ) {
	global $wp_query;

	$login_page = charitable_get_option( 'login_page', 'wp' );

	if ( 'wp' == $login_page ) {
		return false;
	}

	return $wp_query->is_main_query()
		&& isset( $wp_query->query_vars['reset_password'] );
}

add_filter( 'charitable_is_page_reset_password_page', 'charitable_is_reset_password_page', 2 );

/**
 * Returns the URL for the user registration page.
 *
 * This is used when you call charitable_get_permalink( 'registration_page' ).In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @see     charitable_get_permalink
 * @global  WP_Rewrite  $wp_rewrite
 * @param   string      $url
 * @param   array       $args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_registration_page_permalink( $url, $args = array() ) {
	$page = charitable_get_option( 'registration_page', 'wp' );
	$url  = 'wp' == $page ? wp_registration_url() : get_permalink( $page );
	return $url;
}

add_filter( 'charitable_permalink_registration_page', 'charitable_get_registration_page_permalink', 2, 2 );

/**
 * Checks whether the current request is for the campaign editing page.
 *
 * This is used when you call charitable_is_page( 'registration_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @see     charitable_is_page
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_registration_page( $ret = false ) {
	global $post;

	$page = charitable_get_option( 'registration_page', 'wp' );

	if ( 'wp' == $page ) {
		$ret = wp_registration_url() == charitable_get_current_url();
	} elseif ( is_object( $post ) ) {
		$ret = $page == $post->ID;
	}

	return $ret;
}

add_filter( 'charitable_is_page_registration_page', 'charitable_is_registration_page', 2 );

/**
 * Returns the URL for the user profile page.
 *
 * This is used when you call charitable_get_permalink( 'profile_page' ).In
 * general, you should use charitable_get_permalink() instead since it will
 * take into account permalinks that have been filtered by plugins/themes.
 *
 * @see     charitable_get_permalink
 * @global  WP_Rewrite  $wp_rewrite
 * @param   string      $url
 * @param   array       $args
 * @return  string
 * @since   1.0.0
 */
function charitable_get_profile_page_permalink( $url, $args = array() ) {
	$page = charitable_get_option( 'profile_page', false );

	if ( $page ) {
		$url = get_permalink( $page );
	}

	return $url;
}

add_filter( 'charitable_permalink_profile_page', 'charitable_get_profile_page_permalink', 2, 2 );

/**
 * Checks whether the current request is for the campaign editing page.
 *
 * This is used when you call charitable_is_page( 'profile_page' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @see     charitable_is_page
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_profile_page( $ret = false ) {
	global $post;

	$page = charitable_get_option( 'profile_page', false );

	return false == $page || is_null( $post ) ? false : $page == $post->ID;
}

add_filter( 'charitable_is_page_profile_page', 'charitable_is_profile_page', 2 );

/**
 * Returns the URL to which the user should be redirected after signing on or registering an account.
 *
 * @return  string
 * @since   1.0.0
 */
function charitable_get_login_redirect_url() {
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect = $_REQUEST['redirect_to'];
	} elseif ( charitable_get_permalink( 'profile_page' ) ) {
		$redirect = charitable_get_permalink( 'profile_page' );
	} else {
		$redirect = home_url();
	}

	return apply_filters( 'charitable_signon_redirect_url', $redirect );
}

/**
 * Returns the current URL.
 *
 * @see 	https://gist.github.com/leereamsnyder/fac3b9ccb6b99ab14f36
 * @global 	WP 		$wp
 * @return  string
 * @since   1.0.0
 */
function charitable_get_current_url() {
	global $wp;

	$url = esc_url_raw( add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) ) );

	return $url;
}

/**
 * Verifies whether the current user can access the donation receipt.
 *
 * @param   Charitable_Donation $donation
 * @return  boolean
 * @since   1.1.2
 */
function charitable_user_can_access_receipt( Charitable_Donation $donation ) {
	charitable_get_deprecated()->deprecated_function(
		__FUNCTION__,
		'1.4.0',
		'Charitable_Donation::is_from_current_user()'
	);

	return $donation->is_from_current_user();
}
