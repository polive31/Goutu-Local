<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSR_Comments_List {

	// private $respondId = 'custom_respond';

	public function move_recipe_comments_form() {
		if ( is_singular( 'recipe' ) ) {
			// $url = $_SERVER["REQUEST_URI"];
			// $is_comment_reply = strpos($url, 'replytocom');
			// if ( ! $is_comment_reply )
			remove_action( 'genesis_comment_form', 'genesis_do_comment_form' );
			// add_action( 'genesis_list_comments', 'genesis_do_comment_form' , 5 );
			add_action( 'genesis_before_comments', 'genesis_do_comment_form' );
		}
	}

	// public function comment_form_wrap_begin() {
	// 	echo '<div id="' . $this->respondId . '">';
	// }

	// public function comment_form_wrap_end() {
	// 	echo '</div>';
	// }


	/* ****************************************************************
											CUSTOM COMMENTS LIST
	****************************************************************/

	public function custom_comment_text() {
		$title = __('Comments','genesis');
		return ('<h3>' . $title . '</h3>');
	}


	public function custom_comments_prev_link_text() {
		$text = __('Previous comments','foodiepro');
		return $text;
	}

	public function custom_comments_next_link_text() {
		$text = __('Next comments','foodiepro');
		return $text;
	}

	public function add_comments_title_markup($html) {
		$html .= '<a id="comments-section"></a>';
		return $html;
	}


	/* Remove the genesis_default_list_comments function
		 Replace comment list with one including ratings
	-------------------------------------------------------*/
	public function custom_genesis_list_comments() {
		if ( is_singular( CSR_ASsets::post_types() ) ) {
			remove_action( 'genesis_list_comments', 'genesis_default_list_comments' );
			add_action( 'genesis_list_comments', array($this,'custom_star_rating_list_comments') );
		}
	}


	public function custom_star_rating_list_comments() {
		$args = array(
	    'type'          		=> 'comment',
	    'avatar_size'   		=> 50,
			'callback'      		=> array($this,'custom_star_rating_comment_list'),
			// 'style'							=> 'ul',
			// 'reverse_children'	=> false
	    //'per_page' 			=> '2',
		);
		$args = apply_filters( 'genesis_comment_list_args', $args );
		wp_list_comments( $args );
	}


	/* Custom Comment Template */
	public function custom_star_rating_comment_list($comment, $args, $depth) {

		  $GLOBALS['comment'] = $comment;
		  ?>
		  <li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
			<div class="comment-item">
		    <?php do_action( 'genesis_before_comment' ); ?>

		  	<div class="comment-intro">
		        <div class="comment-author">
		        	<div class="comment-avatar">
								<!-- <?php //echo get_avatar($comment,$size='48'); ?> -->
							<?php
								echo PeepsoHelpers::get_comment_author_avatar($comment);
							?>
		        	</div>
		            <?php
		            if ( $depth=='1' )
		            	printf(__('%s says:', 'custom-star-rating'), PeepsoHelpers::get_comment_author_link($comment));
		            else
		            	printf(__('%s responds:', 'custom-star-rating'), PeepsoHelpers::get_comment_author_link($comment)); ?>
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
		  		<?= do_shortcode('[display-star-rating category="all" source="comment"]');?>
				</div>

				<div class="comment-content">
		    <?php comment_text() ?>
				</div>

		    <div class="comment-reply">
						<?php
						$args = array_merge( $args, array(
							'depth' 			=> $depth,
							'max_depth' 	=> $args['max_depth'],
							// 'respond_id'	=> $this->respondId,
						));
						comment_reply_link($args);
						?>
		    </div>

				<div class="comment-edit">
		    	<?php edit_comment_link(__('(Edit)'),'  ','') ?>
				</div>

				<?php do_action( 'genesis_after_comment' );?>

		  </div>
		  <!-- Pas </li> pour commentaires imbriqu�s -->

	<?php
	}


	/* Change the comment reply link to display our own comment form */
	//add_filter('comment_reply_link', 'remove_nofollow', 420, 4);
	public function remove_nofollow($link, $args, $comment, $post){
	  return str_replace("rel='nofollow'", "", $link);
	}



}
