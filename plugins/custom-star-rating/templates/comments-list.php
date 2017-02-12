<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/* Custom Comment Template */
function custom_star_rating_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
    	
      <?php do_action( 'genesis_before_comment' ); ?>
      
    	<div class="comment-intro">
          <div class="comment-author">
          	<div class="comment-avatar">
              <?php echo get_avatar($comment,$size='48'); ?>
          	</div>
              <?php printf(__('%s says:', 'custom-star-rating'), get_comment_author_link()) ?>
          </div>

          <?php if ($comment->comment_approved == '0') : ?>
          <em><?php _e('Your comment is awaiting moderation.') ?></em>
          <br />
          <?php endif; ?>

          <div class="comment-meta">
          	<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
          		<?php printf(__('%1$s at %2$s','custom-star-rating'), get_comment_date(),  get_comment_time()) ?>
          	</a>
          </div>

    	</div>
        
			<div class="comment-rating">
    		<?php echo do_shortcode('[display-star-rating source="comment"]');?>         
  		</div>

			<div class="comment-content">
      <?php comment_text() ?>
			</div>

      <div class="comment-reply">
          <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>
      
			<div class="comment-edit">
      	<?php edit_comment_link(__('(Edit)'),'  ','') ?>
			</div>
			
  		<?php do_action( 'genesis_after_comment' );?>
  		
    </li>
    
<?php
}


?>