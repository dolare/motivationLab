<?php
/**
 * Donation stats widget class. 
 *
 * @package     Charitable/Classes/Charitable_Donation_Stats_Widget
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donation_Stats_Widget' ) ) : 

/**
 * Charitable_Donation_Stats_Widget class. 
 *
 * @since       1.0.0
 */
class Charitable_Donation_Stats_Widget extends WP_Widget {

    /**
     * Instantiate the widget and set up basic configuration.
     * 
     * @access  public
     * @since   1.0.0
     */
    public function __construct() {
        parent::__construct(
            'charitable_donation_stats_widget', 
            __( 'Donation Stats', 'charitable' ),             
            array( 
                'description' => __( 'Show off your donation statistics.', 'charitable' ),
                'customize_selective_refresh' => true 
            )
        );
    }

    /**
     * Display the widget contents on the front-end. 
     *
     * @param   array $args
     * @param   array $instance
     * @access  public 
     * @since   1.0.0
     */
    public function widget( $args, $instance ) {
        $view_args = array_merge( $args, $instance );

        if ( ! isset( $view_args[ 'title' ] ) ) {
            $view_args[ 'title' ] = __( 'Donation Statistics', 'charitable' );
        }

        charitable_template( 'widgets/donation-stats.php', $view_args );
    }    

    /**
     * Display the widget form in the admin.
     *
     * @param   array $instance         The current settings for the widget options. 
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function form( $instance ) {
        $title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'charitable' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' )  ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    /**
     * Update the widget settings in the admin. 
     *
     * @param   array $new_instance         The updated settings. 
     * @param   array $new_instance         The old settings. 
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance[ 'title' ] = isset( $new_instance[ 'title' ] ) ? strip_tags( $new_instance[ 'title' ] ) : $old_instance[ 'title' ];        
        return $instance;
    }
}

endif; // End class_exists check