<?php 
get_header();
global $borntogive_options;
borntogive_sidebar_position_module();
$pageSidebarGet = get_post_meta(get_the_ID(),'borntogive_select_sidebar_from_list', true);
$pageSidebarStrictNo = get_post_meta(get_the_ID(),'borntogive_strict_no_sidebar', true);
$pageSidebarOpt = $borntogive_options['campaign_sidebar'];
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
$campaign = new Charitable_Campaign( get_the_ID() );
$donated = $campaign->get_percent_donated_raw();
$time_left = $campaign->get_time_left();
$goal = $campaign->get_monetary_goal();
$donation_achieved = charitable_format_money( $campaign->get_donated_amount() );
$currency = '';
$donors = $campaign->get_donor_count();
if(have_posts()):while(have_posts()):the_post();
$campaign_desc = get_post_meta(get_the_ID(), '_campaign_description', true);
?>
<!-- Main Content -->
    <div id="main-container">
    	<div class="content">
        	<div class="container">
            	<div class="row">
                	<div class="col-md-<?php echo esc_attr($class); ?>" id="content-col">
                    	<h3><?php echo get_the_title(); ?></h3>
                    	<div class="post-media">
                        	<?php echo get_the_post_thumbnail(get_the_ID()); ?>
                        </div>
                        <div class="cause-progress-and-info">
                        	<span class="label label-default"><?php esc_html_e('Cause progress', 'borntogive'); ?></span>
                            
                        	<div class="progress">
                            	<div class="progress-bar progress-bar-primary" data-appear-progress-animation="<?php echo round($donated, 0, PHP_ROUND_HALF_EVEN).'%'; ?>" data-appear-animation-delay="100"><?php if($donated != ''){ ?><span class="progress-bar-tooltip"><?php echo round($donated, 0, PHP_ROUND_HALF_EVEN).'%'; ?></span><?php } ?></div>
                        	</div>
                            
                       		<div class="pull-left"><?php esc_html_e('Raised', 'borntogive'); ?> <strong><?php echo ''.$currency.$donation_achieved; ?></strong></div>
                       		<div class="pull-right"><?php esc_html_e('Goal', 'borntogive'); ?> <strong class="accent-color"><?php echo esc_attr($goal); ?></strong></div>
                        	<div class="spacer-20"></div>
                        	<div class="row">
                        		<div class="col-md-5 col-sm-5">
                        			<p class="lead"><?php echo $campaign_desc; ?></p>
                      			</div>
                            	<div class="col-md-7 col-sm-7">
                            		<ul class="list-group">
                          				<li class="list-group-item"><?php esc_html_e('Total Donors', 'borntogive'); ?><span class="badge"><?php echo esc_attr($donors); ?></span></li>
                          				<li class="list-group-item"><?php esc_html_e('Remaining Time', 'borntogive'); ?><span class="badge"><?php echo ''.$time_left; ?></span></li>
                          			</ul>
                                	<?php $campaign->donate_button_template(); ?>
                            	</div>
                        	</div>
                     	</div>
                        <div class="post-content">
                        	<?php the_content(); ?>
                        </div>
						<?php if ($borntogive_options['switch_sharing'] == 1 && $borntogive_options['share_post_types']['4'] == '1') { ?>
                            <?php borntogive_share_buttons(); ?>
                        <?php } ?>
                    </div>
                    
                    <!-- Sidebar -->
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
<?php endwhile; endif;
get_footer(); ?>