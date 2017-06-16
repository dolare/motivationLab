<?php
$menu_locations = get_nav_menu_locations();
$borntogive_options = get_option('borntogive_options');
$fbottomcont = (isset($borntogive_options['footer_bottom_cont_type']))?$borntogive_options['footer_bottom_cont_type']:'';
 ?>
    <!-- Site Footer -->
    <?php if(is_active_sidebar('footer-sidebar')) { ?>
    <div class="site-footer">
    	<div class="container">
        	<div class="row">
          		<?php dynamic_sidebar('footer-sidebar'); ?>
          	</div>
     	</div>
    </div>
    <?php } ?>
    <div class="site-footer-bottom">
    	<div class="container">
        	<div class="row">
            	<?php if($fbottomcont == 2 ) { ?>
            		<div class="col-md-12 col-sm-12">
                <?php } else { ?>
            		<div class="col-md-6 col-sm-6">
                <?php } ?>
				<?php if (!empty($borntogive_options['footer_copyright_text'])) { ?>
                	<div class="copyrights-col-left">
                   		<p><?php echo ''.($borntogive_options['footer_copyright_text']); ?></p>
                  	</div>
             	<?php } ?>
                </div>
				<?php if($fbottomcont != 2 ) { ?>
            	<div class="col-md-6 col-sm-6">
					<?php if($fbottomcont == 0 ) { ?>
                        <?php if (!empty($menu_locations['footer-menu'])) { ?>
                            <div class="copyrights-col-right">
                            <?php
                                  wp_nav_menu(array('theme_location' => 'footer-menu', 'depth' => 1, 'container' => '','items_wrap' => '<ul id="%1$s" class="footer-menu">%3$s</ul>'));
                                                ?>
                            </div>
                        <?php } ?>
                    <?php } elseif($fbottomcont == 1 ) { ?>
                	<div class="copyrights-col-right">
                    	<ul class="social-icons pull-right">
                        <?php
                            $socialSites = $borntogive_options['footer_social_links'];
                            foreach ($socialSites as $key => $value) {
                                $string = substr($key, 3);
                                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    echo '<li class="'.$string.'"><a href="mailto:' . $value . '"><i class="fa ' . $key . '"></i></a></li>';
                                }
                                if (filter_var($value, FILTER_VALIDATE_URL)) {
                                    echo '<li class="'.$string.'"><a href="' . esc_url($value) . '" target="_blank"><i class="fa ' . $key . '"></i></a></li>';
                                }
                                elseif($key == 'fa-skype' && $value != '') {
                                    echo '<li class="'.$string.'"><a href="skype:' . $value . '?call"><i class="fa ' . $key . '"></i></a></li>';
                                }
                            }
                        ?>
                    	</ul>
                   	</div>
                	<?php } ?>
           		</div>
               	<?php } ?>
      		</div>
  		</div>
	</div>
    <?php if (isset($borntogive_options['enable_backtotop'])&&$borntogive_options['enable_backtotop'] == 1) { 
echo'<a id="back-to-top"><i class="fa fa-angle-double-up"></i></a> ';
 } ?>
</div>
<!-- End Boxed Body -->
 <?php $SpaceBeforeBody = (isset($borntogive_options['space-before-body']))?$borntogive_options['space-before-body']:'';
    echo ''.$SpaceBeforeBody;
 $payment_gross = '';
 $registrant_id = get_query_var('registrant');
 $borntogive_reg_user = $borntogive_reg_id = $borntogive_exhibition_time = $borntogive_exhibition_date = $venue_title = '';
