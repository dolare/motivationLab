<?php
add_action( 'admin_init', 'add_event_fields_clone' );
add_action( 'save_post', 'borntogive_update_event_fields_data', 10, 2 );
/**
 * Add custom Meta Box to Posts post type
 */
function add_event_fields_clone() 
{
    add_meta_box('event_schedule',esc_html__('Event Tickets Type','borntogive'),'borntogive_event_feilds_output','event','normal','core');
}
/**
 * Print the Meta Box content
 */
function borntogive_event_feilds_output() 
{
    global $post, $line_icons;
	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'event_schedule_meta_box', 'event_schedule_meta_box_nonce' );
    $tickets_type = get_post_meta( $post->ID, 'tickets_type', true );
	
?>
<div id="field_group">
    <div id="field_wrap">
    <?php 
    if ( !empty( $tickets_type) ) 
    {
			$row_head = 1;
        foreach( $tickets_type as $ticket ) 
        {
			$ticket_title = (isset($ticket[0]))?$ticket[0]:'';
			$ticket_total = (isset($ticket[1]))?$ticket[1]:'';
			$ticket_booked = (isset($ticket[2]))?$ticket[2]:'';
			$ticket_price = (isset($ticket[3]))?$ticket[3]:'';
        ?>
        <?php if($row_head==1)
				{ 
					echo '<label class="heading1">'.esc_attr__('Ticket Title', 'borntogive').'</label>';
					echo '<label class="heading2">'.esc_attr__('Total Tickets', 'borntogive').'</label>';
					echo '<label class="heading3">'.esc_attr__('Booked Tickets', 'borntogive').'</label>';
					echo '<label class="heading4">'.esc_attr__('Ticket Cost', 'borntogive').'</label>';
					echo '<br/>';
				}
					?>
        <div class="field_row">
        <div class="field_left">
        
              <input type="text" class="meta_feat_title" name="featured[start_time][]" value="<?php echo esc_attr($ticket_title); ?>" placeholder="Ticket Type">
              <input type="text" class="meta_feat_title" name="featured[end_time][]" value="<?php echo esc_attr($ticket_total); ?>" placeholder="No of Tickets" style="width:15%">
                <input class="meta_sch_title" value="<?php echo esc_attr($ticket_booked); ?>" type="text" name="featured[sch_title][]" placeholder="Booked Tickets" style="width:15%">
                <input class="meta_sch_title" value="<?php echo esc_attr($ticket_price); ?>" type="text" name="featured[sch_price][]" placeholder="Price" style="width:15%">
                <input class="button" type="button" value="<?php esc_html_e('Remove','borntogive'); ?>" onclick="remove_field(this)" /> 
        </div>
          <div class="clear" /></div> 
        </div>
        <?php $row_head++;
        } // endforeach
    } // endif
    ?>
    </div>
    <div style="display:none" id="master-row">
    <div class="field_row">
        <div class="field_left">
              <input type="text" class="meta_feat_title" name="featured[start_time][]" value="" placeholder="Ticket Type">
              <input type="text" class="meta_feat_title" name="featured[end_time][]" value="" placeholder="No of Tickets" style="width:15%">
                <input class="meta_sch_title" value="" type="text" name="featured[sch_title][]" placeholder="Booked Tickets" style="width:15%">
                <input class="meta_sch_title" value="" type="text" name="featured[sch_price][]" placeholder="Price" style="width:15%">
                <input class="button" type="button" value="<?php esc_html_e('Remove','borntogive'); ?>" onclick="remove_field(this)" /> 
        </div>
        <div class="clear"></div>
    </div>
    </div>
    <div id="add_field_row">
      <input class="button" type="button" value="<?php esc_html_e('Add Ticket','borntogive'); ?>" onclick="add_field_row();" />
      <p><?php echo esc_attr_e('Booked Ticket field will update automatically.', 'borntogive'); ?></p>
      <p><?php echo esc_attr_e('Do not add currency in price field, currency should selected from Theme Options.', 'borntogive'); ?></p>
      <p><?php echo esc_attr_e('Do not add Ticket field to use free registration.', 'borntogive'); ?></p>
    </div>
</div>
  <?php
}
/**
 * Save post action, process fields
 */
function borntogive_update_event_fields_data( $post_id, $post_object ) 
{
    if ( ! isset( $_POST['event_schedule_meta_box_nonce'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['event_schedule_meta_box_nonce'], 'event_schedule_meta_box' ) ) {
		return;
	}
    // Doing revision, exit earlier **can be removed**
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  
        return;
    // Doing revision, exit earlier
    if ( 'revision' == $post_object->post_type )
        return;
    // Verify authenticity
	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'event' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	} 
	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['featured'] ) ) {
		return;
	}
    if ( $_POST['featured'] ) 
    {
        // Build array for saving post meta
        $tickets_type = array();
        for ($i = 0; $i < count( $_POST['featured']['start_time'] ); $i++ ) 
        {
            if ( '' != $_POST['featured']['start_time'][ $i ] ) 
            {
				$tickets_type[]  = array($_POST['featured']['start_time'][ $i ], $_POST['featured']['end_time'][ $i ], $_POST['featured']['sch_title'][ $i ], $_POST['featured']['sch_price'][ $i ]);
            }
        }
        if ( $tickets_type ) 
            update_post_meta( $post_id, 'tickets_type', $tickets_type );
        else 
            delete_post_meta( $post_id, 'tickets_type' );
    } 
    // Nothing received, all fields are empty, delete option
    else 
    {
        delete_post_meta( $post_id, 'tickets_type' );
    }
}
function add_admin_scripts_event( $hook ) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'event' === $post->post_type ) {     
            wp_enqueue_script(  'event_clone_tickets_js', BORNTOGIVE_THEME_PATH.'/js/clone_fields.js' );
			wp_enqueue_style(  'event_clone_tickets_style', BORNTOGIVE_THEME_PATH.'/css/clone_fields.css' );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'add_admin_scripts_event', 10, 1 );