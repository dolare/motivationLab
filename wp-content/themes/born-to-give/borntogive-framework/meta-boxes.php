<?php
/* * ** Meta Box Functions **** */
$prefix = 'borntogive_';
global $meta_boxes;
$options = get_option('borntogive_options');
$gmap_api_key = (isset($options['google_map_api']))?$options['google_map_api']:'';
load_theme_textdomain('borntogive', BORNTOGIVE_FILEPATH . '/language');
$meta_boxes = array();
$meta_boxes[] = array(
    'id' => 'post_page_meta_box',
    'title' => esc_html__('Page/Post Header Options', 'borntogive'),
   'pages' => array('post', 'page', 'product', 'campaign', 'event', 'team'),
    'fields' => array(
		array(
            'name' => esc_html__('Page Header Show/Hide', 'borntogive'),
            'id' => $prefix . 'page_header_show_hide',
            'type' => 'select',
            'options' => array(
                1 => esc_html__('Show', 'borntogive'),
                2 => esc_html__('Hide', 'borntogive'),
            ),
            'std' => 1,
        ),
		array(
            'name' => esc_html__('Page Header Title Show/Hide', 'borntogive'),
            'id' => $prefix . 'pages_title_show',
            'type' => 'select',
            'options' => array(
                1 => esc_html__('Show', 'borntogive'),
                2 => esc_html__('Hide', 'borntogive'),
            ),
            'std' => 1,
        ),
		array(
            'name' => esc_html__('Choose Header Type', 'borntogive'),
            'id' => $prefix . 'pages_Choose_slider_display',
            'desc' => esc_html__("Select Banner Type.", 'borntogive'),
            'type' => 'select',
            'options' => array(
				'1' => esc_html__('Banner', 'borntogive'),
				'2' => esc_html__('Banner Image', 'borntogive'),
          		'3' => esc_html__('Flex Slider', 'borntogive'),
          		'5' => esc_html__('Revolution Slider', 'borntogive'),
            ),
			'std' => 2
        ),
		array(
			'name' => esc_html__( 'Banner Color', 'borntogive' ),
			'id' => $prefix.'pages_banner_color',
			'type' => 'color',
		),
		array(
            'name' => esc_html__('Banner Image', 'borntogive'),
            'id' => $prefix . 'header_image',
            'desc' => esc_html__("Upload banner image for header for this Page/Post.", 'borntogive'),
            'type' => 'image_advanced',
            'max_file_uploads' => 1
        ),
        array(
			 'name' => __('Select Revolution Slider from list','borntogive'),
			  'id' => $prefix . 'pages_select_revolution_from_list',
			  'desc' => esc_html__("Select Revolution Slider from list", 'borntogive'),
			  'type' => 'select',
			  'options' => borntogive_RevSliderShortCode(),
		  ),
        //Slider Image
		array(
            'name' => esc_html__('Banner/Slider Height', 'borntogive'),
            'id' => $prefix . 'pages_slider_height',
            'desc' => esc_html__("Enter Height for Banner/Slider Ex-265.", 'borntogive'),
            'type' => 'text',
        ),
        array(
            'name' => esc_html__('Slider Images', 'borntogive'),
            'id' => $prefix . 'pages_slider_image',
            'desc' => esc_html__("Choose Slider Images.", 'borntogive'),
            'type' => 'image_advanced',
        ),
		array(
            'name' => esc_html__('Slider Pagination', 'borntogive'),
            'id' => $prefix . 'pages_slider_pagination',
            'desc' => esc_html__("Enable to show pagination for flexslider.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'yes' => esc_html__('Enable', 'borntogive'),
                'no' => esc_html__('Disable', 'borntogive'),
            ),
						'std'         => 'yes',
        ),
		array(
            'name' => esc_html__('Slider Auto Slide', 'borntogive'),
            'id' => $prefix . 'pages_slider_auto_slide',
            'desc' => esc_html__("Select Yes to slide automatically.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'yes' => esc_html__('Yes', 'borntogive'),
                'no' => esc_html__('No', 'borntogive'),
            ),
						'std'         => 'yes',
        ),
		array(
            'name' => esc_html__('Slider Direction Arrows', 'borntogive'),
            'id' => $prefix . 'pages_slider_direction_arrows',
            'desc' => esc_html__("Select Yes to show flexslider direction arrows.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'yes' => esc_html__('Yes', 'borntogive'),
                'no' => esc_html__('No', 'borntogive'),
            ),
						'std'         => 'yes',
        ),
		array(
            'name' => esc_html__('Slider Effects', 'borntogive'),
            'id' => $prefix . 'pages_slider_effects',
            'desc' => esc_html__("Select effects for flexslider.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'fade' => esc_html__('Fade', 'borntogive'),
                'slide' => esc_html__('Slide', 'borntogive'),
            ),
						'std'         => 'fade',
        ),
        )
);
/* Post Page Background Meta Box
  ================================================== */
