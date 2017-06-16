<?php 
if(is_home()) { $id = get_option('page_for_posts'); }
elseif(class_exists('buddypress') && is_buddypress()){
	$component = bp_current_component();
	$bp_pages = get_option( 'bp-pages' );
	$id = $bp_pages[$component];
}
else { $id = get_the_ID(); }
wp_enqueue_script('borntogive_jquery_flexslider');
$borntogive_options = get_option('borntogive_options');
$type = get_post_meta($id,'borntogive_pages_Choose_slider_display',true);
$pagination = get_post_meta($id,'iborntogive_pages_slider_pagination',true);
$autoplay = get_post_meta($id,'borntogive_pages_slider_auto_slide',true);
$arrows = get_post_meta($id,'borntogive_pages_slider_direction_arrows',true);
$effects = get_post_meta($id,'borntogive_pages_slider_effects',true);
if($type==1 || $type==2 || $type==3) {
	$height = get_post_meta($id,'borntogive_pages_slider_height',true);
} else {
	$height = '';
}
$images = get_post_meta($id,'borntogive_pages_slider_image',false);
if(!empty($images)) { ?>
<!-- Hero Area -->
    <div class="hero-area">
    	<!-- Start Hero Slider -->
      	<div class="flexslider heroflex hero-slider" data-autoplay="<?php echo esc_attr($autoplay); ?>" data-pagination="<?php echo esc_attr($pagination); ?>" data-arrows="<?php echo esc_attr($arrows); ?>" data-style="<?php echo esc_attr($effects); ?>" data-pause="yes">
            <ul class="slides">
            <?php foreach($images as $image) {
									$image_data = borntogive_wp_get_attachment($image);
									$image_src = wp_get_attachment_image_src( $image, 'full', '', array() ); ?>
                <li class="parallax" style="background-image:url(<?php echo esc_url($image_src[0]); ?>)">
                	<div class="flex-caption">
                    	<div class="container">
                        	<div class="flex-caption-table">
                            	<div class="flex-caption-cell">
                                	<div class="flex-caption-text">
                                  <?php if($image_data['postid']) { ?>
                                  <div class="flex-caption-cause">
                            			<h3><a href="<?php echo get_the_permalink($image_data['postid']); ?>"><?php echo get_the_title($image_data['postid']); ?></a></h3>
                    					<p><?php wp_trim_words(borntogive_post_excerpt_by_id($image_data['postid']), 20); ?></p>
                          			</div>
                                  <?php } else { ?>
                                       <?php echo ''.$image_data['description']; ?>
                                        <?php } ?>
                                    </div>
                               	</div>
                          	</div>
                        </div>
                    </div>
                </li>
                <?php } ?>
          	</ul>
       	</div>
        <!-- End Hero Slider -->
    </div>
    <?php } ?>
    <!-- End Page Header -->