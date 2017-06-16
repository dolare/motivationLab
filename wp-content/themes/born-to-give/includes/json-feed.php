<?php
// - standalone json feed -
header('Content-Type:application/json');
// - grab wp load, wherever it's hiding -
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
// - grab date barrier -
//$today6am = strtotime('today 6:00') + ( get_option( 'gmt_offset' ) * 3600 );
$today = date('Y-m-d');
$event_cat_id = '';
if (isset($_POST['event_cat_id'])&&!empty($_POST['event_cat_id'])){
  $event_cat_id = $_POST['event_cat_id'];
	 }
	$month_event = $_POST['month_event'];
$offset = get_option('timezone_string');
		if($offset=='') { $offset = "Australia/Melbourne"; }
	date_default_timezone_set($offset);
	$borntogive_options = get_option('borntogive_options');
	//$month_event = $_POST['month_event'];
if($month_event>=date('Y-m-01'))
{
	$events = borntogive_recur_events_future('future', $event_cat_id);
	ksort($events);
}
else
{
	$events = borntogive_recur_events_past($event_cat_id);
	krsort($events);
}
	if(!empty($events))
	{
		foreach($events as $key=>$value)
		{
			$frequency = get_post_meta($value,'borntogive_event_frequency',true);
			$cat_id = wp_get_post_terms( $value, 'event-category', array('orderby' => 'name', 'order' => 'DESC', 'fields' => 'all') );
			$event_color = '';
			if(!empty($cat_id)) 
			{
				foreach($cat_id as $cat_id_single) {
					$cat_id_single_id = $cat_id_single->term_id; //do something here
					$cat_data = get_option("category_".$cat_id_single_id);
				}
				$event_color = ($cat_data['catBG']!='')?$cat_data['catBG']:$borntogive_options['event_default_color'];
			}
			if($frequency>0) 
			{ 
				$color = ($cat_data['catBG']!='')?$cat_data['catBG']:$borntogive_options['recurring_event_color'];  
				if($color == ''){
					$color = $borntogive_options['event_default_color'];
				}
			} 
			else 
			{ 
				$color = $event_color; 
			}
			$event_start_date = get_post_meta($value, 'borntogive_event_start_dt', true);
			$event_end_date = get_post_meta($value, 'borntogive_event_end_dt', true);
			$event_start_date_unix = strtotime($event_start_date);
			$event_end_date_unix = strtotime($event_end_date);
			$days_total = borntogive_dateDiff($event_start_date, $event_end_date);
			if($days_total<=0)
			{
				$event_start_date_unix = $key;
			}
      $stime = date('c', $event_start_date_unix);
      $etime = date('c', $event_end_date_unix);
		  // - json items -
		  $jsonevents[]= array(
			'id' => $post->ID,
		  'title' => get_the_title($value),
		  'allDay' => false, // <- true by default with FullCalendar
		  'start' => $stime,
		  'end' => $etime,
		  'url' => esc_url(borntogive_event_arg(date('Y-m-d', $key), $value)),
			'backgroundColor' => $color,
			'borderColor' => $color
		        );
		//$fr_repeat++; 
		}
	}
// - fire away -
echo json_encode($jsonevents);