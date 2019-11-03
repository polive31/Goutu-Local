<div>
    <table class="ratings-table">
        <?php
        foreach (CSR_Assets::rating_cats() as $id => $cat) { ?>

            <tr>
                <td align="left" class="rating-title"><?= $cat['question']; ?></td>
                <td align="left"><?= $this->get_category_rating($id); ?></td>
            </tr>

        <?php
        } ?>
    </table>
</div>

<div class="comment-reply">
    <label for="comment"><?= __('Add a comment', 'custom-star-rating'); ?></label>
    <textarea id="comment" name="comment" cols="50" rows="4" aria-required="true"></textarea>
</div>

<?/**
 * .
 *
 * Description.
 *
 * @since Version 3 digits
 */
public function get_category_rating( $id ) {
    $html= '<div class="rating-wrapper" id="star-rating-form">';
    $html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-5" name="rating-' . $id . '" value="5"/>';
    $html.='<label for="rating-input-' . $id . '-5" class="rating-star" title="' . CSR_Assets::get_rating_caption(5, $id) . '"></label>';
    $html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
    $html.='<label for="rating-input-' . $id . '-4" class="rating-star" title="' . CSR_Assets::get_rating_caption(4, $id) . '"></label>';
    $html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
    $html.='<label for="rating-input-' . $id . '-3" class="rating-star" title="' . CSR_Assets::get_rating_caption(3, $id) . '"></label>';
    $html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
    $html.='<label for="rating-input-' . $id . '-2" class="rating-star" title="' . CSR_Assets::get_rating_caption(2, $id) . '"></label>';
    $html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
    $html.='<label for="rating-input-' . $id . '-1" class="rating-star" title="' . CSR_Assets::get_rating_caption(1, $id) . '"></label>';
    $html.='</div>';
    return $html;
}
