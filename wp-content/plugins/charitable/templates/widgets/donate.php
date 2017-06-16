<?php
/**
 * Display a widget with a link to donate to a campaign.
 *
 * Override this template by copying it to yourtheme/charitable/widgets/donate.php
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! charitable_is_campaign_page() && 'current' == $view_args['campaign_id'] ) {
	return;
}

$widget_title = apply_filters( 'widget_title', $view_args['title'] );
$campaign_id  = 'current' == $view_args['campaign_id'] ? get_the_ID() : $view_args['campaign_id'];
$campaign     = charitable_get_campaign( $campaign_id );

if ( $campaign->has_ended() ) {
	return;
}

$suggested_donations = $campaign->get_suggested_donations();

if ( empty( $suggested_donations ) && ! $campaign->get( 'allow_custom_donations' ) ) {
	return;
}

echo $view_args['before_widget'];

if ( ! empty( $widget_title ) ) :
	echo $view_args['before_title'] . $widget_title . $view_args['after_title'];
endif;

$form = new Charitable_Donation_Amount_Form( $campaign );
$form->render();

echo $view_args['after_widget'];
