<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class One_And_One_ModifySettingsPage {

	public function __construct() {

		// hide home description
		if ( oneandone_is_managed() ) {
			add_action( 'admin_head', array( $this, 'change_home_description' ) );
		}

		// show buttons for domain change
		//        add_action('admin_head', array( $this, 'show_buttons_for_domain_change' ) );
	}

	public function show_buttons_for_domain_change() {
		global $pagenow;
		if ( is_admin() && $pagenow == 'options-general.php' ) {
			?>
			<script type="text/javascript">
				(function ($) {
					$(document).ready(function () {
						$('#siteurl, #home').parent().append(' <span>BUTTON</span>');
					});

				})(jQuery);
			</script>
			<?php
		}
	}

	public function change_home_description() {
		global $pagenow;
		if ( is_admin() && $pagenow == 'options-general.php' ) {
			?>
			<style type="text/css">
				#home-description {
					display: none;
				}
			</style>
			<script type="text/javascript">
				(function ($) {
					$(document).ready(function () {
						$('#siteurl').parent().append('<p class="description"><?php _e( 'Website-Url-Description', '1and1-wordpress-wizard' ); ?></p>');
						$('#home').parent().append('<p class="description"><?php _e( 'Website-Url-Description', '1and1-wordpress-wizard' ); ?></p>');
						$('.update-nag').hide();
					});
				})(jQuery);
			</script>
			<?php
		}
	}
}

new One_And_One_ModifySettingsPage();
