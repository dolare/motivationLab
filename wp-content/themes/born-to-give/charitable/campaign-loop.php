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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$campaigns = $view_args[ 'campaigns' ];
$columns = $view_args[ 'columns' ];
$columns = explode('-', $columns);
$carousel = (isset($columns[1]))?$columns[1]:'';
$filters = (isset($columns[2]))?$columns[2]:'';
$excerpt = (isset($columns[3]))?$columns[3]:'';
$pagination = (isset($columns[4]))?$columns[4]:'';
$number = (isset($columns[5]))?$columns[5]:'';
if(is_tax( 'campaign_category' )){
	$number = get_option('posts_per_page');
}
$columns = $columns[0];
$args = charitable_campaign_loop_args( $view_args );
//echo $campaigns->found_posts;
if ( ! $campaigns->have_posts() ) :
    return;
endif;
$paged = (get_query_var('paged'))?get_query_var('paged'):1;
if ( $columns > 1 ) :
    $loop_class = sprintf( 'campaign-loop campaign-grid campaign-grid-%d', $columns );
else : 
    $loop_class = 'campaign-loop campaign-list';
endif;

/**
 * @hook charitable_campaign_loop_before
 */
do_action( 'charitable_campaign_loop_before', $campaigns, $args );
$terms = get_terms('campaign_category');
if($filters==1&&!empty($terms))
{
	echo '<div class="grid-filter">
                    <ul class="nav nav-pills sort-source" data-sort-id="gallery" data-option-key="filter">
                        <li data-option-value="*" class="active"><a href="#"><i class="fa fa-th"></i> <span>Show All</span></a></li>';
												foreach($terms as $term)
												{
                        	echo '<li data-option-value=".campaign_category-'.$term->slug.'"><a href="#"><span>'.$term->name.'</span></a></li>';
												}
                    echo '</ul>
                </div>';
}
if($excerpt!='1')
{
	echo '<style>
			.campaign-description{
				display:none;
			}
			.cause-grid-item .campaign-description-wrapper {
    		padding: 0 30px;
			}
	</style>';
}
if($carousel=='carousel') {
?>
<div class="carousel-wrapper">
<div class="row">
<ul class="owl-carousel carousel-fw" id="causes-slider" data-columns="<?php echo esc_attr($columns); ?>" data-autoplay="" data-pagination="no" data-arrows="yes" data-single-item="no" data-items-desktop="4" data-items-desktop-small="3" data-items-tablet="2" data-items-mobile="1" <?php if ( is_rtl() ) { ?>data-rtl="rtl"<?php } else { ?> data-rtl="ltr" <?php } ?>>

<?php }
elseif($carousel=='list')
{
	echo '<ul class="sort-destination causes-list cause-page-listing" data-sort-id="gallery">';
}
elseif($carousel=="grid")
{
	echo '<div class="row"><ul class="sort-destination isotope gallery-items" data-sort-id="gallery">';
}
$counter = 1;
while( $campaigns->have_posts() ) : 
$campaigns->the_post();
$current_events = $paged*$number;
			$start_page = ($paged!=1)?$paged-1:0;
			$start_page = $start_page*$number;
			if($counter>$start_page&&$counter<=$current_events)
			{
    
		
    charitable_template( 'campaign-loop/campaign.php', $args );
			}
			$counter++;
		if($counter>$current_events)
		{
			break;
		}
endwhile;
echo '</ul>';
wp_reset_postdata();
$pages_total = $campaigns->found_posts/$number;
$pages_total_new = floor($pages_total);
if($pages_total>$pages_total_new)
{
	$pages_total_new = $pages_total_new+1;
}
else
{
	$pages_total_new = $pages_total_new;
}
if($pagination==1)
{
	echo borntogive_pagination($pages_total_new, $campaigns->found_posts, $paged);
} elseif(is_tax( 'campaign_category' )){
	echo borntogive_pagination($pages_total_new, $campaigns->found_posts, $paged);
}
?>
<?php
if($carousel=='carousel')
{
	echo '</div></div>';
}elseif($carousel=='grid')
{
	echo '</div>';
}
/**
 * @hook charitable_campaign_loop_after
 */
do_action( 'charitable_campaign_loop_after', $campaigns, $args );