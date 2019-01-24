<div id="custom_post_submission_form" class="postbox">
    <form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data">
    <div class= "hide-on-preview">
        <input type="hidden" name="post_id" value="<?php echo $post->ID(); ?>" />
        <div class="post-container post-title-container">      	
            <p>
                <h4 id="headline-title"><?php _e( 'post title', 'foodiepro' ); ?><?php if( in_array( 'post_title_check', self::$required_fields ) ) echo '<span class="required-field">*</span>'; ?></h4>
                <input type="text" id="post_title" value="<?php echo isset( $_POST['post_title'] ) ? $_POST['post_title'] : $post->title();  ?>" size="20" name="post_title" />
            </p>
        </div>

<?php if( is_admin() ) { ?>
        <div class="post-author-container">
                <!-- <label for="post-author"><?php _e( 'Your name', 'foodiepro' ); ?><?php if( in_array( 'post-author', self::$required_fields ) ) echo '<span class="required-field">*</span>'; ?></label><br /> -->
            <h4 id="headline-author"><?php _e( 'Author', 'foodiepro' ); ?><?php if( in_array( 'post-author', self::$required_fields ) ) echo '<span class="required-field">*</span>'; ?></h4>
            <p>
                <input type="text" id="post-author" value="<?php echo isset( $_POST['post-author'] ) ? $_POST['post-author'] : $post->author();  ?>" size="50" name="post-author" />
            </p>
        </div>
<?php } ?>
        <div class="post-container post-image-container">     	
            <?php 
                $has_image = $post->image_ID() > 0;
                $image_url = $has_image ? $post->image_url( 'square-thumbnail' ) : self::$PLUGIN_URI . '/img/image_placeholder.png'; ?> 
                <!-- <label for="post_thumbnail" class="post-image"><?php _e( 'Featured image', 'foodiepro' ); ?><?php if( in_array( 'post_thumbnail', self::$required_fields ) ) echo '<span class="required-field">*</span>'; ?></label><br /> -->
            <h4 id="headline-image"><?php _e( 'Featured image', 'foodiepro' ); ?><?php if( in_array( 'post_thumbnail', self::$required_fields ) ) echo '<span class="required-field">*</span>'; ?></h4>
            <p class="post-guidelines">Placez ici votre plus belle photo pour illustrer cet article, afin de donner envie à vos visiteurs de le lire !</p>
            <div class="post-image thumbnail">
                <img src="<?php echo $image_url; ?>" class="post_thumbnail <?php if( !$has_image ) { ?>nodisplay<?php };?>" id="post_thumbnail_preview_" />
                <!-- <div class="post_remove_image_button <?php if( !$has_image ) { ?>nodisplay<?php };?>" id="post_thumbnail_remove_ ?>" title="<?php _e( 'Remove Image', 'foodiepro' ) ?>" /></div> -->
            </div>
            <div class="post-image input">
                <input class="post_thumbnail_image button" type="file" id="post_thumbnail_input_" value="" size="50" name="post_thumbnail" onchange="PreviewImage()" />
            </div>
        </div>
        <div class="post-container post-tags-container">
			
        <!-- <p><label class="post-tags"><?php _e( 'post Tags', 'foodiepro' ) ?></label></p> -->
        <h4 id="headline-tags"><?php _e( 'post Tags', 'foodiepro' ) ?></h4>
        <p>
        <div class="taxonomy-select-spinner"><i class="fa fa-spinner fa-spin"></i></div>
        <!-- <div class="taxonomy-select-spinner nodisplay"><i class="fa fa-spinner fa-spin"></i></div> -->
        <div class="taxonomy-select-boxes nodisplay">
        </p>
        <!-- <div class="taxonomy-select-boxes"> -->
<?php
        $select_fields = array();
      
        
        // General dropdown arguments
        $args = array(
            'echo' => 0,
            'orderby' => 'description',
            'hide_empty' => 0,
        );

        // Generate dropdown markup for each taxonomy (course, cuisine, difficulty, diet...)
        // -----------------------------------------------------------

        foreach( self::$taxonomies as $taxonomy => $options ) {
            $args['taxonomy'] = $taxonomy;
            $args['class'] = "postform $taxonomy";
            $args['show_option_none'] = $options['multiselect']?'':$options['labels']['singular_name'];
            $args['hierarchical'] = 0;
            $args['exclude'] = $options['exclude'];
            $args['tags_post_type'] = 'post';
            $args['orderby'] = $options['orderby'];
            // $args['class'] .= $multiselect?'multiple':'';

            $select_fields[$taxonomy] = array(
                'label' => $options['labels']['singular_name'],
                // Generates dropdown with groups headers in case of hierarchical taxonomies
                'dropdown' => CustomNavigationHelpers::custom_categories_dropdown( $args, $options ),
            );
        }


        // Echoes all dropdowns that were previously built
        // -----------------------------------------------------------
        foreach( $select_fields as $taxonomy => $select_field ) {
    
            // Multiselect
            if( self::$taxonomies[$taxonomy]['multiselect'] ) {
                preg_match( "/<select[^>]+>/i", $select_field['dropdown'], $select_field_match );
                if( isset( $select_field_match[0] ) ) {
                    $select_multiple = preg_replace( "/name='([^']+)/i", "$0[]' data-placeholder='".$select_field['label']."' multiple='multiple", $select_field_match[0] );
                    $select_field['dropdown'] = str_ireplace( $select_field_match[0], $select_multiple, $select_field['dropdown'] );
                }
            }

            // Mark existing post terms as Selected in the dropdown
            $terms = wp_get_post_terms( $post->ID(), $taxonomy, array( 'fields' => 'ids' ) );
            foreach( $terms as $term_id ) {
                $select_field['dropdown'] = str_replace( ' value="'. $term_id .'"', ' value="'. $term_id .'" selected="selected"', $select_field['dropdown'] );
            }

            echo $select_field['dropdown'];
        }
?>
            </div>
        </div>

    <input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce( 'recipe' ); ?>" />
    <div class="recipe-container recipe-general-container">
        <h4 id="headline-general"><?php _e( 'General', 'foodiepro' ); ?></h4>
        <table class="recipe-form" id="recipe-general-form">
            <tr class="recipe-general-form-description">
                <td class="recipe-general-form-label"><label for="recipe_description"><?php _e('Description', 'foodiepro' ); ?><?php if( in_array( 'recipe_description', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
                <td class="recipe-general-form-field">
                <textarea class="recipe-description" name="content" id="recipe_description" rows="4" placeholder="<?php echo __('Provide general information about this recipe', 'foodiepro');?>"><?php echo $recipe->output_description('form'); ?></textarea>
                </td>
            </tr>
        </table>
    </div>

    </div> <!-- /hide-on-preview -->
        <div id="post-form-buttons">
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
        <?php echo wp_nonce_field( 'post_submit', 'submitpost' ); ?>
    </form>
</div>