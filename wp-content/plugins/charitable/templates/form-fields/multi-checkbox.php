 <?php
/**
 * The template used to display form fields with multiple checkboxes.
 *
 * @author  Studio 164a
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! isset( $view_args[ 'form' ] ) || ! isset( $view_args[ 'field' ] ) ) {
    return;
}

$form           = $view_args[ 'form' ];
$field          = $view_args[ 'field' ];
$classes        = $view_args[ 'classes' ];
$is_required    = isset( $field[ 'required' ] ) ? $field[ 'required' ] : false;
$options        = isset( $field[ 'options' ] ) ? $field[ 'options' ] : array();
$value          = isset( $field[ 'value' ] ) ? (array) $field[ 'value' ] : array();

if ( empty( $options ) ) {
    return;
}
?>
<div id="charitable_field_<?php echo $field[ 'key' ] ?>" class="<?php echo $classes ?>">
    
    <?php if ( isset( $field[ 'label' ] ) ) : ?>

        <label for="charitable_field_<?php echo $field[ 'key' ] ?>">
            <?php echo $field[ 'label' ] ?>
            <?php if ( $is_required ) : ?>
                <abbr class="required" title="required">*</abbr>
            <?php endif ?>
        </label>

    <?php endif ?>

    <ul class="options">

    <?php foreach ( $options as $val => $label ) : ?>

        <li>
            <input type="checkbox" name="<?php echo $field[ 'key' ] ?>[]" value="<?php echo $val ?>" <?php checked( in_array( $val, $value ) ) ?> <?php echo charitable_get_arbitrary_attributes( $field ) ?>/>
            <?php echo $label ?>
        </li>

    <?php endforeach ?>

    </ul>
</div>