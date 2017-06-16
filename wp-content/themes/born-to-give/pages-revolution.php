<?php global $borntogive_options;
if(is_home()) { $id = get_option('page_for_posts'); }
elseif(class_exists('buddypress') && is_buddypress()){
	$component = bp_current_component();
	$bp_pages = get_option( 'bp-pages' );
	$id = $bp_pages[$component];
}
else { $id = get_the_ID(); }
$rev_slider = get_post_meta($id,'borntogive_pages_select_revolution_from_list',true);
$rev_slider = preg_replace('/\\\\/', '', $rev_slider); ?>
<div class="hero-area">
	<div class="slider-rev-cont">
     	<div class="tp-limited">
      		<?php echo do_shortcode($rev_slider); ?>
      	</div>
   	</div>
</div>