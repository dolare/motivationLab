<?php
get_header();
$borntogive_options = get_option('borntogive_options');
$sidebar = $borntogive_options['campaign_archive_sidebar'];
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
echo  do_shortcode('[campaigns number="10" category="'.get_query_var('term').'" columns="1-list-0-1"]');
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