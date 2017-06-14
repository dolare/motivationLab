 <?php
/*Front end view of causes element
==================================*/
function borntogive_causes_element_output( $atts ) {
 
/* These arguments are going to function like variables, allowing us to set new values in the front-end editor */
        extract(shortcode_atts(array(
 'causes_view' => 'list',
 'causes_grid_column' => '1',
 'causes_number' => '5',
 'causes_orderby' => 'post_date',
 'causes_order' => 'DESC',
 'causes_terms' => '',
 'causes_ids' => '',
 'causes_users' => '',
 'causes_exclude' => '',
 'causes_inactive' => 0,
 'causes_carousel' => '0',
 'causes_filters' => '',
 'show_causes_excerpt' => 1,
 'show_causes_pagination' => 0,
 ),$atts));
 $output = ($causes_view=="list")?'<div class="causes-list"><ul>':'';
 $output_close = '';
 if($causes_view=="carousel")
 {
	 if($causes_grid_column==6)
	 {
		 $causes_grid_column = 2;
	 }
	 elseif($causes_grid_column==4)
	 {
		 $causes_grid_column = 3;
	 }
	 elseif($causes_grid_column==3)
	 {
		 $causes_grid_column = 4;
	 }
	 elseif($causes_grid_column==12)
	 {
		 $causes_grid_column = 1;
	 }
	 else
	 {
		 $causes_grid_column = 6;
	 }
 }
 if($show_causes_pagination==0)
 {
	 $posts_per_page = $causes_number;
 }
 else
 {
	 $posts_per_page = -1;
 }
 //$output_close = ($causes_grid_column>1)?'</div>':'';
 $output_close .= ($causes_view=="list")?'</ul></div>':'';
 return $output.
 do_shortcode('[campaigns number="'.$posts_per_page.'" order="'.$causes_order.'" orderby="'.$causes_orderby.'" creator="'.$causes_users.'" include_inactive="'.$causes_inactive.'" id="'.$causes_ids.'" exclude="'.$causes_exclude.'" category="'.$causes_terms.'" columns="'.$causes_grid_column.'-'.$causes_view.'-'.$causes_filters.'-'.$show_causes_excerpt.'-'.$show_causes_pagination.'-'.$causes_number.'"]').
 $output_close;
 
}
 