$meta_boxes[] = array(
    'id' => 'post_page_bg_meta_box',
    'title' => esc_html__('Background Options', 'borntogive'),
   	'pages' => array('post', 'page', 'product', 'campaign', 'event', 'team'),
    'fields' => array(
		array(
            'name' => esc_html__('Below options work only in Boxed Layout', 'borntogive'),
            'id' => $prefix . 'boxed_option_heading',
            'type' => 'heading',
		),
		array(
            'name' => esc_html__('Background Color', 'borntogive'),
            'id' => $prefix . 'pages_body_bg_color',
            'desc' => esc_html__("Choose background color for the outer area", 'borntogive'),
            'type' => 'color',
        ),
		array(
            'name' => esc_html__('Background Image', 'borntogive'),
            'id' => $prefix . 'pages_body_bg_image',
            'desc' => esc_html__("Choose background image for the outer area", 'borntogive'),
            'type' => 'image_advanced',
            'max_file_uploads' => 1
        ),
		array(
            'name' => esc_html__('100% Background Image', 'borntogive'),
            'id' => $prefix . 'pages_body_bg_wide',
            'desc' => esc_html__("Choose to have the background image display at 100%.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                '1' => esc_html__('Yes', 'borntogive'),
                '0' => esc_html__('No', 'borntogive'),
            ),
            'std' => 0,
        ),
		array(
            'name' => esc_html__('Background Repeat', 'borntogive'),
            'id' => $prefix . 'pages_body_bg_repeat',
            'desc' => esc_html__("Select how the background image repeats.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'repeat' => esc_html__('Repeat', 'borntogive'),
                'repeat-x' => esc_html__('Repeat Horizontally', 'borntogive'),
                'repeat-y' => esc_html__('Repeat Vertically', 'borntogive'),
                'no-repeat' => esc_html__('No Repeat', 'borntogive'),
            ),
            'std' => 'repeat',
        ),
		array(
            'id' => $prefix . 'wide_option_divider',
            'type' => 'divider',
		),
		array(
            'name' => esc_html__('Below options work in boxed and wide mode:', 'borntogive'),
            'id' => $prefix . 'wide_option_heading',
            'type' => 'heading',
		),
		array(
            'name' => esc_html__('Background Color', 'borntogive'),
            'id' => $prefix . 'pages_content_bg_color',
            'desc' => esc_html__("Choose background color for the Content area", 'borntogive'),
            'type' => 'color',
        ),
		array(
            'name' => esc_html__('Background Image', 'borntogive'),
            'id' => $prefix . 'pages_content_bg_image',
            'desc' => esc_html__("Choose background image for the Content area", 'borntogive'),
            'type' => 'image_advanced',
            'max_file_uploads' => 1
        ),
		array(
            'name' => esc_html__('100% Background Image', 'borntogive'),
            'id' => $prefix . 'pages_content_bg_wide',
            'desc' => esc_html__("Choose to have the background image display at 100%.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                '1' => esc_html__('Yes', 'borntogive'),
                '0' => esc_html__('No', 'borntogive'),
            ),
            'std' => 0,
        ),
		array(
            'name' => esc_html__('Background Repeat', 'borntogive'),
            'id' => $prefix . 'pages_content_bg_repeat',
            'desc' => esc_html__("Select how the background image repeats.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'repeat' => esc_html__('Repeat', 'borntogive'),
                'repeat-x' => esc_html__('Repeat Horizontally', 'borntogive'),
                'repeat-y' => esc_html__('Repeat Vertically', 'borntogive'),
                'no-repeat' => esc_html__('No Repeat', 'borntogive'),
            ),
            'std' => 'repeat',
        ),
	)
);
/* Post Page Social Meta Box
  ================================================== */
