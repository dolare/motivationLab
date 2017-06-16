<?php
    if (post_password_required()) {
        ?>
        <p class="nocomments"><?php esc_html_e('This post is password protected. Enter the password to view comments.', 'borntogive') ?></p>
        <?php
        return;
    }
    /* ----------------------------------------------------------------------------------- */
    /* 	Display the comments + Pings
      /*----------------------------------------------------------------------------------- */
    if (have_comments()) : // if there are comments 
        ?>
        <section class="post-comments">
        <div id="comments" class="clearfix">
            <?php if (!empty($comments_by_type['comment'])) : // if there are normal comments  ?>
                <h3><i class="fa fa-comment"></i>  <?php comments_number(esc_html__('No Comments', 'borntogive'), esc_html__('Comment(1)', 'borntogive'), esc_html__('Comments(%)', 'borntogive')); ?></h4>
                <ol class="comments">
                    <?php wp_list_comments('type=comment&avatar_size=51&callback=borntogive_comment'); ?>
                </ol>
            <?php paginate_comments_links(); endif; ?>
            <?php
            /* ----------------------------------------------------------------------------------- */
            /* 	Deal with no comments or closed comments
              /*----------------------------------------------------------------------------------- */
            if ('closed' == $post->comment_status) : // if the post has comments but comments are now closed 
                ?>
                <p class="nocomments"><?php esc_html_e('Comments are now closed for this article.', 'borntogive') ?></p>
            <?php endif; ?>
            </section>
        <?php else : ?>
            <?php if ('open' == $post->comment_status) : // if comments are open but no comments so far  ?>
            <?php else : // if comments are closed ?>
                <?php if (is_single()) { ?><section class="post-comments"><p class="nocomments"><?php esc_html_e('Comments are closed.', 'borntogive') ?></p></section><?php } ?>
            <?php endif; ?>
        <?php endif; ?>
<?php
/* ----------------------------------------------------------------------------------- */
/* 	Comment Form
  /*----------------------------------------------------------------------------------- */
add_filter('comment_form_defaults', 'borntogive_comment_form');
function borntogive_comment_form($form_options)
{
	$commenter = wp_get_current_commenter();
$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );
    // Fields Array
    $fields = array(
        'author' => '<div class="row">
                                <div class="form-group">
                                    <div class="col-md-4 col-sm-4">
                                        <input type="name" class="form-control input-lg" name="author" id="author" value="'.esc_attr( $commenter['comment_author'] ).'" size="22" tabindex="1" placeholder="'.esc_html__('Your name', 'borntogive').'" />
                                    </div>',
        'email' => '<div class="col-md-4 col-sm-4">
                                        <input type="email" name="email" class="form-control input-lg" id="email" value="'.esc_attr( $commenter['comment_author_email'] ).'" size="22" tabindex="2" placeholder="'.esc_html__('Your email', 'borntogive').'" />
                                    </div>',
        'url' => '<div class="col-md-4 col-sm-4">
                                        <input type="url" class="form-control input-lg" name="url" id="url" value="'.esc_attr( $commenter['comment_author_url'] ).'" size="22" tabindex="3" placeholder="'.esc_html__('Website (optional)', 'borntogive').'" /></div>
                                </div>
                            </div>',
    );
    // Form Options Array
    $form_options = array(
        // Include Fields Array
        'fields' => apply_filters( 'comment_form_default_fields', $fields ),
        // Template Options
        'comment_field' =>
        '<div class="row">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <textarea name="comment" id="comment-textarea" class="form-control input-lg" cols="8" rows="4"  tabindex="4" placeholder="'.esc_html__('Your comment', 'borntogive').'" ></textarea>
                                </div>
                            </div>
                        </div>',
        'must_log_in' => '',
        'logged_in_as' =>
       '',
        'comment_notes_before' =>'',
        'comment_notes_after' => '',
        // Rest of Options
        'id_form' => 'form-comment',
        'id_submit' => 'comment-submit',
        'title_reply' => '
                <h3><i class="fa fa-share"></i> '.esc_html__( 'Post a comment','borntogive' ).'</h3>
                <div class="cancel-comment-reply"></div>',
        'title_reply_to' => esc_html__( 'Leave a Reply to %s','borntogive' ),
        'cancel_reply_link' => esc_html__( 'Cancel reply','borntogive' ),
        'label_submit' => esc_html__( 'Submit your comment', 'borntogive' ),
    );
    return $form_options;
}
comment_form();