add_shortcode( 'borntogive_causes', 'borntogive_causes_element_output' );
/*Front end view of events element
==================================*/
function borntogive_event_element_output( $atts, $content = null) {
		extract(shortcode_atts(array(
		'event_type' => 'future',
		'event_view' => 'list',
		'event_grid_column' => 4,
		'event_pagination' => '',
		'event_terms' => '',
		'event_number' => 5,
		'img_size' => '',
 		'show_events_excerpt' => 1,
		'event_filters' => 0
		),$atts));
	$borntogive_options = get_option('borntogive_options');
	$event_meta_show = $borntogive_options['event_meta_date'];
	$multi_date_separator = (isset($borntogive_options['multi_date_separator']))?$borntogive_options['multi_date_separator']:'';
	$event_date_separator = $borntogive_options['event_multi_separator'];
	if($event_terms!='')
	{
		$terms = explode(',', $event_terms);
	}
	else
	{
		$terms = array();
	}
	$event_output = '';
	if($event_type=="future")
	{
		$events = borntogive_recur_events($event_type, $terms);
		ksort($events);
	}
	else
	{
		$events = borntogive_recur_events_past($terms);
		krsort($events);
	}
	
	if($event_filters==1&&$event_view=="grid")
			{
				$event_terms = get_terms('event-category');
				if(!is_wp_error($event_terms))
				{
					$event_output .= '<div class="grid-filter">
				<ul class="nav nav-pills sort-source" data-sort-id="gallery" data-option-key="filter">
					<li data-option-value="*" class="active"><a href="#"><i class="fa fa-th"></i> <span>'.esc_html__('Show All', 'borntogive-vc').'</span></a></li>';
											foreach($event_terms as $term)
											{
						$event_output .= '<li data-option-value=".term-'.$term->term_id.'"><a href="#"><span>'.$term->name.'</span></a></li>';
											}
				$event_output .= '</ul>
			</div>';
				}
			}
	if($event_view=="list")
	{
		$event_output .= '<ul class="events-compact-list">';
	}
	else
	{
		$event_output .= '<div class="row">
                    <ul class="sort-destination isotope gallery-items" data-sort-id="gallery">';
	}
	$paged = (get_query_var('paged'))?get_query_var('paged'):1;
	if(!empty($events))
	{
		$counter = 1;
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
			// Events List View
			if($event_view=="list")
			{
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
					$event_output .= '<span class="meta-data">'.esc_attr(date_i18n('l', $key)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix)).'</span>';
				}
			}
			else
			{
				if($days_total>=1)
				{
					$event_output .= '<span class="meta-data">'.esc_attr(date_i18n(get_option('date_format'), $event_start_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix));
					if($event_end_date_unix!='')
					{
						$event_output .= $multi_date_separator.esc_attr(date_i18n(get_option('date_format'), $event_end_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_end_date_unix));
					}
					$event_output .= '</span>';
				}
				else
				{
					$event_output .= '<span class="meta-data">'.esc_attr(date_i18n('l', $key)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix));
					if($event_end_date_unix!='')
					{
						$event_output .= '-'.esc_attr(date_i18n(get_option('time_format'), $event_end_date_unix));
					}
					$event_output .= '</span>';
				}
			}
    	$event_output .= '<h4 class="post-title"><a href="'.esc_url($permalink).'">'.get_the_title($value).'</a></h4>';
		if($show_events_excerpt == 1 || $show_events_excerpt == ''){
			$event_output .= wp_trim_words(borntogive_post_excerpt_by_id($value), 20);
		}
      $event_output .= '</div>
                                    </li>';
			}
			// Events Grid View
			else
			{
				$cat_class = '';
				$event_cats = wp_get_object_terms($value, 'event-category');
				if(!is_wp_error($event_cats))
				{
					foreach($event_cats as $cat)
					{
						$cat_class .= ' term-'.$cat->term_id;
					}
				}
				$thumbnail = '';
				if($img_size != ''){
					$post_thumbnail = wpb_getImageBySize( array('post_id' => $value,'thumb_size' => $img_size) );
					$thumbnail = $post_thumbnail['thumbnail'];
				} else {
					$post_thumbnail = get_the_post_thumbnail($value,'borntogive-600x400');
					$thumbnail = $post_thumbnail;
				}
        		$event_output .= '<li class="col-md-'.$event_grid_column.' col-sm-6 grid-item event-grid-item '.$cat_class.' format-standard">
                        	<div class="grid-item-inner">';
													if(has_post_thumbnail($value))
													{
                          		$event_output .= '<a href="'.esc_url($permalink).'" class="media-box">';
															$event_output .= $thumbnail;
                              	$event_output .= '</a>';
													}
                         $event_output .= '<div class="grid-item-content">
                                    	<span class="event-date">
                                        	<span class="date">'.esc_attr(date_i18n('d', $key)).'</span>
                                            <span class="month">'.esc_attr(date_i18n('M', $key)).'</span>
                                            <span class="year">'.esc_attr(date_i18n('Y', $key)).'</span>
                                        </span>';
													if($event_meta_show==1)
														{
															if($days_total>=1)
															{
																$event_output .= '<span class="meta-data">'.esc_attr(date_i18n(get_option('date_format'), $event_start_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix)).'</span>';
															}
															else
															{
																$event_output .= '<span class="meta-data">'.esc_attr(date_i18n('l', $key)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix)).'</span>';
															}
														}
														else
														{
															if($days_total>=1)
															{
																$event_output .= '<span class="meta-data">'.esc_attr(date_i18n(get_option('date_format'), $event_start_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix));
																if($event_end_date_unix!='')
																{
																	$event_output .= $multi_date_separator.esc_attr(date_i18n(get_option('date_format'), $event_end_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_end_date_unix));
																}
																$event_output .= '</span>';
															}
															else
															{
																$event_output .= '<span class="meta-data">'.esc_attr(date_i18n('l', $key)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix));
																if($event_end_date_unix!='')
																{
																	$event_output .= '-'.esc_attr(date_i18n(get_option('time_format'), $event_end_date_unix));
																}
																$event_output .= '</span>';
															}
														}
													$attendees = get_post_meta($value, 'borntogive_event_attendees', true);
													$address = get_post_meta($value, 'borntogive_event_address', true);
                         $event_output .= '<h3 class="post-title"><a href="'.esc_url($permalink).'">'.get_the_title($value).'</a></h3>';
		if($show_events_excerpt == 1){
			$event_output .= '<div class="spacer-10"></div><p>'.wp_trim_words(borntogive_post_excerpt_by_id($value), 20).'</p>';
		}
												 if($attendees!=''||$address!='')
												 {
                                    $event_output .= '<ul class="list-group">';
														if($attendees!='')
														{
                                        $event_output .= '<li class="list-group-item">'.$attendees.'<span class="badge">'.esc_html__('Attendees', 'borntogive').'</span></li>';
														}
														if($address!='')
														{
                                        $event_output .= '<li class="list-group-item">'.$address.'<span class="badge">'.esc_html__('Location', 'borntogive').'</span></li>';
														}
                                    $event_output .= '</ul>';
												 }
                                $event_output .= '</div>
                           	</div>
                        </li>';
			}
			
			
		}
		$counter++;
		if($counter>$current_events)
		{
			break;
		}
		}
		
	}
	else
	{
		
	}
	$event_output .= '</ul>';
	if($event_view=="grid")
	{
		$event_output .= '</div>';
	}
	$pages_total = count($events)/$event_number;
	$pages_total = floor($pages_total);
	if($event_pagination=="1")
	{
		$event_output .= '<div class="clearfix"></div>';
		$event_output .= borntogive_pagination($pages_total, $event_number, $paged); 
	}
	
	return $event_output;
}
 