$meta_boxes[] = array(
    'id' => 'post_page_design_meta_box',
    'title' => esc_html__('Page Design Options', 'borntogive'),
   	'pages' => array('post', 'page', 'product', 'campaign', 'event', 'team'),
    'fields' => array(
		array(
            'name' => esc_html__('Content Width', 'borntogive'),
            'desc' => esc_html__("Enter width of content in px or %", 'borntogive'),
            'id' => $prefix . 'content_width',
            'type' => 'text',
		),
		array(
            'name' => esc_html__('Content Padding Top', 'borntogive'),
            'desc' => esc_html__("Do not include px or % here", 'borntogive'),
            'id' => $prefix . 'content_padding_top',
            'type' => 'number',
		),
		array(
            'name' => esc_html__('Content Padding Bottom', 'borntogive'),
            'desc' => esc_html__("Do not include px or % here", 'borntogive'),
            'id' => $prefix . 'content_padding_bottom',
            'type' => 'number',
		),
		array(
            'name' => esc_html__('Show social sharing buttons', 'borntogive'),
            'id' => $prefix . 'pages_social_show',
            'type' => 'select',
            'options' => array(
                '1' => esc_html__('Show', 'borntogive'),
                '2' => esc_html__('Hide', 'borntogive'),
            ),
            'std' => '1',
        ),
	)
);

	/* Event Meta Box
  ================================================== */
