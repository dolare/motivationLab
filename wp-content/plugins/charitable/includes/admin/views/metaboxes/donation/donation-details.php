<?php
/**
 * Renders the donation details meta box for the Donation post type.
 *
 * @author  Studio 164a
 * @since   1.0.0
 */
global $post;

$meta = charitable_get_donation( $post->ID )->get_donation_meta();

?>
<div id="charitable-donation-details-metabox" class="charitable-metabox">
	<dl>
	<?php foreach ( $meta as $key => $details ) : ?>
		<dt><?php echo $details['label'] ?></dt>
		<dd><?php echo $details['value'] ?></dd>
	<?php endforeach ?>
	</dl>
</div>