add_shortcode( 'borntogive_events', 'borntogive_event_element_output' );
/*Front end view of gallery element
==================================*/
function borntogive_gallery_element_output( $atts, $content = null) {
		extract(shortcode_atts(array(
		'gallery_title' => '',
		'gallery_type' => 'frame',
		'gallery_grid_column' => '4',
		'gallery_caption' => '',
		'gallery_filters' => '',
		'gallery_terms' => '',
		'gallery_number' => 6,
		'img_size' => '',
		'gallery_pagination' => ''
		),$atts));
	$gallery_output = '';
	if($gallery_terms!='')
	{
		$terms = explode(',', $gallery_terms);
		$gallery_args = array('post_type'=>'gallery', 'posts_per_page'=>$gallery_number, 'tax_query'=>array(array('taxonomy'=>'gallery-category', 'field'=>'term_id', 'terms'=>$terms, 'operator'=>'IN')));
	}
	else
	{
		$gallery_args = array('post_type'=>'gallery', 'posts_per_page'=>$gallery_number, 'paged' => get_query_var('paged'));
	}
	$gallery_list = new WP_Query($gallery_args);
	if($gallery_type=="frame")
	{
	$gallery_output .= '<div class="gallery-updates cols2 clearfix">';
	$gallery_output .= '<ul>';
	if($gallery_list->have_posts()):while($gallery_list->have_posts()):$gallery_list->the_post();
	$gallery_format = get_post_format();
	$feat_image_url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
	$thumbnail = '';
	if($img_size != ''){
		$post_thumbnail = wpb_getImageBySize( array('post_id' => get_the_ID(),'thumb_size' => $img_size) );
		$thumbnail = $post_thumbnail['thumbnail'];
	} else {
		$post_thumbnail = get_the_post_thumbnail(get_the_ID(),'borntogive-600x400');
		$thumbnail = $post_thumbnail;
	}
	if($gallery_format=="image")
	{
		$gallery_output .= '<li class="format-'.$gallery_format.' grid-item">
                     	<a href="'.esc_url($feat_image_url).'" class="media-box magnific-image"> '.$thumbnail.' </a>
                 			</li>';
	}
	elseif($gallery_format=="link")
	{
		$link = get_post_meta(get_the_ID(), 'borntogive_gallery_link_url', true);
		$gallery_output .= '<li class="format-'.$gallery_format.' grid-item">
                     	<a href="'.esc_url($link).'" class="media-box" target="_blank"> '.$thumbnail.' </a>
                 			</li>';
	}
	elseif($gallery_format=="video")
	{
		$video = get_post_meta(get_the_ID(), 'borntogive_gallery_video_url', true);
		$gallery_output .= '<li class="format-'.$gallery_format.' grid-item">
                     	<a href="'.esc_url($video).'" class="media-box magnific-video"> '.$thumbnail.' </a>
                 			</li>';
	}
	elseif($gallery_format=="gallery")
	{
		$image_data = get_post_meta(get_the_ID(), 'borntogive_gallery_images', false);
		$gallery_output .= '<li class="format-gallery grid-item">
                   			<div class="media-box">';
    $gallery_output .= borntogive_gallery_flexslider(get_the_ID());
    $gallery_output .= '<ul class="slides">';
		foreach ($image_data as $custom_gallery_images) 
		{
			$large_src = wp_get_attachment_image_src($custom_gallery_images, 'full');
			$gallery_output .= '<li class="item"><a href="' . esc_url($large_src[0]) . '" class="popup-image">';
			$thumbnail = '';
			if($img_size != ''){
				$post_thumbnail = wpb_getImageBySize( array('attach_id' => $custom_gallery_images,'thumb_size' => $img_size) );
				$thumbnail = $post_thumbnail['thumbnail'];
			} else {
				$post_thumbnail = wp_get_attachment_image($custom_gallery_images, 'borntogive-600x400');
				$thumbnail = $post_thumbnail;
			}
			$gallery_output .= $thumbnail;
			$gallery_output .= '</a></li>';
		}
    $gallery_output .= '</ul>
             							</div>
            						</div>
           						</li>';
	}
	endwhile; endif; wp_reset_postdata();
	$gallery_output .= '</ul>
                                <div class="gallery-updates-overlay">
                                    <i class="icon-multiple-image"></i> '.$gallery_title.'
                                </div>
                            </div>';
	}
	else
	{
		if($gallery_filters==1)
		{
			$gallery_terms = get_terms('gallery-category');
			if(!empty($gallery_terms))
			{
			$gallery_output .= '<div class="grid-filter">
                    <ul class="nav nav-pills sort-source" data-sort-id="gallery" data-option-key="filter">
                        <li data-option-value="*" class="active"><a href="#"><i class="fa fa-th"></i> <span>'.esc_html__('Show All', 'borntogive-vc').'</span></a></li>';
												foreach($gallery_terms as $term)
												{
                        	$gallery_output .= '<li data-option-value=".term-'.$term->term_id.'"><a href="#"><span><i class="fa fa-image"></i> '.$term->name.'</span></a></li>';
												}
                    $gallery_output .= '</ul>
                </div>';
			}
		}
		$gallery_output .= '<div class="row">';
		$gallery_output .= '<ul class="sort-destination isotope gallery-items" data-sort-id="gallery">';
		
		if($gallery_list->have_posts()):while($gallery_list->have_posts()):$gallery_list->the_post();
		$gallery_format = get_post_format(get_the_ID());
		$gallery_cats = wp_get_post_terms(get_the_ID(), 'gallery-category');
		$cats = '';
		$thumbnail = '';
		if($img_size != ''){
			$post_thumbnail = wpb_getImageBySize( array('post_id' => get_the_ID(),'thumb_size' => $img_size) );
			$thumbnail = $post_thumbnail['thumbnail'];
		} else {
			$post_thumbnail = get_the_post_thumbnail(get_the_ID(),'borntogive-600x400');
			$thumbnail = $post_thumbnail;
		}
		if(!empty($gallery_cats))
		{
			foreach($gallery_cats as $cat)
			{
				$cats .= 'term-'.$cat->term_id.' ';
			}
		}
		$col_class = $gallery_grid_column;
		if($gallery_format=="gallery")
		{
			$image_data = get_post_meta(get_the_ID(), 'borntogive_gallery_images', false);
			$gallery_src = borntogive_gallery_flexslider(get_the_ID());
			$slider_speed = get_post_meta(get_the_ID(), 'borntogive_gallery_slider_speed', true);
			$slider_pagination = get_post_meta(get_the_ID(), 'borntogive_gallery_slider_pagination', true);
			$slider_autoslide = get_post_meta(get_the_ID(), 'borntogive_gallery_slider_auto_slide', true);
			$slider_arrows = get_post_meta(get_the_ID(), 'borntogive_gallery_slider_direction_arrows', true);
			$slider_effect = get_post_meta(get_the_ID(), 'borntogive_gallery_slider_effects', true);
			if(!empty($image_data))
			{
			$gallery_output .= '<li class="col-md-'.$col_class.' col-sm-6 col-xs-6 grid-item gallery-grid-item '.$cats.' format-gallery">
                            <div class="media-box">
                                <div class="flexslider galleryflex" data-autoplay="'.$slider_autoslide.'" data-pagination="'.$slider_pagination.'" data-arrows="'.$slider_arrows.'" data-style="'.$slider_effect.'" data-pause="yes">';
                                    $gallery_output .= '<ul class="slides">';
									foreach ($image_data as $custom_gallery_images) 
									{
										$large_src = wp_get_attachment_image_src($custom_gallery_images, 'full');
										$gallery_output .= '<li class="item"><a href="' . esc_url($large_src[0]) . '" class="popup-image">';
										$thumbnail = '';
										if($img_size != ''){
											$post_thumbnail = wpb_getImageBySize( array('attach_id' => $custom_gallery_images,'thumb_size' => $img_size) );
											$thumbnail = $post_thumbnail['thumbnail'];
										} else {
											$post_thumbnail = wp_get_attachment_image($custom_gallery_images, 'borntogive-600x400');
											$thumbnail = $post_thumbnail;
										}
										$gallery_output .= $thumbnail;
										$gallery_output .= '</a></li>';
									}
									$gallery_output .= '</ul>'; 
                                $gallery_output .= '</div>
                            </div>';
														if($gallery_caption=="")
														{
                            $gallery_output .= '<div class="grid-item-content">
                                <p>'.get_the_title().'</p>
                            </div>';
														}
                        $gallery_output .= '</li>';
			}
		}
		elseif($gallery_format=="link")
		{
			$link = get_post_meta(get_the_ID(), 'borntogive_gallery_link_url', true);
			$gallery_output .= '<li class="col-md-'.$col_class.' col-sm-6 col-xs-6 grid-item gallery-grid-item '.$cats.' format-link">
                           	<a href="'.esc_url($link).'" target="_blank" class="media-box"> '.$thumbnail.' </a>
                            ';
							if($gallery_caption=="")
							{
								$gallery_output .= '<div class="grid-item-content">
									<p>'.get_the_title().'</p>
								</div>';
							}
                        $gallery_output .= '</li>';
		}
		elseif($gallery_format=="video")
		{
			$video = get_post_meta(get_the_ID(), 'borntogive_gallery_video_url', true);
			$gallery_output .= '<li class="col-md-'.$col_class.' col-sm-6 col-xs-6 grid-item gallery-grid-item '.$cats.' format-video">
                          	<a href="'.$video.'" class="media-box magnific-video"> '.$thumbnail.' </a>
                            ';
														if($gallery_caption=="")
														{
                            $gallery_output .= '<div class="grid-item-content">
                                <p>'.get_the_title().'</p>
                            </div>';
														}
                        $gallery_output .= '</li>';
		}
		else
		{
			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full' );
			$feat_url = '';
			if(!empty($image_src))
			{
				$feat_url = $image_src[0];
			}
			$gallery_output .= '<li class="col-md-'.$col_class.' col-sm-6 col-xs-6 grid-item gallery-grid-item '.$cats.' format-image">
                           	<a href="'.esc_url($feat_url).'" class="media-box magnific-image"> '.$thumbnail.' </a>
                            ';
														if($gallery_caption=="")
														{
                            $gallery_output .= '<div class="grid-item-content">
                                <p>'.get_the_title().'</p>
                            </div>';
														}
                        $gallery_output .= '</li>';
		}
			endwhile; endif; wp_reset_postdata();
			$gallery_output .= '</ul></div>';
			if($gallery_pagination == 1){
				$gallery_output .= '<div class="spacer-10"></div>'.borntogive_pagination($gallery_list->max_num_pages);
			}
	}
	return $gallery_output;
}
 
