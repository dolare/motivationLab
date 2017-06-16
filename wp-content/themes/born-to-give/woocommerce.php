<?php 
get_header();
global $borntogive_options;
borntogive_sidebar_position_module();
$pageSidebarGet = get_post_meta(get_the_ID(),'borntogive_select_sidebar_from_list', true);
$pageSidebarStrictNo = get_post_meta(get_the_ID(),'borntogive_strict_no_sidebar', true);
$pageSidebarOpt = $borntogive_options['product_sidebar'];
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
if(is_product_category()){
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	$term_banner_image = get_option($term->taxonomy . $term->term_id . "_term_banner");
	if($term_banner_image != ''){
		$image_default = $term_banner_image;
	} else {
		$image_default = (isset($borntogive_options['default_product_banner']))?$borntogive_options['default_product_banner']['url']:'';
	}
	$shop_archive_title = (isset($borntogive_options['shop_archive_title']))?$borntogive_options['shop_archive_title']:__('Shop', 'borntogive');
	?>
    <div class="hero-area">
    	<div class="page-banner parallax" style="background-image:url(<?php echo esc_url($image_default); ?>);">
        	<div class="container">
            	<div class="page-banner-text">
        			<h1 class="block-title"><?php echo $shop_archive_title; ?></h1>
                </div>
            </div>
        </div>
    </div>
<?php } else {
$page_header = get_post_meta(get_the_ID(),'borntogive_pages_Choose_slider_display',true);
if($page_header==3||$page_header==4) {
	get_template_part( 'pages', 'flex' );
}
elseif($page_header==5) {
	get_template_part( 'pages', 'revolution' );
}
else {
	get_template_part( 'pages', 'banner' );
}
}
?>
<!-- Start Body Content -->
<div id="main-container">
  	<div class="content">
   		<div class="container">
       		<div class="row">
            	<div class="col-md-<?php echo esc_attr($class); ?>" id="content-col">
            		<?php if ( have_posts() ) :
						woocommerce_content(); echo borntogive_pagination();
						endif; ?>
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
<?php get_footer(); ?>