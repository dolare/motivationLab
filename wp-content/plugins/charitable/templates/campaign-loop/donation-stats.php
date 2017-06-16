<?php
/**
 * Displays the campaign donation stats.
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * @var 	Charitable_Campaign
 */
$campaign = $view_args['campaign'];

?>
<div class="campaign-donation-stats">  
	<?php echo $campaign->get_donation_summary() ?>
</div>