add_shortcode( 'borntogive_gallery', 'borntogive_gallery_element_output' );
/*Front end view of testimonial element
==================================*/
function borntogive_testimonial_element_output( $atts, $content = null) {
		extract(shortcode_atts(array(
		'testimonial_view' => '1',
		'testimonial_number' => 2,
		'testimonial_terms' => '',
		'img_size' => '',
		'testimonial_carousel_column' => '1'
		),$atts));
	$testimonial_output = '<div class="carousel-wrapper">
        <div class="row">';
		if ( is_rtl() )
		{
			$data_rtl = 'data-rtl="rtl"';
		}
		else
		{
			$data_rtl = 'data-rtl="ltr"';
		}
	if($testimonial_view==1){
		$testimonial_output .= '<ul class="owl-carousel carousel-fw" id="stories-slider" data-columns="'.$testimonial_carousel_column.'" data-autoplay="" data-pagination="no" data-arrows="yes" data-single-item="no" data-items-desktop="'.$testimonial_carousel_column.'" data-items-desktop-small="2" data-items-tablet="1" data-items-mobile="1" '.$data_rtl.'>';
	}else{
		$testimonial_output .= '<ul class="owl-carousel carousel-fw" data-columns="'.$testimonial_carousel_column.'" data-autoplay="" data-pagination="no" data-arrows="yes" data-single-item="no" data-items-desktop="'.$testimonial_carousel_column.'" data-items-desktop-small="2" data-items-tablet="1" data-items-mobile="1" '.$data_rtl.'>';
	}
    if($testimonial_terms!='')
	{
		$terms = explode(',', $testimonial_terms);
		$testimonial_args = array('post_type'=>'testimonial', 'posts_per_page'=>$testimonial_number, 'tax_query'=>array(array('taxonomy'=>'testimonial-category', 'field'=>'term_id', 'terms'=>$terms, 'operator'=>'IN')));
	}
	else
	{
		$testimonial_args = array('post_type'=>'testimonial', 'posts_per_page'=>$testimonial_number);
	}                    
	$testimonial_list = new WP_Query($testimonial_args);
	if($testimonial_list->have_posts()):while($testimonial_list->have_posts()):$testimonial_list->the_post();
	
	$testimonial_output .= '<li class="item">';
	if($testimonial_view==1)
	{
	$thumbnail = '';
	if($img_size != ''){
		$post_thumbnail = wpb_getImageBySize( array('post_id' => get_the_ID(),'thumb_size' => $img_size) );
		$thumbnail = $post_thumbnail['thumbnail'];
	} else {
		$post_thumbnail = get_the_post_thumbnail(get_the_ID(),'full');
		$thumbnail = $post_thumbnail;
	}
  	$testimonial_output .= '<div class="row">';
	if(has_post_thumbnail())
	{
		$testimonial_output .= '<div class="col-md-6">';
		$testimonial_output .= $thumbnail;
		$testimonial_output .= '</div>';
	}
	$story_btn = get_post_meta(get_the_ID(), 'borntogive_full_story_btn', true);
	$story_url = get_post_meta(get_the_ID(), 'borntogive_full_story_url', true);
	$story_url_target = get_post_meta(get_the_ID(), 'borntogive_full_story_url_target', true);
	if($story_btn != ''){$storyBTNtext = $story_btn;} else { $storyBTNtext = 'View full story'; }
	if($story_url_target == 1){$storyURLTarget = ' target="_blank"';} else{ $storyURLTarget = '';}
	if($story_url != ''){$storyURL = '<a href="'.$story_url.'" '.$storyURLTarget.' class="btn btn-primary">'.$storyBTNtext.'</a>';} else{ $storyURL = '';}
	$testimonial_output .= '<div class="col-md-6">
                                    	<div class="story-slider-content">
                                    		<div class="story-slider-table">
                                    			<div class="story-slider-cell">
                                                    <blockquote>';
	$testimonial_output .= do_shortcode(get_the_content());
  $testimonial_output .= '</blockquote>
                                                    '.$storyURL.'
                                              	</div>
                                           	</div>
                                      	</div>
                                    </div>
                                </div>';
	}
	elseif($testimonial_view==2)
	{
		$thumbnail = '';
		if($img_size != ''){
			$post_thumbnail = wpb_getImageBySize( array('post_id' => get_the_ID(),'thumb_size' => $img_size) );
			$thumbnail = $post_thumbnail['thumbnail'];
		} else {
			$post_thumbnail = get_the_post_thumbnail(get_the_ID(),'borntogive-70x70');
			$thumbnail = $post_thumbnail;
		}
		$testimonial_output .= '<div class="testimonial-block">
        <blockquote>';
 		$testimonial_output .= do_shortcode(get_the_content());
		$testimonial_output .= '</blockquote>';
		if(has_post_thumbnail())
		{
    	$testimonial_output .= '<div class="testimonial-avatar">';
			$testimonial_output .= $thumbnail;
			$testimonial_output .= '</div>';
		}
  	$testimonial_output .= '<div class="testimonial-info">
                                            <div class="testimonial-info-in">
                                                <strong>'.get_the_title().'</strong>
                                            </div>
                                        </div>
                                    </div>';
	}
	$testimonial_output .= '</li>';
	endwhile; endif; wp_reset_postdata();
	$testimonial_output .= '
									</ul></div>
										</div>';
									return $testimonial_output;
}
 
