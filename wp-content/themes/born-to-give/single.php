<?php 
get_header();
global $borntogive_options;
$post_format = get_post_format();
borntogive_sidebar_position_module();
$pageSidebarGet = get_post_meta(get_the_ID(),'borntogive_select_sidebar_from_list', true);
$pageSidebarStrictNo = get_post_meta(get_the_ID(),'borntogive_strict_no_sidebar', true);
$pageSidebarOpt = (isset($borntogive_options['blog_sidebar']))?$borntogive_options['blog_sidebar']:'';
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
if(!empty($pageSidebar)&&is_active_sidebar($pageSidebar)) {
$sidebar_column = ($sidebar_column=='')?4:$sidebar_column;
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
} else{
	get_template_part( 'pages', 'banner' );
}
?>
<div class="main" role="main">
  <div id="content" class="content full">
    <div class="container">
      <div class="row">
        <div class="col-md-<?php echo esc_attr($class); ?>" id="content-col">
          <?php if(have_posts()):while ( have_posts() ) : the_post(); ?>
          <h3><?php the_title(); ?></h3>
          <?php
			$show_date = (isset($borntogive_options['blog_post_date_meta']))?$borntogive_options['blog_post_date_meta']:1;
			$show_author = (isset($borntogive_options['blog_post_author_meta']))?$borntogive_options['blog_post_author_meta']:1;
			$show_category = (isset($borntogive_options['blog_post_cats_meta']))?$borntogive_options['blog_post_cats_meta']:1;
			$show_thumbnail = (isset($borntogive_options['blog_post_thumbnail']))?$borntogive_options['blog_post_thumbnail']:1;
		   	$post_author_id = get_post_field( 'post_author', get_the_ID() ); ?>
			<div class="meta-data full-meta-data">
				<?php if($show_date == 1){
                    echo '<span><i class="fa fa-calendar"></i> ' .esc_html(get_the_date(get_option('date_format'), get_the_ID()));
                } ?>
                <?php if($show_author == 1){
                    echo '<i class="fa fa-pencil"></i> <a href="'. esc_url(get_author_posts_url($post_author_id)).'">'.esc_attr(get_the_author_meta( 'display_name', $post_author_id )).'</a></span>';
                } ?>
                <?php if($show_category == 1){
                    if ( count( get_the_category() ) ) : echo '<i class="fa fa-folder"></i> ';  the_category(', '); endif;
                } ?>
            </div>
			<?php if($show_date == 1 || $show_author == 1 || $show_category == 1){ ?>
                <div class="spacer-20"></div>
            <?php } ?>
			<?php if($show_thumbnail == 1){
				
				if($post_format=="gallery")
				{
					$image_data = get_post_meta(get_the_ID(), 'borntogive_gallery_images', false);
				echo '<div style="position:relative;text-align:center;">'.borntogive_gallery_flexslider(get_the_ID());
				echo '<ul class="slides">';
					foreach ($image_data as $custom_gallery_images) 
					{
						$large_src = wp_get_attachment_image_src($custom_gallery_images, 'full');
						echo '<li class="item"><a href="' . esc_url($large_src[0]) . '" class="popup-image">';
						$thumbnail = '';
						$thumbnail = wp_get_attachment_image($custom_gallery_images, 'borntogive-600x400');
						echo $thumbnail;
						echo '</a></li>';
					}
				echo '</ul></div></div>';
				} else{
          		if(has_post_thumbnail()) { ?>
              		<div class="post-media">
                  		<?php the_post_thumbnail(); ?>
              		</div>
          		<?php } }
            } ?>
                        <div class="post-content">
                        	<?php the_content(); ?>
                        <?php wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'borntogive' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => ' &frasl;</span>',
						) ); ?>
                      	</div>
                        <?php if (has_tag()) { ?>
                        <div class="tagcloud">
                            <i class="fa fa-tags"></i> 
                            <?php the_tags('', ''); ?>
                        </div>
                        <?php } ?>
						<?php if ($borntogive_options['switch_sharing'] == 1 && $borntogive_options['share_post_types']['1'] == '1') { ?>
                            <?php borntogive_share_buttons(); ?>
                        <?php } ?>
                        <div class="spacer-20"></div>
			<?php $post_author_id = get_post_field( 'post_author', get_the_ID() );
				$author_image = get_the_author_meta('agent-image', $post_author_id);
				$image_url = wp_get_attachment_url($author_image);
				$args = array('class'=>'img-thumbnail');
		  ?>
          <?php $authorinfo = get_the_author_meta("description");
		  	if($authorinfo != ''){
		  ?>
          <section class="about-author">
          	  <?php echo get_avatar( $post_author_id, 100, '','',$args ); ?> 
              <div class="post-author-content">
                  <h3><?php echo esc_attr(get_the_author_meta( 'display_name', $post_author_id )); ?> <span class="label label-primary"><?php esc_html_e('About', 'borntogive'); ?></span></h3>
                  <p><?php echo get_the_author_meta( 'description', $post_author_id ); ?></p>
              </div>
          </section>
          <?php } ?>
          <div class="clearfix"></div>
          <ul class="pager margin-40"> <li><?php echo previous_post_link('%link', __('&laquo; Prev Post', 'borntogive')); ?></li><li><?php echo next_post_link('%link', __('Next Post &raquo;', 'borntogive')); ?></li></ul>
          <?php 
		  endwhile;
		   endif; ?>
          
          <!-- Post Comments -->
          <?php if ( comments_open()) { comments_template('', true); } ?>
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
<!-- End Body Content -->
<?php get_footer(); ?>
