<?php
/**
 * The template for displaying campaign content within loops.
 *
 * Override this template by copying it to yourtheme/charitable/campaign-loop/campaign.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Campaign
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$campaigns = $view_args[ 'campaigns' ];
$columns = $view_args[ 'columns' ];
$columns = explode('-', $columns);
$carousel = $columns[1];
$filters = $columns[2];
$excerpt = (isset($columns[3]))?$columns[3]:'';
$columns = $columns[0];
$campaign = charitable_get_current_campaign();
if($carousel=='carousel')
{
	$list_class = 'item';
}
elseif($carousel=='list')
{
	$list_class = 'causes-list-item cause-item grid-item';
}
else
{
	$list_class = 'col-md-'.$columns.' grid-item cause-grid-item format-standard isotope-item';
}
?>
<li id="campaign-<?php echo get_the_ID() ?>" <?php post_class($list_class) ?>>
<?php 
if($carousel=='carousel') {
	echo '<div class="grid-item cause-grid-item format-standard">
<div class="grid-item-inner">';
}
elseif($carousel=='list') {
	echo '<div class="list-item-inner">';
}
else{
	echo '<div class="grid-item-cont">';
}
    /**
     * @hook charitable_campaign_content_loop_before
     */
    do_action( 'charitable_campaign_content_loop_before', $campaign, $view_args ); 
    
    ?>
    <a href="<?php the_permalink() ?>" class="media-box"> 
        <?php
            /**
             * @hook charitable_campaign_content_loop_before_title
             */
            do_action( 'charitable_campaign_content_loop_before_title', $campaign, $view_args );
        ?>

        <?php
            /**
             * @hook charitable_campaign_content_loop_after_title
             */
            do_action( 'charitable_campaign_content_loop_after_title', $campaign, $view_args );
        ?>
    </a>
    <?php
if($carousel=='carousel') {
	echo '<div class="cause-item-container">';
}
elseif($carousel=='list') {
	echo '<div class="cause-item-container">';
}
else{
	echo '<div class="cause-item-container">';
}

    /**
     * @hook charitable_campaign_content_loop_after
     */
    do_action( 'charitable_campaign_content_loop_after', $campaign, $view_args );
		if($carousel=='carousel')
			{
				echo '</div>
				</div></div>';
			}
			elseif($carousel=='list')
			{
				echo '</div></div>';
			}
			else{
				echo '</div></div>';
			}
?>
</li>