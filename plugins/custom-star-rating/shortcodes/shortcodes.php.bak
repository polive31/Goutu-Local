<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Comment form with rating input shortcode
-----------------------------------------------*/
add_shortcode( 'comment-rating-form', 'display_comment_form_with_rating' );
function display_comment_form_with_rating() {
	$args = array (
		'title_reply' => __( '', '' ), //Default: __( 'Leave a Reply’ )
		'label_submit' => __( 'Send', 'custom-star-rating' ), //default=’Post Comment’
		'comment_field' => output_evaluation_form_html_php(), 
		'logged_in_as' => '', //Default: __( 'Leave a Reply to %s’ )
		'title_reply_to' => __( 'Reply Title', 'custom-star-rating' ), //Default: __( 'Leave a Reply to %s’ )
		'cancel_reply_link' => __( 'Cancel', 'custom-star-rating' ) //Default: __( ‘Cancel reply’ )
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
add_shortcode( 'display-star-rating', 'display_star_rating_shortcode' );
function display_star_rating_shortcode($atts) {
	$a = shortcode_atts( array(
		'source' => 'post', //comment
		'type' => 'stars', //full
	), $atts );

	//PC::debug('In display-star-rating shortcode');
	$full_display=!($a['type']=='stars');
	$comment_rating = ( $a['source'] == 'comment');
	
	if ( $comment_rating ) {
		$id = get_comment_ID();
		//PC::debug( array('get comment ID'=>$id,) );
		$rating = get_comment_meta($id, 'user_rating', true);
		//PC::debug( array('rating from comment'=>$rating,) );
		$rating = $rating==''?'0':$rating;
		$stars = $rating;
		$half = false;
	}
	
	else { // Rating in post meta
		$id = get_the_id();
		if ($full_display) {
			$ratings = get_post_meta( $id , 'user_ratings', true );
			$stats = get_rating_stats( $ratings );
			$rating = $stats['rating'];
			$votes = $stats['votes'];
		}
		else {
			$rating = get_post_meta( $id , 'user_rating', true );
		}	
		//PC::debug(array('$rating from shortcode : '=>$rating));
		$stars = floor($rating);
		$half = ($rating-$stars) >= 0.5;
	}

	//PC:debug(array('votes : '=>$votes,'rating : '=>$rating,'stars : '=>$stars,'half : '=>$half,));	

	if ( ! ( $comment_rating && empty( $rating ) ) ) {
		$html = '<span class="rating" title="' . $rating . ' : ' . rating_caption($rating) . '">';
		$html .= output_stars($stars, $half);
		$html .= '</span>';
	}

	if ( $full_display ) {
		$rating_plural=$votes==1?__('review','foodiepro'):__('reviews','foodiepro'); 
		$html .= '<span class="rating-details">(' . $votes . ' ' . $rating_plural . ')</span>'; //. ' | ' . __('Rate this recipe','foodiepro') . 
	}
		//else {
			//echo '<div class="rating-details">' . __('Be the first to rate this recipe !','foodiepro') . '</div>';
		//}

	return $html;
}

function output_stars($stars, $half) {
	$html = '';
	for ($i = 1; $i <= $stars; $i++) {
		$half = $half&&($i==$stars)?'-half':'';
		$html .= '<i class="fa fa-star' . $half . '"></i>';
	}
	for ($i = $stars+1; $i <= 5; $i++) {
		$html .= '<i class="fa fa-star-o"></i>';
	}
	return $html;
}

?>