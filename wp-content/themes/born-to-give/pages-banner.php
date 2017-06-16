<?php 
$borntogive_options = get_option('borntogive_options');
$post_id = get_the_ID();
$post_type = get_post_type($post_id);
$default_event_banner = (isset($borntogive_options['default_event_banner']))?$borntogive_options['default_event_banner']['url']:'';
$default_post_banner = (isset($borntogive_options['default_post_banner']))?$borntogive_options['default_post_banner']['url']:'';
$default_campaign_banner = (isset($borntogive_options['default_campaign_banner']))?$borntogive_options['default_campaign_banner']['url']:'';
$default_team_banner = (isset($borntogive_options['default_team_banner']))?$borntogive_options['default_team_banner']['url']:'';
$default_product_banner = (isset($borntogive_options['default_product_banner']))?$borntogive_options['default_product_banner']['url']:'';
if($post_type=='event' && $default_event_banner != '')
{
	$image_default = $default_event_banner;
}
elseif($post_type=='post' && $default_post_banner != '')
{
	$image_default = $default_post_banner;
}
elseif($post_type=='campaign' && $default_campaign_banner != '')
{
	$image_default = $default_campaign_banner;
}
elseif($post_type=='team' && $default_team_banner != '')
{
	$image_default = $default_team_banner;
}
elseif($post_type=='product' && $default_product_banner != '')
{
	$image_default = $default_product_banner;
}
else{
	$image_default = (isset($borntogive_options['borntogive_default_banner']))?$borntogive_options['borntogive_default_banner']['url']:'';
}
if(is_home()) { $id = get_option('page_for_posts'); }
elseif(class_exists('buddypress') && is_buddypress()){
	$component = bp_current_component();
	$bp_pages = get_option( 'bp-pages' );
	$id = $bp_pages[$component];
}
else { $id = get_the_ID(); }
$image = $banner_type = '';
$type = get_post_meta($id,'borntogive_pages_Choose_slider_display',true);
if($type==1 || $type==2) {
$height = get_post_meta($id,'borntogive_pages_slider_height',true);
} else {
	$height = '';
}
$color = get_post_meta($id,'borntogive_pages_banner_color',true);
$color = ($color!='' && $color!='#')?$color:'';
if($type==2) {
$image = get_post_meta($id,'borntogive_header_image',true);
$image_src = wp_get_attachment_image_src( $image, 'full', '', array() );
if(is_array($image_src)) { $image = $image_src[0]; } else { $image = $image_default; } }
$event_archive_title = (isset($borntogive_options['events_archive_title']))?$borntogive_options['events_archive_title']:__('Events', 'borntogive');
$blog_archive_title = (isset($borntogive_options['blog_archive_title']))?$borntogive_options['blog_archive_title']:__('Blog', 'borntogive');
$causes_archive_title = (isset($borntogive_options['causes_archive_title']))?$borntogive_options['causes_archive_title']:__('Causes', 'borntogive');
$team_archive_title = (isset($borntogive_options['team_archive_title']))?$borntogive_options['team_archive_title']:__('Team', 'borntogive');
$shop_archive_title = (isset($borntogive_options['shop_archive_title']))?$borntogive_options['shop_archive_title']:__('Shop', 'borntogive');

if($post_type=='event')
{
	$banner_title = $event_archive_title;
}
elseif($post_type=='post')
{
	if(is_single()){
		if($blog_archive_title == '%author%'){
			$post_author_id = get_post_field( 'post_author', get_the_ID() );
			$blog_title = get_the_author_meta('display_name', $post_author_id);
		}
		elseif($blog_archive_title == '%category%'){
			$categories = get_the_category();
			$categories_list = '';
			if(!empty($categories))
			{
			   foreach($categories as $category)
			   {
				$blog_title = $category->name;
			   }
			}
		}
		else {
			$blog_title = $blog_archive_title;
		}
	}
	elseif (is_category() || is_tag()){
		$blog_title = single_term_title("", false);
	}
	elseif (is_author()){
		global $author;
        $userdata = get_userdata($author);
		$blog_title = $userdata->display_name;
	}
	elseif((is_home() || is_archive()) && ($blog_archive_title == '%author%' || $blog_archive_title == '%category%')){
		$blog_title = __('Blog', 'borntogive');
	}
	else{
		$blog_title = $blog_archive_title;
	}
	$banner_title = $blog_title;
}
elseif($post_type=='campaign')
{
	$banner_title = $causes_archive_title;
}
elseif($post_type=='team')
{
	$banner_title = $team_archive_title;
}
elseif($post_type=='product')
{
	$banner_title = $shop_archive_title;
}
else
{
	$banner_title = get_the_title(get_the_ID());
}
?>
 <div class="hero-area">
 <?php if($color!='')
 {
	 echo '<div class="page-banner parallax" style=" background-color:'.esc_attr($color).'; height:'.esc_attr($height).'px'.';">';
 }
 else
 {
	 echo '<div class="page-banner parallax" style="background-image:url('.esc_url($image).'); height:'.esc_attr($height).'px'.';">';
 }
 ?>
        	<div class="container">
            	<div class="page-banner-text">
        			<h1 class="block-title"><?php echo esc_attr($banner_title); ?></h1>
                </div>
            </div>
        </div>
    </div>