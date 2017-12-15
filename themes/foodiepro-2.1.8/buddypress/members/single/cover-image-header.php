<?php
/**
 * BuddyPress - Users Cover Image Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php

/**
 * Fires before the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_member_header' ); 

$user_id =  bp_displayed_user_id();
$member_cover_image_url = bp_attachments_get_attachment('url', array(
  'object_dir' => 'members',
  'item_id' => $user_id,
));	
$url=esc_url( bp_get_displayed_user_link() );
$url_cover = $url . 'profile/change-cover-image';
$url_avatar = $url . 'profile/change-avatar';
$cover_text = __('Update cover picture', 'foodiepro');
$avatar_text = __('Update profile picture', 'foodiepro');
$overlay_css = bp_is_my_profile()?'class="overlay"':'class="hidden"';
$a_css = bp_is_my_profile()?'':'class="disabled"';

?>

<div id="cover-image-container">
		
	<div id="item-header-cover-image">
		<a <?php echo $a_css;?> id="header-cover-image" href="<?php echo $url_cover; ?>">
			<div <?php echo $overlay_css;?> id="cover">
				<div class="overlay-text"><?php echo $cover_text;?></div>
			</div>
			<img src="<?php echo $member_cover_image_url;?>">
			<h1 class="blog-title"><?php echo xprofile_get_field_data( 'Titre de votre blog', $user_id ); ?>	</h1>
			<div id="userid-container">
				<div id="item-userid">
					<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
						<div id="intro"><?php echo __('The Blog Of ','foodiepro');?></div>
						<h2 class="user-nicename"><?php bp_displayed_user_mentionname(); ?></h2>
					<?php endif; ?>
				</div>
			</div>
		</a>
	</div><!-- #item-header-cover-image -->
			

	<div id="item-header-avatar">
		<a <?php echo $a_css;?> href="<?php echo $url_avatar; ?>">	
			<div <?php echo $overlay_css;?> id="avatar">
				<div class="overlay-text"><?php echo $avatar_text;?></div>
			</div>
			<?php bp_displayed_user_avatar( 'type=full' ); ?>
		</a>	
	</div><!-- #item-header-avatar -->


		<div id="item-header-content">

			<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

			<?php

			/**
			 * Fires before the display of the member's header meta.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_before_member_header_meta' ); ?>

			<div id="item-meta">

				<?php if ( bp_is_active( 'activity' ) ) : ?>

					<div id="latest-update">

						<?php //bp_activity_latest_update( bp_displayed_user_id() ); ?>

					</div>

				<?php endif; ?>

				<?php

				 /**
				  * Fires after the group header actions section.
				  *
				  * If you'd like to show specific profile fields here use:
				  * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
				  *
				  * @since 1.2.0
				  */
				 do_action( 'bp_profile_header_meta' );

				 ?>

			</div><!-- #item-meta -->		

		</div><!-- #item-header-content -->

</div><!-- #cover-image-container -->

<div class="item-list-tabs no-ajax" id="subnav"><?php
	/**
	 * Fires in the member header actions section.
	 *
	 * @since 1.2.6
	 */
	do_action( 'bp_member_header_actions' ); ?>				
</div><!-- #item-buttons -->		


<?php 	
if ( bp_is_my_profile() ) { 
	
//	echo '<div class="item-list-tabs" id="main">';
//	echo '</div><!-- .item-list-tabs#main -->';

//	echo "<pre>";
//	print_r(buddypress()->members->nav);	
//	echo "</pre>";
	
	echo '<div class="item-list-tabs no-ajax" id="subnav" role="navigation">';
		echo '<ul>';
			echo '<li class="disabled" id="main-menu-item">';
				bp_display_current_user_nav();
			echo '</li>';
			bp_get_options_nav();
		echo '</ul>';
	echo '</div><!-- .item-list-tabs#subnav -->';
	
} ?>		

<?php

/**
 * Fires after the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_member_header' ); ?>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
do_action( 'template_notices' ); ?>

<?php
/**
 * Render the navigation markup for the displayed user.
 *
 * @since 1.1.0
 */
function bp_display_current_user_nav() {
	$bp = buddypress();

	foreach ( $bp->members->nav->get_primary() as $user_nav_item ) {
		if ( empty( $user_nav_item->show_for_displayed_user ) && ! bp_is_my_profile() ) {
			continue;
		}

		$selected = '';
		if ( bp_is_current_component( $user_nav_item->slug ) ) {
			$selected = ' class="current selected"';

			if ( bp_loggedin_user_domain() ) {
				$link = str_replace( bp_loggedin_user_domain(), bp_displayed_user_domain(), $user_nav_item->link );
			} else {
				$link = trailingslashit( bp_displayed_user_domain() . $user_nav_item->link );
			}

			/**
			 * Filters the navigation markup for the displayed user.
			 *
			 * This is a dynamic filter that is dependent on the navigation tab component being rendered.
			 *
			 * @since 1.1.0
			 *
			 * @param string $value         Markup for the tab list item including link.
			 * @param array  $user_nav_item Array holding parts used to construct tab list item.
			 *                              Passed by reference.
			 */
			
			//echo '<h2 id="' . $user_nav_item->css_id . '-personal-li" ' . $selected . '><a id="user-' . $user_nav_item->css_id . '" href="' . $link . '">' . $user_nav_item->name . '</a></h2>';
			echo '<a id="user-' . $user_nav_item->css_id . '" href="' . $link . '">' . $user_nav_item->name . '</a>';
			//echo apply_filters_ref_array( 'bp_get_displayed_user_nav_' . $user_nav_item->css_id, array( '<h2 id="' . $user_nav_item->css_id . '-personal-li" ' . $selected . '><a id="user-' . $user_nav_item->css_id . '" href="' . $link . '">' . $user_nav_item->name . '</a></h2>', &$user_nav_item ) );
		}
	}
}
