<?php 
/**
 * The template used to display radio form fields.
 *
 * Override this template by copying it to yourtheme/charitable/form-fields/radio.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Form Fields
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! isset( $view_args[ 'form' ] ) || ! isset( $view_args[ 'field' ] ) ) {
    return;
}

$form           = $view_args[ 'form' ];
$field          = $view_args[ 'field' ];
$classes        = $view_args[ 'classes' ];
$is_required    = isset( $field[ 'required' ] ) ? $field[ 'required' ] : false;
$options        = isset( $field[ 'options' ] ) ? $field[ 'options' ] : array();
$value          = isset( $field[ 'value' ] ) ? $field[ 'value' ] : '';

if ( empty( $options ) ) {
    return;
}

?>
<div id="charitable_field_<?php echo $field['key'] ?>" class="<?php echo $classes ?>">
    <?php if ( isset( $field['label'] ) ) : ?>
        <label for="charitable_field_<?php echo $field['key'] ?>">
            <?php echo $field['label'] ?>
            <?php if ( $is_required ) : ?>
                <abbr class="required" title="required">*</abbr>
            <?php endif ?>
        </label>
    <?php endif ?>
    <ul class="charitable-radio-list <?php echo esc_attr( $view_args[ 'classes' ] ) ?>">

        <?php foreach ( $options as $option => $label ) : ?>

            <li><input type="radio" 
                    id="<?php echo $field[ 'key' ] . '-' . $option ?>" 
                    name="<?php echo $field[ 'key' ] ?>"
                    value="<?php echo esc_attr( $option ) ?>"
                    <?php checked( $value, $option ) ?>
                    <?php echo charitable_get_arbitrary_attributes( $field ) ?> />
                <?php echo $label ?>
            </li>

        <?php endforeach ?>

    </ul>
</div>