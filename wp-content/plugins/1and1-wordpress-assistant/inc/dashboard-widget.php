<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class One_And_One_Assistant_Dashboard_Widget {

	public $settings = array(
		'feed_url_de_DE'          => 'https://community.1und1.de/category/wordpress-2/feed/',
		'feed_url_featured_de_DE' => 'https://community.1und1.de/tag/featured-wordpress/feed/',
		'feed_url_en_US'          => 'https://community.1and1.com/category/wordpress/feed/',
		'feed_url_featured_en_US' => 'https://community.1and1.com/tag/featured-wordpress/feed/',
		'max_items'               => 3
	);

	public $moreUrls = array(
		'link_url_de_DE' => 'https://community.1und1.de/category/wordpress-2/?pk_campaign=1and1managed&pk_kwd=newswidget-more-de',
		'link_url_en_US' => 'https://community.1and1.com/category/wordpress/?pk_campaign=1and1managed&pk_kwd=newswidget-more-en',
	);

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_styles_scripts' ) );

		// add new dashboard widget
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );

		// remove default dashboard widgets
		add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_meta' ) );
	}

	public function add_styles_scripts() {
		wp_register_style( '1and1-assistant-dashboard-widget', One_And_One_Wizard::get_css_url( 'assistant-dashboard-widget.css' ), array(), One_And_One_Wizard::VERSION );

		global $pagenow;

		if ( is_admin() && $pagenow == 'index.php' ) {
			wp_enqueue_style( '1and1-assistant-dashboard-widget' );
		}
	}

	public function add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'oneandone_assistant_dashboard_widget',
			__( '1&1 Community News', '1and1-wordpress-wizard' ),
			array( $this, 'dashboard_widget_function' )
		);

		// forcing widget to the top (works only if user never reordered the dashboard widgets)
		global $wp_meta_boxes;

		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$widget_backup    = array( 'oneandone_assistant_dashboard_widget' => $normal_dashboard['oneandone_assistant_dashboard_widget'] );
		unset( $normal_dashboard['oneandone_assistant_dashboard_widget'] );
		$sorted_dashboard                             = array_merge( $widget_backup, $normal_dashboard );
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	public function dashboard_widget_function() {
		//default is EN
		$feedUrl = $this->settings['feed_url_en_US'];

		if ( isset( $this->settings['feed_url_'.get_locale()] ) ) {
			$feedUrl = $this->settings['feed_url_'.get_locale()];
		}

		$feed = fetch_feed( $feedUrl );

		//default is EN
		$moreLinkUrl = $this->moreUrls['link_url_en_US'];

		if ( isset( $this->moreUrls['link_url_'.get_locale()] ) ) {
			$moreLinkUrl = $this->moreUrls['link_url_'.get_locale()];
		}

		$maxitems = 0;

		if ( ! is_wp_error( $feed ) ) {
			$maxitems = $feed->get_item_quantity( $this->settings['max_items'] );
			// We need to load 4 items because one item could be tagged as a featured item
			$feed_items = $feed->get_items( 0, $maxitems + 1 );
		}

		// Get featured feed
		$feedFeaturedUrl = $this->settings['feed_url_featured_en_US'];

		if ( isset( $this->settings['feed_url_featured_'.get_locale()] ) ) {
			$feedFeaturedUrl = $this->settings['feed_url_featured_'.get_locale()];
		}

		$feed_featured = fetch_feed( $feedFeaturedUrl );

		if ( ! is_wp_error( $feed_featured ) ) {
			$feed_featured_item = $feed_featured->get_items( 0, 1 );
		}

		if ( ! empty( $feed_featured_item[0] ) ) {
			$feed_featured_item['featured'] = $feed_featured_item[0];
			unset( $feed_featured_item[0] );
			$this->create_feed_array( $feed_items, $feed_featured_item );
		} else {
			array_pop( $feed_items );
		}
		?>

		<ul>
			<?php if ( $maxitems == 0 ) : ?>
				<li><?php _e( 'No items', '1and1-wordpress-wizard' ); ?></li>
			<?php else : ?>
				<?php // Loop through each feed item and display each item as a hyperlink. ?>
				<?php foreach ( $feed_items as $key => $item ) : ?>
					<?php $class = 'one-and-one-feeditem'; ?>
					<?php if ( $key === 'featured' ) : ?>
						<?php $class = 'one-and-one-featureditem'; ?>
					<?php endif; ?>
					<li class="<?php echo $class; ?>">
						<?php
						//add piwik tracking to the item URLs
						$itmUrl = $item->get_permalink();
						try {
							$piwikTrackingParams = 'pk_campaign=1and1managed&pk_kwd=newswidget';
							$urlArr              = parse_url( $itmUrl );

							if ( isset( $urlArr["query"] ) ) {
								$itmUrl .= '&'.$piwikTrackingParams;
							} else {
								$itmUrl .= '?'.$piwikTrackingParams;
							}
						}
						catch ( Exception $e ) {
							//do nothing!
						}
						?>
						<a target="_blank" class="news-title" href="<?php echo esc_url( $itmUrl ); ?>" title="<?php echo esc_html( $item->get_title() ); ?>">
							<?php echo esc_html( $item->get_title() ); ?>
						</a>
						<p>
							<a target="_blank" href="<?php echo esc_url( $itmUrl ); ?>" title="<?php echo esc_html( $item->get_title() ); ?>">
								<?php echo wp_trim_words( $item->get_description(), 25, null ); ?>
							</a>
						</p>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
			<li>
				<a target="_blank" class="button button-primary" href="<?php echo $moreLinkUrl; ?>" title=""><?php _e( 'community_widget_link_label', '1and1-wordpress-wizard' ); ?></a>
			</li>
		</ul>

		<?php
	}

	public function remove_dashboard_meta() {
		//        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		//        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		//        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		//        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		//        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		//        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		//        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		//        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
	}

	private function create_feed_array( &$feed_items, $feed_featured_item ) {
		$feed_featured_title = $feed_featured_item['featured']->get_title();

		foreach ( $feed_items as $key => $feed_item ) {
			$feed_title = $feed_item->get_title();

			if ( $feed_title == $feed_featured_title ) {
				unset( $feed_items[$key] );
				break;
			}
		}

		if ( count( $feed_items ) > 3 ) {
			$feed_items = array_slice( $feed_items, 0, 3 );
		}

		$feed_items = array_merge( $feed_featured_item, $feed_items );
	}

}

new One_And_One_Assistant_Dashboard_Widget();
