<?php
/**
 * Charitable Template Functions.
 *
 * Functions used with template hooks.
 *
 * @package     Charitable/Functions/Templates
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/***********************************************/
/* HEAD OUTPUT
/***********************************************/

if ( ! function_exists( 'charitable_template_custom_styles' ) ) :

	/**
	 * Add custom styles to the <head> section.
	 *
	 * This is used on the wp_head action.
	 *
	 * @return  void
	 * @since   1.2.0
	 */
	function charitable_template_custom_styles() {
		if ( ! apply_filters( 'charitable_add_custom_styles', true ) ) {
			return;
		}

		$styles = get_transient( 'charitable_custom_styles' );

		if ( false === $styles ) {

			ob_start();

			charitable_template( 'custom-styles.css.php' );

			$styles = ob_get_clean();

			$styles = charitable_compress_css( $styles );

			set_transient( 'charitable_custom_styles', $styles, 0 );
		}

		echo $styles;
	}

endif;

if ( ! function_exists( 'charitable_hide_admin_bar' ) ) :

	/**
	 * Hides the admin bar from header.
	 *
	 * This is designed to be used when viewing the campaign widget.
	 *
	 * @return  void
	 * @since   1.3.0
	 */
	function charitable_hide_admin_bar() {
		?>
<style type="text/css" media="screen">
html { margin-top: 0 !important; }
* html body { margin-top: 0 !important; }
</style>
		<?php
	}

endif;

/**********************************************/
/* BODY CLASSES
/**********************************************/

if ( ! function_exists( 'charitable_add_body_classes' ) ) :

	/**
	 * Adds custom body classes to certain templates.
	 *
	 * @param   string[] $classes
	 * @return  string[]
	 * @since   1.3.0
	 */
	function charitable_add_body_classes( $classes ) {
		if ( charitable_is_page( 'donation_receipt_page' ) ) {
			$classes[] = 'campaign-donation-receipt';
		}

		if ( charitable_is_page( 'donation_processing_page' ) ) {
			$classes[] = 'campaign-donation-processing';
		}

		if ( charitable_is_page( 'campaign_donation_page' ) ) {
			$classes[] = 'campaign-donation-page';
		}

		if ( charitable_is_page( 'campaign_widget_page' ) ) {
			$classes[] = 'campaign-widget';
		}

		if ( charitable_is_page( 'email_preview' ) ) {
			$classes[] = 'email-preview';
		}

		return $classes;
	}

endif;

/**********************************************/
/* SINGLE CAMPAIGN CONTENT
/**********************************************/

