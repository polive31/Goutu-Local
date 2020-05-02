<!-- Recipe Toolbar  -->
<div class="recipe-top">
	<div class="toolbar-buttons">
		<table>
			<tr>

		<?php if (class_exists('CSR_Form')) { ?>

			<td class="toolbar-button alignleft tooltip-onhover " id="rate">

			<a href="#" data-tooltip-id="" class="recipe-review-button tooltip-onclick" onClick="">
			<?= foodiepro_get_icon('edit');?>
			<div class="button-caption"><?php echo __('Rate', 'crm'); ?></div>
				</a>
				<?php
				// if( is_user_logged_in() ) {
				$args = array(
					'content' => __('Comment and rate this recipe', 'crm'),
					'valign' 	=> 'above',
					'halign'	=> 'left',
				);
				Tooltip::display($args);
				$args = array(
					'content' 	=> CSR_Form::get_comment_form_with_rating(),
					'id'		=> 'recipe_rating_form',
					'valign' 	=> 'above',
					'halign'	=> 'left',
					'action'	=> 'click',
					'callout'	=> false,
					// 'class'		=> 'rating-form modal fancy',
					'class'		=> 'rating-form modal big-font uppercase',
					'title'		=> __('Rate this recipe', 'crm'),
					'img'		=> CHILD_THEME_URL . '/images/popup-icons/goutumetre.png',
					'imgdir'	=> CHILD_THEME_PATH . '/images/popup-icons'
				);
				Tooltip::display($args);
				// }
				?>
			</td>

			<?php } ?>
			<?php /*
				<div class="toolbar-button alignleft tooltip tooltip-above tooltip-left" id="shopping">
				/* $shopping_list = new Custom_Recipe_Add_To_Shopping_List( is_user_logged_in() );
				echo $shopping_list->output( $recipe );</div> */
			?>


		<td class="toolbar-button alignleft <?php echo is_user_logged_in() ? 'tooltip-onhover' : 'disabled'; ?>" id="favorite">
			<?php
			$favorite_recipe = new CRM_Favorite();
			echo $favorite_recipe->output_button($recipe); ?>
		</td>

		<td class="toolbar-button alignleft tooltip-onhover" id="like">
			<?php
			$recipe_like = new CPM_Like('recipe');
			$recipe_like->display();
			?>
		</td>

		<!-- Recipe Print Button -->
		<td class="toolbar-button alignright tooltip-onhover" id="print">
			<a class="wpurp-recipe-print" id="recipe_print_button" href="<?php echo $recipe->link_print(); ?>" target="_blank">
				<?= foodiepro_get_icon('print');?>
				<div class="button-caption"><?php echo __('Print', 'crm'); ?></div>
			</a>
			<?php
			$args = array(
				'content' 	=> __('Print this Recipe', 'crm'),
				'valign' 	=> 'above',
				'halign'	=> 'right',
			);
			Tooltip::display($args);
			?>
		</td>

		<!-- Recipe Read Button -->
		<td class="toolbar-button alignright tooltip-onhover" id="read">
			<a class="recipe-read-button" onClick="<?= is_user_logged_in() ? '' : "ga('send','event','recipe-read','click','', 0)"; ?>" />
				<?= foodiepro_get_icon('read');?>
				<div class="button-caption"><?php echo __('Read', 'crm'); ?></div>
			</a>
			<?php
			$args = array(
				'content' 	=>  __('Read this recipe out loud', 'crm'),
				'valign' 	=> 'above',
				'halign'	=> 'center',
			);
			Tooltip::display($args);
			?>
		</td>
		</tr>
		</table>

	</div>

	<?php

	?>


</div>