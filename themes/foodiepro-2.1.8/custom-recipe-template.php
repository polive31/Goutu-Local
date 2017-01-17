<?php

function wpurp_custom_template( $content, $recipe )
{
	ob_start();
	?>

	<div class="recipe">
		
		<div class="recipe-top">
			
			<?php
			echo $recipe->description();;
			?>	
			
			<div class="recipe-buttons">
				<?php
					$print_button = new WPURP_Template_Recipe_Print_Button();
					echo $print_button->output( $recipe );
				?>
			</div>
			
		</div>
		
		<div class="recipe-container">
			
			<div class="image-container">
					<div class="clearfix">
				  <a href="<?php echo $recipe->featured_image_url('full');?>">
						<img src="<?php echo $recipe->featured_image_url('medium-thumbnail');?>">
					</a>
					</div>
					<div class="clearfix">
					[custom-gallery size="mini-thumbnail" link="file" columns="4" gallery-id="joined-pics"]
					</div>
			</div>
		
			<div class="info-container">
				<?php
					$star_rating = new WPURP_Template_Recipe_Stars();
					echo $star_rating->output( $recipe );
				?>
				
				<?php
					$servings = '<div class="label-container"><div id="servings" class="recipe-label">' . __('Serves','foodiepro') . '</div><input type="number" min="1" class="adjust-recipe-servings" data-original="' . $recipe->servings_normalized() . '" data-start-servings="' . $recipe->servings_normalized() . '" value="' . $recipe->servings_normalized() . '"/> ' . $recipe->servings_type() . '</div>';
					echo $servings;
					$prep_time = '<div class="label-container"><div id="prep" class="recipe-label">' . __('Preparation','foodiepro') . '</div>' . $recipe->prep_time() . ' ' . $recipe->prep_time_text() . '</div>';
					echo $prep_time;
					$cook_time= '<div class="label-container"><div id="cook" class="recipe-label">' . __('Cooking','foodiepro') . '</div>' . $recipe->cook_time() . ' ' . $recipe->cook_time_text() . '</div>';
					echo $cook_time;
				?>
				
				
			</div>		
			
		</div>
		
		<div class="recipe-container">
			
			<div class="ingredients-container">

			<?php
				$ingredient_list = new WPURP_Template_Recipe_Ingredients();
				echo $ingredient_list->output( $recipe );
			?>
			</div>

			<?php
				$instructions_list = new WPURP_Template_Recipe_Instructions();
				echo $instructions_list->output( $recipe );
			?>
		
		</div>
		
	</div>

	<?php
    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}

add_filter( 'wpurp_output_recipe', 'wpurp_custom_template', 10, 2 );

?>