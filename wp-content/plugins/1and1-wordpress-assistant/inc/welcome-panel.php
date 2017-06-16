<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class One_And_One_Setup_Welcome_Panel {

	public function change_your_domain() {
		//check if the Prelive Staging Plugin is there...
		if ( file_exists( WPMU_PLUGIN_DIR.'/1and1-prelive-staging-hint/1and1-prelive-staging-hint.php' ) ) {
			require_once WPMU_PLUGIN_DIR.'/1and1-prelive-staging-hint/1and1-prelive-staging-hint.php';
		}

		if ( class_exists( 'OneAndOneProductSubdomainHint' ) ) {
			if ( method_exists( 'OneAndOneProductSubdomainHint', 'IsOneAndOneSiteRunsOnProductSubdomain' ) ) {
				if ( OneAndOneProductSubdomainHint::IsOneAndOneSiteRunsOnProductSubdomain() ): ?>
					<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
						<form action="www" method="POST">
							<div class="one-and-one-info-box">
								<img class="one-and-one-info-box-logo" />
								<div class="one-and-one-info-box-button">
									<a class="button button-primary button-hero" href="<?php esc_html_e( 'domain_change_link', '1and1-wordpress-wizard' ); ?>" target="_blank"><?php _e( 'Change your Domain', '1and1-wordpress-wizard' ); ?></a>
								</div>
								<div class="one-and-one-info-box-description"><?php _e( 'your WordPress is managed by 1&1', '1and1-wordpress-wizard' ); ?></div>
							</div>
						</form>
					</div>
				<?php endif; ?>
				<?php
			}
		}
	}

	public function welcome_panel_dashboard( $context = 'dashboard-panel-second-run' ) {
		?>
		<div id="oneandone-welcome-panel" class="updated welcome-panel oneandone-setup-panel oneandone-setup-panel_second">
			<div class="welcome-panel-content">
				<a title="<?php esc_html_e( 'title_link_logo', '1and1-wordpress-wizard' ); ?>" href="<?php esc_html_e( 'http://www.1and1.com', '1and1-wordpress-wizard' ); ?>" target="_blank">
					<div class="oneandone-setup-image-logo"></div>
				</a>
				<a title="<?php esc_html_e( 'title_link_visual', '1and1-wordpress-wizard' ); ?>" href="<?php echo esc_url( admin_url( 'tools.php?page=1and1-wordpress-wizard' ) ); ?>">
					<div class="oneandone-setup-image"></div>
				</a>
				<div class="oneandone-setup-message">
					<h3><?php _ex( '1&1 Managed WP WordPress', $context, '1and1-wordpress-wizard' ) ?></h3>
					<h2><?php _ex( 'Customize and upgrade your website now.', $context, '1and1-wordpress-wizard' ) ?></h2>
					<div class="panel-float-box first">
						<h4><?php _e( 'Next Steps', '1and1-wordpress-wizard' ); ?></h4>
						<ul>
							<?php
							//check if the Prelive Staging Plugin is there...
							if ( file_exists( WPMU_PLUGIN_DIR.'/1and1-prelive-staging-hint/1and1-prelive-staging-hint.php' ) ) {
								require_once WPMU_PLUGIN_DIR.'/1and1-prelive-staging-hint/1and1-prelive-staging-hint.php';
							}

							if ( class_exists( 'OneAndOneProductSubdomainHint' ) ) {
								if ( method_exists( 'OneAndOneProductSubdomainHint', 'IsOneAndOneSiteRunsOnProductSubdomain' ) ) {
									if ( OneAndOneProductSubdomainHint::IsOneAndOneSiteRunsOnProductSubdomain() ): ?>
										<li><?php printf( '<a href="'.__( 'domain_change_link', '1and1-wordpress-wizard' ).'" class="welcome-icon welcome-view-site">'.__( 'Change your Domain', '1and1-wordpress-wizard' ).'</a>' ); ?></li>
									<?php endif; ?>
								<?php }
							} ?>
							<?php if ( 'page' == get_option( 'show_on_front' ) && ! get_option( 'page_for_posts' ) ) : ?>
								<li><?php printf( '<a href="%s" class="welcome-icon welcome-edit-page">'.__( 'Edit your front page', '1and1-wordpress-wizard' ).'</a>', get_edit_post_link( get_option( 'page_on_front' ) ) ); ?></li>
								<li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">'.__( 'Add additional pages' ).'</a>', admin_url( 'post-new.php?post_type=page' ) ); ?></li>
							<?php elseif ( 'page' == get_option( 'show_on_front' ) ) : ?>
								<li><?php printf( '<a href="%s" class="welcome-icon welcome-edit-page">'.__( 'Edit your front page' ).'</a>', get_edit_post_link( get_option( 'page_on_front' ) ) ); ?></li>
								<li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">'.__( 'Add additional pages' ).'</a>', admin_url( 'post-new.php?post_type=page' ) ); ?></li>
								<li><?php printf( '<a href="%s" class="welcome-icon welcome-write-blog">'.__( 'Add a blog post' ).'</a>', admin_url( 'post-new.php' ) ); ?></li>
							<?php else : ?>
								<li><?php printf( '<a href="%s" class="welcome-icon welcome-write-blog">'.__( 'Write your first blog post', '1and1-wordpress-wizard' ).'</a>', admin_url( 'post-new.php' ) ); ?></li>
								<li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">'.__( 'Add an About page', '1and1-wordpress-wizard' ).'</a>', admin_url( 'post-new.php?post_type=page' ) ); ?></li>
							<?php endif; ?>
							<li><?php printf( '<a href="%s" class="welcome-icon welcome-view-site">'.__( 'View your site' ).'</a>', home_url( '/' ) ); ?></li>
							<?php if ( $this->isProductDomain() ) : ?>
								<li><?php printf( '<a href="%s" target="_blank" class="welcome-icon dashicons-admin-links">'.__( 'dashboard_change_domain', '1and1-wordpress-wizard' ).'</a>', __( 'domain_change_link', '1and1-wordpress-wizard' ) ); ?></li>
							<?php elseif ( ! $this->isSsl() ) : ?>
								<li><?php printf( '<a href="%s" target="_blank" class="welcome-icon dashicons-lock">'.__( 'dashboard_activate_ssl', '1and1-wordpress-wizard' ).'</a>', __( 'domain_change_link', '1and1-wordpress-wizard' ) ); ?></li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="panel-float-box">
						<h4><?php _e( 'More Actions' ); ?></h4>
						<ul>
							<?php if ( current_theme_supports( 'widgets' ) || current_theme_supports( 'menus' ) ) : ?>
								<li>
									<div class="welcome-icon welcome-widgets-menus">
										<?php
										if ( current_theme_supports( 'widgets' ) && current_theme_supports( 'menus' ) ) {
											printf(
												__( 'Manage <a href="%1$s">widgets</a> or <a href="%2$s">menus</a>' ),
												admin_url( 'widgets.php' ), admin_url( 'nav-menus.php' )
											);
										} elseif ( current_theme_supports( 'widgets' ) ) {
											echo '<a href="'.admin_url( 'widgets.php' ).'">'.__( 'Manage widgets' ).'</a>';
										} else {
											echo '<a href="'.admin_url( 'nav-menus.php' ).'">'.__( 'Manage menus' ).'</a>';
										}
										?>
									</div>
								</li>
							<?php endif; ?>
							<?php if ( current_user_can( 'manage_options' ) ) : ?>
								<li><?php printf( '<a href="%s" class="welcome-icon welcome-comments">'.__( 'Turn comments on or off' ).'</a>', admin_url( 'options-discussion.php' ) ); ?></li>
							<?php endif; ?>
							<li>
								<a href="<?php esc_html_e( 'community_link_url', '1and1-wordpress-wizard' ); ?>" target="_blank" class="welcome-icon welcome-learn-more"><?php esc_html_e( 'first_steps_community_link', '1and1-wordpress-wizard' ); ?></a>
							</li>
							<li>
								<a href="<?php echo wp_customize_url(); ?>" class="welcome-icon dashicons-admin-appearance"><?php _e( 'customize_theme_in_widget', '1and1-wordpress-wizard' ); ?></a>
							</li>
						</ul>
					</div>
					<div class="panel-float-box last">
						<div class="oneandone-setup-links">
							<a title="<?php _ex( 'Bullet Point 1', $context, '1and1-wordpress-wizard' ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=1and1-wordpress-wizard' ) ); ?>" class="button button-hero oneandone-welcome-panel-get-started">
								<?php esc_html_e( 'dashboard_widget_website-type', '1and1-wordpress-wizard' ); ?>
							</a>
							<a title="<?php _ex( 'Bullet Point 2', $context, '1and1-wordpress-wizard' ); ?>" href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>" class="button button-hero oneandone-welcome-panel-get-started">
								<?php esc_html_e( 'dashboard_widget_design', '1and1-wordpress-wizard' ); ?>
							</a>
							<a title="<?php _ex( 'Bullet Point 3', $context, '1and1-wordpress-wizard' ); ?>" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-hero oneandone-welcome-panel-get-started">
								<?php esc_html_e( 'dashboard_widget_plugins', '1and1-wordpress-wizard' ); ?>
							</a>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<?php
	}

	public function welcome_panel_message( $context ) {
		?>
		<div id="oneandone-welcome-panel" class="updated welcome-panel oneandone-setup-panel">
			<div class="welcome-panel-content">
				<a title="<?php esc_html_e( 'title_link_logo', '1and1-wordpress-wizard' ); ?>" href="<?php esc_html_e( 'http://www.1and1.com', '1and1-wordpress-wizard' ); ?>" target="_blank">
					<div class="oneandone-setup-image-logo"></div>
				</a>
				<a title="<?php esc_html_e( 'title_link_visual', '1and1-wordpress-wizard' ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=1and1-wordpress-wizard' ) ); ?>">
					<div class="oneandone-setup-image"></div>
				</a>
				<div class="oneandone-setup-message">
					<h3><?php _ex( '1&1 Managed WP WordPress', $context, '1and1-wordpress-wizard' ) ?></h3>
					<h2><?php _ex( 'Configure your WordPress in just a few steps.', $context, '1and1-wordpress-wizard' ) ?></h2>
					<p><?php _ex( '1&1 WP Assistant enables you to get a quick start in WordPress with selected themes and plugins.', $context, '1and1-wordpress-wizard' ) ?></p>
					<div class="oneandone-setup-links">
						<?php if ( $context == 'plugins-panel-second-run' ): ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=1and1-wordpress-wizard&setup_action=choose_functionality' ) ); ?>" class="button button-primary button-hero oneandone-welcome-panel-get-started">
								<?php esc_html_e( 'Step 3 - Selecting plugins', '1and1-wordpress-wizard' ); ?>
							</a>
						<?php else: ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=1and1-wordpress-wizard' ) ); ?>" class="button button-primary button-hero oneandone-welcome-panel-get-started">
								<?php esc_html_e( 'Get Started', '1and1-wordpress-wizard' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private function isProductDomain() {
		$product_domains = array( 'apps-1and1.net', 'apps-1and1.com', 'online.de' );
		$domain          = get_site_url();

		foreach ( $product_domains as $product_domain ) {
			if ( stripos( $domain, $product_domain ) !== false ) {
				return true;
			}
		}

		return false;
	}

	private function isSsl() {
		if ( is_ssl() ) {
			return true;
		}

		return false;
	}
}
