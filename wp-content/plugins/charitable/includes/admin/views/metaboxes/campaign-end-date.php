<?php
/**
 * Renders the end date field for the Campaign post type.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $post;

$title 				= isset( $view_args['title'] ) 		? $view_args['title'] 		: '';
$tooltip 			= isset( $view_args['tooltip'] )	? '<span class="tooltip">' . $view_args['tooltip'] . '</span>'					: '';
$description		= isset( $view_args['description'] )? '<span class="charitable-helper">' . $view_args['description'] . '</span>' 	: '';
$end_date 			= get_post_meta( $post->ID, '_campaign_end_date', true );
$end_date_formatted = 0 == $end_date ? '' : date_i18n( 'F d, Y', strtotime( $end_date ) );
?>
<div id="charitable-campaign-end-date-metabox-wrap" class="charitable-metabox-wrap">
	<label class="screen-reader-text" for="campaign_end_date"><?php echo $title ?></label>
	<input type="text" id="campaign_end_date" name="_campaign_end_date"  placeholder="&#8734;" class="charitable-datepicker" data-date="<?php echo $end_date_formatted ?>" />
	<?php echo $description ?>
</div>
