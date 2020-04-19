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

				if ($recipe->prep_time()) {
					echo '<td><span class="wpurp-title">' .  CPM_Assets::get_label('recipe', 'prep') . '</span></td>';
				}

				if ($recipe->cook_time()) {
					echo '<td><span class="wpurp-title">' .  CPM_Assets::get_label('recipe', 'cook') . '</span></td>';
				}

				if ($recipe->passive_time()) {
					echo '<td><span class="wpurp-title">' .  CPM_Assets::get_label('recipe', 'passive') . '</span></td>';
				}
				?>

			</tr>

			<tr>

				<td><span class="wpurp-title"><?= __('Serves', 'crm'); ?></span> : <?= $servings; ?> <?= $recipe->servings_type(); ?></td>

				<?php

				$time = $recipe->output_time('prep');
				if ($time) { ?>
					<td><span class="wpurp-title"><?= __('Preparation', 'crm'); ?></span> : <?= $time; ?></td>
				<?php }

				$time = $recipe->output_time('cook');
				if ($time) { ?>
					<td><span class="wpurp-title"><?= __('Cooking', 'crm'); ?></span> : <?= $time; ?></td>
				<?php }

				$time = $recipe->output_time('passive');
				if ($time) { ?>
					<td><span class="wpurp-title"><?= __('Wait', 'crm'); ?></span> : <?= $time; ?></td>
				<?php }

				?>

			</tr>
		</table>

		<span class="wpurp-title"><?= __('Ingredients', 'crm'); ?></span>

		<div class="recipe-container ingredients">
			<?php
			$target = 'print';
			$ingredients = $recipe->ingredients();
			$ratio = $servings / $recipe->servings_normalized();
			$args = compact('ingredients', 'target', 'ratio');
			CRM_Assets::echo_template_part('ingredients', false, $args);
			?>
		</div>


		<span class="wpurp-title"><?= __('Instructions', 'crm'); ?></span>
		<div class="recipe-container instructions">
			<?php
			$target = 'print';
			$instructions = $recipe->instructions();
			$args = compact('instructions', 'target');
			CRM_Assets::echo_template_part('instructions', false, $args);
			?>
		</div>

		<?php
		$notes = $recipe->notes();
		if ($notes != '') { ?>
			<span class="wpurp-title"><?= __('Notes', 'crm'); ?></span>
			<div class="recipe-container">
				<?= CRM_Output::stripout_images($notes); ?>
			<?php }
			?>
			</div>

	</div>

</body>
