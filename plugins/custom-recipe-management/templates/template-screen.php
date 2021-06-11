<!-- Class .wpurp-container important for adjustable servings javascript -->
<div class="recipe wpurp-container tooltips-container" id="wpurp-container-recipe-<?php echo $recipe->ID(); ?>" data-id="<?php echo $recipe->ID(); ?>" data-permalink="<?php echo $recipe->link(); ?>" data-servings-original="<?php echo $recipe->servings_normalized(); ?>">


	<?php
	$args = array('recipe' => $recipe);
	CRM_Assets::echo_template_part('toolbar', false, $args);
	?>


	<?php
	$args = array('recipe' => $recipe);
	CRM_Assets::echo_template_part('screen', 'image', $args);
	?>

	<!-- Recipe description -->
	<div class="recipe-container" id="intro">
		<?= do_shortcode($recipe->post_content()); ?>
	</div>

	<!-- Recipe description -->
	<?php
	$video = $recipe->video();
	if ($video) {
	?>
		<div class="recipe-container" id="video">
			<?= wp_oembed_get($video, 560, 315); ?>
		</div>
	<?php
	}
	?>


	<!-- Ingredients + Instructions -->
	<div class="recipe-container" id="main">

		<ul class="desktop-nodisplay menu-bar">
			<li class="menu-tab selected" id="ingredients_menu_tab" data-target="ingredients_container">
				<div><?= __('Ingredients', 'foodiepro') . sprintf( ' (%s)', $recipe->ingredient_count() ); ?></div>
			</li>
			<li class="menu-tab" id="instructions_menu_tab" data-target="instructions_container">
				<div><?= __('Instructions', 'foodiepro'); ?></div>
			</li>
		</ul>

		<div class="ingredients-container" id="ingredients_container">
			<?php
			// Servings
			$terms = $recipe->servings_normalized();
			if ($terms != '') {
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
					<?php
					// $author = get_the_author();
					// if (('ratatouille'==$author) && (get_post_status()=='draft')) {
						?>
						<div class="button incart" id="ingredient_share_button">
							<?= foodiepro_get_icon('in-cart','icon', '', ''); ?>
							<span><?= __('Add to shopping list', 'crm') ?></span>
						</div>
						<textarea style="position:absolute;left:-9999px;top:0" id="shopping_list_copy_buffer"></textarea>
						<?php
					// }
					?>
				</div>
			<?php
			}

			$target = 'screen';
			$ingredients = $recipe->ingredients();
			$ratio = 1;
			$args = compact('ingredients', 'target', 'ratio');
			CRM_Assets::echo_template_part('ingredients', false, $args);

			?>
		</div>

		<?php
		$target = 'screen';
		$instructions = $recipe->instructions();
		$args = compact('instructions', 'target');
		CRM_Assets::echo_template_part('instructions', false, $args);
		?>
	</div>

	<?php

	if (!empty($recipe->notes())) {
	?>
		<div class="recipe-container" id="general">
			<h3> <?= __('Notes', 'crm'); ?> </h3>
			<div class="label-container"><?= do_shortcode($recipe->notes()); ?></div>
		</div>
	<?php }
	?>

</div>
