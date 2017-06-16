<?php
/**
 * Wraps the donor fields inside a div that hides them, with an option to toggle them on.
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Donation Form
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

?>
<p class="charitable-change-user-details">
    <a href="#" data-charitable-toggle="charitable-user-fields"><?php _e( 'Update your details', 'charitable' ) ?></a>
</p><!-- .charitable-change-user-details -->
<div id="charitable-user-fields" class="charitable-hidden">