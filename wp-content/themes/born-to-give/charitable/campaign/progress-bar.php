<?php 
/**
 * Displays the campaign progress bar.
 *
 * Override this template by copying it to yourtheme/charitable/campaign/progress-bar.php
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @var Charitable_Campaign
 */
$campaign = $view_args[ 'campaign' ];

if ( ! $campaign->has_goal() ) :
    return;
endif;
if($campaign->get_percent_donated_raw()<=30)
{
	$color = 'F23827';
}
elseif($campaign->get_percent_donated_raw()>30&&$campaign->get_percent_donated_raw()<=60)
{
	$color = 'F6bb42';
}
else
{
	$color = '8cc152';
}
?>
<a class="cProgress" data-original-title="<?php echo strip_tags($campaign->get_time_left()); ?>" data-toggle="tooltip" data-color="<?php echo esc_attr($color); ?>" data-complete="<?php echo esc_attr($campaign->get_percent_donated_raw()); ?>">
<strong>
<?php echo esc_attr($campaign->get_percent_donated_raw()); ?>
</strong>
</a>