/*** Event Details Meta box ***/   
$meta_boxes[] = array(
    'id' => 'event_meta_box',
    'title' => esc_html__('Event Date', 'borntogive'),
    'pages' => array('event'),
    'fields' => array( 
        // Event Start Date 
		array(
            'name' => esc_html__('Featured Event', 'borntogive'),
            'id' => $prefix . 'featured_event',
            'desc' => esc_html__("Select Featured Event.", 'borntogive'),
            'type' => 'select',
            'options' => array(
				'no' => esc_html__('No','borntogive'),
				'yes' => esc_html__('Yes','borntogive'),
        ),
		), 
        array(
            'name' => esc_html__('Event Start Date', 'borntogive'),
            'id' => $prefix . 'event_start_dt',
            'desc' => esc_html__("Insert date of Event start.", 'borntogive'),
            'type' => 'datetime',
			'js_options' => array(
	              'dateFormat'      => 'yy-mm-dd',
				  'hourMax' => 24,
					'changeMonth'     => true,
					'changeYear'      => true,
					'showButtonPanel' => true,
					'stepMinute' => 5,
					'showSecond' => false,
					'stepSecond' => 10,
				),
        ),
        //Event End Date
        array(
            'name' => esc_html__(' Event End Date', 'borntogive'),
            'id' => $prefix . 'event_end_dt',
            'desc' => esc_html__("Insert date of Event end, multiple days Event could not be recur.", 'borntogive'),
            'type' => 'datetime',
			'js_options' => array(
	              'dateFormat'      => 'yy-mm-dd',
				  'hourMax' => 24,
					'changeMonth'     => true,
					'changeYear'      => true,
					'showButtonPanel' => true,
					'stepMinute' => 5,
					'showSecond' => false,
					'stepSecond' => 10,
				),
        ),
    )
);
/*** Event Address Meta box ***/   
$meta_boxes[] = array(
    'id' => 'event_address_box',
    'title' => esc_html__('Event Details', 'borntogive'),
    'pages' => array('event'),
    'fields' => array( 
		array(
			'name'  => esc_html__('Address', 'borntogive'),
			'id'    => $prefix."event_address",
			'desc'  =>  esc_html__("This field should have real address to get GMap.", 'borntogive'),
			'type' => 'text',
		),   
		array(
			'id'    => $prefix."event_map_location",
			'name' => esc_html__( 'Location', 'borntogive' ),
			'type' => 'map',
			'api_key' => $gmap_api_key,
			'std' => '-6.233406,-35.049906,15', // 'latitude,longitude[,zoom]' (zoom is optional)
			'style' => 'width: 500px; height: 500px',
			'address_field' => 'borntogive_event_address', // Name of text field where address is entered. Can be list of text fields, separated by commas (for ex. city, state)
		),
		array(
			'name'  => esc_html__('Event Manager Name', 'borntogive'),
			'id'    => $prefix."event_manager_name",
			'desc'  =>  esc_html__("Enter event manager name.", 'borntogive'),
			'type' => 'text',
		),
		array(
			'name'  => esc_html__('Event Manager Email', 'borntogive'),
			'id'    => $prefix."event_manager",
			'desc'  =>  esc_html__("Enter event manager email address for contact.", 'borntogive'),
			'type' => 'text',
		),
		array(
			'name' => esc_html__( 'Enable Registration', 'borntogive' ),
			'id' => $prefix.'event_registration',
			'type' => 'checkbox',
			// Value can be 0 or 1
			'std' => 1,
		),  
		array(
			'name' => esc_html__( 'Custom Registration Button URL', 'borntogive' ),
			'id' => $prefix.'custom_event_registration',
			'desc' => esc_html__("For example EventBrite Event page URL of yours.", 'borntogive'),
			'type' => 'text'
		),
		array(
			'name' => esc_html__( 'Open custom URL in new Tab/Window', 'borntogive' ),
			'id' => $prefix.'custom_event_registration_target',
			'type' => 'checkbox',
			// Value can be 0 or 1
			'std' => 1,
		),
		array(
			'name' => esc_html__( 'Total Attendees', 'borntogive' ),
			'id' => $prefix.'event_attendees',
			'desc' => esc_html__("Enter number of attendees allowed for this event.", 'borntogive'),
			'type' => 'text'
		),
		)
);
/*** Event Recurrence Meta box ***/   
$meta_boxes[] = array(
    'id' => 'event_recurring_box',
    'title' => esc_html__('Recurring Event', 'borntogive'),
    'pages' => array('event'),
    'fields' => array( 		 
        //Frequency of Event
		array(
            'name' => esc_html__('Event Frequency Type', 'borntogive'),
            'id' => $prefix . 'event_frequency_type',
            'desc' => esc_html__("Select Frequency Type.", 'borntogive'),
            'type' => 'select',
            'options' => array(
				'0' => esc_html__('Not Required','borntogive'),
				'1' => esc_html__('Fixed Date','borntogive'),
        		'2' => esc_html__('Week Day', 'borntogive'),
        	),
		),
		array(
            'name' => esc_html__('Week of Month', 'borntogive'),
            'id' => $prefix . 'event_day_month',
            'desc' => esc_html__("Select Week of Month.", 'borntogive'),
            'type' => 'select',
            'options' => array(
				'first' => esc_html__('First','borntogive'),
        		'second' => esc_html__('Second', 'borntogive'),
				'third' => esc_html__('Third', 'borntogive'),
				'fourth' => esc_html__('Fourth', 'borntogive'),
				'last' => esc_html__('Last', 'borntogive'),
        	),
		),
		array(
            'name' => esc_html__('Event Week Day', 'borntogive'),
            'id' => $prefix . 'event_week_day',
            'desc' => esc_html__("Select Week Day.", 'borntogive'),
            'type' => 'select',
            'options' => array(
				'sunday' => esc_html__('Sunday','borntogive'),
                'monday' => esc_html__('Monday', 'borntogive'),
				'tuesday' => esc_html__('Tuesday', 'borntogive'),
				'wednesday' => esc_html__('Wednesday', 'borntogive'),
				'thursday' => esc_html__('Thursday', 'borntogive'),
				'friday' => esc_html__('Friday', 'borntogive'),
				'saturday' => esc_html__('Saturday', 'borntogive'),
        	),
		),
        array(
            'name' => esc_html__('Event Frequency', 'borntogive'),
            'id' => $prefix . 'event_frequency',
            'desc' => esc_html__("Select Frequency.", 'borntogive'),
            'type' => 'select',
            'options' => array(
				'35' => esc_html__('Select', 'borntogive'),
                '1' => esc_html__('Every Day', 'borntogive'),
				'2' => esc_html__('Every Second Day', 'borntogive'),
				'3' => esc_html__('Every Third Day', 'borntogive'),
				'4' => esc_html__('Every Fourth Day', 'borntogive'),
				'5' => esc_html__('Every Fifth Day', 'borntogive'),
				'6' => esc_html__('Every Sixth Day', 'borntogive'),
                '7' => esc_html__('Every Week', 'borntogive'),
				'30' => esc_html__('Every Month', 'borntogive'),
            ),
        ),
		//Frequency Count
		array(
            'name' => esc_html__('Number of times to repeat event', 'borntogive'),
            'id' => $prefix . 'event_frequency_count',
            'desc' => esc_html__("Enter the number of how many time this event should repeat.", 'borntogive'),
            'type' => 'text',
        ),
		array(
            'name' => esc_html__('Multiple Days Frequency Type', 'borntogive'),
            'id' => $prefix . 'event_multiple_type',
            'desc' => esc_html__("Select Multiple Days Frequency Type.", 'borntogive'),
            'type' => 'select',
            'options' => array(
							'0' => esc_html__('Exclude','borntogive'),
							'1' => esc_html__('Add Dynamic Date','borntogive'),
							
        ),
		),
		array(
            'name' => esc_html__('Multiple Event Date', 'borntogive'),
            'id' => $prefix . 'event_multiple_dates',
            'desc' => esc_html__("Add/Exclude Multiple Date.", 'borntogive'),
            'type' => 'date',
						'clone' => true,
			'js_options' => array(
				'dateFormat'      =>'yy-mm-dd',
				'changeMonth'     => true,
				'changeYear'      => true,
				'showButtonPanel' => false,
			),
        ),    
		array(
            'name' => esc_html__('Do not change', 'borntogive'),
            'id' => $prefix . 'event_frequency_end',
            'desc' => esc_html__("If any changes done in this file, may your theme will not work like running now.", 'borntogive'),
            'type' => 'hidden',
        ),    
    )
);
/* Post Format Box
  ================================================== */
