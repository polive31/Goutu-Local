		<div id="share-buttons"><?php //echo do_shortcode('[mashshare text=""]'); ?></div>

		<!-- Class .wpurp-container important for adjustable servings javascript -->	
		<div class="recipe wpurp-container" id="wpurp-container-recipe-<?php echo $recipe->ID(); ?>" data-id="<?php echo $recipe->ID(); ?>" data-permalink="<?php echo $recipe->link(); ?>" data-servings-original="<?php echo $recipe->servings_normalized(); ?>">
			<!-- Recipe description -->
			<div class="recipe-container" id="intro">
				<?php

				echo $recipe->output_description();
				
				?>	
			</div>
				
			<!-- Function buttons  -->
			<div class="recipe-top">
					<div class="recipe-buttons">

					<!-- Recipe Rate Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left <?php echo self::$logged_in?'':'disabled';?>" id="rate">
						<a href="<?php echo self::$logged_in?'#':'/connexion';?>" class="recipe-review-button" id="<?php echo self::$logged_in?'recipe-review':'join-us';?>">
						<div class="button-caption"><?php echo __('Rate','foodiepro'); ?></div>
						</a>
						[tooltip text="<?php echo __('Comment and rate this recipe','foodiepro'); ?>" pos="top"]   
					</div>	
					
					<!-- Recipe Add to Cart Button -->
	<!-- 				<div class="recipe-button tooltip tooltip-above tooltip-left" id="shopping">
					<?php 
						$shopping_list = new Custom_Recipe_Add_To_Shopping_List( self::$logged_in );  
						echo $shopping_list->output( $recipe );?>
					</div>	 -->			
					
					<!-- Add To Favorites Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left <?php echo self::$logged_in?'':'disabled';?>" id="favorite">
					<?php
						$favorite_recipe = new Custom_Recipe_Favorite( self::$logged_in );
						echo $favorite_recipe->output( $recipe );?>
					</div>			

					<!-- Like Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left" id="like">
					<?php
						$recipe_like = new Custom_Social_Like_Post( 'recipe' );
						echo $recipe_like->display();?>
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
<!-- 					<script type="text/javascript">
						jQuery( "#recipe-share" ).click(function() {
					    	jQuery( "#share-buttons" ).toggle();
						});
					</script> -->
														
				</div>
				
			</div>
			
			<!-- Image + recipe info -->
			<div class="recipe-container"  id="image">
				
				<div class="image-container">
					<div class="clearfix">
					  	<a href="<?php echo $recipe->featured_image_url('full');?>">
							<img src="<?php echo $recipe->featured_image_url('vertical-thumbnail');?>" alt="<?php echo $imgAlt;?>">
						</a>
					</div>
					<div class="clearfix">
						[custom-gallery size="mini-thumbnail" link="file" columns="4" gallery-id="joined-pics"]
					</div>
				</div>
			
				<div class="info-container">
					
					<div class="label-container">
						<?php echo do_shortcode('[display-star-rating display="full"]');?>
					</div>

					<?php
						// Origin
					  $terms = get_the_term_list( $this->post_ID, 'cuisine', '', ', ', '' ); 
						if ($terms!='') {
							$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Origin','foodiepro') . '</div>' . $terms . '</div>';
							echo $html;
						}		
						
						// Diet
					  $terms = get_the_term_list( $this->post_ID, 'diet', '', ', ', '' ); 
						if ($terms!='') {
							$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Diet','foodiepro') . '</div>' . $terms . '</div>';
							echo $html;
						}	
						
						// Difficulty
					  $terms = get_the_term_list( $this->post_ID, 'difficult', '', '', '' ); 
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
				// Times
				echo $recipe->output_time( 'prep' );				
				echo $recipe->output_time( 'cook' );
				echo $recipe->output_time( 'passive' );

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
