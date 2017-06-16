<?php
add_action( 'admin_init', 'add_event_tickets_registrant' );
/**
 * Add custom Meta Box to Event Registrants post type
 */
function add_event_tickets_registrant() 
{
    add_meta_box('event_tickets',esc_html__('Registrant Tickets','borntogive'),'borntogive_event_tickets_output','event_registrants','normal','core');
}
/**
 * Print the Meta Box content
 */
function borntogive_event_tickets_output() 
{
    global $post, $line_icons;
    $tickets_type = get_post_meta( $post->ID, 'borntogive_registrant_ticket_type', true );
?>
<div id="field_group">
    <div id="field_wrap">
        <div class="field_row">
        <div class="field_left">
        <?php
				if(!empty($tickets_type))
				{
					foreach($tickets_type as $key=>$value)
					{
						echo '<div><label>'.esc_attr__('Ticket Type: ', 'borntogive').esc_attr($key).'</label></div>';
						echo '<div><label>'.esc_attr__('Number of Tickets: ', 'borntogive').esc_attr($value).'</label></div>';
						echo '<br/>';
					}
				}
				?>
        </div>
          <div class="clear" /></div> 
        </div>
    </div>
    </div>
    <?php
}