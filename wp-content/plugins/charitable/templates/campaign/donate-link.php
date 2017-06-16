<?php
/**
 * Displays the donate button to be displayed on campaign pages.
 *
 * Override this template by copying it to yourtheme/charitable/campaign/donate-link.php
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

if ( ! array_key_exists( 'campaign', $view_args ) || ! is_a( $view_args['campaign'], 'Charitable_Campaign' ) ) :
	return;
endif;

$campaign = $view_args['campaign'];

if ( $campaign->has_ended() ) :
	return;
endif;

?>
<div class="campaign-donation">
	<a class="donate-button button" href="#charitable-donation-form" aria-label="<?php echo esc_attr( sprintf( _x( 'Make a donation to %s', 'make a donation to campaign', 'charitable' ), get_the_title( $campaign->ID ) ) ) ?>"><?php _e( 'Donate', 'charitable' ) ?></a>
</div>
