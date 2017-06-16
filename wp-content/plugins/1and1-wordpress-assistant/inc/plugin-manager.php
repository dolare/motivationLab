<?php

// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class One_And_One_Plugin_Manager {

	protected $site_type = '';
	protected $slugs = array();

	public function __construct() {
		if ( isset( $_REQUEST['site_type'] ) AND ! empty( $_REQUEST['site_type'] ) ) {
			$this->site_type = $_REQUEST['site_type'];
		}
	}

	public function get_plugin_manager( $site_type, $theme_id ) {
		add_thickbox();

		if ( empty( $site_type ) ) {
			$site_type = $this->site_type;
		}

		$plugins_json = One_And_One_Sitetype_Filter::get_plugins_slugs( $site_type );
		$plugin_slugs = $this->get_installed_plugin_slugs();
		$plugins_all  = array_unique( array_merge( $plugins_json, $plugin_slugs ) );

		$plugin_meta_filename = One_And_One_Wizard::get_plugin_dir_path().'cache/plugin-'.$site_type.'-meta.txt';

		// Create cache file if not created yet
		if ( ! file_exists( $plugin_meta_filename ) ) {
			$this->update_plugin_meta_cache( $plugins_all, array( $site_type ) );
		}

		$plugins = $this->get_plugin_meta( $site_type );

		// If not cached plugins available, then update cache file
		foreach ( $plugins_all as $slug ) {
			if ( ! isset( $plugins[$slug] ) ) {
				$this->update_plugin_meta_cache( $plugins_all, array( $site_type ) );
				break;
			}
		}

		// Split plugins into sections
		$recommended_plugins = One_And_One_Sitetype_Filter::get_filtered_plugins_by_configkey( $plugins, $site_type, 'recommended' );
		$category_plugins    = $plugins = One_And_One_Sitetype_Filter::get_filtered_category_plugins( $plugins, $site_type );
		?>
		<script type="text/javascript">
			function showBox(id, title) {
				tb_show(title, '#TB_inline?height=500&width=800&inlineId=' + id + '&modal=false', null);
			}

			function showOtherBox(id) {
				tb_show('', '#TB_inline?height=200&width=550&inlineId=' + id + '&modal=false', null);
			}

			jQuery(function ($) {
				$('.oneandone-plugin-browser').on('click', '.oneandone-install-checkbox input', function (evt) {
					$checkbox = $(this);

					if ($checkbox.prop('checked')) {
						$(this).closest('.oneandone-install-checkbox').addClass('checked');
					} else {
						$(this).closest('.oneandone-install-checkbox').removeClass('checked');
					}
				});
			});

			jQuery(document).ready(function () {
				// Select and loop the container element of the elements you want to equalise
				jQuery('.oneandone-plugin-browser').each(function () {
					// Cache the highest
					var highestBox = 0;
					// Select and loop the elements you want to equalise
					jQuery('.plugin-card .plugin-card-top', this).each(function () {
						// If this box is higher than the cached highest then store it
						if (jQuery(this).height() > highestBox) {
							highestBox = jQuery(this).height();
						}

					});
					// Set the height of all those children to whichever was highest
					jQuery('.plugin-card-top', this).height(highestBox);
					jQuery('.add-new-plugin-border', this).height(highestBox - 10);
				});

			});
		</script>

		<form action="<?php echo esc_url( add_query_arg( array( 'setup_action' => 'activate' ) ) ); ?>" method="post">
			<!-- Add nonce-->
			<?php wp_nonce_field( 'activate' ) ?>
			<input type="hidden" name="site_type" value="<?php echo esc_attr( $site_type ); ?>" />
			<input type="hidden" name="theme" value="<?php echo esc_attr( $theme_id ); ?>" />
			<div class="wrap">
				<?php
				include_once( One_And_One_Wizard::get_views_dir_path().'setup-wizard-header.php' );
				One_And_One_Wizard_Header::get_wizard_header( 3, $site_type, $theme_id ); ?>

				<h3 class="clear"><?php esc_html_e( 'Step 3 - Selecting plugins', '1and1-wordpress-wizard' ); ?></h3>
				<p>
					<?php
					printf(
						__( 'Select the desired plugins to expand the range of functions in your WordPress installation.', '1and1-wordpress-wizard' ),
						One_And_One_Wizard::get_site_type_label( $site_type )
					);
					?>
				</p>
				<div id="subNaviSections"></div>
				<br />
				<?php
				$subNaviSections = array();
				?>
				<hr>
				<div class="oneandone-functionality-choice">

					<!-- ============== RECOMMENDED PLUGINS ============== -->

					<?php
					$index = 0;
					if ( $recommended_plugins ) {
						$recommended_headline = _x( 'msg_headline_recommended', 'json_plugins_section', '1and1-wordpress-wizard' );
						$recommended_subline = _x( 'msg_subline_recommended', 'json_plugins_section', '1and1-wordpress-wizard' );
						$subNaviSections['recommended_plugins'] = strip_tags( $recommended_headline );
						?>

						<a name="recommended_plugins" id="recommended_plugins"></a>
						<h3><?php echo $recommended_headline; ?></h3>
						<p><?php echo $recommended_subline; ?></p>

						<div class="oneandone-plugin-browser">
							<?php
							foreach ( $recommended_plugins as $plugin ) {
								$popup_id = "Popup".$index ++;

								$this->get_plugin_item( $plugin, $popup_id, $site_type, true );
							} ?>
						</div>
						<div class="alignright">
							<input type="submit" name="install" value="<?php echo esc_attr__( 'Activate all selected theme/plugins', '1and1-wordpress-wizard' ); ?>" class="button button-primary" />
						</div>
						<br class="clear">
					<?php } ?>

					<!-- ============== CATEGORY PLUGINS ============== -->

					<?php if ( $category_plugins ) {
						$category_plugins_elements = count( $category_plugins );
						$count = 0;

						foreach ( $category_plugins as $category_key => $category ) {
							$count ++;
							$category_headline = _x( 'msg_headline_' . $category_key, 'json_plugins_section', '1and1-wordpress-wizard' );
							$category_subline = _x( 'msg_subline_' . $category_key, 'json_plugins_section', '1and1-wordpress-wizard' );
							$subNaviSections[$category_key] = strip_tags( $category_headline );
							?>

							<br class="clear">
							<hr>
							<div class="alignright">
								<a href="" class="assistant_pluginpage_jump_to_top" title="<?php echo esc_attr__( 'assistant_pluginpage_jump_to_top', '1and1-wordpress-wizard' ); ?>"><?php echo _e( 'assistant_pluginpage_jump_to_top', '1and1-wordpress-wizard' ); ?></a>
							</div>
							<a name="<?php echo $category_key; ?>" id="<?php echo $category_key; ?>"></a>
							<h3><?php echo $category_headline; ?></h3>

							<p><?php echo $category_subline; ?></p>

							<div class="oneandone-plugin-browser">
								<?php
								foreach ( $category as $plugin ) {
									$popup_id = "Popup".$index ++;

									$this->get_plugin_item( $plugin, $popup_id, $site_type );
								}

								if ( $count == $category_plugins_elements ) {
									$this->install_other();
								} ?>
							</div>

							<?php
						}
					}
					?>
				</div>

				<br class="clear">
				<br />
				<script type="text/javascript">
					/* <![CDATA[ */
					var subNaviAsJsonStr =<?php
						$subNaviAsJsonStr = json_encode( $subNaviSections );
						print $subNaviAsJsonStr;
						?>;
					jQuery(function ($) {
						str = '<ul>';
						$.each(subNaviAsJsonStr, function (index, element) {
							str += ' <li style="display: inline;"><a href="#" id="link' + index + '" title="">' + element + '</a></li>';
							str += ' | ';
						});
						str += '</ul>';

						$("#subNaviSections").html(str);

						$.each(subNaviAsJsonStr, function (index, element) {
							$("#link" + index).click(function (evt) {
								evt.preventDefault();
								$('html, body').animate({
									scrollTop: ($("#" + index).offset().top - 20)
								}, 1000);
							});
						});


						$('.assistant_pluginpage_jump_to_top').click(function (evt) {
							//console.log('add click event');
							evt.preventDefault();
							$('html, body').animate({
								scrollTop: ($(".wrap").offset().top - 100 )
							}, 1000);
						});

						// hide #back-top first
						$(".assistant_pluginpage_jump_to_top").hide();

						// fade in #back-top
						$(window).scroll(function () {
							if ($(this).scrollTop() > 100) {
								$('.assistant_pluginpage_jump_to_top').show();
							} else {
								$(".assistant_pluginpage_jump_to_top").hide();
							}
						});


						$('.update-nag').hide();
						$('.is-dismissible').hide();


					});
					/* ]]> */
				</script>
				<div class="alignright">
					<input type="submit" name="install" value="<?php echo esc_attr__( 'Activate all selected theme/plugins', '1and1-wordpress-wizard' ); ?>" class="button button-primary" />
				</div>
				<br class="clear">

				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=1and1-wordpress-wizard' ) ); ?>"><?php esc_html_e( 'Back to the beginning', '1and1-wordpress-wizard' ); ?></a>
				</p>

			</div>

		</form>

		<?php

	}

	public function get_installed_plugin_slugs() {
		if ( empty( $this->slugs ) ) {
			$slugs = array();

			$plugin_info = get_site_transient( 'update_plugins' );
			if ( isset( $plugin_info->no_update ) ) {
				foreach ( $plugin_info->no_update as $path => $plugin ) {
					$slugs[$path] = $plugin->slug;
				}
			}

			if ( isset( $plugin_info->response ) ) {
				foreach ( $plugin_info->response as $path => $plugin ) {
					$slugs[$path] = $plugin->slug;
				}
			}

			// Re-check against installed plugins because the list could be outdated if the user removes plugins manually
			$installed_plugins = get_plugins();
			$slugs             = array_intersect_key( $slugs, $installed_plugins );

			// Remove all plugins that are not specified in the JSON of the Assistant
			$plugins_meta = One_And_One_Sitetype_Filter::get_plugins_slugs( $this->site_type );

			foreach ( $slugs as $key => $value ) {
				if ( ! in_array( $value, $plugins_meta ) ) {
					unset( $slugs[$key] );
				}
			}

			$this->slugs = $slugs;
		}

		return $this->slugs;
	}

	public function update_plugin_meta_cache( $plugins_all, $sitetype = array() ) {
		include_once One_And_One_Wizard::get_plugin_dir_path().'inc/cron-update-plugin-meta.php';
		$cron_class = new OneAndOne_Cron_Update_Plugin_Meta();
		$cron_class->update_plugin_meta( $plugins_all, $sitetype );
	}

	public function get_plugin_meta( $site_type = '' ) {
		if ( empty( $sitetype ) ) {
			$site_type = $this->site_type;
		}

		$plugins              = array();
		$plugin_meta_filename = One_And_One_Wizard::get_plugin_dir_path().'cache/plugin-'.$site_type.'-meta.txt';

		if ( file_exists( $plugin_meta_filename ) ) {
			$plugins = unserialize( file_get_contents( $plugin_meta_filename ) );
		}

		return $plugins;
	}

	protected function get_plugin_item( $plugin, $popup_id, $sitetype, $recommended = false ) {

		$details_link = esc_url( admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin='.$plugin->slug.'&amp;TB_iframe=true&amp;width=772&amp;height=598' ) );

		//check if Author has already a href attribute and NO target
		if (
			( strpos( $plugin->author, 'href=' ) !== false ) &&
			( strpos( $plugin->author, 'target=' ) === false )
		) {
			$authorLink = str_replace( 'href=', 'target="_blank" href=', $plugin->author );
			$author     = ' <cite>'.sprintf( __( 'By %s' ), $authorLink ).'</cite>';

		} else {
			//default
			$author = ' <cite>'.sprintf( __( 'By %s' ), $plugin->author ).'</cite>';
		}


		$icon = '';
		if ( isset( $plugin->icons ) ) {
			$icon_keys = array_keys( $plugin->icons );
			$icon      = $plugin->icons[$icon_keys[0]];
		}

		if ( get_option( 'oneandone_assistant_completed' ) == true ) {
			$is_active = $this->is_active_plugin( $plugin->slug );
		} else {
			$is_active = $recommended;
		}

		?>

		<div class="plugin-card">
			<div class="plugin-card-top">
				<?php if ( $icon ) {
					; ?>
					<a href="<?php echo $details_link; ?>" class="thickbox">
						<img src="<?php echo esc_attr( $icon ); ?>" class="plugin-icon" width="100px" height="100px" style="width: 100px; height:100px;">
					</a>
				<?php } ?>

				<div class="name column-name" style="margin-left: 118px;">
					<h4>
						<a href="<?php echo $details_link; ?>" class="thickbox">
							<?php echo strip_tags( $plugin->name ); ?>
						</a>
					</h4>
				</div>

				<div class="desc column-description" style="margin-left: 118px;">
					<p>
						<i>&quot;<?php echo wp_trim_words( strip_tags( $plugin->short_description ), 20 ); ?>&quot; <?php echo $author; ?></i>
					</p>

					<ul class="plugin-action-buttons">
						<li class="oneandone-install-checkbox<?php echo $is_active == true ? ' checked' : ''; ?>">
							<label for="plugin-<?php echo $plugin->slug; ?>">
								<input id="plugin-<?php echo $plugin->slug; ?>" name="plugins[]"
									   value="<?php echo $plugin->slug; ?>"
									   type="checkbox" <?php echo $is_active == true ? 'checked' : ''; ?>>
								<?php _e( 'Activate', '1and1-wordpress-wizard' ); ?>
							</label>
						</li>
					</ul>
				</div>

				<!--<div class="action-links">-->

				<!--</div>-->
			</div>
		</div>

		<?php
	}

	public function is_active_plugin( $plugin_slug ) {
		static $installed_plugins = false;

		if ( empty( $installed_plugins ) ) {
			$installed_plugins = $this->get_installed_plugin_slugs();
		}

		foreach ( $installed_plugins as $plugin_path => $installed_plugin ) {
			if ( $installed_plugin == $plugin_slug ) {
				return is_plugin_active( $plugin_path );
			}
		}

		return false;
	}

	private function install_other() {
		?>

		<div class="plugin-card-other plugin-card">
			<div class="add-new-plugin-border plugin-card-top" onclick="showOtherBox('other-plugins', '')">
				<div class="plugin-screenshot-other"><span></span></div>
				<h2 class="plugin-name-other"><?php _e( 'plugin_install_other_button_install_other', '1and1-wordpress-wizard' ); ?></h2>
			</div>
		</div>

		<div id="other-plugins" style="display:none">
			<div class="oneandone-theme-info-box">
				<h2 class="oneandone-theme-name">
					<?php _e( 'plugin_install_other_modal_title', '1and1-wordpress-wizard' ); ?>
				</h2>
				<p class="oneandone-theme-description">
					<?php _e( 'plugin_install_other_modal_title_desc', '1and1-wordpress-wizard' ); ?>
				</p>
				<a href="<?php echo admin_url(); ?>plugin-install.php">
					<button style="position: absolute; bottom: 15px; left: 25px;" class="button button-primary">
						<?php _e( 'plugin_install_other_modal_confirm', '1and1-wordpress-wizard' ); ?>
					</button>
				</a>
				<span onclick="self.parent.tb_remove();">
					<button style="position: absolute; bottom: 15px; right: 25px;" class="button button-success">
						<?php _e( 'plugin_install_other_modal_cancel', '1and1-wordpress-wizard' ); ?>
					</button>
				</span>
			</div>
		</div>

		<?php
	}

	public function get_theme_meta( $site_type ) {
		$themes              = array();
		$theme_meta_filename = One_And_One_Wizard::get_plugin_dir_path().'cache/theme-'.$site_type.'-meta.txt';

		if ( file_exists( $theme_meta_filename ) ) {
			$themes = unserialize( file_get_contents( $theme_meta_filename ) );
		}

		return $themes;
	}
}
