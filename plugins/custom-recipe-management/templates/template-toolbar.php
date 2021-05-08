<!-- Recipe Toolbar  -->
<div class="recipe-top">
	<div class="toolbar-buttons">


		<!-- Like Button -->
		<div class="toolbar-button alignleft tooltip-onhover" id="like">
			<?php
			$recipe_like = new CPM_Like('recipe');
			$recipe_like->display();
			?>
		</div>


		<!-- Recipe Print Button -->
		<div class="toolbar-button alignright tooltip-onhover" id="print">
			<a class="wpurp-recipe-print" id="recipe_print_button" href="<?php echo $recipe->link_print(); ?>" target="_blank">
				<?= foodiepro_get_icon('print'); ?>
				<span class="button-caption"><?php echo __('Print', 'crm'); ?></span>
			</a>
			<?php
			$args = array(
				'content' 	=> __('Print this Recipe', 'crm'),
				'valign' 	=> 'above',
				'halign'	=> 'right',
			);
			Tooltip::display($args);
			?>
		</div>

		<!-- Recipe Read Button -->
		<div class="toolbar-button alignright tooltip-onhover" id="read">
			<a class="recipe-read-button" onClick="<?= is_user_logged_in() ? '' : "ga('send','event','recipe-read','click','', 0)"; ?>" />
			<?= foodiepro_get_icon('read'); ?>
			<span class="button-caption"><?php echo __('Read', 'crm'); ?></span>
			</a>
			<?php
			$args = array(
				'content' 	=>  __('Read this recipe out loud', 'crm'),
				'valign' 	=> 'above',
				'halign'	=> 'center',
			);
			Tooltip::display($args);
			?>
		</div>

		<!-- Add To Favorites Button -->
		<div class="toolbar-button alignright tooltip-onhover <?php echo is_user_logged_in() ? '' : 'disabled'; ?>" id="favorite">
			<?php
			$favorite_recipe = new CRM_Favorite();
			echo $favorite_recipe->output_button($recipe); ?>
		</div>



	</div>

	<?php

	?>


</div>
