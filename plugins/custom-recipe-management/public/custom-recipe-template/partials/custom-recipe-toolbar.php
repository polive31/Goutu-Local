<!-- Recipe Toolbar  -->
<div class="recipe-top">
	<div class="toolbar-buttons">

		<!-- Recipe Rate Button -->
		<!-- <div class="toolbar-button alignleft <?php //echo is_user_logged_in()?'tooltip-onhover':'disabled';
													?>" id="rate"> -->
		<div class="toolbar-button alignleft tooltip-onhover fa-before" id="rate">
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
				'id'		=> 'recipe_rating_form',
				'valign' 	=> 'above',
				'halign'	=> 'left',
				'action'	=> 'click',
				'callout'	=> false,
				// 'class'		=> 'rating-form modal fancy',
				'class'		=> 'rating-form modal big-font uppercase',
				'title'		=> __('Rate this recipe', 'foodiepro'),
				'img'		=> CHILD_THEME_URL . '/images/popup-icons/goutumetre.png'
			);
			Tooltip::display($args);
			// }
			?>
		</div>

		<!-- Recipe Add to Cart Button -->
		<!-- 				<div class="toolbar-button alignleft tooltip tooltip-above tooltip-left" id="shopping">
		<?php
		// $shopping_list = new Custom_Recipe_Add_To_Shopping_List( is_user_logged_in() );
		// echo $shopping_list->output( $recipe );
		?>
		</div>	 -->

		<!-- Add To Favorites Button -->
		<div class="toolbar-button alignleft fa-before  <?php echo is_user_logged_in() ? 'tooltip-onhover' : 'disabled'; ?>" id="favorite">
			<?php
			$favorite_recipe = new CRM_Favorite();
			echo $favorite_recipe->output($recipe); ?>
		</div>

		<!-- Like Button -->
		<div class="toolbar-button alignleft tooltip-onhover" id="like">
			<?php
			$recipe_like = new CSN_Like('recipe');
			$recipe_like->display();
			?>
		</div>

		<!-- Recipe Print Button -->
		<div class="toolbar-button alignright tooltip-onhover" id="print">
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
		<!-- <div class="toolbar-button alignright tooltip" id="share">
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
		<div class="toolbar-button alignright tooltip-onhover" id="read">
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
