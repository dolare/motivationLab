<?php get_header();
global $borntogive_options;
borntogive_sidebar_position_module();
$pageSidebar = $borntogive_options['search_sidebar'];
if(!empty($pageSidebar)&&is_active_sidebar($pageSidebar)) {
$class = 8;  
}else{
$class = 12;  
}
$default_header = (isset($borntogive_options['borntogive_default_banner']['url']))?$borntogive_options['borntogive_default_banner']['url']:'';
?>
<div class="hero-area">
    	<div class="page-banner parallax" style="background-image:url(<?php echo esc_url($default_header); ?>);">
        	<div class="container">
            	<div class="page-banner-text">
        			<h1 class="block-title"><?php printf( esc_html__( 'Search Results for: %s', 'borntogive' ), get_search_query() ); ?></h1>
                </div>
            </div>
        </div>
    </div>
  	<!-- Start Body Content -->
  	 <div id="main-container">
    	<div class="content">
        	<div class="container">
            	<div class="row">
                	<div class="col-md-<?php echo esc_attr($class); ?> content-block" id="content-col">
                        <?php if(have_posts()) : ?>
						<?php while(have_posts()) : the_post();
						$post_type = get_post_type(get_the_ID());
						?>
						<div class="blog-list-item">
                        	<div class="row">
                          	<?php if(has_post_thumbnail()) { ?>
                            	<div class="col-md-4 col-sm-4">
                                	<a href="<?php the_permalink(); ?>" class="media-box grid-featured-img">
                                        <?php the_post_thumbnail(); ?>
                                    </a>
                                </div>
                                <div class="col-md-8 col-sm-8">
                        		<?php
								} else { ?>
                                <div class="col-md-12 col-sm-12">
                                <?php } ?>
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <?php if($post_type=='post'){ ?>
										<?php $post_author_id = get_post_field( 'post_author', get_the_ID() );
                                        $meta_data = '<span><i class="fa fa-calendar"></i> ' .esc_html(get_the_date(get_option('date_format'), get_the_ID())).'<i class="fa fa-pencil"></i> <a href="'. esc_url(get_author_posts_url($post_author_id)).'">'.esc_attr(get_the_author_meta( 'display_name', $post_author_id )).'</a></span>'; ?>
                                        <div class="meta-data full-meta-data grid-item-meta">
                                        <?php echo $meta_data; ?>
                                        <?php if ( count( get_the_category() ) ) : echo '<i class="fa fa-folder"></i> ';  the_category(', '); endif; ?>
                                        </div>
                                    <?php } else { ?>
                                        <span class="meta-data grid-item-meta"><i class="fa fa-calendar"></i> <?php echo get_the_date(get_option('date_format'), get_the_ID()); ?></span>
                                    <?php } ?>
                                    <div class="grid-item-excerpt">
                                        <div class="post-content">
                                        <?php if(is_single()) {
                                            the_content();
                                        }
                                        else
                                        {
                                            the_excerpt();
                                        }
                                        ?>
                                        </div>
                                    </div>
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
                        <?php wp_link_pages( array(
                              'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'borntogive' ) . '</span>',
                              'after'       => '</div>',
                              'link_before' => '<span>',
                              'link_after'  => '</span>',
                          ) ); ?>
                        </div>
                        <!-- Pagination -->
                        
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