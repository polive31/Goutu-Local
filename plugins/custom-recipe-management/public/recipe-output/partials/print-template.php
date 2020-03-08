<head>
	<title>Imprimer la recette</title>
	<link href="<?= $stylesheet_uri; ?>" rel="stylesheet" type="text/css">
	<link href="<?= $js_uri; ?>" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">
</head>

<body onload="window.print()">
	<div class="recipe wpurp-container">
		<!-- Class .wpurp-container important for adjustable servings javascript -->
		<span class="wpurp-recipe-title"><?= $recipe->title(); ?></span>

		<table class="wpurp-table">
			<tr>

				<?php


				if ($recipe->servings_normalized()) {
					echo '<td><span class="wpurp-title">' .  __('Serves', 'crm') . '</span></td>';
				}

				if ($recipe->prep_time()) {
					echo '<td><span class="wpurp-title">' .  __('Preparation', 'crm') . '</span></td>';
				}

				if ($recipe->cook_time()) {
					echo '<td><span class="wpurp-title">' .  __('Cooking', 'crm') . '</span></td>';
				}

				if ($recipe->passive_time()) {
					echo '<td><span class="wpurp-title">' .  __('Wait', 'crm') . '</span></td>';
				}
				?>

			</tr>

			<tr>

				<?php

				if ($recipe->servings_normalized()) { ?>
					<td><?= $recipe->servings_normalized(); ?> <?= $recipe->servings_type(); ?></td>
				<?php }

				$time = $recipe->output_time('prep');
				if ($time) { ?>
					<td><?= $time; ?></td>
				<?php }

				$time = $recipe->output_time('cook');
				if ($time) { ?>
					<td><?= $time; ?></td>
				<?php }

				$time = $recipe->output_time('passive');
				if ($time) { ?>
					<td><?= $time; ?></td>
				<?php }

				?>

			</tr>
		</table>

		<span class="wpurp-title"><?= __('Ingredients', 'crm'); ?></span>

		<div class="recipe-container ingredients">
			<?= $this->custom_ingredients_list($recipe, 'print');?>
		</div>


		<span class="wpurp-title"><?= __('Instructions', 'crm'); ?></span>
		<div class="recipe-container instructions">
			<?= $this->custom_instructions_list($recipe, 'print'); ?>
		</div>

		<?php
		$notes = $recipe->notes();
		if ($notes != '') { ?>
			<span class="wpurp-title"><?= __('Notes', 'crm'); ?></span>
			<div class="recipe-container">
				<?= $this->stripout_images($notes); ?>
			<?php }
			?>
			</div>

	</div>

</body>
