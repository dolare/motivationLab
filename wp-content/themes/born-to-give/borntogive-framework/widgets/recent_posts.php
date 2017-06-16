<?php
/*** Widget code for Popular Post ***/
class borntogive_recent_post extends WP_Widget {
	// constructor
	function borntogive_recent_post() {
		 $widget_ops = array('description' => esc_html__( 'Show recent posts with thumbnail','borntogive') );
         parent::__construct(false, $name = esc_html__('Recent Posts with Thumbs','borntogive'), $widget_ops);
	}
	// widget form creation
	function form($instance) {
		// Check values
		if( $instance) {
			 $title = esc_attr($instance['title']);
			 $type = esc_attr($instance['type']);
			 $number = esc_attr($instance['number']);
			 $posts = isset( $instance['posts'] ) ? (bool) $instance['posts'] : false;
		} else {
			 $title = '';
			 $type = '';
			 $number = '';
			 $posts = '';
		}
	?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title', 'borntogive'); ?></label>
            <input class="spTitle" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of posts to show', 'borntogive'); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
        </p> 
        <p>
        	<input type="checkbox" class="spType" id="<?php echo esc_attr($this->get_field_id('posts')); ?>" name="<?php echo esc_attr($this->get_field_name('posts')); ?>" <?php checked( $posts ); ?>> <?php esc_html_e('Hide current post(This will hide the current post from the list of recent posts when on the single post page)', 'borntogive'); ?></label>
     	</p>
	<?php
	}
	// update widget
	function update($new_instance, $old_instance) {
		  $instance = $old_instance;
		  // Fields
		  $instance['title'] = strip_tags($new_instance['title']);
		  $instance['type'] = strip_tags($new_instance['type']);
		  $instance['number'] = strip_tags($new_instance['number']);
		  $instance['posts'] = !empty($new_instance['posts']) ? 1 : 0;
		  
		 return $instance;
	}
	// display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $post_title = apply_filters('widget_title', $instance['title']);
	   $type = apply_filters('widget_type', $instance['type']);
	   $number = apply_filters('widget_number', $instance['number']);
	   $posts_active = ! empty( $instance['posts'] ) ? '1' : '0';
		$hightlight = 2;
	   $numberPost = (!empty($number))? $number : 4 ;	
	   $currentID = get_the_ID();	   
	   echo $args['before_widget'];
		
		if( !empty($instance['title']) ){
			echo $args['before_title'];
			echo apply_filters('widget_title',$instance['title'], $instance, $this->id_base);
			echo $args['after_title'];
		}
		if($posts_active == 1){
			$args_posts = array('post_type' => 'post', 'posts_per_page' => $numberPost, 'post_status' => 'publish', 'post__not_in' => array($currentID));
		} else {
			$args_posts = array('post_type' => 'post', 'posts_per_page' => $numberPost, 'post_status' => 'publish');
		}
		$posts_listing = new WP_Query( $args_posts );
		if ( $posts_listing->have_posts() ):
			echo '<ul>';
			$counter = 1;
			 while ( $posts_listing->have_posts() ):$posts_listing->the_post();
			 echo '<li>';
			 if(has_post_thumbnail(get_the_ID()))
			 {
    			echo '<a href="'.get_the_permalink().'" class="media-box">';
					echo get_the_post_thumbnail(get_the_ID());
					echo '</a>';
					echo '<h5><a href="'.get_the_permalink().'">'.get_the_title().'</a></h5>
          			<span class="meta-data grid-item-meta"><i class="fa fa-calendar"></i> '. get_the_date();'</span>';
			 } else {
					echo '<h5 class="no-padding-left"><a href="'.get_the_permalink().'">'.get_the_title().'</a></h5>
          			<span class="meta-data grid-item-meta no-padding-left"><i class="fa fa-calendar"></i> '. get_the_date(get_option('date_format'), get_the_ID());'</span>';
			 }
			 
       
          echo '</li>';
			 endwhile;
			echo '</ul>';
		else:
			echo esc_html__('No Post Found','borntogive');		
		endif; wp_reset_postdata();
	   echo $args['after_widget'];
	}
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("borntogive_recent_post");'));
?>