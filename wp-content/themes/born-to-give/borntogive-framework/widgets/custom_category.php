<?php
/*** Widget code for Selected Post ***/
class custom_category extends WP_Widget {
	// constructor
	function custom_category() {
		 $widget_ops = array('description' => esc_html__( "Display latest and selected post categories of different post type.", 'borntogive') );
        parent::__construct(false, $name = esc_html__( 'Custom Categories','borntogive'), $widget_ops);
	}
	// widget form creation
	function form($instance) {
	
		// Check values
		if( $instance) {
			 $title = esc_attr($instance['title']);
			 $type = esc_attr($instance['type']);
		} else {
			 $title = '';
			 $type = '';
		}
	?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title', 'borntogive'); ?></label>
            <input class="spTitle" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p><p>
            <label for="<?php echo esc_attr($this->get_field_id('type')); ?>"><?php esc_html_e('Select Post Type', 'borntogive'); ?></label>
            <select class="spType" id="<?php echo esc_attr($this->get_field_id('type')); ?>" name="<?php echo esc_attr($this->get_field_name('type')); ?>">
                <?php
                $post_types = borntogive_get_all_types();
				if(($key = array_search('attachment', $post_types)) !== false){
					unset($post_types[$key]);
				}
                                if(($key = array_search('page', $post_types)) !== false){
					unset($post_types[$key]);
				}
                                if(($key = array_search('staff', $post_types)) !== false){
					unset($post_types[$key]);
				}
		    if(!empty($post_types)){
                    foreach ( $post_types as $post_type ) {
						$activePost = ($type == $post_type)? 'selected' : '';
                        echo '<option value="'. $post_type .'" '.$activePost.'>' . $post_type . '</p>';
                    }
                }else{
                     echo '<option value="no">'.esc_html__( 'No Post Type Found.','borntogive').'</option>';
                }
                ?>
            </select> 
        </p> 
	<?php
	}
         // update widget
	function update($new_instance, $old_instance) {
		  $instance = $old_instance;
		  // Fields
		  $instance['title'] = strip_tags($new_instance['title']);
		  $instance['type'] = strip_tags($new_instance['type']);
		  return $instance;
	}
         // display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $post_title = apply_filters('widget_title', $instance['title']);
	   $type = apply_filters('widget_type', $instance['type']);
	   
	   $numberPost = (!empty($number))? $number : 3 ;	
	   echo $args['before_widget'];
		if( !empty($instance['title']) ){
			echo $args['before_title'];
			echo apply_filters('widget_title',$instance['title'], $instance, $this->id_base);
			echo $args['after_title'];
		}
		if($type=='product')
		{
    	$post_terms = get_terms('product_cat');
    }
		elseif($type="campaign")
		{
			$post_terms = get_terms('campaign_category');
		}
		else
		{
    	$post_terms = get_terms($type.'-category');  
    }
		echo '<ul>';
		foreach ($post_terms as $term) 
		{
    	$term_name = $term->name;
			if($type=='campaign')
			{
				$term_link = get_term_link($term,$type.'_category'); 
			}
			elseif($type=='product')
			{
				$term_link = get_term_link($term,$type.'_cat'); 
			}
			else
			{
      	$term_link = get_term_link($term,$type.'-category'); 
			}
      $count = $term->count;
      if($type == 'event') 
			{
				$events = borntogive_recur_events('future', $term->term_id);	
				$count = count($events);						
			}
     	if((!empty($term_link))&&($count>0))
			{
				echo '<li><a href="' . esc_url($term_link) .'">' . $term_name . '</a> (' . $count . ')</li>';
      }
		}
		echo '</ul>';
    echo $args['after_widget'];
	}
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("custom_category");'));
?>