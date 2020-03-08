		<!-- Class .wpurp-container important for adjustable servings javascript -->
		<div class="recipe wpurp-container tooltips-container" id="wpurp-container-recipe-<?php echo $recipe->ID(); ?>" data-id="<?php echo $recipe->ID(); ?>" data-permalink="<?php echo $recipe->link(); ?>" data-servings-original="<?php echo $recipe->servings_normalized(); ?>">

			<?php
			do_action('wpurp_in_container');
			?>

			<!-- Recipe description -->
			<div class="recipe-container" id="intro">
				<?php echo $recipe->output_description(); ?>
			</div>


			<?php
			include( __DIR__ . '/recipe-toolbar.php');
			?>

			<!-- Image + recipe info -->
			<div class="recipe-container" id="image">

				<div class="image-container">
					<div class="clearfix">
						<?php //discarded $recipe->featured_image_url('vertical-thumbnail'); and used get_the_post_thumbnail_url native wordpress method instead
						?>
						<a href="<?php echo get_the_post_thumbnail_url($this->post_ID, 'full'); ?>" id="lightbox">
							<img src="<?php echo get_the_post_thumbnail_url($this->post_ID, 'vertical-thumbnail'); ?>" alt="<?php echo $imgAlt; ?>">
						</a>
					</div>
				</div>

				<div class="info-container">

					<div class="label-container">
						<?php $tooltip_id = is_user_logged_in() ? 'recipe_rating_form' : 'join_us'; ?>
						<a href="#" data-tooltip-id="<?= $tooltip_id; ?>" class="tooltip-onclick" onClick="">
							<?php echo do_shortcode('[display-star-rating display="full"]'); ?>
						</a>
					</div>

					<?php
					// Origin
					$terms = get_the_term_list($this->post_ID, 'cuisine', '', ', ', '');
					if ($terms != '') {
						$html = '<div class="label-container tag"><div class="recipe-label">' . foodiepro_get_icon('tag') . __('Origin', 'crm') . '</div>' . $terms . '</div>';
						echo $html;
					}

					// Diet
					$terms = get_the_term_list($this->post_ID, 'diet', '', ', ', '');
					if ($terms != '') {
						$html = '<div class="label-container tag"><div class="recipe-label">' . foodiepro_get_icon('tag') . __('Diet', 'crm') . '</div>' . $terms . '</div>';
						echo $html;
					}

					// Difficulty
					$terms = get_the_term_list($this->post_ID, 'difficult', '', '', '');
					if ($terms != '') {
						$html = '<div class="label-container tag"><div class="recipe-label">' . foodiepro_get_icon('tag') . __('Level', 'crm') . '</div>' . $terms . '</div>';
						echo $html;
					}

					// Durations
					$prep_time = $recipe->output_time('prep');
					if ($prep_time) {?>
						<div class="label-container prep-time">
							<div class="recipe-label">
								<?= foodiepro_get_icon('hand') . $recipe->get_title('prep'); ?>
							</div>
								<?= $prep_time; ?>
						</div>
					<?php }

					$cook_time = $recipe->output_time('cook');
					if ($cook_time) {?>
						<div class="label-container cook-time">
							<div class="recipe-label">
								<?= foodiepro_get_icon('hourglass-half') . $recipe->get_title('cook'); ?>
							</div>
								<?= $cook_time; ?>
						</div>
					<?php }

					$wait_time = $recipe->output_time('passive');
					if ($wait_time) {?>
						<div class="label-container passive-time">
							<div class="recipe-label">
								<?= foodiepro_get_icon('pause') . $recipe->get_title('passive'); ?>
							</div>
								<?= $wait_time; ?>
						</div>
					<?php }

					?>
				</div>

			</div>

			<!-- Gallerie -->
			<div class="recipe-container" id="gallery">
				<div class="clearfix">
					[custom-gallery size="mini-thumbnail" columns="4" gallery-id="joined-pics"]
				</div>
			</div>

			<!-- Ingredients + Instructions -->
			<div class="recipe-container" id="main">

				<div class="ingredients-container">
					<?php
					// Servings
					$terms = $recipe->servings_normalized();
					if ($terms != '') {
						$html = '';
						ob_start();
						?>
						<div class="servings-input" id="servings">
							<span class=""><?= __('For ', 'crm'); ?></span>
							<table class="recipe-input">
								<tr>
									<td class="fa qty" id="dec" title="<?= __('Decrease servings', 'crm'); ?>">
										<?= foodiepro_get_icon('minus-circle', 'servings-button'); ?>
									</td>
									<td class="input">
										<input type="number" min="1" class="adjust-recipe-servings" data-original="<?= $recipe->servings_normalized(); ?>" data-start-servings="<?= $recipe->servings_normalized(); ?>" value="<?= $recipe->servings_normalized(); ?>" />
									</td>
									<td class="fa qty" id="inc" title="<?= __('Increase servings', 'crm'); ?>">
										<?= foodiepro_get_icon('plus-circle', 'servings-button'); ?>
									</td>
								</tr>
							</table>
							<span><?php echo $recipe->servings_type(); ?></span>
						</div>
					<?php
						$html .= ob_get_contents();
						ob_end_clean();
						echo $html;
					}
					// Method "with custom function"
					echo $this->custom_ingredients_list($recipe);
					?>
				</div>

				<?php
				echo $this->custom_instructions_list($recipe);
				?>
			</div>

			<?php

			if (!empty($recipe->notes())) {
				?>
				<div class="recipe-container" id="general">
					<h3> <?= __('Notes', 'crm'); ?> </h3>
					<div class="label-container"><?= $recipe->notes() ?></div>
				</div>
			<?php }
			?>

		</div>
