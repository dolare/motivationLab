<?php
/** Do not allow direct access! */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Class One_And_One_Plugin_Adapter
 * Enhances the Assistant Interface according to which plugins have been installed
 */
class One_And_One_Plugin_Adapter {

	/**
	 * WooCommerce Plugin
	 * - adds post-setup buttons for WooCommerce
	 */
	public function adapt_woocommerce() {

        /** Add link to the WooCommerce Wizard */
        add_action( 'oneandone_post_setup_custom_buttons', function() {
            echo sprintf(
				'<a href="%s" class="button button-primary" title="%s" target="_parent">%s</a> &nbsp; &nbsp;',
				admin_url( 'index.php?page=wc-setup' ),
				$this->get_translation( 'Shop configuration' ),
				$this->get_translation( 'Shop configuration' )
			);
		} );
	}

	/**
	 * Wrapper to call a translation
	 *
	 * @param  string $key
	 * @return string
	 */
	public function get_translation( $key ) {
		return __( $key, '1and1-wordpress-wizard' );
	}
}
