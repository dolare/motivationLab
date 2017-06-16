<?php 
/**
 * Displays the campaign content.
 *
 * Override this template by copying it to yourtheme/charitable/content-campaign.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Campaign
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

$campaign = $view_args[ 'campaign' ];
$content = $view_args[ 'content' ];

/**
 * @hook charitable_campaign_content_before
 */
do_action( 'charitable_campaign_content_before', $campaign ); 

echo $content;

/**
 * @hook charitable_campaign_content_after
 */
do_action( 'charitable_campaign_content_after', $campaign );