<?php
/**
 * The template used to display the donor's current details.
 *
 * @author 	Studio 164a
 * @package Charitable/Templates/Donation Form
 * @since 	1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * @var Charitable_User
 */
$user = $view_args['user'];

if ( ! $user ) {
	return;
}

?>
<address class="donor-address"><?php echo $user->get_address() ?></address>
<p class="donor-contact-details">
	<?php printf( '%s: %s', __( 'Email', 'charitable ' ), $user->user_email ) ?>
	<?php if ( $user->__isset( 'donor_phone' ) ) :
		printf( '<br />%s: %s', __( 'Phone number', 'charitable' ), $user->get( 'donor_phone' ) );
	endif ?>
</p>
