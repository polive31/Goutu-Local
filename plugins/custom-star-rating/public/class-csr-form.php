<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class CSR_Form
{


	/* Rating Form display
	------------------------------------------------------------ */
	public static function add_rating_form_before_comment($defaults)
	{
		if (!in_array(get_post_type(),CSR_Assets::RATED_POST_TYPES)) return;

		static $instance=0;

		wp_enqueue_style('custom-star-rating-form');
		$defaults['comment_field'] = self::output_eval_form_input_fields() . $defaults['comment_field'];

		$instance++;
		return $defaults;
	}


	public static function output_eval_form_input_fields()
	{

		ob_start(); ?>

		<div>
			<table class="ratings-table">
				<?php
				foreach (CSR_Assets::rating_cats() as $id => $cat) { ?>

					<tr class="rating-row">
						<td align="left">
							<label class="rating-cat" for="rating-input">
								<?= $cat['question']; ?>
							</label>
							<div class="rating-stars">
								<?= self::get_category_rating($id); ?>
							</div>
						</td>
					</tr>

				<?php
				} ?>
			</table>
		</div>

		<!-- <div class="comment-reply">
			<label for="comment"><?= __('Provide details', 'foodiepro'); ?></label>
			<textarea id="comment" name="comment" cols="50" rows="4" aria-required="true"></textarea>
		</div> -->

	<?php
		$fields = ob_get_contents();
		ob_end_clean();

		return $fields;

	}

	public static function get_category_rating($id)
	{
		$html = '<div class="rating-wrapper" id="star-rating-form">';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-5" name="rating-' . $id . '" value="5"/>';
		$html .= '<label for="rating-input-' . $id . '-5" class="rating-star" title="' . CSR_Assets::get_rating_caption(5, $id) . '"></label>';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
		$html .= '<label for="rating-input-' . $id . '-4" class="rating-star" title="' . CSR_Assets::get_rating_caption(4, $id) . '"></label>';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
		$html .= '<label for="rating-input-' . $id . '-3" class="rating-star" title="' . CSR_Assets::get_rating_caption(3, $id) . '"></label>';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
		$html .= '<label for="rating-input-' . $id . '-2" class="rating-star" title="' . CSR_Assets::get_rating_caption(2, $id) . '"></label>';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
		$html .= '<label for="rating-input-' . $id . '-1" class="rating-star" title="' . CSR_Assets::get_rating_caption(1, $id) . '"></label>';
		$html .= '</div>';

		return $html;
	}


}