add_shortcode( 'borntogive_testimonial', 'borntogive_testimonial_element_output' );
/*Front end view of post element
==================================*/
function borntogive_post_element_output( $atts, $content = null) {
		extract(shortcode_atts(array(
		'post_view' => 'list',
		'post_number' => 2,
		'post_grid_column' => 6,
		'post_content' => 2,
		'post_terms' => '',
		'post_authors' => '',
		'img_size' => '',
		'posts_pagination' => '',
		'posts_tags' => '',
		'posts_post_date' => 1,
		'posts_post_author' => '',
		'posts_categories' => '',
		'post_excerpt_length' => 30,
		'post_closing_text' => ''
		),$atts));
	if($post_grid_column==4)
	{
		$carousel_col_class = 3;
	}
	elseif($post_grid_column==3)
	{
		$carousel_col_class = 4;
	}
	elseif($post_grid_column==6)
	{
		$carousel_col_class = 2;
	}
	
	if($post_view!="list"){
		$post_output = '<div class="row">';
	}
	else {
		$post_output = '';	
	}
	if($post_view=="carousel")
	{
		if ( is_rtl() )
		{
			$data_rtl = 'data-rtl="rtl"';
		}
		else
		{
			$data_rtl = 'data-rtl="ltr"';
		}
		$post_output .= '<ul class="owl-carousel carousel-fw" id="news-slider" data-columns="'.esc_attr($carousel_col_class).'" data-autoplay="" data-pagination="yes" data-arrows="no" data-single-item="no" data-items-desktop="'.esc_attr($carousel_col_class).'" data-items-desktop-small="2" data-items-tablet="1" data-items-mobile="1" '.$data_rtl.'>';
	}
	elseif($post_view=="list")
	{
		$post_output .= '<div class="content-block">';
	}
	else
	{
		$post_output .= '<ul class="grid-holder isotope gallery-items" data-sort-id="gallery">';
	}
	if($post_terms==''&&$post_authors=='')
	{
		$post_args = array('post_type'=>'post', 'posts_per_page'=>$post_number, 'paged' => get_query_var('paged'));
	}
	elseif($post_terms==''&&$post_authors!='')
	{
		$authors = explode(',', $post_authors);
		$post_args = array('post_type'=>'post', 'author__in' => $authors , 'posts_per_page'=>$post_number, 'paged' => get_query_var('paged'));
	}
	elseif($post_terms!=''&&$post_authors=='')
	{
		$terms = explode(',', $post_terms);
		$post_args = array('post_type'=>'post' , 'posts_per_page'=>$post_number, 'tax_query'=>array(array('taxonomy'=>'category', 'field'=>'term_id', 'terms'=>$terms, 'operator'=>'IN')));
	}
	$post_list = new WP_Query($post_args);
	if($post_list->have_posts()):while($post_list->have_posts()):$post_list->the_post();
	$thumbnail = '';
	if($img_size != ''){
		$post_thumbnail = wpb_getImageBySize( array('post_id' => get_the_ID(),'thumb_size' => $img_size) );
		$thumbnail = $post_thumbnail['thumbnail'];
	} else {
		$post_thumbnail = get_the_post_thumbnail(get_the_ID(),'borntogive-600x400');
		$thumbnail = $post_thumbnail;
	}
	$post_author_id = get_post_field( 'post_author', get_the_ID() );
	$categories = get_the_category();
	$categories_list = '';
	if(!empty($categories))
	{
		   foreach($categories as $category)
		   {
				   $categories_list .= '<a href="'.get_category_link($category->term_id).'" class="category-links-custom">'.$category->name.'</a>';
		   }
	}
	$meta_data_date = '<i class="fa fa-calendar"></i> ' .esc_html(get_the_date(get_option('date_format'), get_the_ID()));
	$meta_data_author = '<i class="fa fa-pencil"></i> ' .'<a href="'. esc_url(get_author_posts_url($post_author_id)).'">'.esc_attr(get_the_author_meta( 'display_name', $post_author_id )).'</a>';
	
	if($post_view=="carousel")
	{
	$post_output .= '<li class="item">
                          	<div class="grid-item blog-grid-item format-standard">
                         		<div class="grid-item-inner">';
	if(has_post_thumbnail())
	{
		$post_output .= '<a href="'.get_permalink().'" class="media-box">';
		$post_output .= $thumbnail;
		$post_output .= '</a>';
	} else {
		$post_output .= '<a href="'.get_permalink().'" class="media-box">';
		$post_output .= '<img src="' . vc_asset_url( 'vc/no_image.png' ) . '" alt="" height="100%">';
		$post_output .= '</a>';
	}
	$post_output .= '<div class="grid-item-content">
                                                        <h3 class="post-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
                                                        if($posts_post_date == 1 || $posts_post_author == 1 || $posts_categories == 1){
															$post_output .= '<div class="meta-data grid-item-meta full-meta-data">';
															if($posts_post_date == 1){
																$post_output .= $meta_data_date;
															}
															if($posts_post_author == 1){
																$post_output .= $meta_data_author;
															}
															if($posts_categories == 1){
																$post_output .= '<i class="fa fa-folder"></i> '.$categories_list;
															}
															$post_output .= '</div>';
														}
                                                $post_output .= '</div>
                                                </div>
                                            </div>
                                        </li>';
	}
	elseif($post_view=="list")
	{
		$post_output .= '<div class="blog-list-item format-standard">
                        	<div class="row">';
							if(has_post_thumbnail()) {
    	$post_output .= '<div class="col-md-4 col-sm-4">
                       		<a href="'.get_permalink().'" class="media-box grid-featured-img">
                           		'.$thumbnail.'
                            </a>
                      	</div>
						<div class="col-md-8 col-sm-8">';
							} else {
		$post_output .= '<div class="col-md-12 col-sm-12">';					
							}
   		$post_output .= '<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		if($posts_post_date == 1 || $posts_post_author == 1 || $posts_categories == 1){               	
			$post_output .= '<div class="meta-data grid-item-meta full-meta-data">';
			if($posts_post_date == 1){
				$post_output .= $meta_data_date;
			}
			if($posts_post_author == 1){
				$post_output .= $meta_data_author;
			}
			if($posts_categories == 1){
				$post_output .= '<i class="fa fa-folder"></i> '.$categories_list;
			}
			$post_output .= '</div>';
		}
        $post_output .= '<div class="grid-item-excerpt">';
			if($post_content == 1){
				$post_content_type = get_the_content('');	
			} else {
				$post_content_type = borntogive_excerpt($post_excerpt_length, $post_closing_text);
			}
		$post_output .= $post_content_type;
		$post_output .= '</div>
						<a href="'.get_permalink().'" class="basic-link">'.esc_html__('Read more', 'borntogive-vc').'</a>
					</div>
				</div>
			</div>';
	}
	else
	{
		$post_output .= '<li class="col-md-'.$post_grid_column.' col-sm-6 grid-item blog-grid-item format-standard">
                        <div class="grid-item-inner">';
		if(has_post_thumbnail())
		{
   		$post_output .= '<a href="'.get_permalink().'" class="media-box">
                                '.$thumbnail.'
                            </a>';
		}
    $post_output .= '<div class="grid-item-content">
                                <h3 class="post-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
                                if($posts_post_date == 1 || $posts_post_author == 1 || $posts_categories == 1){               	
								$post_output .= '<div class="meta-data grid-item-meta full-meta-data">';
								if($posts_post_date == 1){
									$post_output .= $meta_data_date;
								}
								if($posts_post_author == 1){
									$post_output .= $meta_data_author;
								}
								if($posts_categories == 1){
									$post_output .= '<i class="fa fa-folder"></i> '.$categories_list;
								}
								$post_output .= '</div>';
							}
								if($post_content == 1){
									$post_content_type = get_the_content('');	
								} else {
									$post_content_type = borntogive_excerpt($post_excerpt_length, $post_closing_text);
								}
   	$post_output .= $post_content_type;                             
						if($posts_tags == 1){
                   			$post_output .= '<div class="tagcloud">';
									if (has_tag())
                                    {
										$alltags = '';
										$tags_output = get_the_tags('', ', ');
										foreach($tags_output as $tag){
											$alltags .= ' <a href="'.get_tag_link($tag->term_id).'">'.$tag->name.'</a>';
										}
                                        $post_output .= '<div class="post-meta">';
                                        $post_output .= '<i class="fa fa-tags"></i> ';
                                        $post_output .= $alltags;
                                        $post_output .= '</div>';
                                    }
                                $post_output .= '</div>';
								}
                            $post_output .= '</div>
                        </div>
                    </li>';
	}
	endwhile; endif; wp_reset_postdata();
	if($post_view=="carousel")
	{
		$post_output .= '</ul>';
	}
	if($post_view=="list")
	{
		$post_output .= '</div>';
	}
	else
	{
		$post_output .= '</ul>';
	}
	
	if($post_view!="list"){
		$post_output .= '</div>';
	}
	if($posts_pagination == 1){
		$post_output .= '<div class="spacer-10"></div>'.borntogive_pagination($post_list->max_num_pages);
	}
									return $post_output;
}
 
