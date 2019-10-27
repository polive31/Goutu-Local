		<!-- Class .wpurp-container important for adjustable servings javascript -->
		<div class="recipe wpurp-container tooltips-container" id="wpurp-container-recipe-<?php echo $recipe->ID(); ?>" data-id="<?php echo $recipe->ID(); ?>" data-permalink="<?php echo $recipe->link(); ?>" data-servings-original="<?php echo $recipe->servings_normalized(); ?>">

			<?php
			do_action('wpurp_in_container');
			?>

			<!-- Recipe description -->
			<div class="recipe-container" id="intro">
				<?php echo $recipe->output_description(); ?>
			</div>

			<div class="share-buttons">
				<?= do_shortcode('[social-sharing-buttons target="recipe" class="small bubble"]'); ?>
			</div>

			<!-- Function buttons  -->
			<div class="recipe-top">
				<div class="recipe-buttons">

					<!-- Recipe Rate Button -->
					<!-- <div class="recipe-button alignleft <?php //echo is_user_logged_in()?'tooltip-onhover':'disabled';
																?>" id="rate"> -->
					<div class="recipe-button alignleft tooltip-onhover" id="rate">
						<?php
						// $ga = WP_MINIFY?is_user_logged_in()?'':"ga('send','event','join-us','click','recipe-rate', 0)":'';
						// echo $ga;
						?>
						<!-- <a href="<?php //echo is_user_logged_in()?'#':'/connexion';
										?>" class="recipe-review-button tooltip-onclick" data-tooltip-id="<?php //echo is_user_logged_in()?'':'join_us';
																											?>" onClick="<?php //echo $ga
																															?>"> -->
						<a href="#" data-tooltip-id="" class="recipe-review-button tooltip-onclick" onClick="">
							<div class="button-caption"><?php echo __('Rate', 'foodiepro'); ?></div>
						</a>
						<?php
						// if( is_user_logged_in() ) {
						$args = array(
							'content' => __('Comment and rate this recipe', 'foodiepro'),
							'valign' 	=> 'above',
							'halign'	=> 'left',
						);
						Tooltip::display($args);
						$args = array(
							'content' => do_shortcode('[comment-rating-form]'),
							'id'			=> 'recipe_rating_form',
							'valign' 	=> 'above',
							'halign'	=> 'left',
							'action'	=> 'click',
							'callout'	=> false,
							'class'		=> 'rating-form modal',
							'title'		=> __('Rate this recipe', 'foodiepro'),
							'img'		=> CHILD_THEME_URL . '/images/popup-icons/fiche_recette.png'
						);
						Tooltip::display($args);
						// }
						?>
					</div>

					<!-- Recipe Add to Cart Button -->
					<!-- 				<div class="recipe-button alignleft tooltip tooltip-above tooltip-left" id="shopping">
					<?php
					// $shopping_list = new Custom_Recipe_Add_To_Shopping_List( is_user_logged_in() );
					// echo $shopping_list->output( $recipe );
					?>
					</div>	 -->

					<!-- Add To Favorites Button -->
					<div class="recipe-button alignleft <?php echo is_user_logged_in() ? 'tooltip-onhover' : 'disabled'; ?>" id="favorite">
						<?php
						$favorite_recipe = new CRM_Favorite();
						echo $favorite_recipe->output($recipe); ?>
					</div>

					<!-- Like Button -->
					<div class="recipe-button alignleft tooltip-onhover" id="like">
						<?php
						$recipe_like = new Custom_Social_Like_Post('recipe');
						$recipe_like->display();
						?>
					</div>

					<!-- Recipe Print Button -->
					<div class="recipe-button alignright tooltip-onhover" id="print">
						<a class="wpurp-recipe-print recipe-print-button" href="<?php echo $recipe->link_print(); ?>" target="_blank">
							<div class="button-caption"><?php echo __('Print', 'foodiepro'); ?></div>
						</a>
						<?php
						$args = array(
							'content' 	=> __('Print this Recipe', 'foodiepro'),
							'valign' 	=> 'above',
							'halign'	=> 'right',
						);
						Tooltip::display($args);
						?>
					</div>

					<!-- Recipe Share Button -->
					<!-- <div class="recipe-button alignright tooltip" id="share">
						<a class="recipe-share-button" id="recipe-share" cursor-style="pointer">
							<div class="button-caption"><?php echo __('Share', 'foodiepro'); ?></div>
						</a>
						<?php //echo Custom_WPURP_Templates::output_tooltip(__('Share this recipe','foodiepro'),'above');
						$args = array(
							// 'content' 	=>  do_shortcode('[social-sharing-buttons target="recipe" class="small bubble"]'),
							'valign' 	=> 'above',
							'halign'	=> 'left',
							'class'		=> 'transparent large'
						);
						Tooltip::display($args);
						?>
					</div>				 -->

					<!-- Recipe Read Button -->
					<div class="recipe-button alignright tooltip-onhover" id="read">
						<a class="recipe-read-button" onClick="<?= is_user_logged_in() ? '' : "ga('send','event','recipe-read','click','', 0)"; ?>" />
						<div class="button-caption"><?php echo __('Read', 'foodiepro'); ?></div>
						</a>
						<?php
						$args = array(
							'content' 	=>  __('Read this recipe out loud', 'foodiepro'),
							'valign' 	=> 'above',
							'halign'	=> 'center',
						);
						Tooltip::display($args);
						?>
					</div>

				</div>

				<?php

				?>


			</div>

			<!-- Image + recipe info -->
			<div class="recipe-container" id="image">

				<div class="image-container">
					<div class="clearfix">
						<?php //discarded $recipe->featured_image_url('vertical-thumbnail'); and used get_the_post_thumbnail_url native wordpress method instead
						?>
						<a href="<?php echo get_the_post_thumbnail_url($this->post_ID, 'full'); ?>" id="lightbox">
							<img src="<?php echo get_the_post_thumbnail_url($this->post_ID, 'horizontal-thumbnail'); ?>" alt="<?php echo $imgAlt; ?>">
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

			<div class="recipe-container" id="general">
				<?php
				$test = $recipe->notes();
				if ($test != '') {
					$html = '<h3>' . __('Notes', 'foodiepro') . '</h3>';
					$html .= '<div class="label-container">' . $test . '</div>';
					echo $html;
				}
				?>
			</div>

		</div>
