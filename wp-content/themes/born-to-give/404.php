<?php
header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");
get_header();
$default_header = (isset($borntogive_options['borntogive_default_banner']['url']))?$borntogive_options['borntogive_default_banner']['url']:'';
?>
<div class="hero-area">
    	<div class="page-banner parallax" style="background-image:url(<?php echo esc_url($default_header); ?>);">
        	<div class="container">
            	<div class="page-banner-text">
        			<h1 class="block-title"><?php esc_html_e('404 Error', 'borntogive'); ?></h1>
                </div>
            </div>
        </div>
    </div>
    <!-- Start Body Content -->
  	<div class="main" role="main">
    	<div id="content" class="content full">
    		<div class="container">
            	<div class="row">
                	<!-- Posts List -->
                    <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
                    	<!-- Post -->
                        <article class="page-404">
                          	<div class="text-align-center">
                          		<h2><?php esc_html_e('404', 'borntogive'); ?></h2>
                               	<strong><?php esc_html_e('Sorry - Page Not Found!', 'borntogive'); ?></strong></p>
								<?php esc_html_e('The page you are looking for was moved, removed, renamed', 'borntogive'); ?><br><?php esc_html_e('or might never existed. You stumbled upon a broken link', 'borntogive'); echo ':('; ?>
                      		</div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
   	</div>
    <!-- End Body Content -->
<?php get_footer(); ?>