add_shortcode( 'borntogive_post', 'borntogive_post_element_output' );
/*Front end view of grid container element
==================================*/
function borntogive_gridcontainer_element_output( $atts, $content = null) {
		extract(shortcode_atts(array(
		'grid_image' => '',
		'grid_description' => '',
		'grid_url' => '',
		),$atts));
	$image_src = wp_get_attachment_image_src($grid_image);
	$grid_output = '<div class="grid-item">';
	if(filter_var($grid_url, FILTER_VALIDATE_URL) === TRUE)
	{
		$grid_output .= '<a href="'.$grid_url.'">';
	}
	if(!empty($image_src))
	{
  	$grid_output .= '<img src="'.$image_src[0].'" alt="">';
	}
	if($grid_description!='')
	{
		$grid_output .= '<div class="grid-item-content">
                    		<p>'.$grid_description.'</p>
                     </div>';
	}
	if(filter_var($grid_url, FILTER_VALIDATE_URL) === TRUE)
	{
		$grid_output .= '</a>';
	}
	$grid_output .= '</div>';
	return $grid_output;
}
 
add_shortcode( 'borntogive_gridcontainer', 'borntogive_gridcontainer_element_output' );
/*Front end view of team element
==================================*/
function borntogive_team_element_output( $atts, $content = null) {
		extract(shortcode_atts(array(
		'team_details' => '1',
		'team_number' => '',
		'team_carousel' => 1,
		'team_linked' => 1,
		'team_terms' => '',
		'team_excerpt' => 30,
		'team_more_text' => '',
		'team_closing_text' => '...',
		'team_carousel_column' => 2,
		'team_carousel_style' => 'grid',
		'team_design_style' => '',
		'team_content' => 2,
		'img_size' => 'full',
		'id' => ''
		),$atts));
	$team_output = '<div class="'.$team_design_style.'">';
	$team_grid_column = $team_grid_single_item = $team_grid_single = '';
	if($team_carousel_column == 3){
		$team_grid_column = '30.3333%';
	}elseif($team_carousel_column == 4){
		$team_grid_column = '22%';
	}elseif($team_carousel_column == 5){
		$team_grid_column = '17%';
	}elseif($team_carousel_column == 6){
		$team_grid_column = '13.6666%';
	}elseif($team_carousel_column == 1){
		$team_grid_column = '100%';
		$team_grid_single_item = 'team-custom-grid-single-item';
		$team_grid_single = 'team-custom-grid-single';
	}else{
		$team_grid_column = '47%';
	}
	
	if($team_carousel_style=="list")
	{
		$team_output .= '<div class="content-block">';
	} else {
		
		if($team_carousel==1)
		{
			if ( is_rtl() )
			{
				$data_rtl = 'data-rtl="rtl"';
			}
			else
			{
				$data_rtl = 'data-rtl="ltr"';
			}
			$team_output .= '<div class="carousel-wrapper">
							<div class="row">
								<ul class="owl-carousel carousel-fw" data-columns="'.$team_carousel_column.'" data-autoplay="" data-pagination="no" data-arrows="yes" data-single-item="no" data-items-desktop="'.$team_carousel_column.'" data-items-desktop-small="3" data-items-tablet="2" data-items-mobile="2" '.$data_rtl.'>';
		}
		else
		{
			$team_output .= '<ul class="isotope-grid team-custom-grid '.$team_grid_single.'">';	
		}
	}
	$team_compact_class = ($team_details!=1)?'grid-staff-item-compact':'';
	if($team_terms=='')
	{
		$team_args = array('post_type'=>'team', 'posts_per_page'=>$team_number);
	}
	else
	{
		$team_terms = explode(',', $team_terms);
		$team_args = array('post_type'=>'team', 'posts_per_page'=>$team_number, 'tax_query'=>array(array('taxonomy'=>'team-category', 'field'=>'term_id', 'terms'=>$team_terms, 'operator'=>'IN')));
	}
	$team_list = new WP_Query($team_args);
	if($team_list->have_posts()):while($team_list->have_posts()):$team_list->the_post();
	$thumbnail = '';
	if($img_size != ''){
		$team_thumbnail = wpb_getImageBySize( array('post_id' => get_the_ID(),'thumb_size' => $img_size) );
		$thumbnail = $team_thumbnail['thumbnail'];
	} else {
		$team_thumbnail = get_the_post_thumbnail(get_the_ID(),'full');
		$thumbnail = $team_thumbnail;
	}
	$position = get_post_meta(get_the_ID(), 'borntogive_staff_position', true);
	$facebook = get_post_meta(get_the_ID(), 'borntogive_staff_member_facebook', true);
	$twitter = get_post_meta(get_the_ID(), 'borntogive_staff_member_twitter', true);
	$gplus = get_post_meta(get_the_ID(), 'borntogive_staff_member_gplus', true);
	$linkedin = get_post_meta(get_the_ID(), 'borntogive_staff_member_linkedin', true);
	$pinterest = get_post_meta(get_the_ID(), 'borntogive_staff_member_pinterest', true);
	$email = get_post_meta(get_the_ID(), 'borntogive_staff_member_email', true);
	$phone = get_post_meta(get_the_ID(), 'borntogive_staff_member_phone', true);
	if($phone != ''){
		$phoneno = '<span class="label label-primary margin-10" style="display:inline-block; font-size:14px;"><i class="fa fa-phone"></i> '. $phone.'</span>';	
	} else {
		$phoneno = '';	
	}
	$social = '';
	$social_data = array();
	$social_data = array('envelope'=>$email, 'facebook'=>$facebook, 'twitter'=>$twitter, 'google-plus'=>$gplus, 'linkedin'=>$linkedin, 'pinterest'=>$pinterest);
	if($facebook!=''||$twitter!=''||$gplus!=''||$linkedin!=''||$pinterest!=''||$email!='')
	{
	$social .= '<ul class="social-icons-rounded social-icons-colored">';
	foreach($social_data as $key=>$value)
	{
		if($value!='')
		{
			$url = $value;
			if($key=="envelope")
			{
				$url = 'mailto:'.$value;
			}
			$social .= '<li class="'.$key.'">
								<a href="'.$url.'">
									<i class="fa fa-'.$key.'"></i>
								</a>
							</li>';
		}
	}
	$social .= '</ul>';
	}
	if($team_carousel_style=="list" && ($team_carousel == 1 || $team_carousel == 0)){
		$team_output .= '<div class="blog-list-item team-list-item format-standard">
		<div class="row">';
		if(has_post_thumbnail())
		{
			$team_output .= '<div class="col-md-4 col-sm-4">';
			if($team_linked == 1 || $team_linked != ''){
				$team_output .= '<a href="'.get_permalink().'" class="media-box">';
			}
			$team_output .= $thumbnail;
			if($team_linked == 1 || $team_linked != ''){
				$team_output .= '</a>';
			}
			$team_output .= '</div>';
		}
		if(has_post_thumbnail())
		{
			$team_output .= '<div class="col-md-8 col-sm-8">';
		} else {
			$team_output .= '<div class="col-md-12 col-sm-12">';					
		}
   		$team_output .= '<h3 class="margin-5">';
		if($team_linked == 1 || $team_linked != ''){
			$team_output .= '<a href="'.get_permalink().'">';
		}
		$team_output .= get_the_title();
		if($team_linked == 1 || $team_linked != ''){
			$team_output .= '</a>';
		}
		$team_output .= '</h3>';
		if($position!='')
		{                             
		$team_output .= '<span class="meta-data margin-20">'.$position.'</span>';
		}
		if($team_details==1){
			$team_output .= $social;
			$team_output .= $phoneno;
			$team_output .= '<div class="spacer-20"></div>';
		}
		
		if($team_content == 1){
			$team_content_type = get_the_content('');	
		} elseif($team_excerpt != '' && $team_excerpt != 0) {
			$team_content_type = borntogive_excerpt($team_excerpt, $team_closing_text, $team_more_text);
		}
		$team_output .= $team_content_type;
		$team_output .= '</div>
					</div>
				</div>';
	} else {
		
		if($team_carousel!=1)
		{
			$team_output .= '<li class="grid-item team-custom-grid-item '.$team_grid_single_item.' format-standard" style="width:'.$team_grid_column.'">
			<div class="grid-staff-item '.$team_compact_class.'">
							<div class="grid-item-inner">';
		} 
		else
		{
			$team_output .= '<li class="item format-standard">
			<div class="grid-item grid-staff-item '.$team_compact_class.'">
							<div class="grid-item-inner">';
		}
		if(has_post_thumbnail())
		{
			if($team_linked == 1 || $team_linked != ''){
				$team_output .= '<a href="'.get_permalink().'" class="media-box">';
			}
			$team_output .= $thumbnail;
			if($team_linked == 1 || $team_linked != ''){
				$team_output .= '</a>';
			}
		}
		$team_output .= '<div class="grid-item-content">';
		$team_output .= '<h3 class="margin-5">';
		if($team_linked == 1 || $team_linked != ''){
			$team_output .= '<a href="'.get_permalink().'">';
		}
		$team_output .= get_the_title();
		if($team_linked == 1 || $team_linked != ''){
			$team_output .= '</a>';
		}
		$team_output .= '</h3>';
		if($position!='')
		{                             
		$team_output .= '<span class="meta-data margin-20">'.$position.'</span>';
		}
		if($team_details==1){
			$team_output .= $social;
			$team_output .= $phoneno;
		}
		if($team_content == 1){
			$team_content_type = get_the_content('');	
		} elseif($team_excerpt != '' && $team_excerpt != 0) {
			$team_content_type = borntogive_excerpt($team_excerpt, $team_closing_text, $team_more_text);
		}
		$team_output .= $team_content_type;

		$team_output .= '</div></div></div></li>';
	}
	endwhile; endif; wp_reset_postdata();
	if($team_carousel_style=="list"){
		$team_output .= '</div>';
	} else {
		if($team_carousel!=1)
		{
			$team_output .= '</ul>';
		}
		else{
			$team_output .= '</ul></div></div>';
		}
	}
	$team_output .= '</div>';
	return $team_output;
}
 
