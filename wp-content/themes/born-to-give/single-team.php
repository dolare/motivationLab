<?php
get_header();
global $borntogive_options;
borntogive_sidebar_position_module();
$pageSidebarGet = get_post_meta(get_the_ID(),'borntogive_select_sidebar_from_list', true);
$pageSidebarStrictNo = get_post_meta(get_the_ID(),'borntogive_strict_no_sidebar', true);
$pageSidebarOpt = $borntogive_options['team_sidebar'];
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
$facebook = get_post_meta(get_the_ID(), 'borntogive_staff_member_facebook', true);
	$twitter = get_post_meta(get_the_ID(), 'borntogive_staff_member_twitter', true);
	$gplus = get_post_meta(get_the_ID(), 'borntogive_staff_member_gplus', true);
	$linkedin = get_post_meta(get_the_ID(), 'borntogive_staff_member_linkedin', true);
	$pinterest = get_post_meta(get_the_ID(), 'borntogive_staff_member_pinterest', true);
	$email = get_post_meta(get_the_ID(), 'borntogive_staff_member_email', true);
	$phone = get_post_meta(get_the_ID(), 'borntogive_staff_member_phone', true);
	if($phone != ''){
		$phoneno = '<span class="label label-primary" style="display:inline-block; font-size:14px;"><i class="fa fa-phone"></i> '. $phone.'</span>';	
	} else {
		$phoneno = '';	
	}
	$social = '';
	$social_data = array();
	$social_data = array('envelope'=>$email, 'facebook'=>$facebook, 'twitter'=>$twitter, 'google-plus'=>$gplus, 'linkedin'=>$linkedin, 'pinterest'=>$pinterest);
	if($facebook!=''||$twitter!=''||$gplus!=''||$linkedin!=''||$pinterest!=''||$email!='')
	{
	$social .= '<ul class="social-icons-rounded social-icons-colored margin-20">';
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
?>
<?php
echo '<div id="main-container">
  	<div class="content">
   		<div class="container">
       		<div class="row">
					<div class="col-md-'.esc_attr($class).'" id="content-col">';
if(have_posts()):while(have_posts()):the_post();
	echo '<h3 class="margin-0">'.get_the_title().'</h3>';
	echo '<span class="meta-data">'.get_post_meta(get_the_ID(), 'borntogive_staff_position', true).'</span><div class="spacer-30"></div>';
	if(has_post_thumbnail())
	{
		echo '<div class="post-media">
		'.get_the_post_thumbnail(get_the_ID()).'
			</div>
				';
	}
	echo ''.$social;
	echo ''.$phoneno;
	echo '<div class="spacer-30"></div><div class="post-content">';
	the_content();
	echo '</div>';
endwhile; endif; ?>
                        <?php echo apply_filters('the_content', get_post_meta(get_the_ID(), 'borntogive_campaign_editor', true)); ?>
						<?php if ($borntogive_options['switch_sharing'] == 1 && $borntogive_options['share_post_types']['4'] == '1') { ?>
                            <?php borntogive_share_buttons(); ?>
                        <?php } 
echo '</div>';
if(is_active_sidebar($pageSidebar)&&$pageSidebar!='') { ?>
<!-- Sidebar -->
<div class="col-md-<?php echo esc_attr($sidebar_column); ?>" id="sidebar-col">
<?php dynamic_sidebar($pageSidebar); ?>
</div>
<?php }
echo '</div></div></div></div>';
?>
<?php
get_footer();
?>