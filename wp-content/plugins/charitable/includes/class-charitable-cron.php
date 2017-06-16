<?php
/**
 * Charitable Events
 *
 * @package     Charitable/Classes/Charitable_Cron
 * @version     1.1.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Cron' ) ) : 

/**
 * Charitable_Cron
 *
 * @since       1.1.0
 */
class Charitable_Cron {

    /**
     * The single instance of this class.  
     *
     * @var     Charitable_Cron|null
     * @access  private
     * @static
     */
    private static $instance = null;  

    /**
     * Returns and/or create the single instance of this class.  
     *
     * @return  Charitable_Cron
     * @access  public
     * @since   1.2.0
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Charitable_Cron();
        }

        return self::$instance;
    }

    /**
     * Create class object.
     * 
     * @access  private
     * @since   1.1.0
     */
    private function __construct() {
        add_action( 'charitable_daily_scheduled_events', array( $this, 'check_expired_campaigns' ) );
    }

    /**
     * Schedule Charitable event hooks. 
     *
     * @return  void
     * @access  public
     * @static
     * @since   1.1.0
     */
    public static function schedule_events() {        
        if ( ! wp_next_scheduled( 'charitable_daily_scheduled_events' ) ) {
            wp_schedule_event( time(), 'daily', 'charitable_daily_scheduled_events' );
        }
    }

    /**
     * Check for expired campaigns. 
     *
     * @return  void
     * @access  public
     * @since   1.1.0
     */
    public function check_expired_campaigns() {
        $yesterday = date( 'Y-m-d H:i:s', strtotime( '-24 hours' ) );

        $args = array(
            'fields' => 'ids',
            'post_type' => Charitable::CAMPAIGN_POST_TYPE,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key'       => '_campaign_end_date',
                    'value'     => array( $yesterday, date( 'Y-m-d H:i:s' ) ),
                    'compare'   => 'BETWEEN',
                    'type'      => 'datetime'
                )
            )
        );

        $campaigns = get_posts( $args );

        if ( empty( $campaigns ) ) {
            return;
        }

        foreach ( $campaigns as $campaign_id ) {
            do_action( 'charitable_campaign_end', $campaign_id );
        }
    }
}

endif; // End class_exists check