add_shortcode( 'borntogive_team', 'borntogive_team_element_output' );
/*Front end view of featured link element
==================================*/
function borntogive_featurl_element_output( $atts, $content = null) {
		extract(shortcode_atts(array(
		'feat_head_line' => '',
		'feat_title' => '',
		'feat_url' => '',
		'feat_url_target' => 0,
		'feat_custom_bg' => '',
		'feat_custom_text' => ''
		),$atts));
		if($feat_custom_bg != ''){
			$custom_bg = $feat_custom_bg;
		} else {
			$custom_bg = '';	
		}
		if($feat_custom_text != ''){
			$custom_color = $feat_custom_text;
		} else {
			$custom_color = '';	
		}
	$feat_output = '';
	if($feat_url_target == 1){
		$ftarget = ' target="_blank"';
	} else {
		$ftarget = ' ';
	}
	if($feat_url != ''){$feat_output = '<a href="'.$feat_url.'"'.$ftarget;}
	else{$feat_output = '<div';}
	$feat_output .= ' class="featured-link" style="background:'.$custom_bg.'; color:'.$custom_color.';">
        	<span>'.$feat_head_line.'</span>
        	<strong>'.$feat_title.'</strong>';
    if($feat_url != ''){$feat_output .= '</a>';}
	else{$feat_output .= '</div>';}
	return $feat_output;
}
 
add_shortcode( 'borntogive_feat_link', 'borntogive_featurl_element_output' );
/*Front end view of featured Text element
==================================*/
function borntogive_feattext_element_output( $atts, $content = null) {
		extract(shortcode_atts(array(
		'feat_head' => '',
		'feat_content' => '',
		),$atts));
	//echo "saibaba";
	$feattext_output = '';
	$feattext_output = '<div class="featured-text" style="height: 127px;">
											<span>'.$feat_head.'</span>
											<strong>'.$feat_content.'</strong>
											</div>';
	return $feattext_output;
}
 
add_shortcode( 'borntogive_feat_text', 'borntogive_feattext_element_output' );