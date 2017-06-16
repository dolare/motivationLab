<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class One_And_One_Site_Selection_Step {

	static public function get_site_selection() {
		add_thickbox();
		?>

		<form action="<?php echo esc_url( add_query_arg( array( 'setup_action' => 'choose_appearance' ) ) ); ?>"
			  method="post">
			<?php wp_nonce_field( 'choose_appearance' ); ?>
			<div class="wrap">
				<?php include_once( One_And_One_Wizard::get_views_dir_path().'setup-wizard-header.php' ); ?>
				<?php One_And_One_Wizard_Header::get_wizard_header( 1 ); ?>
				<h3 class="clear"><?php esc_html_e( 'Step 1 - Selecting website type', '1and1-wordpress-wizard' ); ?></h3>
				<p><?php esc_html_e( 'Here you can select the desired website type.', '1and1-wordpress-wizard' ); ?></p>
				<br />
				<div class="oneandone-site-type-browser">
					<?php
					$site_types = One_And_One_Sitetype_Filter::get_sitetypes();

					if ( $site_types ) {
						foreach ( $site_types as $site_type_id => $site_type ) {
							?>
							<div class="oneandone-site-selection">
								<div class="oneandone-site-type-picture">
									<?php if ( $site_type['image'] ): ?>
    									<img src="<?php echo One_And_One_Wizard::get_images_url().$site_type['image'] ?>" alt="" />
								    <?php endif; ?>
								</div>
                                <span class="oneandone-site-type-description">
									<h3><?php echo _x( $site_type['headline'], 'website-types', '1and1-wordpress-wizard' ); ?></h3>
									<p><?php echo _x( $site_type['description'], 'website-types', '1and1-wordpress-wizard' ); ?></p>
								</span>
								<h3 class="oneandone-site-type-name">
									<?php echo _x( $site_type['headline'], 'website-types', '1and1-wordpress-wizard' ); ?>
								</h3>
								<div class="oneandone-site-type-actions" style="-ms-filter:'alpha(Opacity=1)';opacity:1">
									<input type="submit" name="sitetype[<?php echo $site_type_id; ?>]"
										   value="<?php esc_attr_e( 'Select', '1and1-wordpress-wizard' ); ?>"
										   class="button button-primary" />
								</div>
							</div>
							<?php
						}
					} else {
						echo '<strong style="font-size:14px;">';
						esc_html_e( "The website types couldn't get retrieved. Please refresh the page.", '1and1-wordpress-wizard' );
						echo '</strong>';
					}
					?>
				</div>
				<br class="clear" />
			</div>
		</form>
		<script>
			jQuery(document).ready(function ($) {
				$('.oneandone-site-type-browser').on('click', '.oneandone-site-selection', function () {
					console.log("click on layer");
					$('input:submit', this).trigger('click');
				});

				$('.oneandone-site-type-browser').on('click', '.button-primary', function (evt) {
					evt.stopPropagation();
				});
			});
		</script>
		<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(function ($) {
				$('.update-nag').hide();
				$('.notice').hide();
			});
			/* ]]> */
		</script>
		<?php
	}
}