if ( ! function_exists( 'charitable_template_campaign_content' ) ) :

	/**
	 * Display the campaign content.
	 *
	 * This is used on the_content filter.
	 *
	 * @param   string $content
	 * @return  string
	 * @since   1.0.0
	 */
	function charitable_template_campaign_content( $content ) {

		if ( ! charitable_is_main_loop() || Charitable::CAMPAIGN_POST_TYPE != get_post_type() ) {
			return $content;
		}

		/**
		 * If this is the donation form, and it's showing on a separate page, return the content.
		 */
		if ( charitable_is_page( 'campaign_donation_page' ) ) {

			if ( 'separate_page' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
				return $content;
			}

			if ( false !== get_query_var( 'donate', false ) ) {
				return $content;
			}
		}

		/**
		 * If you do not want to use the default campaign template, use this filter and return false.
		 *
		 * @uses    charitable_use_campaign_template
		 */
		if ( ! apply_filters( 'charitable_use_campaign_template', true ) ) {
			return $content;
		}

		/**
		 * Remove ourselves as a filter to prevent eternal recursion if apply_filters('the_content')
		 * is called by one of the templates.
		 */
		remove_filter( 'the_content', 'charitable_template_campaign_content' );

		ob_start();

		charitable_template( 'content-campaign.php', array( 'content' => $content, 'campaign' => charitable_get_current_campaign() ) );

		$content = ob_get_clean();

		add_filter( 'the_content', 'charitable_template_campaign_content' );

		return $content;
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_description' ) ) :

	/**
	 * Display the campaign description before the summary and rest of content.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_description( $campaign ) {
		charitable_template( 'campaign/description.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_finished_notice' ) ) :

	/**
	 * Display the campaign finished notice.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_finished_notice( $campaign ) {
		if ( ! $campaign->has_ended() ) {
			return;
		}

		charitable_template( 'campaign/finished-notice.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_percentage_raised' ) ) :

	/**
	 * Display the percentage that the campaign has raised in summary block.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  boolean     True if the template was displayed. False otherwise.
	 * @since   1.0.0
	 */
	function charitable_template_campaign_percentage_raised( $campaign ) {
		if ( ! $campaign->has_goal() ) {
			return false;
		}

		charitable_template( 'campaign/summary-percentage-raised.php', array( 'campaign' => $campaign ) );

		return true;
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_donation_summary' ) ) :

	/**
	 * Display campaign goal in summary block.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  true
	 * @since   1.0.0
	 */
	function charitable_template_campaign_donation_summary( $campaign ) {
		charitable_template( 'campaign/summary-donations.php', array( 'campaign' => $campaign ) );
		return true;
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_donor_count' ) ) :

	/**
	 * Display number of campaign donors in summary block.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  true
	 * @since   1.0.0
	 */
	function charitable_template_campaign_donor_count( $campaign ) {
		charitable_template( 'campaign/summary-donors.php', array( 'campaign' => $campaign ) );
		return true;
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_time_left' ) ) :

	/**
	 * Display the amount of time left in the campaign in the summary block.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  boolean     True if the template was displayed. False otherwise.
	 * @since   1.0.0
	 */
	function charitable_template_campaign_time_left( $campaign ) {
		if ( $campaign->is_endless() ) {
			return false;
		}

		charitable_template( 'campaign/summary-time-left.php', array( 'campaign' => $campaign ) );
		return true;
	}

endif;

if ( ! function_exists( 'charitable_template_donate_button' ) ) :

	/**
	 * Display donate button or link in the campaign summary.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  boolean     True if the template was displayed. False otherwise.
	 * @since   1.0.0
	 */
	function charitable_template_donate_button( $campaign ) {
		if ( $campaign->has_ended() ) {
			return false;
		}

		$campaign->donate_button_template();

		return true;
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_summary' ) ) :

	/**
	 * Display campaign summary before rest of campaign content.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_summary( $campaign ) {
		charitable_template( 'campaign/summary.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_progress_bar' ) ) :

	/**
	 * Output the campaign progress bar.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_progress_bar( $campaign ) {
		charitable_template( 'campaign/progress-bar.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_donate_button' ) ) :

	/**
	 * Output the campaign donate button.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_donate_button( $campaign ) {
		charitable_template( 'campaign/donate-button.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_donate_link' ) ) :

	/**
	 * Output the campaign donate link.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_donate_link( $campaign ) {
		charitable_template( 'campaign/donate-link.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_status_tag' ) ) :

	/**
	 * Output the campaign status tag.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_status_tag( $campaign ) {
		charitable_template( 'campaign/status-tag.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_donation_form_in_page' ) ) :

	/**
	 * Add the donation form straight into the campaign page.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_donation_form_in_page( Charitable_Campaign $campaign ) {
		if ( $campaign->has_ended() ) {
			return;
		}

		if ( 'same_page' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
			charitable_get_current_donation_form()->render();
		}
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_modal_donation_window' ) ) :

	/**
	 * Adds the modal donation window to a campaign page.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_modal_donation_window() {

		global $wp_query;

		if ( Charitable::CAMPAIGN_POST_TYPE != get_post_type() ) {
			return;
		}

		if ( isset( $wp_query->query_vars['donate'] ) ) {
			return;
		}

		$campaign = charitable_get_current_campaign();

		if ( $campaign->has_ended() ) {
			return;
		}

		if ( 'modal' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
			charitable_template( 'campaign/donate-modal-window.php', array( 'campaign' => $campaign ) );
		}

	}

endif;

/**********************************************/
/* CAMPAIGN LOOP
/**********************************************/

if ( ! function_exists( 'charitable_template_campaign_loop' ) ) :

	/**
	 * Display the campaign loop.
	 *
	 * This is used instead of the_content filter.
	 *
	 * @param   WP_Query $campaigns
	 * @param   int      $columns
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_loop( $campaigns = false, $columns = 1 ) {
		if ( ! $campaigns ) {
			global $wp_query;
			$campaigns = $wp_query;
		}

		charitable_template( 'campaign-loop.php', array( 'campaigns' => $campaigns, 'columns' => $columns ) );
	}

endif;

if ( ! function_exists( 'charitable_template_responsive_styles' ) ) :

	/**
	 * Add responsive styles for the campaign loop.
	 *
	 * @param   WP_Query $campaigns The campaigns that will be displayed.
	 * @param   array    $args The view arguments.
	 * @return 	void
	 * @since 	1.4.0
	 */
	function charitable_template_responsive_styles( $campaigns, $args ) {
		if ( ! isset( $args['responsive'] ) || ! $args['responsive'] ) {
			return;
		}

		$breakpoint = '768px';

		if ( preg_match( '/[px|em]/', $args['responsive'] ) ) {
			$breakpoint = $args['responsive'];
		}
?>		
<style type="text/css" media="screen">
@media only screen and (max-width: <?php echo $breakpoint ?>) {
	.campaign-loop.campaign-grid .campaign.hentry { width: 100% !important; }
}
</style>
<?php
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_loop_thumbnail' ) ) :

	/**
	 * Output the campaign thumbnail on campaigns displayed within the loop.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_loop_thumbnail( $campaign ) {
		charitable_template( 'campaign-loop/thumbnail.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_loop_donation_stats' ) ) :

	/**
	 * Output the campaign donation status on campaigns displayed within the loop.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_loop_donation_stats( $campaign ) {
		charitable_template( 'campaign-loop/donation-stats.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_loop_donate_link' ) ) :

	/**
	 * Output the campaign donation status on campaigns displayed within the loop.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @param   mixed[] $args
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_campaign_loop_donate_link( $campaign, $args = array() ) {
		if ( isset( $args['button'] ) && 'donate' != $args['button'] ) {
			return;
		}

		$campaign->donate_button_loop_template();
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_loop_more_link' ) ) :

	/**
	 * Output the read more link on campaigns displayed within the loop.
	 *
	 * @param   Charitable_Campaign $campaign
	 * @param   mixed[] $args
	 * @return  void
	 * @since   1.2.3
	 */
	function charitable_template_campaign_loop_more_link( $campaign, $args = array() ) {
		if ( ! isset( $args['button'] ) || 'details' != $args['button'] ) {
			return;
		}

		charitable_template( 'campaign-loop/more-link.php', array( 'campaign' => $campaign ) );
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_loop_add_modal' ) ) :

	/**
	 * Checks if the modal option is enabled and hooks the modal template up to wp_footer if it is.
	 *
	 * @return  void
	 * @since   1.2.3
	 */
	function charitable_template_campaign_loop_add_modal() {
		if ( 'modal' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
			add_action( 'wp_footer', 'charitable_template_campaign_loop_modal_donation_window' );
		}
	}

endif;

if ( ! function_exists( 'charitable_template_campaign_loop_modal_donation_window' ) ) :

	/**
	 * Adds the modal donation window to the campaign loop.
	 *
	 * @return  void
	 * @since   1.2.3
	 */
	function charitable_template_campaign_loop_modal_donation_window() {
		charitable_template( 'campaign-loop/donate-modal-window.php' );
	}

endif;

/**********************************************/
/* DONATION RECEIPT
/**********************************************/

if ( ! function_exists( 'charitable_template_donation_receipt_content' ) ) :

	/**
	 * Display the donation form. This is used with the_content filter.
	 *
	 * @param   string  $content
	 * @return  string
	 * @since   1.0.0
	 */
	function charitable_template_donation_receipt_content( $content ) {

		if ( ! in_the_loop() || ! charitable_is_page( 'donation_receipt_page' ) ) {
			return $content;
		}

		/* If we are NOT using the automatic option, this is a static page with the shortcode, so don't filter again. */
		if ( 'auto' != charitable_get_option( 'donation_receipt_page', 'auto' ) ) {
			return $content;
		}

		return charitable_template_donation_receipt_output( $content );

	}

endif;

if ( ! function_exists( 'charitable_template_donation_receipt_output' ) ) :

	/**
	 * Render the donation receipt. This can be used by the [donation_receipt] shortcode or through `the_content` filter.
	 *
	 * @param   string  $content
	 * @return  string
	 * @since   1.0.0
	 */
	function charitable_template_donation_receipt_output( $content ) {
		$donation = charitable_get_current_donation();

		if ( ! $donation ) {
			return $content;
		}

		ob_start();

		if ( ! $donation->is_from_current_user() ) {

			charitable_template( 'donation-receipt/not-authorized.php', array( 'content' => $content ) );

			return ob_get_clean();

		}

		do_action( 'charitable_donation_receipt_page', $donation );

		charitable_template( 'content-donation-receipt.php', array( 'content' => $content, 'donation' => $donation ) );

		$content = ob_get_clean();

		return $content;
	}

endif;

if ( ! function_exists( 'charitable_template_donation_receipt_summary' ) ) :

	/**
	 * Display the donation receipt summary.
	 *
	 * @param   Charitable_Donation $donation
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_donation_receipt_summary( Charitable_Donation $donation ) {
		charitable_template( 'donation-receipt/summary.php', array( 'donation' => $donation ) );
	}

endif;

if ( ! function_exists( 'charitable_template_donation_receipt_offline_payment_instructions' ) ) :

	/**
	 * Display the offline payment instructions, if applicable.
	 *
	 * @param   Charitable_Donation $donation
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_donation_receipt_offline_payment_instructions( Charitable_Donation $donation ) {
		if ( 'offline' != $donation->get_gateway() ) {
			return;
		}

		charitable_template( 'donation-receipt/offline-payment-instructions.php', array( 'donation' => $donation ) );
	}

endif;

if ( ! function_exists( 'charitable_template_donation_receipt_details' ) ) :

	/**
	 * Display the donation details.
	 *
	 * @param   Charitable_Donation $donation
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_donation_receipt_details( Charitable_Donation $donation ) {
		charitable_template( 'donation-receipt/details.php', array( 'donation' => $donation ) );
	}


endif;

/**********************************************/
/* DONATION FORM
/**********************************************/

if ( ! function_exists( 'charitable_template_donation_form_content' ) ) :

	/**
	 * Display the donation form. This is used with the_content filter.
	 *
	 * @param   string  $content
	 * @return  string
	 * @since   1.0.0
	 */
	function charitable_template_donation_form_content( $content ) {

		if ( ! charitable_is_main_loop() || ! charitable_is_page( 'campaign_donation_page' ) ) {
			return $content;
		}

		if ( 'separate_page' != charitable_get_option( 'donation_form_display', 'separate_page' )
		 	&& false === get_query_var( 'donate', false ) ) {
			return $content;
		}

		ob_start();

		charitable_template( 'content-donation-form.php' );

		return ob_get_clean();
	}

endif;

if ( ! function_exists( 'charitable_template_donation_form_login' ) ) :

	/**
	 * Display a prompt to login at the start of the user fields block.
	 *
	 * @param   Charitable_Donation_Form_Interface $form The donation form object.
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_donation_form_login( Charitable_Donation_Form_Interface $form ) {

		$user = $form->get_user();

		if ( $user ) {
			return;
		}

		charitable_template( 'donation-form/donor-fields/login-form.php' );
	}

endif;

if ( ! function_exists( 'charitable_template_donation_form_donor_details' ) ) :

	/**
	 * Display the donor's saved details if the user is logged in.
	 *
	 * @param   Charitable_Donation_Form_Interface $form The donation form object.
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_donation_form_donor_details( Charitable_Donation_Form_Interface $form ) {
		$user = $form->get_user();

		/* Verify that the user is logged in and has all required fields filled out */
		if ( ! $user || ! $form->user_has_required_fields() ) {
			return;
		}

		charitable_template( 'donation-form/donor-fields/donor-details.php', array( 'user' => $user ) );
	}

endif;

if ( ! function_exists( 'charitable_template_donation_form_donor_fields_hidden_wrapper_start' ) ) :

	/**
	 * If the user is logged in, adds a wrapper around the donor fields that hide them.
	 *
	 * @param   Charitable_Donation_Form_Interface $form The donation form object.
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_donation_form_donor_fields_hidden_wrapper_start( Charitable_Donation_Form_Interface $form ) {
		/* Verify that the user is logged in and has all required fields filled out */
		if ( ! $form->get_user()  || ! $form->user_has_required_fields() ) {
			return;
		}

		charitable_template( 'donation-form/donor-fields/hidden-fields-wrapper-start.php' );
	}

endif;

if ( ! function_exists( 'charitable_template_donation_form_donor_fields_hidden_wrapper_end' ) ) :

	/**
	 * Closes the hidden donor fields wrapper div if the user is logged in.
	 *
	 * @param   Charitable_Donation_Form_Interface $form The donation form object.
	 * @return  void
	 * @since   1.0.0
	 */
	function charitable_template_donation_form_donor_fields_hidden_wrapper_end( Charitable_Donation_Form_Interface $form ) {
		/* Verify that the user is logged in and has all required fields filled out */
		if ( ! $form->get_user()  || ! $form->user_has_required_fields() ) {
			return;
		}

		charitable_template( 'donation-form/donor-fields/hidden-fields-wrapper-end.php' );
	}

endif;

/**********************************************/
/* DONATION PROCESSING PAGE
/**********************************************/

if ( ! function_exists( 'charitable_template_donation_processing_content' ) ) :

	/**
	 * Render the content of the donation processing page.
	 *
	 * @param   string $content
	 * @return  string
	 * @since   1.2.0
	 */
	function charitable_template_donation_processing_content( $content ) {

		if ( ! charitable_is_page( 'donation_processing_page' ) ) {
			return $content;
		}

		$donation = charitable_get_current_donation();

		if ( ! $donation ) {
			return $content;
		}

		$content = apply_filters( 'charitable_processing_donation_' . $donation->get_gateway(), $content, $donation );

		return $content;

	}

endif;

/**********************************************/
/* ACCOUNT PAGES
/**********************************************/

if ( ! function_exists( 'charitable_template_forgot_password_content' ) ) :

	/**
	 * Render the content of the forgot password page.
	 *
	 * @param   string $content
	 * @return  string
	 * @since   1.4.0
	 */
	function charitable_template_forgot_password_content( $content = '' ) {

		if ( ! charitable_is_page( 'forgot_password_page' ) ) {
			return $content;
		}

		ob_start();

		if ( isset( $_GET['email_sent'] ) ) {

			charitable_template( 'account/forgot-password-sent.php' );

		} else {

			charitable_template( 'account/forgot-password.php', array(
				'form' => new Charitable_Forgot_Password_Form(),
			) );

		}

		$content = ob_get_clean();

		return $content;
	}

endif;

if ( ! function_exists( 'charitable_template_reset_password_content' ) ) :

	/**
	 * Render the content of the reset password page.
	 *
	 * @param   string $content
	 * @return  string
	 * @since   1.4.0
	 */
	function charitable_template_reset_password_content( $content = '' ) {
		if ( ! charitable_is_page( 'reset_password_page' ) ) {
			return $content;
		}

		ob_start();

		charitable_template( 'account/reset-password.php', array(
			'form' => new Charitable_Reset_Password_Form(),
		) );

		$content = ob_get_clean();

		return $content;

	}

endif;

if ( ! function_exists( 'charitable_template_form_login_link' ) ) :

	/**
	 * Display a link to the login form.
	 *
	 * @param 	Charitable_Registration_Form|null $form
	 * @return 	void
	 * @since 	1.4.2
	 */
	function charitable_template_form_login_link( $form = null ) {

		/**
		 * For backwards compatibility, since previously the
		 * Form object was not passed to the hook.
		 */
		if ( is_null( $form ) ) {
			return;
		}

		if ( ! $form->get_login_link() ) {
			return;
		}

		printf( '<p>%s</p>', $form->get_login_link() );

	}

endif ;

/**********************************************/
/* NOTICES
/**********************************************/

if ( ! function_exists( 'charitable_template_notices' ) ) :

	/**
	 * Render any notices.
	 *
	 * @param   array $notices
	 * @return  void
	 * @since   1.4.0
	 */
	function charitable_template_notices( $notices = array() ) {
		if ( empty( $notices ) ) {
			$notices = charitable_get_notices()->get_notices();
		}

		charitable_template( 'form-fields/notices.php', array(
			'notices' => $notices,
		) );

	}

endif;
