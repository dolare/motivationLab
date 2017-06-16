<?php
/**
 * Campaign categories/tags widget class. 
 *
 * @version     1.0.0
 * @package     Charitable/Widgets/Charitable_Campaign_Terms_Widget
 * @category    Class
 * @author      Eric Daams
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Campaign_Terms_Widget' ) ) : 

/**
 * Charitable_Campaign_Terms_Widget class. 
 *
 * @since       1.0.0
 */
class Charitable_Campaign_Terms_Widget extends WP_Widget {

    /**
     * Instantiate the widget and set up basic configuration.
     * 
     * @access  public
     * @since   1.0.0
     */
    public function __construct() {
        parent::__construct(
            'charitable_campaign_terms_widget', 
            __( 'Campaign Categories / Tags', 'charitable' ), 
            array( 
                'description' => __( 'Displays your Charitable campaign categories or tags.', 'charitable' ),
                // 'customize_selective_refresh' => true 
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
        charitable_template( 'widgets/campaign-terms.php', $view_args );
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
        $title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $taxonomy  = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'campaign_category';
        $show_count = isset( $instance['show_count'] ) && $instance['show_count'];
        $hide_empty  = isset( $instance['hide_empty'] ) && $instance['hide_empty'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title') ?>"><?php _e( 'Title:', 'charitable' ) ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?>" value="<?php echo $title ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('taxonomy') ?>"><?php _e( 'Taxonomy', 'charitable' ) ?></label>
            <select name="<?php echo $this->get_field_name('taxonomy') ?>" id="<?php echo $this->get_field_id('taxonomy') ?>">
                <option value="campaign_category" <?php selected( 'campaign_category', $taxonomy ) ?>><?php _e( 'Categories', 'charitable' ) ?></option>
                <option value="campaign_tag" <?php selected( 'campaign_tag', $taxonomy ) ?>><?php _e( 'Tags', 'charitable' ) ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_count') ?>"><?php _e( 'Show count:', 'charitable' ) ?></label>
            <input type="checkbox" name="<?php echo $this->get_field_name('show_count') ?>" id="<?php echo $this->get_field_id('show_count') ?>" <?php checked( $show_count ) ?> />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('hide_empty') ?>"><?php _e( 'Hide empty:', 'charitable' ) ?></label>
            <input type="checkbox" name="<?php echo $this->get_field_name('hide_empty') ?>" id="<?php echo $this->get_field_id('hide_empty') ?>" <?php checked( $hide_empty ) ?> />
        </p>
        <?php
    }

    /**
     * Update the widget settings in the admin. 
     *
     * @param   array   $new_instance   The updated settings. 
     * @param   array   $new_instance   The old settings. 
     * @return  void
     * @access  public
     * @since   1.0.0
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']  = isset( $new_instance['title'] ) ? $new_instance['title'] : $old_instance['title'];
        $instance['taxonomy'] = isset( $new_instance['taxonomy'] ) ? $new_instance['taxonomy'] : $old_instance['taxonomy'];
        $instance['show_count'] = isset( $new_instance['show_count'] ) && $new_instance['show_count'] == 'on';
        $instance['hide_empty']  = isset( $new_instance['hide_empty'] ) && $new_instance['hide_empty'] == 'on';
        return $instance;
    }   
}

endif; // End class_exists check