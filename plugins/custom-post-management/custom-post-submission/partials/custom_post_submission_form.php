        <input type="hidden" name="post_id" value="<?= $post_ID; ?>" />
        <div class="post-container post-title-container">      	
            <p>
                <h4 id="headline-title"><?php _e( 'Post Title', 'foodiepro' ); ?><?php if( in_array( $this->post_type . '_title_check', $required_fields ) ) echo '<span class="required-field">*</span>'; ?></h4>
                <input type="text" id="post_title" value="<?= isset( $_POST['post_title'] ) ? $_POST['post_title'] : $post->post_title;  ?>" size="20" name="post_title" />
            </p>
        </div>

<?php if( is_admin() ) { ?>
        <div class="post-author-container">
            <!-- <label for="post-author"><?php _e( 'Your name', 'foodiepro' ); ?><?php if( in_array( 'post-author', $required_fields ) ) echo '<span class="required-field">*</span>'; ?></label><br /> -->
            <h4 id="headline-author"><?php _e( 'Author', 'foodiepro' ); ?><?php if( in_array( 'post-author', $required_fields ) ) echo '<span class="required-field">*</span>'; ?></h4>
            <p>
                <input type="text" id="post-author" value="<?= isset( $_POST['post-author'] ) ? $_POST['post-author'] : $post->post_author;  ?>" size="50" name="post-author" />
            </p>
        </div>
<?php } ?>
        <div class="post-container post-image-container">     	
            <?php 
            // $image_url = CPM_Assets::get_post_image_url( $post );
            $image_url = get_the_post_thumbnail_url( $post->ID ,'mini-thumbnail');;
            ?> 
            <h4 id="headline-image"><?php _e( 'Featured image', 'foodiepro' ); ?><?php if( in_array( $this->post_type . '_thumbnail', $required_fields ) ) echo '<span class="required-field">*</span>'; ?></h4>
            <p class="post-guidelines"><?= __('Add here your best picture for this post, in order to attract visitors !', 'foodiepro'); ?></p>
            <div class="post-image thumbnail <?php if( !$image_url ) { ?>nodisplay<?php };?>">
                <img src="<?= $image_url; ?>" class="post_thumbnail" id="<?= $this->post_type; ?>_thumbnail_preview_" />
                <div class="<?= $this->post_type; ?>_remove_image_button <?php if( !$image_url ) { ?>nodisplay<?php };?>" id="post_thumbnail_remove_ ?>" title="<?php _e( 'Remove Image', 'foodiepro' ) ?>" /></div>
            </div>
            <div class="post-image input">
                <input class="post_thumbnail_image button" type="file" id="<?= $this->post_type; ?>_thumbnail_input_" value="" size="50" name="<?= $this->post_type; ?>_thumbnail" />
            </div>
        </div>
        <div class="post-container post-tags-container">
                <h4 id="headline-tags"><?php _e( 'Post Tags', 'foodiepro' ) ?></h4>
                <p>
                    <div class="taxonomy-select-spinner"><i class="fa fa-spinner fa-spin"></i></div>
                    <div class="taxonomy-select-boxes nodisplay">
                    <?= $this->display_taxonomies( $post_ID, $required_fields ); ?>
                    </div>
                </p>
        </div>
