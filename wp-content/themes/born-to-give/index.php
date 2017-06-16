<?php
get_header();
borntogive_sidebar_position_module();
$borntogive_options = get_option('borntogive_options');
$id = get_option('page_for_posts');
if($id==0||$id=='')
{
	$id = get_the_ID();
}
$page_sidebarget = get_post_meta($id,'borntogive_select_sidebar_from_list', true);
$pageSidebarStrictNo = get_post_meta($id,'borntogive_strict_no_sidebar', true);
$pageSidebarOpt = (isset($borntogive_options['blog_archive_sidebar']))?$borntogive_options['blog_archive_sidebar']:'';
$blog_content_type = (isset($borntogive_options['blog_content_type']))?$borntogive_options['blog_content_type']:'';
if($page_sidebarget != ''){
	$pageSidebar = $page_sidebarget;
}elseif($pageSidebarOpt != ''){
	$pageSidebar = $pageSidebarOpt;
}else{
	$pageSidebar = '';
}
if($pageSidebarStrictNo == 1){
	$pageSidebar = '';
}
$sidebar_column = get_post_meta($id,'borntogive_sidebar_columns_layout',true);
$sidebar_column = ($sidebar_column=='')?4:$sidebar_column;
if(!empty($pageSidebar)&&is_active_sidebar($pageSidebar)) {
$left_col = 12-$sidebar_column;
$class = $left_col;  
}else{
$class = 12;  
} 
$page_header = get_post_meta($id,'borntogive_pages_Choose_slider_display',true);
if($page_header==3||$page_header==4) {
	get_template_part( 'pages', 'flex' );
}
elseif($page_header==5) {
	get_template_part( 'pages', 'revolution' );
} else{
	get_template_part( 'pages', 'banner' );
}
?>
<!-- Start Body Content -->
  	 <div id="main-container">
    	<div class="content">
        	<div class="container">
            	<div class="row">
                	<div class="col-md-<?php echo esc_attr($class); ?> content-block" id="content-col">
                        <?php if(have_posts()) : ?>
						<?php while(have_posts()) : the_post();
									?>
									<div <?php post_class('blog-list-item'); ?>>
                        	<div class="row">
                          <?php if(has_post_thumbnail()) { ?>
                            	<div class="col-md-4 col-sm-4">
                                	<a href="<?php the_permalink(); ?>" class="media-box grid-featured-img">
                                        <?php the_post_thumbnail(); ?>
                                    </a>
                                </div>
                        		<?php } ?>
                                <?php if(has_post_thumbnail()) { ?><div class="col-md-8 col-sm-8"><?php } else { ?><div class="col-md-12 col-sm-12"><?php } ?>
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									  <?php
                                        $show_date = (isset($borntogive_options['blog_date_meta']))?$borntogive_options['blog_date_meta']:1;
                                        $show_author = (isset($borntogive_options['blog_author_meta']))?$borntogive_options['blog_author_meta']:1;
                                        $show_category = (isset($borntogive_options['blog_cats_meta']))?$borntogive_options['blog_cats_meta']:1;
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
                                    <div class="grid-item-excerpt">
                                    <div class="post-content">
                                    <?php if(is_single()) {
                                        		the_content();
									  }
									  else
									  {
										  if($blog_content_type == 0){
										  	the_content('');
										  }
										  else{
											echo borntogive_excerpt();
										  }
									  }
									  ?>
                                      </div>
                                    </div>
									<?php wp_link_pages( array(
                                          'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'borntogive' ) . '</span>',
                                          'after'       => '</div>',
                                          'link_before' => '<span>',
											'link_after'  => ' &frasl;</span>',
                                      ) ); ?>
                                    <a href="<?php the_permalink(); ?>" class="basic-link"><?php esc_html_e('Read more', 'borntogive'); ?></a>
                                </div>
                            </div>
                        </div>
						<?php endwhile; ?>
                  		<?php else:
						echo get_template_part('content', 'none');
						endif;?>
                        <div class="page-pagination">
                        <?php if(!function_exists('borntogive_pagination'))
												{
													next_posts_link( '&laquo; Older Entries');
                        	previous_posts_link( 'Newer Entries &raquo;' );
												}
												else
												{
													echo borntogive_pagination();
												} ?>
                        </div>
                        <!-- Pagination -->
                   </div>
                   <?php if(is_active_sidebar($pageSidebar)) { ?>
                    <!-- Sidebar -->
                    <div class="sidebar col-md-<?php echo esc_attr($sidebar_column); ?>" id="sidebar-col">
                    	<?php dynamic_sidebar($pageSidebar); ?>
                    </div>
                    <?php } ?>
                        </div>
                    </div>
                </div>
         	</div>
    <!-- End Body Content -->
<?php get_footer(); ?>