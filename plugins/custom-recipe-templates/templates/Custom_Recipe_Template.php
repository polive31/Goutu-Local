<?php


class Custom_Recipe_Template extends Custom_WPURP_Templates {
	
	public function __construct() {
		parent::__construct();
		/* Custom recipe template */
		add_filter( 'wpurp_output_recipe', array($this,'wpurp_custom_recipe_template'), 10, 2 );
	}

	public function wpurp_custom_recipe_template( $content, $recipe ) {

		$post_ID = get_the_ID();

		ob_start();
		
		// Debug
			//echo '<pre>' . print_r(get_post_meta($post_ID), true) . '</pre>';
		
		// Output JSON+LD metadata & rich snippets
			echo $this->json_ld_meta_output($recipe,'');
		?>
		
		<div id="share-buttons"><?php //echo do_shortcode('[mashshare text=""]'); ?></div>

		<!-- Class .wpurp-container important for adjustable servings javascript -->	
		<div class="recipe wpurp-container" id="wpurp-container-recipe-<?php echo $recipe->ID(); ?>" data-id="<?php echo $recipe->ID(); ?>" data-permalink="<?php echo $recipe->link(); ?>" data-servings-original="<?php echo $recipe->servings_normalized(); ?>">
			<!-- Recipe description -->
			<div class="recipe-container" id="intro">
				<?php
				echo $recipe->description();
				?>	
			</div>
				
			<!-- Function buttons  -->
			<div class="recipe-top">
					<div class="recipe-buttons">

					<!-- Recipe Rate Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left" id="rate">
						<a href="<?php echo $this->logged_in?'#':'/connexion';?>" class="recipe-review-button" id="<?php echo $this->logged_in?'recipe-review':'join-us';?>">
						<div class="button-caption"><?php echo __('Rate','foodiepro'); ?></div>
						</a>
						[tooltip text="<?php echo __('Comment and rate this recipe','foodiepro'); ?>" pos="top"]   
					</div>	
					
					<!-- Recipe Add to Cart Button -->
	<!-- 				<div class="recipe-button tooltip tooltip-above tooltip-left" id="shopping">
					<?php 
						$shopping_list = new Custom_Recipe_Add_To_Shopping_List( $this->logged_in );  
						echo $shopping_list->output( $recipe );?>
					</div>	 -->			
					
					<!-- Add To Favorites Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left" id="favorite">
					<?php
						$favorite_recipe = new Custom_Recipe_Favorite( $this->logged_in );
						echo $favorite_recipe->output( $recipe );?>
					</div>			

					<!-- Like Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left" id="like">
					<?php
						$recipe_like = new Custom_Social_Like_Post( $this->logged_in );
						echo $recipe_like->output();?>
					</div>		

					<!-- Recipe Print Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-right" id="print">
						<a class="wpurp-recipe-print recipe-print-button" href="<?php echo $recipe->link_print(); ?>" target="_blank">
						<div class="button-caption"><?php echo __('Print', 'foodiepro'); ?></div>
						</a>
						[tooltip text="<?php echo __('Print this recipe','foodiepro'); ?>" pos="top"]   
					</div>	
										
					<!-- Recipe Share Button -->
					<div class="recipe-button tooltip tooltip-above" id="share">
						<a class="recipe-share-button" id="recipe-share" cursor-style="pointer">
						<div class="button-caption"><?php echo __('Share','foodiepro'); ?></div>
						</a> 
						<?php //echo Custom_WPURP_Templates::output_tooltip(__('Share this recipe','foodiepro'),'top');
							$share = do_shortcode('[mashshare]');
						?>  
						[tooltip text='<?php echo $share;?>' pos="top"] 
					</div>				
					<script type="text/javascript">
						jQuery( "#recipe-share" ).click(function() {
					    	jQuery( "#share-buttons" ).toggle();
						});
					</script>
														
				</div>
				
			</div>
			
			<!-- Image + recipe info -->
			<div class="recipe-container"  id="image">
				
				<div class="image-container">
					<div class="clearfix">
					  <a href="<?php echo $recipe->featured_image_url('full');?>">
							<img src="<?php echo $recipe->featured_image_url('vertical-thumbnail');?>">
						</a>
					</div>
					<div class="clearfix">
						[custom-gallery size="mini-thumbnail" link="file" columns="4" gallery-id="joined-pics"]
					</div>
				</div>
			
				<div class="info-container">
					
					<div class="label-container">
						[display-star-rating display="full"]
					</div>

					<?php
						// Origin
					  $terms = get_the_term_list( $post_ID, 'cuisine', '', ', ', '' ); 
						if ($terms!='') {
							$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Origin','foodiepro') . '</div>' . $terms . '</div>';
							echo $html;
						}		
						
						// Diet
					  $terms = get_the_term_list( $post_ID, 'diet', '', ', ', '' ); 
						if ($terms!='') {
							$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Diet','foodiepro') . '</div>' . $terms . '</div>';
							echo $html;
						}	
						
						// Difficulty
					  $terms = get_the_term_list( $post_ID, 'difficult', '', '', '' ); 
						if ($terms!='') {
							$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Level','foodiepro') . '</div>' . $terms . '</div>';
							echo $html;
						}			
					
						// Servings
						$terms = $recipe->servings_normalized();
						if ($terms!='') {
							$html = '<div class="label-container" id="servings">';
							$html .= '<div class="recipe-label">' . __('Serves','foodiepro') . '</div>';
							$html .= '<div class="recipe-input">';
							$html .= '<i id="dec" class="fa fa-minus-circle"></i>';
							$html .= '<input type="number" min="1" class="adjust-recipe-servings" data-original="' . $recipe->servings_normalized() . '" data-start-servings="' . $recipe->servings_normalized() . '" value="' . $recipe->servings_normalized() . '"/>';
							$html .= '<i id="inc" class="fa fa-plus-circle"></i>';
							$html .= ' ' . $recipe->servings_type();
							$html .= '</div>';
							$html .= '</div>';
							echo $html;
						}
						
						?>
	<script>
		jQuery(".recipe-input i").on("click", function() {
			//console.log("Button Click !!!");
		  var $button = jQuery(this);
		  var $input= $button.parent().find("input");
		  var oldValue = $input.val();
		  //console.log("Old value : " + oldValue );
		  //console.log( "button id " + $button.attr('id') );
		  if ($button.attr('id') == "inc") {
			//console.log("INC Click !!!");
			  var newVal = parseFloat(oldValue) + 1;
			} else {
			//console.log("DEC Click !!!");
		    if (oldValue > 1) {
		      var newVal = parseFloat(oldValue) - 1;
		    } else {
		      newVal = 1;
		    }
		  }
		  $input.val(newVal);
		  $input.trigger("change");
		});
	</script>
	
<?php					
						// Prep time
						$test = $recipe->prep_time();
						if ($test!='') {
							$html = '<div class="label-container" id="prep"><div class="recipe-label">' . __('Preparation','foodiepro') . '</div>' . $test . ' ' . $recipe->prep_time_text() . '</div>';
							echo $html;
						}
						
						// Prep time
						$test = $recipe->cook_time();
						if ($test!='') {
							$html= '<div class="label-container" id="cook"><div class="recipe-label">' . __('Cooking','foodiepro') . '</div>' . $test . ' ' . $recipe->cook_time_text() . '</div>';
							echo $html;
							}
						
						$test = $recipe->passive_time();
						if ($test!='') {
							$html = '<div class="label-container" id="wait"><div class="recipe-label">' . __('Wait','foodiepro') . '</div>' . $test . ' ' . $recipe->passive_time_text() . '</div>';
							echo $html;					
						}
					?>
					
					
				</div>		
				
			</div>
			
			<!-- Ingredients + Instructions -->
			<div class="recipe-container" id="main">
				
				<div class="ingredients-container"> 
					<?php
					// Method "with custom function"
						echo $this->custom_ingredients_list($recipe,'');
					?>
				</div>

				<?php
						echo $this->custom_instructions_list($recipe,'');
				?>
			</div>
			
			<div class="recipe-container"  id="general">
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


	public function json_ld_meta_output( $recipe, $args ) {
		
		$Custom_Metadata = new Custom_Recipe_Metadata;
		$metadata = in_array( WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ), array( 'json', 'json-inline' ) ) ? $Custom_Metadata->get_metadata( $recipe ) : '';

		ob_start();?>

		<?php
		echo $metadata;

		$output = ob_get_contents();
	  ob_end_clean();

		return $output;
	}

