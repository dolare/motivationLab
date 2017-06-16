<?php 
get_header();
global $borntogive_options;
borntogive_sidebar_position_module();
$theme_info = wp_get_theme();
$tickets_type = get_post_meta(get_the_ID(), 'tickets_type', true);
$multiple_tickets = (!empty($tickets_type))?1:'';
$invalid_name = esc_html__('You must enter your name','borntogive');
$invalid_email = esc_html__('You must enter your email','borntogive');
$process = esc_html__('Sending Information to Event Manager...', 'borntogive');
$tickets_empty = esc_html__('Please select tickets', 'borntogive');
wp_enqueue_script('borntogive_event_register_validation', BORNTOGIVE_THEME_PATH . '/js/event-register-validation.js', array('jquery'), $theme_info->get( 'Version' ), true);
wp_localize_script('borntogive_event_register_validation', 'event_registration', array('url' => admin_url('admin-ajax.php'),'name'=>$invalid_name,'emails'=>$invalid_email, 'process'=>$process, 'tickets'=>$tickets_empty, 'multiple'=>$multiple_tickets));
if(get_query_var('reg')==1||get_query_var('reg')==2||get_query_var('reg')==3)
{
	wp_enqueue_script('borntogive_event_pay',BORNTOGIVE_THEME_PATH . '/js/event_pay.js',array('jquery'),'',true);
	wp_localize_script('borntogive_event_pay', 'event_payment', array('name'=>get_query_var('reg')));
}
$pageSidebarGet = get_post_meta(get_the_ID(),'borntogive_select_sidebar_from_list', true);
$pageSidebarStrictNo = get_post_meta(get_the_ID(),'borntogive_strict_no_sidebar', true);
$pageSidebarOpt = $borntogive_options['event_sidebar'];
if($pageSidebarGet != ''){
	$pageSidebar = $pageSidebarGet;
}elseif($pageSidebarOpt != ''){
	$pageSidebar = $pageSidebarOpt;
}else{
	$pageSidebar = '';
}
if($pageSidebarStrictNo == 1){
	$pageSidebar = '';
}
$sidebar_column = get_post_meta(get_the_ID(),'borntogive_sidebar_columns_layout',true);
$sidebar_column = ($sidebar_column=='')?4:$sidebar_column;
if(!empty($pageSidebar)&&is_active_sidebar($pageSidebar)) {
$left_col = 12-$sidebar_column;
$class = $left_col;  
}else{
$class = 12;  
}
$page_header = get_post_meta(get_the_ID(),'borntogive_pages_Choose_slider_display',true);
if($page_header==3||$page_header==4) {
	get_template_part( 'pages', 'flex' );
}
elseif($page_header==5) {
	get_template_part( 'pages', 'revolution' );
} else {
	get_template_part( 'pages', 'banner' );
}
$event_start_time = strtotime(get_post_meta(get_the_ID(), 'borntogive_event_start_dt', true));
$event_end_time = strtotime(get_post_meta(get_the_ID(), 'borntogive_event_end_dt', true));
if(get_query_var('event_date'))
{
$this_date = get_query_var('event_date');
}
else
{
	$events = borntogive_recur_events("future", "", "","", "");
	ksort($events);
	foreach($events as $key=>$value)
	{
		if($value==get_the_ID())
		{
			$this_date = date('Y-m-d', $key);
			break;
		}
		else
		{
			$this_date = date('Y-m-d', $event_start_time);
		}
	}
}
update_post_meta(get_query_var('registrant'), 'borntogive_registrant_event_date', get_query_var('event_date'));
$this_date = strtotime($this_date);
$event_url = borntogive_event_arg(date('Y-m-d', $this_date), get_the_ID());
$paid = '';
if(!empty($tickets_type))
{
	foreach($tickets_type as $ticket_one)
	{
		if(is_numeric($ticket_one[3]))
		{
			$paid = 1;
			break;
		}
	}
}
//Verify Paypal Payment
$transaction_id=isset($_REQUEST['tx'])?esc_attr($_REQUEST['tx']):'';
$st = '';
	if($transaction_id!='') 
	{
			$st = isset($_REQUEST['st'])?esc_attr($_REQUEST['st']):'';
			$payment_gross = isset($_REQUEST['amt'])?esc_attr($_REQUEST['amt']):'';
			update_post_meta(get_query_var('registrant'), 'borntogive_registrant_payment_status', $st);
			update_post_meta(get_query_var('registrant'), 'borntogive_registrant_paid_amount', $payment_gross);
			update_post_meta(get_query_var('registrant'), 'borntogive_registrant_transaction', $transaction_id);
	}
