<?php
/*** Widget code for Tabbed Content ***/
class borntogive_tabs_widget extends WP_Widget {
	// constructor
	function borntogive_tabs_widget() {
		 $widget_ops = array('description' => esc_html__( 'Show Recent Posts, Recent Comments, Tags','borntogive') );
         parent::__construct(false, $name = esc_html__('Tabbed content widget','borntogive'), $widget_ops);
	}
	// widget form creation
	function form($instance) {
		// Check values
		if( $instance) {
			 $posts = isset( $instance['posts'] ) ? (bool) $instance['posts'] : false;
			 $comments = isset( $instance['comments'] ) ? (bool) $instance['comments'] : false;
			 $tags = isset( $instance['tags'] ) ? (bool) $instance['tags'] : false;
			 $posts_no = esc_attr($instance['posts_no']);
			 $comments_no = esc_attr($instance['comments_no']);
			 $tags_no = esc_attr($instance['tags_no']);
		} else {
			 $posts = '';
			 $comments = '';
			 $tags = '';
			 $posts_no = '';
			 $comments_no = '';
			 $tags_no = '';
		}
	?>
        
        <p>
        	<strong style="display:block"><?php esc_html_e('Choose tabs content', 'borntogive'); ?></strong>
            <label style="display:inline-block; width:45%;" for="<?php echo esc_attr($this->get_field_id('posts')); ?>">
            <input type="checkbox" class="spType" id="<?php echo esc_attr($this->get_field_id('posts')); ?>" name="<?php echo esc_attr($this->get_field_name('posts')); ?>" <?php checked( $posts ); ?>> <?php esc_html_e('Recent Posts', 'borntogive'); ?></label>
            <label style="display:inline-block; width:45%;" for="<?php echo esc_attr($this->get_field_id('comments')); ?>">
            <input type="checkbox" class="spType" id="<?php echo esc_attr($this->get_field_id('comments')); ?>" name="<?php echo esc_attr($this->get_field_name('comments')); ?>" <?php checked( $comments ); ?>> <?php esc_html_e('Recent Comments', 'borntogive'); ?></label>
            <label style="display:inline-block; width:45%;" for="<?php echo esc_attr($this->get_field_id('tags')); ?>">
            <input type="checkbox" class="spType" id="<?php echo esc_attr($this->get_field_id('tags')); ?>" name="<?php echo esc_attr($this->get_field_name('tags')); ?>" <?php checked( $tags ); ?>> <?php esc_html_e('Tags', 'borntogive'); ?></label>
            
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('posts_no')); ?>"><?php esc_html_e('Number of recent posts to show', 'borntogive'); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('posts_no')); ?>" name="<?php echo esc_attr($this->get_field_name('posts_no')); ?>" type="text" value="<?php echo esc_attr($posts_no); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('comments_no')); ?>"><?php esc_html_e('Number of recent comments to show', 'borntogive'); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('comments_no')); ?>" name="<?php echo esc_attr($this->get_field_name('comments_no')); ?>" type="text" value="<?php echo esc_attr($comments_no); ?>" />
        </p> 
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('tags_no')); ?>"><?php esc_html_e('Number of tags to show', 'borntogive'); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('tags_no')); ?>" name="<?php echo esc_attr($this->get_field_name('tags_no')); ?>" type="text" value="<?php echo esc_attr($tags_no); ?>" />
        </p> 
	<?php
	}
	// update widget
	function update($new_instance, $old_instance) {
		  $instance = $old_instance;
		  // Fields
		  $instance['posts'] = !empty($new_instance['posts']) ? 1 : 0;
		  $instance['comments'] = !empty($new_instance['comments']) ? 1 : 0;
		  $instance['tags'] = !empty($new_instance['tags']) ? 1 : 0;
		  $instance['posts_no'] = strip_tags($new_instance['posts_no']);
		  $instance['comments_no'] = strip_tags($new_instance['comments_no']);
		  $instance['tags_no'] = strip_tags($new_instance['tags_no']);
		  
		 return $instance;
	}
	// display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $posts_active = ! empty( $instance['posts'] ) ? '1' : '0';
	   $comments_active = ! empty( $instance['comments'] ) ? '1' : '0';
	   $tags_active = ! empty( $instance['tags'] ) ? '1' : '0';
	   $posts_number = apply_filters('widget_posts_number', $instance['posts_no']);
	   $comments_number = apply_filters('widget_comments_number', $instance['comments_no']);
	   $tags_number = apply_filters('widget_tags_number', $instance['tags_no']);
	   
	   $numberPosts = (!empty($posts_number))? $posts_number : 3;	
	   $numberComments = (!empty($comments_number))? $comments_number : 3;	
	   $numberTags = (!empty($tags_number))? $tags_number : 10;	
	   	   
	   echo $args['before_widget'];
		
		if( !empty($instance['title']) ){
			echo $args['before_title'];
			echo apply_filters('widget_title',$instance['title'], $instance, $this->id_base);
			echo $args['after_title'];
		}
		if($posts_active == 1 || $comments_active == 1 || $tags_active == 1){
          	echo '<div class="tabbed_widgets tabs">
                 	<ul class="nav nav-tabs">';
                    	if($posts_active == 1){echo'<li> <a data-toggle="tab" href="#Trecent">'.__('Recent Posts','borntogive').'</a> </li>';}
                    	if($comments_active == 1){echo'<li> <a data-toggle="tab" href="#Tcomments">'.__('Recent Comments ','borntogive').'</a> </li>';}
                    	if($tags_active == 1){echo'<li> <a data-toggle="tab" href="#Ttags">'.__('Post Tags','borntogive').'</a> </li>';}
                echo '     	
                 	</ul>
             		<div class="tab-content">
                     	';
		}
		
		if($posts_active == 1){
			$args_posts = array('post_type' => 'post', 'posts_per_page' => $numberPosts, 'post_status' => 'publish');
			$posts_listing = new WP_Query( $args_posts );
			if ( $posts_listing->have_posts() ):
				echo '<div id="Trecent" class="tab-pane widget_borntogive_recent_post"><ul>';
				$counter = 1;
				 while ( $posts_listing->have_posts() ):$posts_listing->the_post();
				 echo '<li>';
				 if(has_post_thumbnail(get_the_ID()))
				 {
					echo '<a href="'.get_the_permalink().'" class="media-box">';
						echo get_the_post_thumbnail(get_the_ID());
						echo '</a>';
				 }
		   		echo '<h5><a href="'.get_the_permalink().'">'.get_the_title().'</a></h5>
			  	<span class="meta-data grid-item-meta"><i class="fa fa-calendar"></i> '.get_the_date(get_option('date_format'), get_the_ID());'</span>
			  	</li>';
				 endwhile;
				echo '</ul></div>';
			else:
				echo esc_html__('No Posts Found','borntogive');		
			endif; wp_reset_postdata();
		}
		
		
		if($comments_active == 1){
				$output = '';
				$comments = get_comments( apply_filters( 'widget_comments_args', array(
					'number'      => $numberComments,
					'status'      => 'approve',
					'post_status' => 'publish'
				) ) );
				$output .= '<div id="Tcomments" class="tab-pane widget_recent_comments">';
				$output .= '<ul id="recentcomments">';
				if ( is_array( $comments ) && $comments ) {
					// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
					$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
					_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );
		
					foreach ( (array) $comments as $comment ) {
						$output .= '<li class="recentcomments">';
						/* translators: comments widget: 1: comment author, 2: post link */
						$output .= sprintf( _x( '%1$s on %2$s', 'widgets', 'borntogive' ),
							'<span class="comment-author-link">' . get_comment_author_link( $comment ) . '</span>',
							'<a href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
						);
						$output .= '</li>';
					}
		}
		$output .= '</ul></div>';
		
		echo ''.$output;
		}
		
		if($tags_active == 1){
			if ( function_exists( 'wp_tag_cloud' ) ) :
				echo '<div id="Ttags" class="tab-pane"><div class="tagcloud">';
				wp_tag_cloud('taxonomy=post_tag&number='.$numberTags.'');
				echo "</div></div>\n";
			endif;
		}
		
		
		if($posts_active == 1 || $comments_active == 1 || $tags_active == 1){
			echo '</div></div>';	
		}
	   echo $args['after_widget'];
	}
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("borntogive_tabs_widget");'));
?>