$meta_boxes[] = array(
    'title' => esc_html__('Post Format Gallery', 'borntogive'),
    'pages' => array('post', 'gallery'),
	'show' => array(
	'post_format' => array('gallery'),
		),
    'fields' => array(
		array(
            'name' => esc_html__('Gallery Images', 'borntogive'),
            'id' => $prefix . 'gallery_images',
            'desc' => esc_html__("Upload images for gallery.", 'borntogive'),
            'type' => 'image_advanced',
            'max_file_uploads' => 30
        ),
		array(
            'name' => esc_html__('Slider Speed', 'borntogive'),
            'id' => $prefix . 'gallery_slider_speed',
            'desc' => esc_html__("Default Slider Speed is 5000.", 'borntogive'),
            'type' => 'text',
        ),
       array(
            'name' => esc_html__('Slider Pagination', 'borntogive'),
            'id' => $prefix . 'gallery_slider_pagination',
            'desc' => esc_html__("Enable to show pagination for slider.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'yes' => esc_html__('Enable', 'borntogive'),
                'no' => esc_html__('Disable', 'borntogive'),
            ),
						'std'         => 'yes',
        ),
		array(
            'name' => esc_html__('Slider Auto Slide', 'borntogive'),
            'id' => $prefix . 'gallery_slider_auto_slide',
            'desc' => esc_html__("Select Yes to slide automatically.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'yes' => esc_html__('Yes', 'borntogive'),
                'no' => esc_html__('No', 'borntogive'),
            ),
        ),
		array(
            'name' => esc_html__('Slider Direction Arrows', 'borntogive'),
            'id' => $prefix . 'gallery_slider_direction_arrows',
            'desc' => esc_html__("Select Yes to show slider direction arrows.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'yes' => esc_html__('Yes', 'borntogive'),
                'no' => esc_html__('No', 'borntogive'),
            ),
						'std'         => 'yes',
        ),
		array(
            'name' => esc_html__('Slider Effects', 'borntogive'),
            'id' => $prefix . 'gallery_slider_effects',
            'desc' => esc_html__("Select effects for slider.", 'borntogive'),
            'type' => 'select',
            'options' => array(
                'fade' => esc_html__('Fade', 'borntogive'),
                'slide' => esc_html__('Slide', 'borntogive'),
            ),
						'std'         => 'fade',
        ),
    )
);
/* Post Format Box
  ================================================== */
