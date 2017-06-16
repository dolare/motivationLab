<?php 
/**
 * Displays the credit card expiration select boxes.
 *
 * Override this template by copying it to yourtheme/charitable/cc-expiration.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Donation Form
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
$current_year   = date( 'Y' );

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
    <select name="<?php echo $field['key'] ?>[month]" class="month">
        <?php foreach ( range( 1, 12 ) as $month ) : 
            $padded_month = sprintf( '%02d', $month );
            ?>
            <option value="<?php echo $padded_month ?>"><?php echo $padded_month ?></option>
        <?php endforeach ?>
    </select>
    <select name="<?php echo $field['key'] ?>[year]" class="year">
        <?php for ( $i = 0; $i < 15; $i++ ) :
            $year = $current_year + $i;
            ?>
            <option value="<?php echo $year ?>"><?php echo $year ?></option>
        <?php endfor ?>
    </select>
</div><!-- #charitable_field_<?php echo $field['key'] ?> -->