if($registrant_id!='')
{
	$transaction_id=isset($_REQUEST['tx'])?esc_attr($_REQUEST['tx']):'';
	if($transaction_id!='') 
	{
			$payment_gross = isset($_REQUEST['amt'])?esc_attr($_REQUEST['amt']):'';
	}
	$borntogive_reg_user = get_the_title($registrant_id);
	$borntogive_reg_id = get_post_meta($registrant_id, 'borntogive_registrant_registration_number', true);
	$borntogive_exhibition_time = get_post_meta($registrant_id, 'borntogive_registrant_exhibition_time', true);
	if(get_post_type(get_the_ID())=="event")
	{
		$borntogive_exhibition_time = get_post_meta(get_the_ID(), 'borntogive_event_start_dt', true);
		$borntogive_exhibition_time = strtotime($borntogive_exhibition_time);
		$borntogive_exhibition_time = date(get_option('time_format'), $borntogive_exhibition_time);
	}
	$borntogive_exhibition_date = get_post_meta($registrant_id, 'borntogive_registrant_event_date', true);
	$borntogive_exhibition_date = strtotime($borntogive_exhibition_date);
	$borntogive_exhibition_date = date_i18n(get_option('date_format'), $borntogive_exhibition_date);
}
else
{
	if(get_post_type(get_the_ID())=="event")
	{
		$borntogive_exhibition_time = get_post_meta(get_the_ID(), 'borntogive_event_start_dt', true);
		$borntogive_exhibition_time = strtotime($borntogive_exhibition_time);
		$borntogive_exhibition_time = date(get_option('time_format'), $borntogive_exhibition_time);
	}
}
$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all');
	$venue_title = get_post_meta( get_the_ID(), 'borntogive_event_address', true );
$borntogive_options = get_option('borntogive_options');
$paypal_currency = (isset($borntogive_options['paypal_currency']))?$borntogive_options['paypal_currency']:'USD'; ?>
</div>
<!--Ticket Modal-->
<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel"><?php esc_attr_e('Your ticket for the: ','borntogive'); echo get_the_title(); ?></h4>
					</div>
					<div class="modal-body">
						<!-- Event Register Tickets -->
						<div class="ticket-booking-wrapper">
							<div class="ticket-booking">
								<div class="event-ticket ticket-form">
									<div class="event-ticket-left">
										<div class="ticket-id"><?php echo esc_attr($borntogive_reg_id); ?></div>
										<div class="ticket-handle"></div>
										<div class="ticket-cuts ticket-cuts-top"></div>
										<div class="ticket-cuts ticket-cuts-bottom"></div>
									</div>
									<div class="event-ticket-right">
										<div class="event-ticket-right-inner">
											<div class="row">
												<div class="col-md-9 col-sm-9">
													<span class="registerant-info">
                          <?php echo esc_attr($borntogive_reg_user); ?>
													</span>
													 <span class="meta-data"><?php esc_html_e('Title','borntogive'); ?></span>
													 <h4 id="dy-event-title"><?php echo get_the_title(); ?></h4>
												</div>
												<div class="col-md-3 col-sm-3">
													<span class="ticket-cost"><?php echo esc_attr($paypal_currency); echo esc_attr($payment_gross); ?></span>
												</div>
											</div>
											<div class="event-ticket-info">
												<div class="row">
													<div class="col">
														<p class="ticket-col" id="dy-event-date"><?php echo esc_attr($borntogive_exhibition_date); ?></p>
													</div>
													<div class="col">
														<p class="ticket-col event-location" id="dy-event-location"><?php echo esc_attr($venue_title); ?></p>
													</div>
													<div class="col">
														<p id="dy-event-time"><?php echo esc_attr($borntogive_exhibition_time); ?></p>
													</div>
												</div>
											</div>
											<span class="event-area"></span>
											<div class="row">
												<div class="col-md-12">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default inverted" data-dismiss="modal"><?php esc_attr_e('Close', 'borntogive'); ?></button>
						<button type="button" class="btn btn-primary" onClick="window.print()"><?php esc_attr_e('Print', 'borntogive'); ?></button>
					</div>
				</div>
			</div>
		</div>
<?php wp_footer(); ?>
</body>
</html>