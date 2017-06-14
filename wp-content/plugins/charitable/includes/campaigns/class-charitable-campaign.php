 <?php
/**
 * Campaign model
 *
 * @version     1.0.0
 * @package     Charitable/Classes/Charitable_Campaign
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Campaign' ) ) :

	/**
	 * Campaign Model
	 *
	 * @since       1.0.0
	 */
	class Charitable_Campaign {

		/**
		 * @var WP_Post The WP_Post object associated with this campaign.
		 */
		private $post;

		/**
		 * @var int The timestamp for the expiry for this campaign.
		 */
		private $end_time;

		/**
		 * @var decimal The fundraising goal for the campaign.
		 */
		private $goal;

		/**
		 * @var WP_Query The donations made to this campaign.
		 */
		private $donations;

		/**
		 * @var int The amount donated to the campaign.
		 */
		private $donated_amount;

		/**
		 * @var Charitable_Donation_Form The form object for this campaign.
		 */
		private $donation_form;

		/**
		 * Class constructor.
		 *
		 * @param   mixed   $post       The post ID or WP_Post object for this this campaign.
		 * @access  public
		 * @since   1.0.0
		 */
		public function __construct( $post ) {
			if ( ! is_a( $post, 'WP_Post' ) ) {
				$post = get_post( $post );
			}

			$this->post = $post;
		}

		/**
		 * Magic getter.
		 *
		 * @return  mixed
		 * @access  public
		 * @since   1.0.0
		 */
		public function __get( $key ) {
			if ( property_exists( $this->post, $key ) ) {
				return $this->post->$key;
			}

			return $this->get( $key );
		}

		/**
		 * Returns the campaign's post_meta values. _campaign_ is automatically prepended to the meta key.
		 *
		 * @see     get_post_meta
		 * @param   string  $meta_name      The meta name to search for.
		 * @param   bool    $single         Whether to return a single value or an array.
		 * @return  mixed                   This will return an array if single is false. If it's true,
		 *                                  the value of the meta_value field will be returned.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get( $meta_name, $single = true ) {
			$meta_name = '_campaign_' . $meta_name;
			return apply_filters( 'charitable_campaign_get_meta_value', get_post_meta( $this->post->ID, $meta_name, $single ), $meta_name, $single, $this );
		}

		/**
		 * Returns whether the campaign is endless (i.e. no end date has been set).
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function is_endless() {
			return 0 == $this->end_date;
		}

		/**
		 * Return the suggested amounts, or an empty array if there are none.
		 *
		 * @return  array
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_suggested_donations() {
			$value = get_post_meta( $this->post->ID, '_campaign_suggested_donations', true );

			if ( ! is_array( $value ) ) {
				$value = array();
			}

			return apply_filters( 'charitable_campaign_suggested_donations', $value, $this );
		}

		/**
		 * Returns the end date in your preferred format.
		 *
		 * If a format is not provided, the user-defined date_format in Wordpress settings is used.
		 *
		 * @param   string  $date_format    A date format accepted by PHP's date() function.
		 * @return  string|false        String if an end date is set. False if campaign has no end date.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_end_date( $date_format = '' ) {
			if ( $this->is_endless() ) {
				return false;
			}

			if ( ! strlen( $date_format ) ) {
				$date_format = get_option( 'date_format', 'd/m/Y' );
			}

			/* Filter the end date format using the charitable_campaign_end_date_format hook. */
			$date_format = apply_filters( 'charitable_campaign_end_date_format', $date_format, $this );

			/* This is how the end date is stored in the database, so just return that directly. */
			if ( 'Y-m-d H:i:s' == $date_format ) {
				return $this->end_date;
			}

			return date( $date_format, $this->get_end_time() );
		}

		/**
		 * Returns the timetamp of the end date.
		 *
		 * @return  int|false           Int if campaign has an end date. False if campaign has no end date.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_end_time() {
			if ( ! isset( $this->end_time ) ) {

				if ( $this->is_endless() ) {
					return false;
				}

				/* The date is stored in the format of Y-m-d H:i:s. */
				$date_time  = explode( ' ', $this->end_date );
				$date       = explode( '-', $date_time[0] );
				$time       = explode( ':', $date_time[1] );
				$this->end_time = mktime( $time[0], $time[1], $time[2], $date[1], $date[2], $date[0] );
			}
			return $this->end_time;
		}

		/**
		 * Returns the amount of time left in the campaign in seconds.
		 *
		 * @return  int $time_left Int if campaign has an end date. False if campaign has no end date.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_seconds_left() {
			if ( $this->is_endless() ) {
				return false;
			}

			$time_left = $this->get_end_time() - current_time( 'timestamp' );
			return $time_left < 0 ? 0 : $time_left;
		}

		/**
		 * Returns the amount of time left in the campaign as a descriptive string.
		 *
		 * @uses charitable_campaign_ended          Change the text displayed when there is no time left.
		 * @uses charitabile_campaign_minutes_left  Change the text displayed when there is less than an hour left.
		 * @uses charitabile_campaign_hours_left    Change the text displayed when there is less than a day left.
		 * @uses charitabile_campaign_days_left     Change the text displayed when there is more than a day left.
		 * @uses charitable_campaign_time_left      Change the text displayed when there is time left. This will
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_time_left() {
			if ( $this->is_endless() ) {
				return '';
			}

			$hour = 3600;
			$day = 86400;

			$seconds_left = $this->get_seconds_left();

			/* Condition 1: The campaign has finished. */
			if ( 0 === $seconds_left ) {

				$time_left = apply_filters( 'charitable_campaign_ended', __( 'Campaign has ended', 'charitable' ), $this );

			} /* Condition 2: There is less than an hour left. */
			elseif ( $seconds_left <= $hour ) {

				$minutes_remaining = ceil( $seconds_left / 60 );
				$time_left = apply_filters( 'charitabile_campaign_minutes_left',
					sprintf( _n( '%s Minute Left', '%s Minutes Left', $minutes_remaining, 'charitable' ), '<span class="amount time-left minutes-left">' . $minutes_remaining . '</span>' ),
					$this
				);

			} /* Condition 3: There is less than a day left. */
			elseif ( $seconds_left <= $day ) {

				$hours_remaining = floor( $seconds_left / 3600 );
				$time_left = apply_filters( 'charitabile_campaign_hours_left',
					sprintf( _n( '%s Hour Left', '%s Hours Left', $hours_remaining, 'charitable' ), '<span class="amount time-left hours-left">' . $hours_remaining . '</span>' ),
					$this
				);

			} /* Condition 4: There is more than a day left. */
			else {

				$days_remaining = floor( $seconds_left / 86400 );
				$time_left = apply_filters( 'charitabile_campaign_days_left',
					sprintf( _n( '%s Day Left', '%s Days Left', $days_remaining, 'charitable' ), '<span class="amount time-left days-left">' . $days_remaining . '</span>' ),
					$this
				);

			}

			return apply_filters( 'charitable_campaign_time_left', $time_left, $this );
		}

		/**
		 * Returns whether the campaign has ended.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function has_ended() {
			return ! $this->is_endless() && 0 == $this->get_seconds_left();
		}

		/**
		 * Return a text notice to say that a campaign has finished.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_finished_notice() {
			if ( ! $this->has_ended() ) {
				return '';
			}

			if ( ! $this->has_goal() ) {
				$message = __( 'This campaign ended %s ago', 'charitable' );
			} elseif ( $this->has_achieved_goal() ) {
				$message = __( 'This campaign successfully reached its funding goal and ended %s ago', 'charitable' );
			} else {
				$message = __( 'This campaign failed to reach its funding goal %s ago', 'charitable' );
			}

			return apply_filters( 'charitable_campaign_finished_notice', sprintf( $message, '<span class="time-ago">' . human_time_diff( $this->get_end_time() ) . '</span>' ), $this );
		}

		/**
		 * Return the time since the campaign finished, or zero if it's still going.
		 *
		 * @return  int
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_time_since_ended() {
			if ( 0 !== $this->get_seconds_left() ) {
				return 0;
			}

			return current_time( 'timestamp' ) - $this->get_end_time();
		}

		/**
		 * Returns the fundraising goal of the campaign.
		 *
		 * @return  string|false  Amount if goal is set. False otherwise.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_goal() {
			if ( ! isset( $this->goal ) ) {
				$this->goal = $this->has_goal() ? $this->get( 'goal' ) : false;
			}

			return $this->goal;
		}

		/**
		 * Returns whether a goal has been set (anything greater than $0 is a goal).
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function has_goal() {
			return 0 < $this->get( 'goal' );
		}

		/**
		 * Returns the fundraising goal formatted as a monetary amount.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_monetary_goal() {
			if ( ! $this->has_goal() ) {
				return '';
			}

			return charitable_format_money( $this->get( 'goal' ) );
		}

		/**
		 * Returns whether the goal has been achieved.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.0.0
		 */
		public function has_achieved_goal() {
			return $this->get_donated_amount( true ) >= $this->get_goal();
		}

		/**
		 * Return the campaign status.
		 *
		 * If the campaign is published, this will return whether either 'active' or 'finished'.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_status() {
			$status = $this->post->post_status;

			if ( 'publish' == $status ) {
				$status = $this->has_ended() ? 'finished' : 'active';
			}

			return apply_filters( 'charitable_campaign_status', $status, $this->post->post_status, $this );
		}

		/**
		 * Return a status key for the campaign.
		 *
		 * This will return one of the following:
		 *
		 * inactive : A campaign that is not published.
		 * ended : A campaign without a goal has finished
		 * successful : A campaign with a goal has finished & achieved its goal
		 * unsucessful : A campaign with a goal has finished without achieving its goal
		 * ending : A campaign is ending soon.
		 * active : A campaign that is active and not ending soon.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.3.7
		 */
		public function get_status_key() {
			$ending_soon_threshold = apply_filters( 'charitable_campaign_ending_soon_threshold', WEEK_IN_SECONDS );

			$ended = $this->has_ended();

			if ( 'publish' != $this->post->post_status ) {
				return 'inactive';
			}

			if ( $ended && ! $this->has_goal() ) {
				return 'ended';
			}

			if ( $ended && $this->has_achieved_goal() ) {
				return 'successful';
			}

			if ( $ended ) {
				return 'unsucessful';
			}

			if ( ! $this->is_endless() && $this->get_seconds_left() < $ending_soon_threshold ) {
				return 'ending';
			}

			return 'active';
		}

		/**
		 * Return the campaign status tag.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_status_tag() {

			$key              = $this->get_status_key();
			$show_achievement = apply_filters( 'charitable_campaign_show_achievement_status_tag', true );
			$show_active_tag  = apply_filters( 'charitable_campaign_show_active_status_tag', false );

			switch ( $key ) {

				case 'ended' :
					$tag = __( 'Ended', 'charitable' );
					break;

				case 'successful' :
					$tag = $show_achievement ? __( 'Successful', 'charitable' ) : __( 'Ended', 'charitable' );
					break;

				case 'unsucessful' :
					$tag = $show_achievement ? __( 'Unsuccessful', 'charitable' ) : __( 'Ended', 'charitable' );
					break;

				case 'ending' :
					$tag = __( 'Ending Soon', 'charitable' );
					break;

				case 'active' :
					$tag = $show_active_tag ? __( 'Active', 'charitable' ) : '';
					break;

				default :
					$tag = '';

			}

			return apply_filters( 'charitable_campaign_status_tag', $tag, $key, $this );
		}

		/**
		 * Returns the donations made to this campaign.
		 *
		 * @return  WP_Query
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donations() {
			$this->donations = get_transient( self::get_donations_cache_key( $this->ID ) );

			if ( false === $this->donations ) {

				$this->donations = charitable_get_table( 'campaign_donations' )->get_donations_on_campaign( $this->ID );

				set_transient( self::get_donations_cache_key( $this->ID ), $this->donations, 0 );
			}

			return $this->donations;
		}

		/**
		 * Return the current amount of donations.
		 *
		 * @param 	boolean $sanitize Whether to sanitize the amount. False by default.
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donated_amount( $sanitize = false ) {
			$this->donated_amount = get_transient( self::get_donation_amount_cache_key( $this->ID ) );

			if ( false === $this->donated_amount ) {
				$this->donated_amount = charitable_get_table( 'campaign_donations' )->get_campaign_donated_amount( $this->ID );

				set_transient( self::get_donation_amount_cache_key( $this->ID ), $this->donated_amount, 0 );
			}

			$amount = $sanitize ? charitable_sanitize_amount( $this->donated_amount ) : $this->donated_amount;

			return apply_filters( 'charitable_campaign_donated_amount', $amount, $this, $sanitize );
		}

		/**
		 * Return a string describing the campaign's donation summary.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donation_summary() {
			$currency_helper = charitable_get_currency_helper();

			if ( $this->has_goal() ) {
				$ret = sprintf( _x( '%s donated of %s goal', 'amount donated of goal', 'charitable' ),
					'<span class="amount">' . $currency_helper->get_monetary_amount( $this->get_donated_amount() ) . '</span>',
					'<span class="goal-amount">' . $currency_helper->get_monetary_amount( $this->get( 'goal' ) ) . '</span>'
				);
			} else {
				$ret = sprintf( _x( '%s donated', 'amount donated', 'charitable' ),
					'<span class="amount">' . $currency_helper->get_monetary_amount( $this->get_donated_amount() ) . '</span>'
				);
			}

			return apply_filters( 'charitable_donation_summary', $ret, $this );
		}

		/**
		 * Return the percentage donated. Use this if you want a formatted string.
		 *
		 * @return  string|false        String if campaign has a goal. False if no goal is set.
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_percent_donated() {
			$percent = $this->get_percent_donated_raw();

			if ( false === $percent ) {
				return $percent;
			}

			$percent = number_format( $percent, 2 );

			return apply_filters( 'charitable_percent_donated', $percent . '%', $percent, $this );
		}

		/**
		 * Returns the percentage donated as a number.
		 *
		 * @return  int
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_percent_donated_raw() {
			if ( ! $this->has_goal() ) {
				return false;
			}

			return ( $this->get_donated_amount( true ) / $this->get_goal() ) * 100;
		}

		/**
		 * Return the number of people who have donated to the campaign.
		 *
		 * @return  int
		 * @since   1.0.0
		 */
		public function get_donor_count() {
			return apply_filters( 'charitable_campaign_donor_count', charitable_get_table( 'campaign_donations' )->count_campaign_donors( $this->ID ), $this );
		}

		/**
		 * Returns the donation form object.
		 *
		 * @return  Charitable_Donation_Form_Interface
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donation_form() {
			if ( ! isset( $this->donation_form ) ) {

				$form_class = apply_filters( 'charitable_donation_form_class', 'Charitable_Donation_Form', $this );

				$this->donation_form = new $form_class( $this );
			}

			return $this->donation_form;
		}

		/**
		 * Returns the amount to be donated to the campaign as it is currently set in the session.
		 *
		 * @return  int
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_donation_amount_in_session() {
			$donation = charitable_get_session()->get_donation_by_campaign( $this->ID );
			$amount   = is_array( $donation ) ? $donation['amount'] : 0;
			return apply_filters( 'charitable_session_donation_amount', $amount, $this );
		}

		/**
		 * Renders the donate button template.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.0.0
		 */
		public function donate_button_template() {
			if ( $this->has_ended() ) {
				return;
			}

			$display_option = charitable_get_option( 'donation_form_display', 'separate_page' );

			switch ( $display_option ) {
				case 'separate_page' :
					$template_name = 'campaign/donate-button.php';
					break;

				case 'same_page' :
					$template_name = 'campaign/donate-link.php';
					break;

				case 'modal' :
					$template_name = 'campaign/donate-modal.php';
					break;

				default :
					$template_name = apply_filters( 'charitable_donate_button_template', 'campaign/donate-button.php', $this );
			}

			charitable_template( $template_name, array( 'campaign' => $this ) );
		}

		/**
		 * Renders the donate button template.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.2.3
		 */
		public function donate_button_loop_template() {
			if ( $this->has_ended() ) {
				return;
			}

			$display_option = charitable_get_option( 'donation_form_display', 'separate_page' );

			switch ( $display_option ) {
				case 'modal' :
					$template_name = 'campaign-loop/donate-modal.php';
					break;

				default :
					$template_name = apply_filters( 'charitable_donate_button_loop_template', 'campaign-loop/donate-link.php', $this );
			}

			charitable_template( $template_name, array( 'campaign' => $this ) );
		}
		/**
		 * Returns the campaign creator.
		 *
		 * By default, this just returns the user from the post_author field, but
		 * it can be overridden by plugins.
		 *
		 * @return  int $user_id
		 * @access  public
		 * @since   1.0.0
		 */
		public function get_campaign_creator() {
			return apply_filters( 'charitable_campaign_creator', $this->post->post_author, $this );
		}

		/**
		 * Sanitize the campaign goal.
		 *
		 * @param   string  $value
		 * @return  string|int
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function sanitize_campaign_goal( $value ) {
			if ( empty( $value ) || ! $value ) {
				return 0;
			}

			return charitable_get_currency_helper()->sanitize_monetary_amount( $value );
		}

		/**
		 * Sanitize the campaign end date.
		 *
		 * We use WP_Locale to parse the month that the user has set.
		 *
		 * @global 	WP_Locale $wp_locale
		 * @param   string    $value
		 * @return  string|int
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function sanitize_campaign_end_date( $value ) {
			$end_date = charitable_sanitize_date( $value, 'Y-m-d 00:00:00' );

			if ( ! $end_date ) {
				$end_date = 0;
			}

			return $end_date;
		}

		/**
		 * Sanitize the campaign suggested donations.
		 *
		 * @param   array $value
		 * @return  array
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function sanitize_campaign_suggested_donations( $value ) {
			if ( ! is_array( $value ) ) {
				return array();
			}

			$value = array_filter( $value, array( 'Charitable_Campaign', 'filter_suggested_donation' ) );

			if ( empty( $value ) ) {
				return $value;
			}

			foreach ( $value as $key => $suggestion ) {
				$value[ $key ]['amount'] = charitable_sanitize_amount( $suggestion['amount'] );
			}

			return $value;
		}

		/**
		 * Filter out any suggested donations that do not have an amount set.
		 *
		 * @param   array|string $donation
		 * @return  boolean
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function filter_suggested_donation( $donation ) {
			if ( is_array( $donation ) ) {
				return isset( $donation['amount'] ) && ! empty( $donation['amount'] );
			}

			return ! empty( $donation['amount'] );
		}

		/**
		 * Sanitize any checkbox value.
		 *
		 * @param   mixed $value
		 * @return  boolean
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function sanitize_checkbox( $value ) {
			return intval( true == $value || 'on' == $value );
		}

		/**
		 * Sanitize the campaign description.
		 *
		 * @param   string $value
		 * @return  string
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function sanitize_campaign_description( $value ) {
			return sanitize_text_field( $value );
		}

		/**
		 * Sanitize the value provided for custom donations.
		 *
		 * @param   mixed $value
		 * @param   array $submitted
		 * @return  boolean
		 * @access  public
		 * @static
		 * @since   1.3.6
		 */
		public static function sanitize_custom_donations( $value, $submitted ) {
			$checked = self::sanitize_checkbox( $value );

			if ( $checked ) {
				return $checked;
			}

			/* If suggested donations are not set, custom donations needs to be enabled. */
			if ( ! isset( $submitted['_campaign_suggested_donations'] ) ) {
				return 1;
			}

			$suggested_donations = self::sanitize_campaign_suggested_donations( $submitted['_campaign_suggested_donations'] );

			if ( empty( $suggested_donations ) ) {
				return 1;
			}

			return $checked;
		}

		/**
		 * Flush donations cache.
		 *
		 * @param   int     $campaign_id
		 * @return  void
		 * @access  public
		 * @static
		 * @since   1.0.0
		 */
		public static function flush_donations_cache( $campaign_id ) {
			delete_transient( self::get_donations_cache_key( $campaign_id ) );
			delete_transient( self::get_donation_amount_cache_key( $campaign_id ) );
		}

		/**
		 * Returns the key used for caching all donations made to this campaign.
		 *
		 * @param   int $campaign_id The ID of the campaign.
		 * @return  string
		 * @access  private
		 * @static
		 * @since   1.0.0
		 */
		private static function get_donations_cache_key( $campaign_id ) {
			return 'charitable_campaign_' . $campaign_id . '_donations';
		}

		/**
		 * Returns the key used for caching the donation amount for this campaign.
		 *
		 * @param   int $campaign_id The ID of the campaign.
		 * @return  string
		 * @access  private
		 * @static
		 * @since   1.0.0
		 */
		private static function get_donation_amount_cache_key( $campaign_id ) {
			return 'charitable_campaign_' . $campaign_id . '_donation_amount';
		}

		/**
		 * @deprecated Since 1.4.12
		 */
		public static function sanitize_meta( $value, $key, $submitted ) {
			charitable_get_deprecated()->deprecated_function( __METHOD__, '1.4.2' );
			return apply_filters( 'charitable_sanitize_campaign_meta' . $key, $value, $submitted );
		}
	}

endif; // End class_exists check
