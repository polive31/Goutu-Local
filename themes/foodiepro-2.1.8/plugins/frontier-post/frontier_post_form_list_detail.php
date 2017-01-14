<?php 



// list of users post based on current theme settings

global $fps_access_check_msg;
//Reset access message
$fps_access_check_msg = "";
			
$concat= get_option("permalink_structure")?"?":"&";    
//set the permalink for the page itself
$frontier_permalink = get_permalink();

$tmp_status_list = get_post_statuses( );

// Add future to list
$tmp_status_list['future'] = __("Future", "frontier-post");


$tmp_info_separator = " | ";

//Display before text from shortcode
if ( strlen($fpost_sc_parms['frontier_list_text_before']) > 1 )
	echo '<div id="frontier_list_text_before">'.$fpost_sc_parms['frontier_list_text_before'].'</div>';


// Dummy translation of ago for human readable time
$crap = __("ago", "frontier-post");


if (strlen(trim($fpost_sc_parms['frontier_add_link_text']))>0)
	$tmp_add_text = $fpost_sc_parms['frontier_add_link_text'];
else
	$tmp_add_text = __("Create New", "frontier-post")." ".fp_get_posttype_label_singular($fpost_sc_parms['frontier_add_post_type']);
		


//Display message
frontier_post_output_msg();



//*******************************************************************************************************
//  Quickpost
//*******************************************************************************************************

frontier_quickpost($fpost_sc_parms);

	

if( $user_posts->found_posts > 0 )
	{
	echo '<div class="frontier-post-list_form">';
	
	$status_groupby = array( 'draft' => 'Brouillons', 'pending' => 'En attente de relecture', 'publish' => 'Publiés'); 
	
	$userID = $current_user->ID;
	
foreach ($status_groupby as $key => $status_value) {
	
echo '<div class="frontier-post-status posts-' . $key . '">';
	echo '<h2>' . $status_value . '</h2>';
	
	$query = new WP_Query( array('cat'=>"9987" , 'post_status' => $key , 'author' => $userID) );
	
	if ( $query->have_posts() ) {
		while ($query->have_posts()) {
		$query->the_post();
				
		// only display private posts if author is current users
		if ($post->post_status == "private" && $current_user->ID != $post->post_author )
			continue;
		
		$tmp_status_class="frontier-post-list-status-".$post->post_status;
		
		?>
			
		<div class="frontier-list-item <?php echo $tmp_status_class; ?>">				
				
			<?php
			// show status if pending or draft
			//if ($post->post_status == "pending" || $post->post_status == "draft" || $post->post_status == "future")
			//	echo '<div class="frontier-status-header" id="'.$tmp_status_class.'">'.__("Status", "frontier-post").': '.(array_key_exists($post->post_status,$tmp_status_list) ? $tmp_status_list[$post->post_status] : $post->post_status).'</div>';
			?>
			
			<div class="frontier-list-item-content-wrapper">
			
			<div class="frontier-list-item-content" id="frontier-post-img">
				<?php the_post_thumbnail( array(50,50), array('class' => 'frontier-post-list-thumbnail') ); ?>
			</div>
			<div class="frontier-list-item-content" id="frontier-post-title">
				<a id="frontier-post-new-list-title-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</div>
			
					
				<?php
				
				echo '<div class="frontier-list-item-content" id="frontier-post-status">';
					//echo __("Status", "frontier-post").': '.( isset($tmp_status_list[$post->post_status]) ? $tmp_status_list[$post->post_status] : $post->post_status );
					//if ($post->post_status === 'future' )
						//echo " (".$post->post_date.")";
						
					//echo $tmp_info_separator;
					echo __("Author", "frontier-post").': ';
					the_author();
					
					// Show word count
					//echo $tmp_info_separator; 
					//echo __("Words", "frontier-post").": ".str_word_count( strip_tags( $post->post_content ) );
					
					// show publish date
					echo $tmp_info_separator; 
					if ($post->post_status === 'publish' )
						{
						printf( _x( 'Il y a %s', '%s = human-readable time difference', 'frontier-post' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); 
						echo $tmp_info_separator; 
						}
					
					$postlink = esc_url( get_permalink() )."#comments";
					echo '<a class="frontier-post-comment-link" id="frontier-query-comment-link" href="'.$postlink.'">'.frontier_get_icon('comments2').'&nbsp;'.intval($post->comment_count).'</a><br>';
					
					// get taxonomy information
					echo fp_get_tax_values($post->ID, $tmp_info_separator); 
					
				echo '</div>';
				
				/*
				echo frontier_get_icon('comments2').'&nbsp;'.intval($post->comment_count);
				
				
				echo $tmp_info_separator; 
				echo __("Categories", "frontier-post").': ';
				the_category(', '); 
				echo $tmp_info_separator; 
				echo __("Tags", "frontier-post").': ';
				the_tags(', '); 
				*/
				echo '<div class="frontier-list-item-content" id="frontier-post-actions">';
					echo frontier_post_edit_link($post, $fp_show_icons, $frontier_permalink);
					// Action modifiée pour appeler Gravity Forms en utilisant le plugin https://fr.wordpress.org/plugins/gravity-forms-post-updates/ 
					//echo '<a id="frontier-post-list-text-edit" class="frontier-post-list-text" href="' . apply_filters('gform_update_post/edit_url', $post->ID, get_permalink(8542)) . '"> Modifier </a>';
					echo frontier_post_approve_link($post, $fp_show_icons, $frontier_permalink);
					echo frontier_post_preview_link($post, $fp_show_icons, $frontier_permalink);
					echo frontier_post_delete_link($post, $fp_show_icons, $frontier_permalink);
					//echo frontier_post_display_links($post, $fp_show_icons, $frontier_permalink);
				echo '</div>';
				
				
				?>
					
			</div> 
		</div>
		
		<?php
		//echo '<hr>';
		
		} // end while have posts 
		
	} // end if query have post
	
	else {
		echo 'Aucun article dans cette catégorie.';
	}// end the Loop
	
echo '</div>'; // frontier-post-status header
} // end foreach
	
	echo '<div class="frontier-list-footer">';
	
		if ( fp_bool($fpost_sc_parms['frontier_pagination']) )
			{
			$pagination = paginate_links( 
				array(
					'base' => add_query_arg( 'pagenum', '%#%'),
					'format' => '',
					'prev_text' => __( '&laquo;', 'frontier-post' ),
					'next_text' => __( '&raquo;', 'frontier-post' ),
					'total' => $user_posts->max_num_pages,
					'current' => $pagenum,
					'add_args' => false  //due to wp 4.1 bug (trac ticket 30831)
					) 
				);

			//if ( $pagination ) 
			//	echo $pagination;
			if ( $pagination ) 
				{
				echo '<div id="frontier-post-pagination">'.$pagination.'</div>';
				}
			
			}
		if ( !fp_bool($fpost_sc_parms['frontier_list_all_posts']) )
			echo '<div class="frontier-post-count">'.__("Number of posts already created by you: ", "frontier-post").$user_posts->found_posts.'</div>';
	
		echo '</div>';
	
	echo '</div>';
	} // end if have posts
else
	{
		echo '<div class="frontier-post-count">';
		_e('Sorry, you do not have any posts (yet)', 'frontier-post');
		echo '</div>';
	} // end post count
	
//Re-instate $post for the page
wp_reset_postdata();

?>