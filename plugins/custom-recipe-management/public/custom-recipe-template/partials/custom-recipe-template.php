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
			include(self::$_PluginPath . 'custom-recipe-template/partials/custom-recipe-toolbar.php');
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
						$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Origin', 'foodiepro') . '</div>' . $terms . '</div>';
						echo $html;
					}

					// Diet
					$terms = get_the_term_list($this->post_ID, 'diet', '', ', ', '');
					if ($terms != '') {
						$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Diet', 'foodiepro') . '</div>' . $terms . '</div>';
						echo $html;
					}

					// Difficulty
					$terms = get_the_term_list($this->post_ID, 'difficult', '', '', '');
					if ($terms != '') {
						$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Level', 'foodiepro') . '</div>' . $terms . '</div>';
						echo $html;
					}

					// Durations
					echo $recipe->output_time('prep');
					echo $recipe->output_time('cook');
					echo $recipe->output_time('passive');

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
							<span class=""><?= __('For ', 'foodiepro'); ?></span>
							<table class="recipe-input">
								<tr>
									<td class="fa qty" id="dec" title="<?= __('Decrease servings', 'foodiepro'); ?>">&nbsp;</td>
									<td class="input">
										<input type="number" min="1" class="adjust-recipe-servings" data-original="<?= $recipe->servings_normalized(); ?>" data-start-servings="<?= $recipe->servings_normalized(); ?>" value="<?= $recipe->servings_normalized(); ?>" />
									</td>
									<td class="fa qty" id="inc" title="<?= __('Increase servings', 'foodiepro'); ?>">&nbsp;</td>
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
					echo $this->custom_ingredients_list($recipe, '');
					?>
				</div>

				<?php
				echo $this->custom_instructions_list($recipe, '');
				?>
			</div>

			<?php

			if (!empty($recipe->notes())) {
				?>
				<div class="recipe-container" id="general">
					<h3> <?= __('Notes', 'foodiepro'); ?> </h3>
					<div class="label-container"><?= $recipe->notes() ?></div>
				</div>
			<?php }
			?>

		</div>
