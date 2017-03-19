<?php


class Custom_Recipe_Submission_Template extends Custom_Recipe_Templates {
	
	
	private $recipe_items;
	
	public function __construct() {
		/* Custom submission form template */
		add_filter( 'wpurp_user_submissions_form', array($this,'custom_submission_form_template'), 10, 2 );

	}

	public function custom_submission_form_template( $form, $recipe ) {

		$post_ID = get_the_ID();
		PC::debug( array('Is single'=>is_single() ) );
		PC::debug( array('Is single(recipe)'=>is_single('recipe') ) );
		//PC::debug( array('Get post action'=> get_current_screen() ) );
		$required_fields = WPUltimateRecipe::option( 'user_submission_required_fields', array() );
		$this->class_hydrate($recipe, $required_fields);
		
		ob_start();?>
		
		<div id="custom_recipe_submission_form" class="postbox">
		    <form id="new_recipe" name="new_recipe" method="post" action="" enctype="multipart/form-data">
		        <input type="hidden" name="recipe_id" value="<?php echo $recipe->ID(); ?>" />
		        <div class="recipe-title-container">
		            <p>
		                <label for="recipe_title"><?php _e( 'Recipe title', 'foodiepro' ); ?><?php if( in_array( 'recipe_title_check', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label><br />
		                <input type="text" id="recipe_title" value="<?php echo isset( $_POST['recipe_title'] ) ? $_POST['recipe_title'] : $recipe->title();  ?>" size="20" name="recipe_title" />
		            </p>
		        </div>	
		
        <div class="recipe-image-container">
<?php $has_image = $recipe->image_ID() > 0 ? true : false; ?>
<?php if ( !current_user_can( 'upload_files' ) || WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) != '1' ) { ?>
            <p>
                <label for="recipe_thumbnail"><?php _e( 'Featured image', 'foodiepro' ); ?><?php if( in_array( 'recipe_thumbnail', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label><br />
                <?php if( $has_image ) { ?>
                <img src="<?php echo $recipe->image_url( 'thumbnail' ); ?>" class="recipe_thumbnail" /><br/>
                <?php } ?>
                <input class="recipe_thumbnail_image button" type="file" id="recipe_thumbnail" value="" size="50" name="recipe_thumbnail" />
            </p>
<?php } else { ?>
            <p>
                <input name="recipe_thumbnail" class="recipe_thumbnail_image" type="hidden" value="<?php echo $recipe->image_ID(); ?>" />
                <input class="recipe_thumbnail_add_image button button<?php if($has_image) { echo ' wpurp-hide'; } ?>" rel="<?php echo $recipe->ID(); ?>" type="button" value="<?php _e( 'Add Featured Image', 'foodiepro' ); ?>" />
                <input class="recipe_thumbnail_remove_image button<?php if(!$has_image) { echo ' wpurp-hide'; } ?>" type="button" value="<?php _e('Remove Featured Image', 'foodiepro' ); ?>" />
                <?php if( in_array( 'recipe_thumbnail', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?>
                <br /><img src="<?php echo $recipe->image_url( 'thumbnail' ); ?>" class="recipe_thumbnail" />
            </p>
<?php } ?>
        </div>
               <div class="recipe-tags-container">
            <p class="taxonomy-select-boxes">
<?php
        $select_fields = array();
        $multiselect = WPUltimateRecipe::option( 'recipe_tags_user_submissions_multiselect', '1' ) == '1' ? true : false;

        $taxonomies = WPUltimateRecipe::get()->tags();
        unset( $taxonomies['ingredient'] );

        $args = array(
            'echo' => 0,
            'orderby' => 'NAME',
            'hide_empty' => 0,
            'hierarchical' => 1,
        );

        $hide_tags = WPUltimateRecipe::option( 'user_submission_hide_tags', array() );

        foreach( $taxonomies as $taxonomy => $options ) {
            if( !in_array( $taxonomy, $hide_tags ) ) {
                $args['show_option_none'] = $multiselect ? '' : $options['labels']['singular_name'];
                $args['taxonomy'] = $taxonomy;
                $args['name'] = 'recipe-' . $taxonomy;

                $select_fields[$taxonomy] = array(
                    'label' => $options['labels']['singular_name'],
                    'dropdown' => wp_dropdown_categories( $args ),
                );
            }
        }

        if( WPUltimateRecipe::option( 'recipe_tags_user_submissions_categories', '0' ) == '1' ) {
            $args['show_option_none'] = $multiselect ? '' : __( 'Category', 'foodiepro' );
            $args['taxonomy'] = 'category';
            $args['name'] = 'recipe-category';

            $exclude = WPUltimateRecipe::option( 'user_submission_hide_category_terms', array() );
            $args['exclude'] = implode( ',', $exclude );

            $select_fields['category'] = array(
                'label' => __( 'Category', 'foodiepro' ),
                'dropdown' => wp_dropdown_categories( $args ),
            );
        }

        if( WPUltimateRecipe::option( 'recipe_tags_user_submissions_tags', '0' ) == '1' ) {
            $args['show_option_none'] = $multiselect ? '' : __( 'Tag', 'foodiepro' );
            $args['taxonomy'] = 'post_tag';
            $args['name'] = 'recipe-post_tag';

            $exclude = WPUltimateRecipe::option( 'user_submission_hide_tag_terms', array() );
            $args['exclude'] = implode( ',', $exclude );

            $select_fields['post_tag'] = array(
                'label' => __( 'Tag', 'foodiepro' ),
                'dropdown' => wp_dropdown_categories( $args ),
            );
        }

        foreach( $select_fields as $taxonomy => $select_field ) {

            // Multiselect
            if( $multiselect ) {
                preg_match( "/<select[^>]+>/i", $select_field['dropdown'], $select_field_match );
                if( isset( $select_field_match[0] ) ) {
                    $select_multiple = preg_replace( "/name='([^']+)/i", "$0[]' data-placeholder='".$select_field['label']."' multiple='multiple", $select_field_match[0] );
                    $select_field['dropdown'] = str_ireplace( $select_field_match[0], $select_multiple, $select_field['dropdown'] );
                }
            }

            // Selected terms
            $terms = wp_get_post_terms( $recipe->ID(), $taxonomy, array( 'fields' => 'ids' ) );
            foreach( $terms as $term_id ) {
                $select_field['dropdown'] = str_replace( ' value="'. $term_id .'"', ' value="'. $term_id .'" selected="selected"', $select_field['dropdown'] );
            }

            echo $select_field['dropdown'];
        }
?>
            </p>
        </div>
<?php
        $wpurp_user_submission = true;
        echo $this->output_recipe_form( $recipe, $required_fields );
?>

        <p align="right">
            <?php if( WPUltimateRecipe::option( 'user_submission_preview_button', '1') == '1' ) { ?>
            <input type="submit" value="<?php _e( 'Preview', 'foodiepro' ); ?>" id="preview" name="preview" />
            <?php } ?>
            <input type="submit" value="<?php _e( 'Submit', 'foodiepro' ); ?>" id="submit" name="submit" />
        </p>
        <input type="hidden" name="action" value="post" />
        <?php echo wp_nonce_field( 'recipe_submit', 'submitrecipe' ); ?>
    </form>
</div>



		<?php 
		$html = ob_get_contents();
	  ob_end_clean();

	  return $html;
	}

	private function output_recipe_form( $recipe, $required_fields ) {

		// Recipe should never be null. Construct just allows easy access to WPURP_Recipe functions in IDE.
		if( is_null( $recipe ) ) $recipe = new WPURP_Recipe(0);
		if( !isset( $required_fields ) ) $required_fields = array();

		ob_start();
		?>

		<script>
	    function autoSuggestTag(id, type) {
	        <?php if( WPUltimateRecipe::option( 'disable_ingredient_autocomplete', '' ) !== '1' ) { ?>
	        jQuery('#' + id).suggest("<?php echo get_bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=" + type);
	        <?php } ?>
	    }
		</script>
		<input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce( 'recipe' ); ?>" />
		
		<div class="recipe-general-container">
	    <h4><?php _e( 'General', 'foodiepro' ); ?></h4>
	    <table class="recipe-general-form">
	  
        <tr class="recipe-general-form-description">
            <div class="recipe-general-form-label"><label for="recipe_description"><?php _e('Description', 'foodiepro' ); ?><?php if( in_array( 'recipe_description', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></div>
            <div class="recipe-general-form-field">
                <textarea name="recipe_description" id="recipe_description" rows="4"><?php echo esc_html( $recipe->description() ); ?></textarea>
            </div>
        </tr> 
        
        <?php
				foreach ($this->recipe_items as $key=>$item) {
		    	$this->output_recipe_item($key,$item);
				}?>
	    </table>
		</div>

		<div class="recipe-ingredients-container">
		    <h4><?php _e( 'Ingredients', 'foodiepro' ); ?></h4>
		    <?php $ingredients = $recipe->ingredients(); ?>
		    
		    <div class="recipe-ingredients-form">
		        <div class="header">
		        	
			        <div class="ingredient-group ingredient-group-first">
			            <div><strong><?php _e( 'Group', 'foodiepro' ); ?>:</strong></div>
			            <div>
			                <span class="ingredient-groups-disabled"><?php echo __( 'Main Ingredients', 'foodiepro' ) . ' ' . __( '(this label is not shown)', 'foodiepro' ); ?></span>
			                <?php
			                $previous_group = '';
			                if( isset( $ingredients[0] ) && isset( $ingredients[0]['group'] ) ) {
			                    $previous_group = $ingredients[0]['group'];
			                }
			                ?>
			                <span class="ingredient-groups-enabled"><input type="text" class="ingredient-group-label" value="<?php echo esc_attr( $previous_group ); ?>" /></span>
			            </div>
			        </div> <!-- ingredient-group ingredient-group-first -->
			        
			        <div class="screen ingredients-header">
		            <div class="ingredient-item" id="controls"></div>
		            <div class="ingredient-item" id="name"><?php _e( 'Ingredient', 'foodiepro' ); ?> <span class="wpurp-required">(<?php _e( 'required', 'foodiepro' ); ?>)</span></div>
		            <div class="ingredient-item" id="amount"><?php _e( 'Quantity', 'foodiepro' ); ?></div>
		            <div class="ingredient-item" id="unit"><?php _e( 'Unit', 'foodiepro' ); ?></div>
		            <div class="ingredient-item" id="notes"><?php _e( 'Notes', 'foodiepro' ); ?></div>
			        </div> <!-- ingredient-field-header -->
			        
		        </div> <!-- header -->
		        
		        <div class="ingredients-body">
		        		        
			        <div class="ingredient-group-stub">
		            <div><strong><?php _e( 'Group', 'foodiepro' ); ?>:</strong></div>
		            <div><input type="text" class="ingredient-group-label" /></div>
		            <div><span class="ingredient-group-delete"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/minus.png" width="16" height="16"></span></div>
			        </div> <!-- ingredient-group-stub -->
			    
			        <!-- Output existing ingredients (when editing existing recipe) -->
			   			<?php $i = $this->output_existing_ingredients($ingredients); ?>
			   			<?php $this->output_ingredient('', $i, true); ?>

		        </div> <!-- body -->
		    </div> <!-- recipe-ingredients-form -->
		    
		    <div class="section" id="add-item-box">
		        <a href="#" id="ingredients-add"><?php _e( 'Add an ingredient', 'foodiepro' ); ?></a>
		    </div>
		    
		    <div class="section" id="add-group-box">
		        <a href="#" id="ingredients-add-group"><?php _e( 'Add an ingredient group', 'foodiepro' ); ?></a>
		    </div>
		    
		    <div class="section recipe-form-notes">
		        <?php _e( "<strong>Use the TAB key</strong> while adding ingredients, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'foodiepro' ); ?>
		    </div>
		</div>

		<div class="recipe-instructions-container">
		    <h4><?php _e( 'Instructions', 'foodiepro' ); ?></h4>
		    <?php $instructions = $recipe->instructions(); ?>
		    <div class="recipe-instructions-form">
		    	
		        <div class="head">
		        <div class="instruction-group instruction-group-first">
		            <div>
		                <strong><?php _e( 'Group', 'foodiepro' ); ?>:</strong>
		                <span class="instruction-groups-disabled"><?php echo __( 'Main Instructions', 'foodiepro' ) . ' ' . __( '(this label is not shown)', 'foodiepro' ); ?></span>
		                <?php
		                $previous_group = '';
		                if( isset( $instructions[0] ) && isset( $instructions[0]['group'] ) ) {
		                    $previous_group = $instructions[0]['group'];
		                }
		                ?>
		                <span class="instruction-groups-enabled"><input type="text" class="instruction-group-label" value="<?php echo esc_attr( $previous_group ); ?>"/></span>
		            </div>
		        </div> <!-- instruction-groups-disabled -->
		        </div> <!-- head -->
		      
		      
	        <div class="instruction-group-stub">
	          <div>
	              <strong><?php _e( 'Group', 'foodiepro' ); ?>:</strong>
	              <input type="text" class="instruction-group-label" />
	          </div>
	          <div class="center-column"><span class="instruction-group-delete"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/minus.png" width="16" height="16"></span></div>
	        </div> <!-- instruction-group-stub -->
	    
		    <?php
		    $i = 0;

		    if( $instructions )
		    {
		        foreach( $instructions as $instruction ) {
		            if( !isset( $instruction['group'] ) ) {
		                $instruction['group'] = '';
		            }

		            if( $instruction['group'] != $previous_group )
		            { ?>
		                <div class="instruction-group">
		                    <div>
		                        <strong><?php _e( 'Group', 'foodiepro' ); ?>:</strong>
		                        <input type="text" class="instruction-group-label" value="<?php echo esc_attr( $instruction['group'] ); ?>"/>
		                    </div>
		                    <div class="center-column"><span class="instruction-group-delete"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/minus.png" width="16" height="16"></span></div>
		                </div> <!-- instruction-group -->
		    <?php
		                $previous_group = $instruction['group'];
		            }

		            if( !isset( $instruction['image'] ) ) {
		                $instruction['image'] = '';
		            }

		            if( $instruction['image'] )
		            {
		                $image = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
		                $image = $image[0];
		                $has_image = true;
		            }
		            else
		            {
		                $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
		                $has_image = false;
		            }
		            ?>
		            <div class="instruction">
		                <div class="sort-handle"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16"></div>
		                <div>
		                    <textarea name="recipe_instructions[<?php echo $i; ?>][description]" rows="4" id="ingredient_description_<?php echo $i; ?>"><?php echo $instruction['description']; ?></textarea>
		                    <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]"    class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="<?php echo esc_attr( $instruction['group'] ); ?>" />
		                <?php if ( isset( $wpurp_user_submission ) && ( !current_user_can( 'upload_files' ) || WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) != '1' ) ) { ?>
		                    <?php _e( 'Instruction Step Image', 'foodiepro' ); ?>:<br/>
		                    <?php if( $has_image ) { ?>
		                    <img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" />
		                    <input type="hidden" value="<?php echo $instruction['image']; ?>" name="recipe_instructions[<?php echo $i; ?>][image]" /><br/>
		                    <?php } ?>
		                    <input class="recipe_instructions_image button" type="file" id="recipe_thumbnail" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
		                </div>
		                <?php } else { ?>
		                </div>
		                <div>
		                    <input name="recipe_instructions[<?php echo $i; ?>][image]" class="recipe_instructions_image" type="hidden" value="<?php echo $instruction['image']; ?>" />
		                    <input class="recipe_instructions_add_image button<?php if($has_image) { echo ' wpurp-hide'; } ?>" rel="<?php echo $recipe->ID(); ?>" type="button" value="<?php _e( 'Add Image', 'foodiepro' ) ?>" />
		                    <input class="recipe_instructions_remove_image button<?php if(!$has_image) { echo ' wpurp-hide'; } ?>" type="button" value="<?php _e( 'Remove Image', 'foodiepro' ) ?>" />
		                    <br /><img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" />
		                    <?php } ?>
		                </div>
		                <div><span class="instructions-delete"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/minus.png" width="16" height="16"></span></div>
		            </div> <!-- instruction -->
		            <?php 
		            $i++;
		        }

		    }

		    $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
		    ?>
		            <div class="instruction">
		                <div class="sort-handle"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16"></div>
		                <div>
		                    <textarea name="recipe_instructions[<?php echo $i; ?>][description]" rows="4" id="ingredient_description_<?php echo $i; ?>"></textarea>
		                    <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]"    class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="" />
		                    <?php if ( isset( $wpurp_user_submission ) && ( !current_user_can( 'upload_files' ) || WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) != '1' ) ) { ?>
		                        <?php _e( 'Instruction Step Image', 'foodiepro' ); ?>:<br/>
		                        <input class="recipe_instructions_image button" type="file" id="recipe_thumbnail" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
		                        </div>
		                    <?php } else { ?>
		                </div>
		                <div>

		                    <input name="recipe_instructions[<?php echo $i; ?>][image]" class="recipe_instructions_image" type="hidden" value="" />
		                    <input class="recipe_instructions_add_image button" rel="<?php echo $recipe->ID(); ?>" type="button" value="<?php _e('Add Image', 'foodiepro' ) ?>" />
		                    <input class="recipe_instructions_remove_image button wpurp-hide" type="button" value="<?php _e( 'Remove Image', 'foodiepro' ) ?>" />
		                    <br /><img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" />
		                    <?php } ?>
		                </div>
		                <div><span class="instructions-delete"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/minus.png" width="16" height="16"></span></div>
		            </div> <!-- instruction -->
		            
		        </div> <!-- body -->
		    </div> <!-- table -->

		    <div class="section" id="add-item-box">
		        <a href="#" id="instructions-add"><?php _e( 'Add an instruction', 'foodiepro' ); ?></a>
		    </div>
		    <div class="section" id="add-group-box">
		        <a href="#" id="instructions-add-group"><?php _e( 'Add an instruction group', 'foodiepro' ); ?></a>
		    </div>
		    <div class="recipe-form-notes">
		        <?php _e( "<strong>Use the TAB key</strong> while adding instructions, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'foodiepro' ); ?>
		    </div>
		</div>

		<div class="recipe-notes-container">
		    <h4><?php _e( 'Recipe notes', 'foodiepro' ) ?></h4>
		    <?php
		    $options = array(
		        'textarea_rows' => 7
		    );

		    if( isset( $wpurp_user_submission ) ) {
		        $options['media_buttons'] = false;
		    }

		    wp_editor( $recipe->notes(), 'recipe_notes',  $options );
		    ?>
		</div>
		<?php
		$custom_fields_addon = WPUltimateRecipe::addon( 'custom-fields' );
		if( $custom_fields_addon && ( !isset( $wpurp_user_submission ) || WPUltimateRecipe::option( 'recipe_fields_in_user_submission', '1' ) == '1' ) )
		{
		    $custom_fields = $custom_fields_addon->get_custom_fields();
		    $custom_fields_in_user_submission = WPUltimateRecipe::option( 'recipe_fields_user_submission', array_keys( $custom_fields ) );

		    if( count( $custom_fields ) > 0 ) {
		?>
		<div class="recipe-custom-fields-container">
		    <h4><?php _e( 'Custom Fields', 'foodiepro' ) ?></h4>
		    <table class="recipe-general-form">
		        <?php foreach( $custom_fields as $key => $custom_field ) {
		            if( isset( $wpurp_user_submission ) && !in_array( $key, $custom_fields_in_user_submission ) ) continue;
		            ?>
		            <tr>
		                <div class="recipe-general-form-label"><label for="<?php echo $key; ?>"><?php echo $custom_field['name']; ?><?php if( in_array( $key, $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></div>
		                <div class="recipe-general-form-field">
		                    <textarea name="<?php echo $key; ?>" id="<?php echo $key; ?>" rows="1"><?php echo $recipe->custom_field( $key ); ?></textarea>
		                </div>
		            </tr>
		        <?php } ?>
		    </table>
		</div>
		<?php }
		} ?>

		<?php 
		$html = ob_get_contents();
	  ob_end_clean();

	  return $html;

	}
	
	
	public function output_recipe_item($class, $args) {?>
		
      <tr class="recipe-general-form-<?php echo $class?>">
        <div class="recipe-general-form-label"><label for="recipe_<?php echo $class?>"><?php echo $args['title'] ?><?php if( $args['required'] ) echo '<span class="wpurp-required">*</span>'; ?></label></div>
        <div class="recipe-general-form-field"> <?php
        	PC::debug(array('$args'=>$args));
        	foreach ($args['inputs'] as $key=>$arg) {
            echo '<input type="text" class="' . $arg[0] . '" name="recipe_' . $key . '" id="recipe_' . $key . '" value="' . esc_attr( $arg[1] ) . '" />';
        	}?>
          <div class="recipe-general-form-notes"> <?php echo $args['notes'] ?></div>
        </div>
    	</tr>
    	<tr class="spacer"><div>&nbsp;</div></tr>
    
		<?php 

	}
	
	private function output_existing_ingredients($ingredients) {

	  $i = 0;
	  if( $ingredients ) {
      foreach( $ingredients as $ingredient ) {

        if( WPUltimateRecipe::option( 'ignore_ingredient_ids', '' ) != '1' && isset( $ingredient['ingredient_id'] ) ) {
            $term = get_term( $ingredient['ingredient_id'], 'ingredient' );
            if ( $term !== null && !is_wp_error( $term ) ) {
                $ingredient['ingredient'] = $term->name;
            }
        }

        if( !isset( $ingredient['group'] ) ) {
            $ingredient['group'] = '';
        }

        if( $ingredient['group'] != $previous_group ) { ?>
            <div class="ingredient-group">
                <div><strong><?php _e( 'Group', 'foodiepro' ); ?>:</strong></div>
                <div colspan="2"><input type="text" class="ingredient-group-label" value="<?php echo esc_attr( $ingredient['group'] ); ?>" /></div>
                <div class="center-column"><span class="ingredient-group-delete"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/minus.png" width="16" height="16"></span></div>
            </div>
            <?php
            $previous_group = $ingredient['group'];
        }
        
        $this->output_ingredient($ingredient, $i, false);

        $i++;
        
      }
		}
		
		return $i;
		
	}
		
	private function output_ingredient($ingredient, $index, $new) {?>
			
    <div class="section" id="ingredient">
  		
  		<div class="ingredient-item" id="controls">
  			<div class="sort-handle"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16"></div>
  			<div class="mobile"><span class="ingredients-delete"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/minus.png" width="16" height="16"></span></div>
  		</div> <!-- ingredient-item#controls -->
  		
    	<div class="ingredient-item" id="name">
  			<div class="mobile"><?php _e( 'Ingredient', 'foodiepro' ); ?> <span class="wpurp-required">(<?php _e( 'required', 'foodiepro' ); ?>)</span></div>
  			
  			<?php if( isset( $wpurp_user_submission ) && WPUltimateRecipe::option( 'user_submission_ingredient_list', '0' ) == '1' ) { 
  				output_ingredients_list($index);
  			}
				else {?>
    			<input type="text" 
    				name="recipe_ingredients[<?php echo $index; ?>][ingredient]" 
    				class="ingredients_name" id="ingredients_<?php echo $index; ?>" 
    				onfocus="autoSuggestTag('ingredients_<?php echo $index; ?>', 'ingredient');" 
    				<?php if($index == 0 && $new) { echo 'placeholder="' . __( 'olive oil', 'foodiepro' ) . '"'; } ?> 
    				<?php if(! $new) {echo 'value="' . esc_attr( $ingredient['ingredient'] ) . '"'; } ?> 
    			/>
    			
    		<?php } ?>
    	</div> <!-- ingredient-item#name -->
    	
    	<div class="ingredient-item" id="amount">
    		<div class="mobile"><?php _e( 'Quantity', 'foodiepro' ); ?></div>
    		<input type="text" 
    			name="recipe_ingredients[<?php echo $index; ?>][amount]" 
    			class="ingredients_amount" id="ingredients_amount_<?php echo $index; ?>" 
      		<?php if ($index == 0 && $new) { echo 'placeholder="1"'; } ?> 
    			<?php if (! $new) {echo 'value="' . esc_attr( $ingredient['amount'] ) . '"'; } ?> 
    		/>
    	</div> <!-- ingredient-item#amount -->
    
    	<div class="ingredient-item" id="unit">
    		<div class="mobile"><?php _e( 'Unit', 'foodiepro' ); ?></div>
    		<input type="text"   
    			name="recipe_ingredients[<?php echo $index; ?>][unit]" 
    			class="ingredients_unit" id="ingredients_unit_<?php echo $index; ?>" 
    			<?php if ($index == 0 && $new) { echo 'placeholder="' . __( 'tbsp', 'foodiepro' ) . '"'; } ?>
    			<?php if (! $new) {echo 'value="' . esc_attr( $ingredient['unit'] ) .'"'; } ?> 
    		/>
    	</div> <!-- ingredient-item#unit -->

      <div class="ingredient-item" id="notes">
      	<div class="mobile"><?php _e( 'Notes', 'foodiepro' ); ?></div>
        <textarea rows="1" 
        	name="recipe_ingredients[<?php echo $index; ?>][notes]" 
        	class="notes ingredients_notes" id="ingredient_notes_<?php echo $index; ?>" 
        	<?php if ($index == 0 && $new) { echo 'placeholder="' . __( 'extra virgin', 'foodiepro' ) . '"'; } ?> 
        	<?php if (! $new) {echo 'value="' . esc_attr( $ingredient['notes'] ) . '"'; } ?> 
        ></textarea> <!-- IMPORTANT : always put </textarea> on the same line as the <textarea> tag otherwise placeholder won't appear -->
    	</div> <!-- ingredient-item#notes -->
      
	    <input type="hidden" 
	    	name="recipe_ingredients[<?php echo $index; ?>][group]" 
	    	class="ingredients_group" id="ingredient_group_<?php echo $index; ?>"
    		value="<?php if (! $new) {echo esc_attr( $ingredient['group'] ); } ?>" 
	    />
      
      <div class="ingredient-item" id="controls">
      	<div class="screen"><span class="ingredients-delete"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/minus.png" width="16" height="16"></span></div>
      </div>
	  
	  </div> <!-- Ingredient -->
	  
	<?php
	}
	
	private function output_ingredients_list($index) {?>
 		<select name="recipe_ingredients[<?php echo $index; ?>][ingredient]" id="ingredients_<?php echo $index; ?>">
			<option value=""><?php _e( 'Select an ingredient', 'foodiepro' ); ?></option>
        <?php
        $args = array(
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hide_empty'    => false,
        );
        $ingredient_terms = get_terms( 'ingredient', $args );
        foreach( $ingredient_terms as $term ) { ?>
		    	<option value="<?php echo esc_attr( $term->name ); ?>">
		    		<?php echo $term->name; ?>
		    	</option>
		    <?php } ?>
		</select>		
	<?php 
	}
	
	private function class_hydrate($recipe, $required_fields) {
		
		$this->recipe_items = array(
    	'servings' => array(
	    	'title' => __( 'Servings', 'foodiepro' ),
	    	'notes' => __( '(e.g. 2 people, 3 loafs, ...)', 'foodiepro' ),
	    	'required' => in_array( 'recipe_servings', $required_fields ),
		    'inputs' => array( 
		    	'servings' => array( 'value', $recipe->servings() ),
		    	'servings_type' => array ( 'unit', $recipe->servings_type() ),
		    )
    	),	
			'prep-time' => array(
	    	'title' => __( 'Prep Time', 'foodiepro' ),
	    	'notes' => __( '(e.g. 20 minutes, 1-2 hours, ...)', 'foodiepro' ),
	    	'required' => in_array( 'prep_time', $required_fields ),
		    'inputs' => array( 
		    	'prep_time' => array( 'value', $recipe->prep_time() ),
		    	'prep_time_text' => array ( 'unit', $recipe->prep_time_text() ),
		   	)
		  ),
    	'cook-time' => array(
	    	'title' => __( 'Cook Time', 'foodiepro' ),
	    	'notes' => __( '(e.g. 20 minutes, 1-2 hours, ...)', 'foodiepro' ),
	    	'required' => false,
	   		'inputs' => array( 
		    	'prep_time' => array( 'value', $recipe->prep_time() ),
		    	'prep_time_text' => array ( 'unit', $recipe->prep_time_text() ),
		    )
		  ),
    	'passive-time' => array(
	    	'title' => __( 'Passive Time', 'foodiepro' ),
	    	'notes' => __( '(e.g. 20 minutes, 1-2 hours, ...)', 'foodiepro' ),
	    	'required' => false,
	   		'inputs' => array( 
		    	'passive_time' => array( 'value', $recipe->prep_time() ),
		    	'passive_time_text' => array ( 'unit', $recipe->prep_time_text() ),
		    )
		  ),
    ); 		
	}
	
}
    