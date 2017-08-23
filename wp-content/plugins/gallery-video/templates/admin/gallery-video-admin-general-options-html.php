<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap">
	<?php require(GALLERY_VIDEO_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'gallery-video-admin-free-banner.php');?>
	<p class="pro_info">
		<?php echo __('These features are available in the Professional version of the plugin only.', 'gallery-video'); ?>
		<a href="http://huge-it.com/wordpress-video-gallery/" target="_blank" class="button button-primary"><?php _e('Enable', 'gallery-video');?></a>
	</p>
	<div style="clear: both;"></div>
	<div id="poststuff">
		<?php $path_site = GALLERY_VIDEO_IMAGES_URL.'/admin_images'; ?>
		<div id="post-body-content" class="videogallery-options">
			<div id="post-body-heading">
				<h3>General Options</h3>
				<a href="#" class="save-videogallery-options button-primary">Save</a>
			</div>
			<form action="admin.php?page=Options_video_gallery_styles&task=save" method="post" id="adminForm" name="adminForm">
				<div id="videogallery-options-list">
					<ul id="videogallery-view-tabs">
						<li><a href="#videogallery-view-options-0"><?php _e('Video Gallery/Content-Popup', 'gallery-video');?></a></li>
						<li><a href="#videogallery-view-options-1"><?php _e('Content Video Slider', 'gallery-video');?></a></li>
						<li><a href="#videogallery-view-options-2"><?php _e('Lightbox-Video Gallery', 'gallery-video');?></a></li>
						<li><a href="#videogallery-view-options-3"><?php _e('Video Slider', 'gallery-video');?></a></li>
						<li><a href="#videogallery-view-options-4"><?php _e('Thumbnails', 'gallery-video');?></a></li>
						<li><a href="#videogallery-view-options-5"><?php _e('Justified', 'gallery-video');?></a></li>
						<li><a href="#videogallery-view-options-6"><?php _e('Blog Style Gallery', 'gallery-video');?></a></li>
					</ul>
					<ul class="options-block" id="videogallery-view-tabs-contents">
						<div class="gallery_options_grey_overlay"></div>
						<li id="videogallery-view-options-0" class="active">
							<img style="width: 100%;" src='<?php echo esc_url($path_site); ?>/Content popup tab1.png' >
						</li>
						<li id="videogallery-view-options-1">
							<img style="width: 100%;" src='<?php echo esc_url($path_site); ?>/Content-Video-slider-tab2.png' >
						</li>
						<li id="videogallery-view-options-2">
							<img style="width: 100%;" src='<?php echo esc_url($path_site); ?>/lightbox-tab3.png' >
						</li>
						<li id="videogallery-view-options-3">
							<img style="width: 100%;" src='<?php echo esc_url($path_site); ?>/slider--tab4.png' >
						</li>
						<li id="videogallery-view-options-4">
							<img style="width: 100%;" src='<?php echo esc_url($path_site); ?>/thumbnails-tab5.png' >
						</li>
						<li id="videogallery-view-options-5">
							<img style="width: 100%;" src='<?php echo esc_url($path_site); ?>/justified-tab6.png' >
						</li>
						<li id="videogallery-view-options-6">
							<img style="width: 100%;" src='<?php echo esc_url($path_site); ?>/blockstyle-tab7.png' >
						</li>
					</ul>

					<div id="post-body-footer">
						<a href="#" class="save-videogallery-options button-primary"><?php _e('Save', 'gallery-video');?></a>
						<div class="clear"></div>
					</div>
			</form>
		</div>
	</div>
</div>
</div>
<input type="hidden" name="option" value=""/>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="controller" value="options"/>
<input type="hidden" name="op_type" value="styles"/>
<input type="hidden" name="boxchecked" value="0"/>