<?php
/**
 * Display a widget with donors, either for a specific campaign or sitewide.
 *
 * Override this template by copying it to yourtheme/charitable/widgets/donors.php
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! charitable_is_campaign_page() && 'current' == $view_args[ 'campaign_id' ] ) {
    return;
}

$widget_title   = apply_filters( 'widget_title', $view_args['title'] );
$donors         = $view_args[ 'donors' ];

$campaign_id = $view_args[ 'campaign_id' ];

if ( 'all' == $view_args[ 'campaign_id' ] ) {
    $campaign_id = false;
}

if ( 'current' == $view_args[ 'campaign_id' ] ) {
    $campaign_id = get_the_ID();
}

/* If there are no donors and the widget is configured to hide when empty, return now. */
if ( ! $donors->count() && $view_args[ 'hide_if_no_donors' ] ) {
    return;
}

echo $view_args['before_widget'];

if ( ! empty( $widget_title ) ) :
    echo $view_args['before_title'] . $widget_title . $view_args['after_title'];
endif;

if ( $donors->count() ) :
    ?>
    
    <ol class="donors-list">

        <?php foreach ( $donors as $donor ) : ?>

            <li class="donor">  

                <?php 

                echo ''.$donor->get_avatar();
                
                if ( $view_args[ 'show_name'] ) : ?>

                    <p class="donor-name"><?php echo esc_attr($donor->get_name()); ?></p>

                <?php 

                endif;

                if ( $view_args[ 'show_location' ] && strlen( $donor->get_location() ) ) : ?>

                    <div class="donor-location"><?php echo esc_attr($donor->get_location()); ?></div>

                <?php 

                endif;

                if ( $view_args[ 'show_amount' ] ) : ?>

                    <div class="donor-donation-amount"><?php echo charitable_format_money( $donor->get_amount( $campaign_id ) ) ?></div>

                <?php endif ?>

            </li>

        <?php endforeach ?>

    </ol>

<?php
else : 

    ?>

    <p><?php esc_html_e( 'No donors yet. Be the first!', 'borntogive' ) ?></p>

    <?php

endif;

echo $view_args['after_widget'];