<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Custom Comment Form with PHP (called from shortcodes.php)
------------------------------------------------------------ */
function output_evaluation_form_html_php() {

	ob_start();?>
	
	<table class="ratings-table">
	<tr>
	<td class="rating-title"><?php echo __('How did you like this dish ?','custom-star-rating');?></td>
	<td align="left"><?php echo output_rating_form( '1' );?></td>
	</tr>
	<tr>
	<td class="rating-title"><?php echo __('How clear was the recipe ?','custom-star-rating');?></td>
	<td align="left"><?php echo output_rating_form( '2' );?></td>
	</tr>
	</table>
	
	<div class="comment-reply">
	<label for="comment"><?php echo _x( 'Comment', 'noun' );?></label>
	<textarea id="comment" name="comment" cols="40" rows="8" aria-required="true"></textarea>
	</div>

<?php
	$rating_form = ob_get_contents();
	ob_end_clean();
	
	return $rating_form;

}

function output_rating_form( $id ) {
	
	$html= '<div class="rating-wrapper" id="star-rating-form">';
	$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-5" name="rating-' . $id . '" value="5"/>';
	$html.='<label for="rating-input-' . $id . '-5" class="rating-star" title="' . rating_caption(5) . '"></label>';
	$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
	$html.='<label for="rating-input-' . $id . '-4" class="rating-star" title="' . rating_caption(4) . '"></label>';
	$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
	$html.='<label for="rating-input-' . $id . '-3" class="rating-star" title="' . rating_caption(3) . '"></label>';
	$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
	$html.='<label for="rating-input-' . $id . '-2" class="rating-star" title="' . rating_caption(2) . '"></label>';
	$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
	$html.='<label for="rating-input-' . $id . '-1" class="rating-star" title="' . rating_caption(1) . '"></label>';
	$html.='</div>';
  
  return $html;
	
}

function output_rating_form2($id, $title) {
  ob_start();?>
	
	<div class="rating-wrapper" id="star-rating-form">
	<input type="radio" class="rating-input" id="rating-input-<?php echo $id;?>-5" name="rating-<?php echo $id;?>" value="5"/>
	<label for="rating-input-<?php echo $id;?>-5" class="rating-star" title="<?php echo rating_caption(5);?>"></label>
	<input type="radio" class="rating-input" id="rating-input-<?php echo $id;?>-4" name="rating-<?php echo $id;?>" value="4"/>
	<label for="rating-input-<?php echo $id;?>-4" class="rating-star" title="<?php echo rating_caption(4);?>"></label>
	<input type="radio" class="rating-input" id="rating-input-<?php echo $id;?>-3" name="rating-<?php echo $id;?>" value="3"/>
	<label for="rating-input-<?php echo $id;?>-3" class="rating-star" title="<?php echo rating_caption(3);?>"></label>
	<input type="radio" class="rating-input" id="rating-input-<?php echo $id;?>-2" name="rating-<?php echo $id;?>" value="2"/>
	<label for="rating-input-<?php echo $id;?>-2" class="rating-star" title="<?php echo rating_caption(2);?>"></label>
	<input type="radio" class="rating-input" id="rating-input-<?php echo $id;?>-1" name="rating-<?php echo $id;?>" value="1"/>
	<label for="rating-input-<?php echo $id;?>-1" class="rating-star" title="<?php echo rating_caption(1);?>"></label>
	</div>

	<?php
  $html = ob_get_contents();
  ob_end_clean();
  
  return $html;
	
}



?>