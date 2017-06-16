<?php 
/**
 * Renders the campaign's donation form settings in the metabox for the Campaign post type.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $post;

$form_fields = charitable()->get_registered_object('Charitable_Campaign_Post_Type')->get_donation_form_fields();

$selected_fields = (array) get_post_meta( $post->ID, '_campaign_donation_form_fields', true );
?>
<section class="charitable-metabox-section">
	<h4 class="charitable-metabox-section-header"><?php _e( 'Donation Form', 'charitable' ) ?></h4>
	<p class="charitable-metabox-field">

	</p>
	<ul class="charitable-metabox-field charitable-checkbox-field">
		<?php foreach ( $form_fields as $field_key => $field ) : ?>
		<li>
			<?php if ( isset( $field['required_in_form'] ) && $field['required_in_form'] ) : ?>
				<input type="checkbox" 
					id="campaign_donation_form_fields_<?php echo $field_key ?>" 
					name="_campaign_donation_form_fields[]" 
					value="<?php echo $field_key ?>" 
					checked 
					disabled 
				/>
			<?php else : ?>
				<input type="checkbox" 
					id="campaign_donation_form_fields_<?php echo $field_key ?>" 
					name="_campaign_donation_form_fields[]" 
					value="<?php echo $field_key ?>" 
					<?php checked( in_array($field_key, $selected_fields) ) ?> 
				/>
			<?php endif ?>
			<label for="campaign_donation_form_fields_<?php echo $field_key ?>">
				<?php echo $field['label'] ?>
			</label>

		</li>
	<?php endforeach ?>
	</ul>
</section>