$meta_boxes[] = array(
    'title' => esc_html__('Post Format Video', 'borntogive'),
    'pages' => array('post', 'gallery'),
	'show' => array(
	'post_format' => array('video'),
		),
    'fields' => array(
        array(
            'name' => esc_html__('Video Url', 'borntogive'),
            'id' => $prefix . 'gallery_video_url',
            'desc' => esc_html__("Enter the Youtube or Vimeo URL.", 'borntogive'),
            'type' => 'url',
        ),
    )
);

/* Testimonials Meta Box
  ================================================== */
$meta_boxes[] = array(
    'title' => esc_html__('Post Format Video', 'borntogive'),
    'pages' => array('testimonial'),
    'fields' => array(
        array(
            'name' => esc_html__('View Full Story Button Text', 'borntogive'),
            'id' => $prefix . 'full_story_btn',
            'desc' => esc_html__("Enter Button text/label for the Full story button. Default is: View Full Story)", 'borntogive'),
            'type' => 'text',
        ),
        array(
            'name' => esc_html__('View Full Story Button URL', 'borntogive'),
            'id' => $prefix . 'full_story_url',
            'desc' => esc_html__("Enter URL to show button below testimonial content in Testimonial Type 1 (Full width story slider)", 'borntogive'),
            'type' => 'url',
        ),
        array(
            'name' => esc_html__('Open in New Tab', 'borntogive'),
            'id' => $prefix . 'full_story_url_target',
            'desc' => esc_html__("Check if you wish to open the above URL in a new window", 'borntogive'),
            'type' => 'checkbox',
        ),
    )
);
/* Post Format Box
  ================================================== */
$meta_boxes[] = array(
    'title' => esc_html__('Post Format Link', 'borntogive'),
    'pages' => array('post', 'gallery'),
	'show' => array(
	'post_format' => array('link'),
		),
    'fields' => array(
        array(
            'name' => esc_html__('Link Url', 'borntogive'),
            'id' => $prefix . 'gallery_link_url',
            'desc' => esc_html__("Enter the Link URL.", 'borntogive'),
            'type' => 'url',
        ),
    )
);
/* Post Format Box
  ================================================== */
$meta_boxes[] = array(
    'title' => esc_html__('Post Format Audio', 'borntogive'),
    'pages' => array('post', 'gallery'),
	'show' => array(
	'post_format' => array('audio'),
		),
    'fields' => array(
        array(
            'name' => esc_html__('Audio', 'borntogive'),
            'id' => $prefix . 'gallery_uploaded_audio',
            'desc' => esc_html__("Soundcloud Audio URL", 'borntogive'),
            'type' => 'text',
        ),
    )
);
/* Post Format Box
  ================================================== */
$meta_boxes[] = array(
    'title' => esc_html__('Post Format Quote', 'borntogive'),
    'pages' => array('post', 'gallery'),
	'show' => array(
	'post_format' => array('quote'),
		),
    'fields' => array(
		array(
            'name' => esc_html__('Quotation Author', 'borntogive'),
            'id' => $prefix . 'gallery_quote_author',
            'desc' => esc_html__("Author of quote.", 'borntogive'),
            'type' => 'text',
        ),
    )
);
/* Team Meta Box
  ================================================== */
