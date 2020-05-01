	<!-- Image + recipe info -->
	<div class="recipe-container" id="image">

		<div class="image-container">
			<div class="clearfix">
				<?php //discarded $recipe->featured_image_url('vertical-thumbnail'); and used get_the_post_thumbnail_url native wordpress method instead
				$imgID = $recipe->featured_image();
				$imgAlt = get_post_meta($imgID, '_wp_attachment_image_alt', true);
				if (empty($imgAlt)) $imgAlt = $recipe->title();
				$image_url = get_the_post_thumbnail_url($recipe->ID(), 'vertical-thumbnail');
				$image_url_full = get_the_post_thumbnail_url($recipe->ID(), 'full');
				if (empty($image_url)) {
					$image_url = CPM_Assets::get_fallback_img_url('recipe', '');
					$image_url_full = CPM_Assets::get_fallback_img_url('recipe', '');
				}
				?>
				<a href="<?= $image_url_full; ?>" id="lightbox">
					<img src="<?= $image_url; ?>" alt="<?php echo $imgAlt; ?>">
				</a>
			</div>
		</div>

		<div class="info-container">

			<div class="label-container">
				<?php //$tooltip_id = is_user_logged_in() ? 'recipe_rating_form' : 'join_us';?>
				<?= (class_exists('CSR_Rating')) ? CSR_Rating::render('post') : ''; ?>
			</div>

			<?php
			// Origin
			$terms = get_the_term_list($recipe->ID(), 'cuisine', '', ', ', '');
			if ($terms != '') {
				$html = '<div class="label-container tag"><div class="recipe-label">' . foodiepro_get_icon('tag') . __('Origin', 'crm') . '</div>';
				$html .= '<div class="data-container">' . $terms . '</div></div>';
				echo $html;
			}

			// Diet
			// $terms = get_the_term_list($recipe->ID(), 'diet', '', ', ', '');
			$terms = get_the_term_list($recipe->ID(), 'diet', '', '<br> ', '');
			if ($terms != '') {
				$html = '<div class="label-container tag"><div class="recipe-label">' . foodiepro_get_icon('tag') . __('Diet', 'crm') . '</div>';
				$html .= '<div class="data-container">' . $terms . '</div></div>';
				echo $html;
			}

			// Difficulty
			$terms = get_the_term_list($recipe->ID(), 'difficult', '', '', '');
			if ($terms != '') {
				$html = '<div class="label-container tag"><div class="recipe-label">' . foodiepro_get_icon('tag') . __('Level', 'crm') . '</div>';
				$html .= '<div class="data-container">' . $terms . '</div></div>';
				echo $html;
			}

			// Durations
			$prep_time = $recipe->output_time('prep');
			if ($prep_time) { ?>
				<div class="label-container prep-time">
					<div class="recipe-label">
						<?= foodiepro_get_icon('hand') . __('Preparation', 'crm'); ?>
					</div>
					<div class="data-container">
						<?= $prep_time; ?>
					</div>
				</div>
				<?php }

$cook_time = $recipe->output_time('cook');
if ($cook_time) { ?>
				<div class="label-container cook-time">
					<div class="recipe-label">
						<?= foodiepro_get_icon('hourglass-half') . __('Cooking', 'crm'); ?>
					</div>
					<div class="data-container">
						<?= $cook_time; ?>
					</div>
				</div>
				<?php }

$wait_time = $recipe->output_time('passive');
if ($wait_time) { ?>
				<div class="label-container passive-time">
					<div class="recipe-label">
						<?= foodiepro_get_icon('pause') . __('Wait', 'crm'); ?>
					</div>
					<div class="data-container">
						<?= $wait_time; ?>
					</div>
				</div>
			<?php }

			?>
		</div>

	</div>

	<!-- Gallerie -->
	<div class="recipe-container" id="gallery">
		<div class="clearfix">
			<?php echo do_shortcode('[custom-gallery size="mini-thumbnail" columns="4" gallery-id="joined-pics"]');
			?>
		</div>
	</div>
