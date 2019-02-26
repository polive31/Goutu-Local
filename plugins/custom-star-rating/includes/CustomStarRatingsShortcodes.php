<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatingsShortcodes extends CustomStarRatingsMeta {
	
	public function __construct() {
		parent::__construct();
		
		add_shortcode( 'json-ld-rating', 			array( $this, 'display_json_ld_rating') );
		add_shortcode( 'comment-rating-form', array( $this, 'display_comment_form_with_rating') );
		add_shortcode( 'display-star-rating', array( $this, 'display_star_rating_shortcode') );
		// add_shortcode( 'add-comment-form', array($this,'add_comment_form_shortcode') );
	}

	/* Add Comment Form 
	-----------------------------------------------*/
	public function add_comment_form_shortcode() {
	//		$comments_args = array( 
	//			'title_reply' => __( '', 'genesis' ), 
	//      'comment_field'=>'<p class="comment-form-comment"></p>', 
	//		);

		wp_enqueue_style('custom-star-rating');

		$comment_args='';
	    ob_start();
	    comment_form($comment_args);
	    $cform = ob_get_contents();
	    ob_end_clean();
	    return $cform;
	}


	/* Rating in string (not graphical) format for json encode
	-----------------------------------------------*/
	public function display_json_ld_rating($atts) {
		$a = shortcode_atts( array(
			'category' => 'global', //any rating category...
		), $atts );
		
		$post_id = get_the_id();
		
		$ratings = get_post_meta( $post_id , 'user_ratings' );
		$votes = count ($ratings);
			
		$rating = get_post_meta( $post_id , 'user_rating_' . $a['category'], true);	
			
//		$ratings_cat = array_column($ratings, $a['category']);
//		if ( isset($ratings_cat) )
//			$stats = $this->get_rating_stats( $ratings_cat );
		//$stats = implode(' ', $stats);
	
		$stats = $rating . ' ' . $votes;
		return $stats;
    
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
			
		wp_enqueue_style('custom-star-rating');
		ob_start();
		//display_rating_form();
		comment_form($args);
		$cr_form = ob_get_contents();
		ob_end_clean();
		return $cr_form;
	}

	
	/* Output star rating shortcode 
	---------------------------------------------*/
	public function display_star_rating_shortcode($atts) {
		$a = shortcode_atts( array(
			'source' => 'post', //comment
			'display' => 'normal', //minimal = only stars, normal = caption + stars, full = with votes
			'category' => 'all',  // which rating(s) is(are) to be displayed : "all", "global", "rating", "clarity"...
			'markup' => 'table',  // span, list...
		), $atts );
		
		wp_enqueue_style('custom-star-rating');

		$display_style = $a['display'];
		$comment_rating = ( $a['source'] == 'comment');
		
		if ($a['markup']=='table') {
			$otag = '<table class="ratings-table" id="rating">'; // main opening tag
			$ctag = '</table>'; // mai opening tag
			$rotag = '<tr>'; // row opening tag
			$rctag = '</tr>'; // row closing tag
			$cotag = '<td'; // cell opening tag
			$cctag = '</td>'; // cell closing tag
		}
		elseif ($a['markup']=='list') {
			$otag = '<ul class="ratings-table" id="rating">'; // main opening tag
			$ctag = '</ul>'; // mai opening tag
			$rotag = '<li>'; // row opening tag
			$rctag = '</li>'; // row closing tag
			$cotag = '<div'; // cell opening tag
			$cctag = '</div>'; // cell closing tag
		}		
		elseif ($a['markup']=='span') {
			$otag = '<span class="ratings-table" id="rating">'; // main opening tag
			$ctag = '</span>'; // mai opening tag
			$rotag = ''; // row opening tag
			$rctag = ''; // row closing tag
			$cotag = '<span'; // cell opening tag
			$cctag = '</span>'; // cell closing tag
		}

		//if (!$comment_rating) $this->dbg('In POST rating display shortcode','');
		//if ($comment_rating) $this->dbg('In COMMENT rating display shortcode','');

		// Setup categories to be displayed
		if ( $a['category']=='all' ) $display_cats=self::$ratingCats;	
		elseif ( $a['category']=='global' ) $display_cats=self::$ratingGlobal;				
		else {
			$shortcode_cats = explode(' ', $a['category']);
			foreach ($shortcode_cats as $key) {
				$display_cats[$key]=self::$ratingCats[$key];	
			}
		}

		// Setup ratings source
		if ( $comment_rating ) {
			$comment_id = get_comment_ID();
		}
		else { // Rating in post meta
			$post_id = get_the_id();
			if ($display_style == 'full') { // displays number of votes
				$ratings = get_post_meta( $post_id , 'user_ratings' );
			}
		}

		ob_start();
	
		?>
		<?php echo $otag; ?>
		<?php
		foreach ($display_cats as $id=>$cat) {
			
			if ( $comment_rating ) {
				$rating=$this->get_comment_rating($comment_id,$id);
			}
			elseif ($display_style == 'full') { // displays number of votes
				if ( $id=='global' ) {
					$rating = $this->get_post_rating( $post_id , 'global');
					$votes = count($ratings);
				}
				else {
					$stats=$this->get_post_stats($ratings,$id);
					$rating=$stats['rating'];
					$votes=$stats['votes'];
				}
			}
			else {
				$rating = $this->get_post_rating( $post_id , $id);
			}

			$rating=empty($rating)?0:(int)$rating;
			$stars = floor($rating);
			$half = ($rating-$stars) >= 0.5;
			?>
			<?= $rotag;?>
			<?php
			if ( ! ( $comment_rating && $rating==0 ) ) { // Don't show empty ratings in comments 	
				if ( $display_style!='minimal' ) {
				?>
				<?= $cotag;?> class="rating-category" title="<?php echo __($cat['legend'], 'custom-star-rating')?>"><?php echo __($cat['title'], 'custom-star-rating')?>
				<?= $cctag;?>
				<?php
				}?>
				<?= $cotag;?> class="rating" title="<?php echo $rating?> : <?php echo $this->rating_caption($rating,$id)?>">
				<a class="pum-trigger" id="recipe-review"><?php echo $this->output_stars($stars, $half)?></a>
				<?= $cctag;?>
			<?php
			}
			if ( $display_style=='full' && !empty( $votes ) ) {
				$rating_plural=sprintf(_n('%s review','%s reviews',$votes,'custom-star-rating'), $votes); ?>
				<?= $cotag;?> class="rating-details"><a href="#comments-section"><?php echo $rating_plural ?></a><?= $cctag;?> 
			<?php 
			}?>
			<?= $rctag;?>
			<?php	
		}?>
		<?= $ctag;?>
		<?php 
			//else {
				//echo '<div class="rating-details">' . __('Be the first to rate this recipe !','custom-star-rating') . '</div>';
			//}

		$html = ob_get_contents();
	  	ob_end_clean();	

		return $html;
	}
	
	
	/* Output stars
	-------------------------------------------------------------*/
	public function output_stars_simple($stars, $half) {
		$html = '';
		for ($i = 1; $i <= $stars; $i++) {
			$html .= '<i class="fa fa-star"></i>';
		}
		for ($i = $stars+1; $i <= 5; $i++) {
			if ( ($i == ($stars+1) ) && $half ) {
				$html .= '<i class="fa fa-star-half-o"></i>';
			}
			else {
				$html .= '<i class="fa fa-star-o"></i>';
			}
		}
		return $html;
	}
	
		/* Output stars stacked
	-------------------------------------------------------------*/
	public function output_stars_table($stars, $half) {
		$html = '';
		for ($i = 1; $i <= $stars; $i++) {
			$html .= '<span class="fa-stack full"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>';
		}
		for ($i = $stars+1; $i <= 5; $i++) {
			if ( ($i == ($stars+1) ) && $half ) {
				$html .= '<span class="fa-stack full"><i class="fa fa-star-half-o fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>';
			}
			else {
				$html .= '<span class="fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>';
			}
		}
		return $html;
	}
	
		/* Output stars div
	-------------------------------------------------------------*/
	public function output_stars($stars, $half) {
		$html = '';
		for ($i = 1; $i <= $stars; $i++) {
			$html .= '<div class="fa-stack full"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></div>';
		}
		for ($i = $stars+1; $i <= 5; $i++) {
			if ( ($i == ($stars+1) ) && $half ) {
				$html .= '<div class="fa-stack full"><i class="fa fa-star-half-o fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></div>';
			}
			else {
				$html .= '<div class="fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></div>';
			}
		}
		return $html;
	}
	
		/* Output stars FI
	-------------------------------------------------------------*/
	public function output_stars_png($stars, $half) {
		$html = '';
		for ($i = 1; $i <= $stars; $i++) {
			$html .= '<i class="fi fi-star fi-on"></i>';
		}
		for ($i = $stars+1; $i <= 5; $i++) {
			if ( ($i == ($stars+1) ) && $half ) {
				$html .= '<i class="fi"><i class="fi fi-star fi-half fi-on"></i><i class="fi fi-star fi-half fi-off"></i></i>';
			}
			else {
				$html .= '<i class="fi fi-star fi-off"></i>';
			}
		}
		return $html;
	}

	/* Custom Comment Form 
	------------------------------------------------------------ */
	public function output_evaluation_form() {
		
		ob_start();?>
		
		<div>
			<table class="ratings-table">			
			<?php
			foreach (self::$ratingCats as $id => $cat) {?>
	
			<tr>
				<td align="left" class="rating-title"><?php echo __($cat['question'],'custom-star-rating');?></td>
				<td align="left"><?php echo $this->output_rating_form( $id );?></td>
			</tr>
			
			<?php
			}?>	
			</table>
		</div>
		
		<div class="comment-reply">
		<label for="comment"><?php echo __('Add a comment','custom-star-rating' );?></label>
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
		$html.='<label for="rating-input-' . $id . '-5" class="rating-star" title="' . $this->rating_caption(5, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
		$html.='<label for="rating-input-' . $id . '-4" class="rating-star" title="' . $this->rating_caption(4, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
		$html.='<label for="rating-input-' . $id . '-3" class="rating-star" title="' . $this->rating_caption(3, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
		$html.='<label for="rating-input-' . $id . '-2" class="rating-star" title="' . $this->rating_caption(2, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
		$html.='<label for="rating-input-' . $id . '-1" class="rating-star" title="' . $this->rating_caption(1, $id) . '"></label>';
		$html.='</div>';
	  
	  return $html;
		
	}

}