$meta_boxes[] = array(
    'id' => 'team_meta_box',
    'title' => esc_html__('Team Member Information', 'borntogive'),
    'pages' => array('team'),
	'priority' => 'high',
    'fields' => array(
		array(
            'name' => esc_html__('Position', 'borntogive'),
            'id' => $prefix . 'staff_position',
            'desc' => esc_html__("Enter designation of team member.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Email', 'borntogive'),
            'id' => $prefix . 'staff_member_email',
            'desc' => esc_html__("Enter the team member's Email.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Phone no.', 'borntogive'),
            'id' => $prefix . 'staff_member_phone',
            'desc' => esc_html__("Enter the team member's Phone number.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Facebook', 'borntogive'),
            'id' => $prefix . 'staff_member_facebook',
            'desc' => esc_html__("Enter team member's Facebook Account URL.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Twitter', 'borntogive'),
            'id' => $prefix . 'staff_member_twitter',
            'desc' => esc_html__("Enter team member's Twitter Account URL.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Google Plus', 'borntogive'),
            'id' => $prefix . 'staff_member_gplus',
            'desc' => esc_html__("Enter team member's Google Plus Profile URL.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Linkedin', 'borntogive'),
            'id' => $prefix . 'staff_member_linkedin',
            'desc' => esc_html__("Enter team member's Linkedin Profile URL.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Pinterest', 'borntogive'),
            'id' => $prefix . 'staff_member_pinterest',
            'desc' => esc_html__("Enter team member's Pinterest Profile URL.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
    )
);
/* Registrant Meta Box
  ================================================== */
$meta_boxes[] = array(
    'id' => 'registrant_meta_box',
    'title' => esc_html__('Registrant Details', 'borntogive'),
    'pages' => array('event_registrants', 'exhibition_reg'),
		'fields' => array(
		array(
            'name' => esc_html__('Time', 'borntogive'),
            'id' => $prefix . 'registrant_exhibition_time',
            'type' => 'text',
            'std' => '',
        ),
		),
    'fields' => array(
		array(
            'name' => esc_html__('Email', 'borntogive'),
            'id' => $prefix . 'registrant_email',
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Phone', 'borntogive'),
            'id' => $prefix . 'registrant_phone',
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Address', 'borntogive'),
            'id' => $prefix . 'registrant_address',
            'type' => 'textarea',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Addition Notes', 'borntogive'),
            'id' => $prefix . 'registrant_additional_notes',
            'type' => 'textarea',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Date', 'borntogive'),
            'id' => $prefix . 'registrant_event_date',
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Transaction ID', 'borntogive'),
            'id' => $prefix . 'registrant_transaction',
			//'desc' => esc_html__("Value is verified online.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Payment Status', 'borntogive'),
            'id' => $prefix . 'registrant_payment_status',
			//'desc' => esc_html__("Value is verified online.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Paid Amount', 'borntogive'),
            'id' => $prefix . 'registrant_paid_amount',
			//'desc' => esc_html__("Value is verified online.", 'borntogive'),
            'type' => 'text',
            'std' => '',
        ),
		array(
            'name' => esc_html__('Registration Number', 'borntogive'),
            'id' => $prefix . 'registrant_registration_number',
            'type' => 'text',
            'std' => '',
        ),
    )
);
/* * ******************* META BOX REGISTERING ********************** */
/**
 * Register meta boxes
 *
 * @return void
 */
function borntogive_register_meta_boxes() {
    global $meta_boxes;
    // Make sure there's no errors when the plugin is deactivated or during upgrade
    if (class_exists('RW_Meta_Box')) {
        foreach ($meta_boxes as $meta_box) {
            new RW_Meta_Box($meta_box);
        }
    }
}
// Hook to 'admin_init' to make sure the meta box class is loaded before
// (in case using the meta box class in another plugin)
// This is also helpful for some conditionals like checking Page template, categories, etc.
add_action('admin_init', 'borntogive_register_meta_boxes');
/* * ******************* META BOX CHECK ********************** */
/**
 * Check if meta boxes is included
 *
 * @return bool
 */
function rw_maybe_include($template_file) {
    // Include in back-end only
    if (!defined('WP_ADMIN') || !WP_ADMIN)
        return false;
    // Always include for ajax
    if (defined('DOING_AJAX') && DOING_AJAX)
        return true;
    // Check for post IDs
    $checked_post_IDs = array();
    if (isset($_GET['post']))
        $post_id = $_GET['post'];
    elseif (isset($_POST['post_ID']))
        $post_id = $_POST['post_ID'];
    else
        $post_id = false;
    $post_id = (int) $post_id;
    if (in_array($post_id, $checked_post_IDs))
        return true;
    // Check for Page template
    $checked_templates = array($template_file);
    $template = get_post_meta($post_id, '_wp_page_template', true);
    if (in_array($template, $checked_templates))
        return true;
// If no condition matched
    return false;
}
?>