<?php

add_filter( 'wpurp_output_recipe_print', 'wpurp_custom_print_template', 10, 2 );
wp_enqueue_style( 'custom-recipe-print', get_stylesheet_directory_uri() . '/assets/css/custom-recipe-print.css', array(), CHILD_THEME_VERSION );

function wpurp_custom_print_template( $content, $recipe )
{
	ob_start();
	
	$post_ID = get_the_ID();

	?>

	<div class="recipe wpurp-container">
	<!-- Class .wpurp-container important for adjustable servings javascript -->	

		<div class="recipe-container">
		
			<div class="info-container">
				<?php
					// Rating
					$rating = output_recipe_rating( $post_ID ); ?>
					<div class="label-container">
						<div class="rating" id="stars-<?php echo $rating['stars'];?>"></div>
						<?php 
						if ( $rating['votes']!=0 ) {
							$rating_plural=$rating['votes']==1?__('review','foodiepro'):__('reviews','foodiepro'); 
							echo '<div class="rating-details">(' . $rating['votes'] . ' ' . $rating_plural . ')</div>'; //. ' | ' . __('Rate this recipe','foodiepro') . 
						}
						//else {
							//echo '<div class="rating-details">' . __('Be the first to rate this recipe !','foodiepro') . '</div>';
						//}
						?>
					</div>
				
				<?php
				
					// Servings
					$test = $recipe->servings_normalized();
					if ($test!='') {
						$html = '<div class="label-container"><div id="servings" class="recipe-label">' . __('Serves','foodiepro') . '</div><input type="number" min="1" class="adjust-recipe-servings" data-original="' . $recipe->servings_normalized() . '" data-start-servings="' . $recipe->servings_normalized() . '" value="' . $recipe->servings_normalized() . '"/> ' . $recipe->servings_type() . '</div>';
						echo $html;
					}
					
					// Prep time
					$test = $recipe->prep_time();
					if ($test!='') {
						$html = '<div class="label-container"><div id="prep" class="recipe-label">' . __('Preparation','foodiepro') . '</div>' . $test . ' ' . $recipe->prep_time_text() . '</div>';
						echo $html;
					}
					
					// Prep time
					$test = $recipe->cook_time();
					if ($test!='') {
						$html= '<div class="label-container"><div id="cook" class="recipe-label">' . __('Cooking','foodiepro') . '</div>' . $test . ' ' . $recipe->cook_time_text() . '</div>';
						echo $html;
						}
					
					$test = $recipe->passive_time();
					if ($test!='') {
						$html = '<div class="label-container"><div id="wait" class="recipe-label">' . __('Wait','foodiepro') . '</div>' . $test . ' ' . $recipe->passive_time_text() . '</div>';
						echo $html;					
					}
				?>
				
				
			</div>		
			
		</div>
		
		<div class="recipe-container">
			
			<div class="ingredients-container"> 
				<?php
				// Method "with custom function"
					echo custom_ingredients_list($recipe,'');
				?>
			</div>

			<?php
					echo custom_instructions_list($recipe,'');
			?>
		</div>
		
		<div class="recipe-container">
			<?php
			// Related Posts
			rp4wp_children();
			?>
		</div>
		
		<div class="recipe-container">
			<?php
			$test = $recipe->notes();
			if ($test!='') {
				$html= '<h3>' . __('Notes','foodiepro') . '</h3>';
				$html.= '<div class="label-container">' . $test . '</div>';
				echo $html;
				}
			?>
		</div>
		
	</div>

	<?php
    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}
    
?>