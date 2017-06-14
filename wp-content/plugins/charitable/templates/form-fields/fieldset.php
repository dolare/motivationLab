 <?php
/**
 * The template used to display select fieldsets.
 *
 * @author 	Studio 164a
 * @package Charitable/Templates/Form Fields
 * @since 	1.0.0
 * @version 1.0.0
 */

if ( ! isset( $view_args['form'] ) || ! isset( $view_args['field'] ) ) {
	return;
}

$form 			= $view_args['form'];
$field 			= $view_args['field'];
$classes 		= $view_args['classes'];
$fields 		= isset( $field['fields'] ) ? $field['fields'] : array();

if ( count( $fields ) ) :

?>
<fieldset class="<?php echo $classes ?>">
	<?php
	if ( isset( $field['legend'] ) ) : ?>

		<div class="charitable-form-header"><?php echo $field['legend'] ?></div>

	<?php
	endif;

	$i = 1;

	foreach ( $fields as $key => $fieldset_field ) :

		do_action( 'charitable_form_field', $fieldset_field, $key, $form, $i );

		$i += apply_filters( 'charitable_form_field_increment', 1, $fieldset_field, $key, $form );

	endforeach;

	?>
</fieldset>
<?php

endif;
