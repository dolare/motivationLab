<?php 
/**
 * Renders the donation details meta box for the Donation post type.
 *
 * @author  Studio 164a
 * @since   1.0.0
 */
global $post;

$logs = get_post_meta( $post->ID, '_donation_log', true );

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$date_time_format = "$date_format - $time_format";
?>
<div id="charitable-donation-log-metabox" class="charitable-metabox">
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e( 'Date &amp; Time', 'charitable' ) ?></th>
                <th><?php _e( 'Log', 'charitable' ) ?></th>
            </th>
        </thead>
        <?php foreach ( $logs as $log ) : ?>
        <tr>
            <td><?php echo date_i18n( $date_time_format, $log[ 'time' ] ) ?></td>
            <td><?php echo $log[ 'message' ] ?></td>
        </tr>
        <?php endforeach ?>
    </table>
</div>