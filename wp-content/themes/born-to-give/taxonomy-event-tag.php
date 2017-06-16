<?php
get_header();
$borntogive_options = get_option('borntogive_options');
$event_meta_show = $borntogive_options['event_meta_date'];
$sidebar = $borntogive_options['event_archive_sidebar'];
if($sidebar!=''&&is_active_sidebar($sidebar))
{
	$class = 8;
}
else
{
	$class = 12;
}
$default_header = (isset($borntogive_options['borntogive_default_banner']['url']))?$borntogive_options['borntogive_default_banner']['url']:'';
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
$term_banner_image = get_option($term->taxonomy . $term->term_id . "_term_banner");
if($term_banner_image != ''){
	$banner_image = $term_banner_image;
}
else
{
	$banner_image = $default_header;
}
$event_number = 10;
$counter = 1;
$paged = (get_query_var('paged'))?get_query_var('paged'):1;
?>
<div class="hero-area">
    	<div class="page-banner parallax" style="background-image:url(<?php echo esc_url($banner_image); ?>);">
        	<div class="container">
            	<div class="page-banner-text">
        			<h1 class="block-title"><?php single_cat_title(); ?></h1>
                </div>
            </div>
        </div>
    </div>
<?php
echo '<div id="main-container">
  	<div class="content">
   		<div class="container">
       		<div class="row">
					<div class="col-md-'.$class.'">';
					$events = borntogive_recur_events('future', $term->term_id);
					ksort($events);
					$event_output = '<ul class="events-compact-list">';
					if(!empty($events))
					{
					foreach($events as $key=>$value)
					{
						$current_events = $paged*$event_number;
			$start_page = ($paged!=1)?$paged-1:0;
			$start_page = $start_page*$event_number;
			if($counter>$start_page&&$counter<=$current_events)
			{
			$event_start_date = get_post_meta($value, 'borntogive_event_start_dt', true);
			$event_end_date = get_post_meta($value, 'borntogive_event_end_dt', true);
			$event_start_date_unix = strtotime($event_start_date);
			$event_end_date_unix = strtotime($event_end_date);
			$permalink = borntogive_event_arg(date('Y-m-d', $key), $value);
			$days_total = borntogive_dateDiff($event_start_date, $event_end_date);
						$event_output .= '<li class="event-list-item">	
                              	<span class="event-date">
                               	<span class="date">'.esc_attr(date_i18n('d', $key)).'</span>
                                 <span class="month">'.esc_attr(date_i18n('M', $key)).'</span>
                                 <span class="year">'.esc_attr(date_i18n('Y', $key)).'</span>
          				             </span>
                              	<div class="event-list-cont">';
																				
      			if($event_meta_show==1)
						{
							if($days_total>=1)
							{
								$event_output .= '<span class="meta-data">'.esc_attr(date_i18n(get_option('date_format'), $event_start_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix)).'</span>';
							}
							else
							{
								$event_output .= '<span class="meta-data">'.esc_attr(date_i18n('l', $key)).', '.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix)).'</span>';
							}
						}
						else
						{
							if($days_total>=1)
							{
								$event_output .= '<span class="meta-data">'.esc_attr(date_i18n(get_option('date_format'), $event_start_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix));
								if($event_end_date_unix!='')
								{
									$event_output .= ' - '.esc_attr(date_i18n(get_option('date_format'), $event_end_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_end_date_unix));
								}
								$event_output .= '</span>';
							}
							else
							{
								$event_output .= '<span class="meta-data">'.esc_attr(date_i18n('l', $key)).', '.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix));
								if($event_end_date_unix!='')
								{
									$event_output .= ' - '.esc_attr(date_i18n(get_option('time_format'), $event_end_date_unix));
								}
								$event_output .= '</span>';
							}
						}
    				$event_output .= '<h4 class="post-title"><a href="'.esc_url($permalink).'">'.get_the_title($value).'</a></h4>';
						$event_output .= wp_trim_words(borntogive_post_excerpt_by_id($value), 20);
      			$event_output .= '</div></li>';
			} $counter++;
					}
					}
					else
					{
						$event_output .= '<li>'.esc_html__('Sorry, no event found in this category', 'borntogive').'</li>';
					}
					$pages_total = count($events)/$event_number;
					$pages_total = floor($pages_total);
					$event_output .= borntogive_pagination($pages_total, $event_number, $paged); 
					$event_output .= '</ul>';
					echo ''.$event_output;
echo '</div>';
if($sidebar!=''&&is_active_sidebar($sidebar))
{
	echo '<div class="col-md-4">';
	dynamic_sidebar($sidebar);
	echo '</div>';
}
echo '</div></div></div></div>';
?>
<?php
get_footer();
?>