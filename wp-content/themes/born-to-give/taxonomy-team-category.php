<?php
get_header();
$borntogive_options = get_option('borntogive_options');
$sidebar = $borntogive_options['team_archive_sidebar'];
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
					$team_output = '<ul class="grid-holder gallery-items">';
if(have_posts()):while(have_posts()):the_post();
	$position = get_post_meta(get_the_ID(), 'borntogive_staff_position', true);
	$facebook = get_post_meta(get_the_ID(), 'borntogive_staff_member_facebook', true);
	$twitter = get_post_meta(get_the_ID(), 'borntogive_staff_member_twitter', true);
	$gplus = get_post_meta(get_the_ID(), 'borntogive_staff_member_gplus', true);
	$pinterest = get_post_meta(get_the_ID(), 'borntogive_staff_member_pinterest', true);
	$email = get_post_meta(get_the_ID(), 'borntogive_staff_member_email', true);
	$social = '';
	$social_data = array();
	$social_data = array('email'=>$email, 'facebook'=>$facebook, 'twitter'=>$twitter, 'google-plus'=>$gplus, 'pinterest'=>$pinterest);
	if($facebook!=''||$twitter!=''||$gplus!=''||$pinterest!=''||$email!='')
	{
	$social .= '<ul class="social-icons-rounded social-icons-colored">';
	foreach($social_data as $key=>$value)
	{
		if($value!='')
		{
			$url = $value;
			if($key=="email")
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
	$team_output .= '<li class="grid-item col-md-4 col-sm-4 grid-staff-item format-standard">';
	$team_output .= '<div class="grid-item-inner">';
	if(has_post_thumbnail())
	{
  	$team_output .= '<a href="'.get_permalink().'" class="media-box">'.get_the_post_thumbnail().'</a>';
	}
	$team_output .= '<div class="grid-item-content">
                                	<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
	if($position!='')
	{                             
	$team_output .= '<span class="meta-data">'.$position.'</span>';
	}
		$team_output .= $social;
		$team_output .= borntogive_excerpt();
		$team_output .= '</div>';
	$team_output .= '</div>';
		$team_output .= '</li>';
endwhile; endif;
$team_output .= '</ul>';
echo ''.$team_output;
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