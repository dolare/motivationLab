<?php
/**
 * Displays the campaign loop.
 *
 * Override this template by copying it to yourtheme/charitable/campaign-loop.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Campaign
 * @since   1.0.0
 * @version 1.2.3
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

$campaigns = $view_args['campaigns'];
$columns   = $view_args['columns'];
$args      = charitable_campaign_loop_args( $view_args );

if ( ! $campaigns->have_posts() ) :
	return;
endif;

if ( $columns > 1 ) :
	$loop_class = sprintf( 'campaign-loop campaign-grid campaign-grid-%d', $columns );
else :
	$loop_class = 'campaign-loop campaign-list';
endif;

/**
 * @hook charitable_campaign_loop_before
 */
do_action( 'charitable_campaign_loop_before', $campaigns, $args );

?>
<ol class="<?php echo $loop_class ?>">

<?php
while ( $campaigns->have_posts() ) :

	$campaigns->the_post();

	charitable_template( 'campaign-loop/campaign.php', $args );

endwhile;

wp_reset_postdata();
?>
</ol>
<?php

/**
 * @hook charitable_campaign_loop_after
 */
do_action( 'charitable_campaign_loop_after', $campaigns, $args );