$attendees = get_post_meta(get_the_ID(), 'borntogive_event_attendees', true);
$manager_name = get_post_meta(get_the_ID(), 'borntogive_event_manager_name', true);
$manager_email = get_post_meta(get_the_ID(), 'borntogive_event_manager', true);
$event_address = get_post_meta(get_the_ID(), 'borntogive_event_address', true);
$registrationswitch = get_post_meta(get_the_ID(),'borntogive_event_registration', true);
$registrationcurl = get_post_meta(get_the_ID(),'borntogive_custom_event_registration', true);
$registrationcurltarget = get_post_meta(get_the_ID(),'borntogive_custom_event_registration_target', true);
if($registrationcurltarget == 1){
	$rurltar = ' target="_blank"';
} else {
	$rurltar = '';	
}
?>
<!-- Main Content -->
    <div id="main-container">
    	<div class="content">
        	<div class="container">
            	<div class="row">
                	<div class="col-md-<?php echo esc_attr($class); ?>" id="content-col">
                    	<h3><?php echo get_the_title(); ?></h3>
						<?php echo get_the_term_list( $post->ID, 'event-category', '<i class="fa fa-folder"></i> ', ', ', '<div class="spacer-30"></div>' ); ?>
                      <?php if(has_post_thumbnail(get_the_ID())) { ?>
                    	<div class="post-media">
                        	<?php echo get_the_post_thumbnail(get_the_ID()); ?>
                        </div>
                        <?php } ?>
                        <div class="row">
                        	<div class="col-md-6 col-sm-6">
                                <span class="event-date">
                                    <span class="date"><?php echo esc_attr(date_i18n('d', $this_date)); ?></span>
                                    <span class="month"><?php echo esc_attr(date_i18n('M', $this_date)); ?></span>
                                    <span class="year"><?php echo esc_attr(date_i18n('Y', $this_date)); ?></span>
                                </span>
                               	<?php 
								$event_start_date = get_post_meta(get_the_ID(), 'borntogive_event_start_dt', true);
								$event_end_date = get_post_meta(get_the_ID(), 'borntogive_event_end_dt', true);
								$event_start_date_unix = strtotime($event_start_date);
								$event_end_date_unix = strtotime($event_end_date);
								$permalink = borntogive_event_arg(date('Y-m-d', $this_date), get_the_ID());
								$days_total = borntogive_dateDiff($event_start_date, $event_end_date);
								$event_meta_show = $borntogive_options['event_meta_date'];
								$event_map_show = (isset($borntogive_options['show_event_map']))?$borntogive_options['show_event_map']:1;
								$event_direction_btn = (isset($borntogive_options['show_direction_link']))?$borntogive_options['show_direction_link']:1;
								$multi_date_separator = (isset($borntogive_options['multi_date_separator']))?$borntogive_options['multi_date_separator']:'';
								$event_date_separator = $borntogive_options['event_multi_separator'];
									if($event_meta_show==1)
									{
										if($days_total>=1)
										{
											$event_output .= '<span class="meta-data">'.esc_attr(date_i18n(get_option('date_format'), $event_start_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix)).'</span>';
										}
										else
										{
											$event_output .= '<span class="meta-data">'.esc_attr(date_i18n('l', $this_date)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix)).'</span>';
										}
									}
									else
									{
										if($days_total>=1)
										{
											echo '<span class="meta-data">'.esc_attr(date_i18n(get_option('date_format'), $event_start_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix));
											if($event_end_date_unix!='')
											{
												echo $multi_date_separator.esc_attr(date_i18n(get_option('date_format'), $event_end_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_end_date_unix));
											}
											echo '</span>';
										}
										else
										{
											echo '<span class="meta-data">'.esc_attr(date_i18n('l', $event_start_date_unix)).$event_date_separator.esc_attr(date_i18n(get_option('time_format'), $event_start_date_unix)); 
											if($event_end_date_unix!='')
											{
												echo '-'.esc_attr(date_i18n(get_option('time_format'), $event_end_date_unix));
											}
											echo '</span>';
										}
									}
								?>
								<?php if($registrationswitch != 0){
								if($registrationcurl != ''){ ?>
									<a href="<?php echo $registrationcurl; ?>" <?php echo $rurltar; ?> class="btn btn-primary btn-event-single-book"><?php esc_html_e('Book Online','borntogive'); ?></a>
								<?php }
								else { ?>
									 <a href="#" data-target="#event_register" data-toggle="modal" class="btn btn-primary btn-event-single-book"><?php esc_html_e('Book Online','borntogive'); ?></a>
								<?php } } ?>
                      		</div>
                            <div class="col-md-6 col-sm-6">
                                <ul class="list-group event-list-group">
                                <?php if($attendees!='')
								  {
	 echo '<li class="list-group-item">'.$attendees.'<span class="badge">'.esc_html__('Attendees', 'borntogive').'</span></li>';
								  }
								  if($event_address!='')
								  {
									  echo '<li class="list-group-item">'.$event_address.'<span class="badge">'.esc_html__('Location', 'borntogive').'</span>';
									  if($event_map_show==1){
										 	echo '<br><a href="#" class="basic-link toggle-event-map">'.esc_html__('Map','borntogive').'</a>';
									  }
									  if($event_direction_btn==1){
										 	echo '<a href="https://www.google.com/maps/dir//'.$event_address.'" target="_blank" class="basic-link">'.esc_html__('Directions','borntogive').'</a>';
									  }
									   echo '<div class="map-toggle-window">'.do_shortcode('[gmap address="'.$event_address.'"]').'</div>';
									   echo '</li>';
								  }
								  if($manager_name!='')
								  {
									  if($manager_email != ''){
									  	echo '<li class="list-group-item"><a href="mailto:'.$manager_email.'">'.$manager_name.'</a><span class="badge">'.esc_html__('Manager', 'borntogive').'</span></li>';
									  } else {
									  	echo '<li class="list-group-item">'.$manager_name.'<span class="badge">'.esc_html__('Manager', 'borntogive').'</span></li>';
									  }
								  }
								  ?> 
                                </ul>
                            </div>
                        </div>
                        <div class="spacer-20"></div>
                      	<?php 
						if(have_posts()):while(have_posts()):the_post();
						
						echo '<div class="post-content">';
							the_content();
						echo '</div>';
						endwhile; endif; ?>
                        <?php echo apply_filters('the_content', get_post_meta(get_the_ID(), 'borntogive_campaign_editor', true)); ?>
                        
						<?php echo get_the_term_list( $post->ID, 'event-tag', '<i class="fa fa-tag"></i> ', ', ', '' ); ?>
						<?php if ($borntogive_options['switch_sharing'] == 1 && $borntogive_options['share_post_types']['3'] == '1') { ?>
                            <?php borntogive_share_buttons(); ?>
                        <?php } ?>
                    </div>
                    <?php if(is_active_sidebar($pageSidebar)) { ?>
                    <!-- Sidebar -->
                    <div class="col-md-<?php echo esc_attr($sidebar_column); ?>" id="sidebar-col">
                    	<?php dynamic_sidebar($pageSidebar); ?>
                    </div>
                    <?php } ?>
            	</div>
           	</div>
     	</div>
 	</div>
    <!--Event Registration Popup Start-->
    <?php
		$borntogive_options = get_option('borntogive_options');
		$paypal_payment = (isset($borntogive_options['paypal_site']))?$borntogive_options['paypal_site']:'';
		$paypal_payment = ($paypal_payment=="1")?"https://www.paypal.com/cgi-bin/webscr":"https://www.sandbox.paypal.com/cgi-bin/webscr";
		$paypal_src = (!empty($tickets_type)&&$paid==1)?$paypal_payment:'';
		$business_email = (isset($borntogive_options['paypal_email']))?$borntogive_options['paypal_email']:'';
		$paypal_currency = (isset($borntogive_options['paypal_currency']))?$borntogive_options['paypal_currency']:'USD';
		wp_localize_script('borntogive_event_register_validation', 'event_registration_new', array('paypal_src'=>$paypal_src, 'reg'=>esc_html__('Register', 'borntogive'), 'pays'=>esc_html__('Proceed to Paypal', 'borntogive')));
		?>
