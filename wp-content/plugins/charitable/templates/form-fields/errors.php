<?php
/**
 * The template used to display error messages.
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.3.0
 */

if ( ! isset( $view_args['errors'] ) ) {
	return;
}

$errors = $view_args['errors'];

?>
<div class="charitable-form-errors charitable-notice">
	<ul class="errors">
		<?php foreach ( $errors as $error ) : ?>
			<li><?php echo $error ?></li>
		<?php endforeach ?>
	</ul>
</div>
