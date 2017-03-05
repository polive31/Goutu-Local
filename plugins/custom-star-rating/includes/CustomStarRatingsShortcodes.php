<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatingsShortcodes extends CustomStarRatings {
	
	public function __construct() {
		parent::__construct();
		add_shortcode( 'comment-rating-form', array($this,'display_comment_form_with_rating') );
		add_shortcode( 'display-star-rating', array($this,'display_star_rating_shortcode') );
	}

	/* Comment form with rating input shortcode
	-----------------------------------------------*/
	public function display_comment_form_with_rating() {
		$args = array (
			'title_reply' => '', //Default: __( 'Leave a Reply� )
			'label_submit' => __( 'Send', 'custom-star-rating' ), //default=�Post Comment�
			'comment_field' => $this->output_evaluation_form(), 
			'logged_in_as' => '', //Default: __( 'Leave a Reply to %s� )
			'title_reply_to' => __( 'Reply Title', 'custom-star-rating' ), //Default: __( 'Leave a Reply to %s� )
			'cancel_reply_link' => __( 'Cancel', 'custom-star-rating' ), //Default: __( �Cancel reply� )
			'rating_cats' => 'all',  //Default: "id1 id2..."
			);
		
	  ob_start();
	  
	  //display_rating_form();
	  comment_form($args);
	  
	  $cr_form = ob_get_contents();
	  ob_end_clean();
	  
	  return $cr_form;
	}


	/* Output post rating shortcode 
	---------------------------------------------*/
	public function display_star_rating_shortcode($atts) {
		$a = shortcode_atts( array(
			'source' => 'post', //comment
			'type' => 'stars', //full
			'rating_cats' => 'all',  //Default: "id1 id2..."
		), $atts );

		
		$full_display=!($a['type']=='stars');
		$comment_rating = ( $a['source'] == 'comment');
		
		if ( $comment_rating ) {
			//$this->dbg('In COMMENT display-star-rating shortcode','');
			$comment_id = get_comment_ID();
			foreach ($this->ratingCats as $id=>$cat) {
				$rating[$id] = get_comment_meta($comment_id, 'user_rating_' . $cat['id'], true);
			}
		}
		
		else { // Rating in post meta
			//$this->dbg('In POST display-star-rating shortcode','');
			$post_id = get_the_id();
			if ($full_display) { // displays number of votes
				$ratings = get_post_meta( $post_id , 'user_ratings' );
				//$this->dbg('In POST display-star-rating shortcode','');
				//$this->dbg('$ratings: ',$ratings);
				foreach ($this->ratingCats as $id=>$cat) {
					$cat_ratings = array_column($ratings, $cat['id']);
					if (isset ( $cat_ratings) ) {
						$stats = $this->get_rating_stats( $cat_ratings );
						//$this->dbg(' Stats for this category : ', $stats );	
						
						$rating[$id] = $stats['rating'];
						$votes[$id] = $stats['votes'];
					}
				}	
			}
			else { // displays only stars
				foreach ($this->ratingCats as $id=>$cat) {
					$rating[$id] = get_post_meta( $post_id , 'user_rating_' . $cat['id'], true );
				}
			}	
		}


		ob_start();
	
		?>
		<table class="ratings-table">
		<?php
		foreach ($this->ratingCats as $id=>$cat) {
			$rating[$id]=empty($rating[$id])?0:$rating[$id];
			$stars = floor($rating[$id]);
			$half = ($rating[$id]-$stars) >= 0.5;
			?>
			<tr>
			<?php
			if ( ! ( $comment_rating && $rating[$id]==0 ) ) { // Don't show empty ratings in comments 	
				?> 
				<td class="rating-category"><?php echo __($cat['title'], 'custom-star-rating')?></td>
				<td class="rating" title="<?php echo $rating[$id]?> : <?php echo $this->rating_caption($rating[$id])?>">
				<?php echo $this->output_stars($stars, $half)?>
				</td>
			<?php
			}
			if ( $full_display && !empty( $votes[$id] ) ) {
				$rating_plural=sprintf(_n('%s review','%s reviews',$votes[$id],'custom-star-rating'), $votes[$id]); ?>
				<td class="rating-details">(<?php echo $rating_plural ?>)</td> 
			<?php 
			}?>
			</tr>
			<?php	
		}?>
		</table>
		<?php 
			//else {
				//echo '<div class="rating-details">' . __('Be the first to rate this recipe !','custom-star-rating') . '</div>';
			//}

		$html = ob_get_contents();
	  ob_end_clean();	

		return $html;
	}


	/* Custom Comment Form 
	------------------------------------------------------------ */
	public function output_evaluation_form() {
		
		ob_start();?>
		
		<table class="ratings-table">
			
		<?php
		foreach ($this->ratingCats as $id => $cat) {?>
		
		<tr>
		<td class="rating-title"><?php echo __($cat['question'],'custom-star-rating');?></td>
		<td align="left"><?php echo $this -> output_rating_form( $id );?></td>
		</tr>
		
		<?php
		}?>	
		
		</table>
		
		<div class="comment-reply">
		<label for="comment"><?php echo _x( 'Comment', 'noun' );?></label>
		<textarea id="comment" name="comment" cols="50" rows="6" aria-required="true"></textarea>
		</div>

	<?php
		$rating_form = ob_get_contents();
		ob_end_clean();
		
		return $rating_form;

	}

	public function output_rating_form( $id ) {
		
		$html= '<div class="rating-wrapper" id="star-rating-form">';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-5" name="rating-' . $id . '" value="5"/>';
		$html.='<label for="rating-input-' . $id . '-5" class="rating-star" title="' . $this->rating_caption(5) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
		$html.='<label for="rating-input-' . $id . '-4" class="rating-star" title="' . $this->rating_caption(4) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
		$html.='<label for="rating-input-' . $id . '-3" class="rating-star" title="' . $this->rating_caption(3) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
		$html.='<label for="rating-input-' . $id . '-2" class="rating-star" title="' . $this->rating_caption(2) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
		$html.='<label for="rating-input-' . $id . '-1" class="rating-star" title="' . $this->rating_caption(1) . '"></label>';
		$html.='</div>';
	  
	  return $html;
		
	}


}


?>









