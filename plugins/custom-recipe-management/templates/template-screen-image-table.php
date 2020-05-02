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

		<div class="recipe-container" id="gallery">
			<div class="clearfix">
				<?php echo do_shortcode('[custom-gallery size="mini-thumbnail" columns="4" gallery-id="joined-pics"]');
				?>
			</div>
		</div>

		<div class="info-container">
			<table>

			<tr class="label-container" colspan="2">
				<?= (class_exists('CSR_Rating')) ? CSR_Rating::render('post') : ''; ?>
			</tr>

			<?php
			// Origin
			$terms = get_the_term_list($recipe->ID(), 'cuisine', '', ', ', '');
			if ($terms != '') { ?>
				<tr class="label-container tag">
					<td class="recipe-label"><?= foodiepro_get_icon('tag') . __('Origin', 'crm'); ?></td>
					<td class="data-container"><?= $terms; ?></td>
				</tr>
			<?php }

			// Diet
			// $terms = get_the_term_list($recipe->ID(), 'diet', '', ', ', '');
			$terms = get_the_term_list($recipe->ID(), 'diet', '', '<br> ', '');
			if ($terms != '') { ?>
				<tr class="label-container tag">
					<td class="recipe-label"><?= foodiepro_get_icon('tag') . __('Diet', 'crm'); ?></td>
					<td class="data-container"><?= $terms; ?></td>
				</tr>
			<?php }

			// Difficulty
			$terms = get_the_term_list($recipe->ID(), 'difficult', '', '', '');
			if ($terms != '') { ?>
			<tr class="label-container tag">
				<td class="recipe-label"><?= foodiepro_get_icon('tag') . __('Level', 'crm'); ?></td>
				<td class="data-container"><?= $terms; ?></td>
			</tr>
			<?php }

			// Durations
			$prep_time = $recipe->output_time('prep');
			if ($prep_time) { ?>
			<tr class="label-container prep-time">
				<td class="recipe-label">
					<?= foodiepro_get_icon('hand') . __('Preparation', 'crm'); ?>
				</td>
				<td class="data-container">
					<?= $prep_time; ?>
				</td>
			</tr>
			<?php }

			$cook_time = $recipe->output_time('cook');
			if ($cook_time) { ?>
			<tr class="label-container cook-time">
				<td class="recipe-label">
					<?= foodiepro_get_icon('hourglass-half') . __('Cooking', 'crm'); ?>
				</td>
				<td class="data-container">
					<?= $cook_time; ?>
				</td>
			</tr>
			<?php }

			$wait_time = $recipe->output_time('passive');
			if ($wait_time) { ?>
			<tr class="label-container passive-time">
				<td class="recipe-label">
					<?= foodiepro_get_icon('pause') . __('Wait', 'crm'); ?>
				</td>
				<td class="data-container">
					<?= $wait_time; ?>
				</td>
			</tr>
			<?php } ?>
			</table>
		</div>

	</div>