	public function custom_ingredients_list( $recipe, $args ) {
	    $out = '';
	    $previous_group = '';
	    $vocals = array('a','e','i','o','u');
	    $exceptions = array('huile','herbes');
	    
	    $first_group = true;
	    //$out .= '<ul class="wpurp-recipe-ingredients">';
	    
	    foreach( $recipe->ingredients() as $ingredient ) {

	        if( WPUltimateRecipe::option( 'ignore_ingredient_ids', '' ) != '1' && isset( $ingredient['ingredient_id'] ) ) {
	            $term = get_term( $ingredient['ingredient_id'], 'ingredient' );
	            if ( $term !== null && !is_wp_error( $term ) ) {
	                $ingredient['ingredient'] = $term->name;
	            }
	        }

	        if( $ingredient['group'] != $previous_group ) { //removed isset($ingredient['group'] ) && 
	            $out .= $first_group ? '' : '</ul>';
	            $out .= '<li class="ingredient-group">' . $ingredient['group'] . '</li>';
	            $previous_group = $ingredient['group'];
	            $out .= '<ul class="wpurp-recipe-ingredients">';
							$first_group = false;
	        }

	        $fraction = false;
	        $fraction = strpos($ingredient['amount'], '/') === false ? $fraction : true;

	        $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeIngredient"' : '';

	        $out .= '<li class="wpurp-recipe-ingredient"' . $meta . '>';
	        $out .= '<span class="recipe-ingredient-quantity-unit"><span class="wpurp-recipe-ingredient-quantity recipe-ingredient-quantity" data-normalized="'.$ingredient['amount_normalized'].'" data-fraction="'.$fraction.'" data-original="'.$ingredient['amount'].'">'.$ingredient['amount'].'</span> <span class="wpurp-recipe-ingredient-unit recipe-ingredient-unit" data-original="'.$ingredient['unit'].'">'.$ingredient['unit'].'</span></span>';

	        $taxonomy = get_term_by('name', $ingredient['ingredient'], 'ingredient');
	        $taxonomy_slug = is_object( $taxonomy ) ? $taxonomy->slug : $args['ingredient_name'];

	        $plural = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'plural' );
	        $plural = is_array( $plural ) ? false : $plural;
	        ////PC::debug( array('Plural array'=>$plural) );
	        
	        $plural_data = $plural ? ' data-singular="' . esc_attr( $ingredient['ingredient'] ) . '" data-plural="' . esc_attr( $plural ) . '"' : '';

	        $out .= ' <span class="wpurp-recipe-ingredient-name recipe-ingredient-name"' . $plural_data . '>';

					$ingredient_name = remove_accents( $ingredient['ingredient'] );
					$first_letter = $ingredient_name[0];
					$first_word = strtolower( explode(' ', trim($ingredient_name))[0] );
					
					if ( $ingredient['unit']!='' ) {
						if ( in_array($first_letter, $vocals) || in_array( $first_word, $exceptions) )
							$out .= _x('of ','vowel','foodiepro');
						else 
							$out .= _x('of ','consonant','foodiepro');					
					}

	        $ingredient_links = WPUltimateRecipe::option('recipe_ingredient_links', 'archive_custom');

	        $closing_tag = '';
	        if ( !empty( $taxonomy ) && $ingredient_links != 'disabled' ) {

	            if( $ingredient_links == 'archive_custom' || $ingredient_links == 'custom' ) {
	                $custom_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'link' );
	            } else {
	                $custom_link = false;
	            }

	            if( WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'hide_link' ) !== '1' ) {
	                if( $custom_link !== false && $custom_link !== '' ) {
	                    $nofollow = WPUltimateRecipe::option( 'recipe_ingredient_custom_links_nofollow', '0' ) == '1' ? ' rel="nofollow"' : '';

	                    $out .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.WPUltimateRecipe::option( 'recipe_ingredient_custom_links_target', '_blank' ).'"' . $nofollow . '>';
	                    $closing_tag = '</a>';
	                } else if( $ingredient_links != 'custom' ) {
	                    $out .= '<a href="'.get_term_link( $taxonomy_slug, 'ingredient' ).'">';
	                    $closing_tag = '</a>';
	                }
	            }
	        }

	        $out .= $plural && ($ingredient['unit']!='' || $ingredient['amount_normalized'] > 1) ? $plural : $ingredient['ingredient'];
	        $out .= $closing_tag;
	        $out .= '</span>';

	        if( $ingredient['notes'] != '' ) {
	            $out .= ' ';
	            $out .= '<span class="wpurp-recipe-ingredient-notes recipe-ingredient-notes">'.$ingredient['notes'].'</span>';
	        }

	        $out .= '</li>';
	    }
	    //$out .= '</ul>';

	    return $out;
	}
			
	public function custom_instructions_list( $recipe, $args ) {
	    $out = '';
	    $previous_group = '';
	    $instructions = $recipe->instructions();
	    
	    $out .= '<ol class="wpurp-recipe-instruction-container">';
	    $first_group = true;
	    
	    for( $i = 0; $i < count($instructions); $i++ ) {
					
	        $instruction = $instructions[$i];
					$first_inst = false;
					
					if( $instruction['group'] != $previous_group ) { /* Entering new instruction group */
							$first_inst = true;
	            $out .= $first_group ? '' : '</ol>';
	            $out .= '<div class="wpurp-recipe-instruction-group recipe-instruction-group">' . $instruction['group'] . '</div>';
	            $out .= '<ol class="wpurp-recipe-instructions">';
	            $previous_group = $instruction['group'];
	    				$first_group = false;
	        }

	        $style = $first_inst ? ' li-first' : '';
	        $style .= !isset( $instructions[$i+1] ) || $instruction['group'] != $instructions[$i+1]['group'] ? ' li-last' : '';

	        $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeInstructions"' : '';

	        $out .= '<li class="wpurp-recipe-instruction ' . $style . '">';
	        //$out .= '<div' . $meta . '>'.$instruction['description'].'</div>';
	        $out .= '<span>' . $instruction['description'] . '</span>';

	        if( $instruction['image'] != '' ) {
	            $thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
	            $thumb_url = $thumb['0'];

	            $full_img = wp_get_attachment_image_src( $instruction['image'], 'full' );
	            $full_img_url = $full_img['0'];

	            $title_tag = WPUltimateRecipe::option( 'recipe_instruction_images_title', 'attachment' ) == 'attachment' ? esc_attr( get_the_title( $instruction['image'] ) ) : esc_attr( $instruction['description'] );
	            $alt_tag = WPUltimateRecipe::option( 'recipe_instruction_images_alt', 'attachment' ) == 'attachment' ? esc_attr( get_post_meta( $instruction['image'], '_wp_attachment_image_alt', true ) ) : esc_attr( $instruction['description'] );

	            if( WPUltimateRecipe::option( 'recipe_images_clickable', '0' ) == 1 ) {
	                $out .= '<div><a href="' . $full_img_url . '" rel="lightbox" title="' . $title_tag . '">';
	                $out .= '<img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/>';
	                $out .= '</a></div>';
	            } else {
	                $out .= '<div><img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/></div>';
	            }
	        }

	        $out .= '</li>';
	    }
			$out .= '</ol>';

	    return $out;
	}

	
}