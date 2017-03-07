<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatingsCommentsList extends CustomStarRatings {
	
	public function __construct() {
		parent::__construct();
		add_action( 'genesis_before_content', array($this,'custom_genesis_list_comments') );
	}
	
	/* Output debug information 
	--------------------------------------------------------------*/	
	public function display_debug_info() {
		//$this->dbg('In Custom Rating Comments List child class !','' );
		//$this->dbg('Rated types: ', $this->ratedPostTypes );
	}	
	
	
	/* Remove the genesis_default_list_comments function
		 Replace comment list with one including ratings
	-------------------------------------------------------*/
	public function custom_genesis_list_comments() {
		if ( is_singular( $this->ratedPostTypes ) ) {
			remove_action( 'genesis_list_comments', 'genesis_default_list_comments' );
			add_action( 'genesis_list_comments', array($this,'custom_star_rating_list_comments') );
		}
	}
	
	
	public function custom_star_rating_list_comments() {
		$args = array(
	    'type'          => 'comment',
	    'avatar_size'   => 50,
	    'callback'      => array($this,'custom_star_rating_comment_list'),
	    //'per_page' 			=> '2',
		);
		$args = apply_filters( 'genesis_comment_list_args', $args );		
		wp_list_comments( $args );
	}


	/* Custom Comment Template */
	public function custom_star_rating_comment_list($comment, $args, $depth) {
		
  $GLOBALS['comment'] = $comment; 
  ?>
  <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
  	  	
	<div class="comment-item">
 	
    <?php do_action( 'genesis_before_comment' ); ?>
    
  	<div class="comment-intro">
        <div class="comment-author">
        	<div class="comment-avatar">
            <?php echo get_avatar($comment,$size='48'); ?>
        	</div>
            <?php 
            if ( $depth=='1' )
            	printf(__('%s says:', 'custom-star-rating'), get_comment_author_link());
            else 
            	printf(__('%s responds:', 'custom-star-rating'), get_comment_author_link()); ?>
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
		
  </div>
  <!-- Pas </li> pour commentaires imbriqués -->
  
	<?php
	}
	
	
	/* Change the comment reply link to display our own comment form */
	//add_filter('comment_reply_link', 'remove_nofollow', 420, 4);
	public function remove_nofollow($link, $args, $comment, $post){
	  return str_replace("rel='nofollow'", "", $link);
	}

	
	
}
