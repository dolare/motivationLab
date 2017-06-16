<?php
/**
 * Display the table of products requiring licenses. 
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

$helper = charitable_get_helper( 'licenses' );
$products = $helper->get_products();

if ( empty( $products ) ) : 
    return;
endif;

foreach ( $products as $key => $product ) :

    $license = $helper->get_license_details( $key );    

    if ( is_array( $license ) ) {
        $is_active = $license[ 'valid' ];
        $license_key = $license[ 'license' ];
    }
    else {
        $is_active = false;
        $license_key = $license;
    }
    
    ?>
    <div class="charitable-settings-object charitable-licensed-product cf">
        <h4><?php echo $product[ 'name' ] ?></h4>
        <input type="text" name="charitable_settings[licenses][<?php echo $key ?>]" id="charitable_settings_licenses_<?php echo $key ?>" class="charitable-settings-field" placeholder="<?php _e( 'Add your license key', 'charitable' ) ?>" value="<?php echo $license_key ?>" />
        <?php if ( $license ) : ?>
            <div class="license-meta">
                <?php if ( $is_active ) : ?>
                    <a href="<?php echo $helper->get_license_deactivation_url( $key ) ?>" class="button-secondary license-deactivation"><?php _e( 'Deactivate License' ) ?></a>
                    <span class="license-expiration-date"><?php printf( '%s %s.', __( 'Expiring in', 'charitable' ), human_time_diff( strtotime( $license[ 'expiration_date' ] ), time() ) ) ?></span>
                <?php elseif( is_array( $license ) ) : ?>
                    <span class="license-invalid"><?php _e( 'This license is not valid', 'charitable' ) ?></span>
                <?php else : ?>
                    <span class="license-invalid"><?php _e( 'We could not validate this license', 'charitable' ) ?></span>
                <?php endif ?>
            </div>
        <?php endif ?>        
    </div>

    <?php
endforeach;