<div class="modal fade" id="event_register" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="event_registerLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            	<h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Register for Event: ','borntogive'); ?><span class="accent-color payment-to-cause"><?php echo get_the_title(); ?></span></h4>
                            </div>
                            <div class="modal-body">
                            <form id="event_register_form" class="" name="" class="" action="<?php echo esc_url($paypal_src); ?>" method="post">
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="" id="username" name="fname" class="form-control" placeholder="<?php esc_html_e('First name', 'borntogive'); ?> (Required)">
                                <input type="hidden" value="<?php echo esc_attr(get_the_ID()); ?>" id="event_id">
                                <input type="hidden" value="<?php echo esc_attr(date_i18n(get_option('date_format'), $this_date)); ?>" id="event_date">
                            </div>
                        	<div class="col-md-6">
                        		<input id="lastname" type="text" value="" name="lname" class="form-control" placeholder="<?php esc_html_e('Last name', 'borntogive'); ?>">
                            </div>
                      	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="" name="email" id="email" class="form-control" placeholder="<?php esc_html_e('Your email', 'borntogive'); ?> (Required)">
                            </div>
                        	<div class="col-md-6">
                        		<input id="phone" type="phone" name="phone" class="form-control" placeholder="<?php esc_html_e('Your phone', 'borntogive'); ?>">
                            </div>
                       	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<textarea id="address" rows="3" cols="5" class="form-control" placeholder="<?php esc_html_e('Your Address', 'borntogive'); ?>"></textarea>
                            </div>
                        	<div class="col-md-6">
                        		<textarea id="notes" rows="3" cols="5" class="form-control" placeholder="<?php esc_html_e('Additional Notes', 'borntogive'); ?>"></textarea>
                            </div>
                       	</div>
                        <?php
						$book_number = 1;
						if(!empty($tickets_type))
						{
                        echo '<table width="100%" class="table-tickets">';
						echo '<tr class="head-table-tickets">';
							echo '<td>'.esc_attr__('Type', 'borntogive').'</td>';
							echo '<td>'.esc_attr__('Available ', 'borntogive').'</td>';
							echo '<td>'.esc_attr__('Price', 'borntogive').'</td>';
							echo '<td>'.esc_attr__('Quantity', 'borntogive').'</td>';
							echo '<td>'.esc_attr__('Total', 'borntogive').'</td>';
						echo '</tr>';
						foreach($tickets_type as $tickets)
						{
							$available_ticket = $tickets[1]-$tickets[2];
							$available_ticket = ($available_ticket>=0)?$available_ticket:0;
							$field_tickets_available = ($available_ticket>10)?10:$available_ticket;
								echo '<tr>';
									echo '<td>'.$tickets[0].'</td>';
									echo '<td>'.esc_attr($available_ticket).'</td>';
									if(is_numeric($tickets[3]))
									{
										echo '<td>'.$paypal_currency.' '.esc_attr($tickets[3]).'</td>';
									}
									elseif($tickets[3]=='')
									{
										echo '<td>'.esc_html__('Free', 'borntogive').'</td>';
									}
									else
									{
										echo '<td>'.esc_attr($tickets[3]).'</td>';
									}
									echo '<td>';
									if($available_ticket>0)
									{
										echo '<select data-title="'.$tickets[0].'" data-price="'.esc_attr($tickets[3]).'" class="event-tickets selectpicker">';
										for($x=0; $x<=$field_tickets_available; $x++)
										{
											echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';
										}
										echo '</select>';
									}
									else
									{
										echo '<label>'.esc_attr_e('All Tickets Booked', 'borntogive').'</label>';
									}
									echo '</td>';
									echo '<td>';
									if($available_ticket>0&&$tickets[3]>0)
									{
										//Tickets Total Price
										echo $paypal_currency.' <span class="total-cost-event"></span></label>';
									}
									echo '</td>';
								echo '</tr>';
									
							$book_number++;
						}
						echo '<input type="hidden" name="rm" value="2">';
						echo '<input type="hidden" name="amount" value="">';	
						echo '<input type="hidden" name="cmd" value="_xclick">';
						echo '<input type="hidden" name="business" value="'.$business_email.'">';
						echo '<input type="hidden" name="currency_code" value="'.$paypal_currency.'">';
						echo '<input type="hidden" name="item_name" value="'.stripslashes(get_the_title(get_the_ID())).'">';
						echo '<input type="hidden" name="item_number" value="'.get_the_ID().'">';
						echo '<input type="hidden" name="return" value="'.esc_url($event_url).'" />';
						}
							echo '</table>';
						?>	
                        <?php wp_nonce_field( 'ajax-exhibition-nonce', 'security' );
						if(empty($tickets_type)||$paid==0)
												{ ?>
                        <input id="submit-registration" type="submit" name="donate" class="btn btn-primary btn-lg btn-block" value="<?php esc_html_e('Register', 'borntogive'); ?>">
                        <?php } elseif($available_ticket>0&&$paid==1) { ?>
                        <input id="submit-registration" type="submit" name="donate" class="btn btn-primary btn-lg btn-block" value="<?php esc_html_e('Proceed to Paypal', 'borntogive'); ?>">
                        <?php } ?><br/>
						<div class="message"></div>
                    </form>
                            </div>
                            <div class="modal-footer">
                            	<p class="small short"><?php esc_html_e('Make sure to copy Registration number after successful submission.', 'borntogive'); ?></p>
                            </div>
                        </div>
                        </div>
                    </div>
<!--Event Registration Popup End-->
<!--Event Payment Thanks Popup-->
<div class="modal fade" id="event_register_thanks" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="event_register_thanksLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            	<h4 class="modal-title"><?php esc_html_e('Registered Successfully','borntogive'); ?></h4>
                            </div>
                            <div class="modal-body">
                            <div class="text-align-center error-404">
                                <h1 class="huge"><?php esc_html_e('Thanks','borntogive'); ?></h1>
                                  <hr class="sm">
                                  <p><strong><?php esc_html_e('Thank you for payment.','borntogive'); ?></strong></p>
                          					<p><?php esc_html_e('Your payment is verified online.', 'borntogive');
                          					echo '<br>';
                          					esc_html_e('Your payment status showing payment ','borntogive'); echo '<strong>'.$st.'</strong>'; ?></p>
                          	</div>
                            </div>
                            <div class="modal-footer">
                            	<a href="" id="find-ticket" class="btn btn-primary btn-lg btn-block"><?php echo esc_attr_e('Find Ticket', 'borntogive'); ?></a>
                            </div>
                        </div>
                        </div>
                    </div>
<?php get_footer(); ?>