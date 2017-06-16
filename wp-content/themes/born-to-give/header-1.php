<?php
$borntogive_options = get_option('borntogive_options');
$menu_locations = get_nav_menu_locations();
$logo = (isset($borntogive_options['logo_upload']))?$borntogive_options['logo_upload']:'';
$logo_retina = (isset($borntogive_options['retina_logo_upload']))?$borntogive_options['retina_logo_upload']:'';
$logo_sticky = (isset($borntogive_options['logo_upload_sticky']))?$borntogive_options['logo_upload_sticky']:'';
$logo_retina_sticky = (isset($borntogive_options['retina_logo_upload_sticky']))?$borntogive_options['retina_logo_upload_sticky']:'';
$logo_url = (isset($logo['url']))?$logo['url']:'';
$logo_retina_url = (!empty($borntogive_options['retina_logo_upload']['url'])!='')?$logo_retina['url']:$logo_url;
$logo_sticky_url = (!empty($borntogive_options['logo_upload_sticky']['url'])!='')?$logo_sticky['url']:$logo_url;
$logo_retina_sticky_url_check = (!empty($borntogive_options['retina_logo_upload_sticky']['url'])!='')?$logo_retina_sticky['url']:'';

if($logo_retina_sticky_url_check != ''){
	$logo_retina_sticky_url = $logo_retina_sticky_url_check;
}
elseif($logo_sticky_url != ''){
	$logo_retina_sticky_url = $logo_sticky_url;
}
elseif($logo_retina_url != ''){
	$logo_retina_sticky_url = $logo_retina_url;
}
elseif($logo_url != ''){
	$logo_retina_sticky_url = $logo_url;
}
else{
	$logo_retina_sticky_url = '';
}

$retina_logo_width = (isset($borntogive_options['retina_logo_width']))?$borntogive_options['retina_logo_width']:'199';
$retina_logo_height = (isset($borntogive_options['retina_logo_height']))?$borntogive_options['retina_logo_height']:'30';
$sticky_retina_logo_width = (isset($borntogive_options['sticky_retina_logo_width']))?$borntogive_options['sticky_retina_logo_width']:'199';
$sticky_retina_logo_height = (isset($borntogive_options['sticky_retina_logo_height']))?$borntogive_options['sticky_retina_logo_height']:'30';
?>
<div class="site-header-wrapper">
        <!-- Site Header -->
        <header class="site-header">
            <div class="container">
                <div class="site-logo">
                <?php if($logo_url == '' && $logo_retina_url == '' && $logo_sticky_url == '' && $logo_retina_sticky_url == ''){ ?>
                	<a href="<?php echo esc_url( home_url('/') ); ?>" class="static-logo">
                    <span class="site-name"><?php echo get_bloginfo('name'); ?></span>
                	<span class="site-tagline"><?php echo get_bloginfo('description'); ?></span>
                    </a>
                <?php } else { ?>
                    <a href="<?php echo esc_url( home_url('/') ); ?>" class="default-logo"><img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo get_bloginfo('name'); ?>"></a>
                    <a href="<?php echo esc_url( home_url('/') ); ?>" class="default-retina-logo"><img src="<?php echo esc_url($logo_retina_url); ?>" alt="<?php echo get_bloginfo('name'); ?>" width="<?php echo esc_attr($retina_logo_width); ?>" height="<?php echo esc_attr($retina_logo_height); ?>"></a>
                    <a href="<?php echo esc_url( home_url('/') ); ?>" class="sticky-logo"><img src="<?php echo esc_url($logo_sticky_url); ?>" alt="<?php echo get_bloginfo('name'); ?>"></a>
                    <a href="<?php echo esc_url( home_url('/') ); ?>" class="sticky-retina-logo"><img src="<?php echo esc_url($logo_retina_sticky_url); ?>" alt="<?php echo get_bloginfo('name'); ?>" width="<?php echo esc_attr($sticky_retina_logo_width); ?>" height="<?php echo esc_attr($sticky_retina_logo_height); ?>"></a>
                    <?php } ?>
                </div>
             	<a href="#" class="visible-sm visible-xs" id="menu-toggle"><i class="fa fa-bars"></i></a>
                <?php if(isset($borntogive_options['header_info_1_text'])&&$borntogive_options['header_info_1_text']!='') { ?>
                <div class="header-info-col"><?php if($borntogive_options['header_info_1_icon']!='') { ?><i class="fa <?php echo esc_attr($borntogive_options['header_info_1_icon']); ?>"></i><?php } ?> <?php echo esc_attr($borntogive_options['header_info_1_text']); ?></div><?php } ?>
                <?php if (isset($borntogive_options['enable-search'])&&$borntogive_options['enable-search'] == 1) {
						imic_search_button_header();
					} ?>
					<?php if (isset($borntogive_options['enable-cart'])&&$borntogive_options['enable-cart'] == 1) {
					   echo imic_cart_button_header();
				} ?>
                <?php if (!empty($menu_locations['primary-menu'])) { 
			wp_nav_menu(array('theme_location' => 'primary-menu', 'container' => '','items_wrap' => '<ul id="%1$s" class="sf-menu dd-menu pull-right">%3$s</ul>', 'walker' => new borntogive_mega_menu_walker)); } ?>
            </div>
        </header>
    </div>