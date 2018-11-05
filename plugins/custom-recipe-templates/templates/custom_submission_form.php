<?php $required_fields = WPUltimateRecipe::option( 'user_submission_required_fields', array() ); ?>
<div id="wpurp_user_submission_form" class="postbox">
    <form id="new_recipe" name="new_recipe" method="post" action="" enctype="multipart/form-data">
    <div class= "hide-on-preview">
        <input type="hidden" name="recipe_id" value="<?php echo $recipe->ID(); ?>" />
        <div class="recipe-container recipe-title-container">      	
            <p>
                <!-- <label for="recipe_title" class="recipe-title"><?php _e( 'Recipe title', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe_title_check', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label><br /> -->
                <h4 id="headline-title"><?php _e( 'Recipe title', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe_title_check', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></h4>
                <input type="text" id="recipe_title" value="<?php echo isset( $_POST['recipe_title'] ) ? $_POST['recipe_title'] : $recipe->title();  ?>" size="20" name="recipe_title" />
            </p>
        </div>

<?php if( !is_user_logged_in() ) { ?>
        <div class="recipe-author-container">
                <!-- <label for="recipe-author"><?php _e( 'Your name', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe-author', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label><br /> -->
            <h4 id="headline-author"><?php _e( 'Your name', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe-author', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></h4>
            <p>
                <input type="text" id="recipe-author" value="<?php echo isset( $_POST['recipe-author'] ) ? $_POST['recipe-author'] : $recipe->author();  ?>" size="50" name="recipe-author" />
            </p>
        </div>
<?php } ?>
        <div class="recipe-container recipe-image-container">     	
<?php $image_url = $recipe->image_ID() > 0 ? $recipe->image_url( 'square-thumbnail' ) : WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png'; ?>
<?php if ( !current_user_can( 'upload_files' ) || WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) != '1' ) { ?>
                <!-- <label for="recipe_thumbnail" class="recipe-image"><?php _e( 'Featured image', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe_thumbnail', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label><br /> -->
            <h4 id="headline-image"><?php _e( 'Featured image', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe_thumbnail', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></h4>
            <p>
                <img src="<?php echo $image_url; ?>" class="recipe_thumbnail" id="instruction_thumbnail_preview_" /><br/>
                <input class="recipe_thumbnail_image button" type="file" id="recipe_thumbnail_input_" value="" size="50" name="recipe_thumbnail" onchange="PreviewImage()" />
            </p>
<?php } else { ?>
            <p>
                <input name="recipe_thumbnail" class="recipe_thumbnail_image" type="hidden" value="<?php echo $recipe->image_ID(); ?>" />
                <input class="recipe_thumbnail_add_image button button<?php if($has_image) { echo ' wpurp-hide'; } ?>" rel="<?php echo $recipe->ID(); ?>" type="button" value="<?php _e( 'Add Featured Image', 'wp-ultimate-recipe' ); ?>" />
                <input class="recipe_thumbnail_remove_image button<?php if(!$has_image) { echo ' wpurp-hide'; } ?>" type="button" value="<?php _e('Remove Featured Image', 'wp-ultimate-recipe' ); ?>" />
                <?php if( in_array( 'recipe_thumbnail', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?>
                <br /><img src="<?php echo $recipe->image_url( 'thumbnail' ); ?>" class="recipe_thumbnail" />
            </p>
<?php } ?>
        </div>
        <div class="recipe-container recipe-tags-container">
			
        <!-- <p><label class="recipe-tags"><?php _e( 'Recipe Tags', 'foodiepro' ) ?></label></p> -->
        <h4 id="headline-tags"><?php _e( 'Recipe Tags', 'foodiepro' ) ?></h4>
        <p>
        <div class="taxonomy-select-spinner"><i class="fa fa-spinner fa-spin"></i></div>
        <!-- <div class="taxonomy-select-spinner nodisplay"><i class="fa fa-spinner fa-spin"></i></div> -->
        <div class="taxonomy-select-boxes nodisplay">
        </p>
        <!-- <div class="taxonomy-select-boxes"> -->
<?php
        $select_fields = array();

        $taxonomies = WPUltimateRecipe::get()->tags();
        unset( $taxonomies['ingredient'] );
        unset( $taxonomies['wpurp_keyword'] );

        // $taxonomies['category']=array(
        //                             'labels'=>array(
        //                                 'singular_name'=>__( 'Category', 'wp-ultimate-recipe' ),
        //                             ),
        //                         );
        $taxonomies['post_tag']=array(
                                    'labels'=>array(
                                        'singular_name'=>__( 'Tag', 'wp-ultimate-recipe' ),
                                    ),
                                );        
        
        // General dropdown arguments
        $args = array(
            'echo' => 0,
            'orderby' => 'description',
            'hide_empty' => 0,
        );

        // Generate dropdown markup for each taxonomy (course, cuisine, difficulty, diet...)
        // -----------------------------------------------------------
        foreach( $taxonomies as $taxonomy => $options ) {
            $args['taxonomy'] = $taxonomy;
            $args['show_option_none'] = (CustomArchive::is_multiselect($taxonomy) ) ?'':$options['labels']['singular_name'];
            $args['hierarchical'] = CustomArchive::is_hierarchical($taxonomy)?1:0;
            $args['exclude'] = $this->excluded_terms($taxonomy);
            $args['tags_post_type'] = 'recipe';
            $args['orderby'] = CustomArchive::orderby($taxonomy);
            // $args['class'] .= $multiselect?'multiple':'';

            $select_fields[$taxonomy] = array(
                'label' => $options['labels']['singular_name'],
                'dropdown' => $this->custom_dropdown_categories( $args, $options ),
                // 'dropdown' => wp_dropdown_categories( $args ),
            );
        }


        // Echoes all dropdowns that were previously built
        // -----------------------------------------------------------
        foreach( $select_fields as $taxonomy => $select_field ) {
    
            // Multiselect
            if( CustomArchive::is_multiselect($taxonomy) ) {
                preg_match( "/<select[^>]+>/i", $select_field['dropdown'], $select_field_match );
                if( isset( $select_field_match[0] ) ) {
                    $select_multiple = preg_replace( "/name='([^']+)/i", "$0[]' data-placeholder='".$select_field['label']."' multiple='multiple", $select_field_match[0] );
                    $select_field['dropdown'] = str_ireplace( $select_field_match[0], $select_multiple, $select_field['dropdown'] );
                }
            }

            // Mark existing post terms as Selected in the dropdown
            $terms = wp_get_post_terms( $recipe->ID(), $taxonomy, array( 'fields' => 'ids' ) );
            foreach( $terms as $term_id ) {
                $select_field['dropdown'] = str_replace( ' value="'. $term_id .'"', ' value="'. $term_id .'" selected="selected"', $select_field['dropdown'] );
            }

            echo $select_field['dropdown'];
        }
?>
            </div>
        </div>
<?php
        $wpurp_user_submission = true;
        // echo dirname(__DIR__) . '/templates/partials/submission_form_ingredients_instructions.php';
        // include( dirname(__DIR__) . '/templates/partials/submission_form_ingredients_instructions.php' );
        include( 'partials/submission_form_ingredients_instructions.php' );
?>
<!-- <?php if( WPUltimateRecipe::option( 'user_submissions_use_security_question', '' ) == '1' ) { ?>
    <div class="security-question-container">
        <h4><?php _e( 'Security Question', 'wp-ultimate-recipe' ); ?><span class="wpurp-required">*</span></h4>
        <p>
            <label for="security-answer"><?php echo WPUltimateRecipe::option( 'user_submissions_security_question', '4 + 7 =' ); ?></label> <input type="text" id="security-answer" value="<?php echo isset( $_POST['security-answer'] ) ? $_POST['security-answer'] : '';  ?>" size="25" name="security-answer" />
        </p>
    </div>
<?php } ?> -->
    </div> <!-- /hide-on-preview -->
        <div id="recipe-form-buttons">
            <?php if (in_array('preview', $buttons)) {;?>    
                    <input type="submit" value="<?php _e( 'Preview', 'foodiepro' ); ?>" id="preview" name="preview" />
                <!-- <div class="formbutton" id="preview"> -->
                    <!-- <span><?php _e( 'Preview', 'foodiepro' ); ?></span>     -->
                    <!-- <input type="hidden" value="" id="preview" name="preview" /> -->
                <!-- </div> -->
            <?php }; ?>
            <?php if (in_array('edit', $buttons)) {;?>    
                    <input type="submit" value="<?php _e( 'Edit', 'foodiepro' ); ?>" id="edit" name="edit" />
                <!-- <div class="formbutton" id="edit"> -->
                    <!-- <span><?php _e( 'Edit', 'foodiepro' ); ?></span> -->
                    <!-- <input type="hidden" value="" id="edit" name="edit" /> -->
                <!-- </div> -->
            <?php }; ?>        
            <?php if (in_array('draft', $buttons)) {;?>    
                    <input type="submit" value="<?php _e( 'Draft', 'foodiepro' ); ?>" id="draft" name="draft" />
                <!-- <div class="formbutton" id="draft"> -->
                    <!-- <span><?php _e( 'Draft', 'foodiepro' ); ?></span> -->
                    <!-- <input type="hidden" value="" id="draft" name="draft" /> -->
                <!-- </div> -->
            <?php }; ?>
            <?php if (in_array('publish', $buttons)) {;?>    
                    <input type="submit" value="<?php _e( 'Publish', 'foodiepro' ); ?>" id="publish" name="publish" />
                <!-- <div class="formbutton" id="publish"> -->
                    <!-- <span><?php _e( 'Publish', 'foodiepro' ); ?></span> -->
                    <!-- <input type="hidden" value="" id="publish" name="publish" /> -->
                <!-- </div> -->
            <?php }; ?>
        </div>
        <input type="hidden" name="action" value="post" />
        <?php echo wp_nonce_field( 'recipe_submit', 'submitrecipe' ); ?>
    </form>
</div>