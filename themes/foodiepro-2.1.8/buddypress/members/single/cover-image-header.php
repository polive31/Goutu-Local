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

$user_id =  get_current_user_id();

$member_cover_image_url = bp_attachments_get_attachment('url', array(
  'object_dir' => 'members',
  'item_id' => $user_id,
));

?>

<div id="cover-image-container">
	
		
	<?php
		$link=esc_url( bp_get_displayed_user_link() );
		$text = __('Update cover picture', 'foodiepro');
	?>
		
	<div id="item-header-cover-image">
		
		<div class="cover-image-link <?php echo $css; ?>"> 
		<a id="header-cover-image" href="<?php echo $link . 'profile/change-cover-image'; ?>">
			<img src="<?php echo $member_cover_image_url;?>">
		</a>
		</div>
		<div class="blog-title">
			<h1><?php echo xprofile_get_field_data( 'Titre de votre blog', $user_id ); ?>	</h1>
		</div>	
		<?php if ( bp_is_my_profile() ) { ?>
			<div class="overlay"><?php echo $text;?></div>
		<?php }	?>			
		
	</div><!-- #item-header-cover-image -->
			
		<div id="item-header-avatar">
			<?php 
			if ( bp_is_my_profile() ) {
				$text = __('Update profile picture', 'foodiepro');
			}
			?>			
			<a class="<?php echo $css; ?>" title="<?php echo $text;?>" href="<?php echo $link . 'profile/change-avatar'; ?>">	
				<?php bp_displayed_user_avatar( 'type=full' ); ?>
			</a>
			<div class="overlay"><?php echo $text;?></div>
			
		</div><!-- #item-header-avatar -->

		<div id="item-header-content">

			<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
				<h2 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h2>
			<?php endif; ?>

			<div id="item-buttons"><?php

				/**
				 * Fires in the member header actions section.
				 *
				 * @since 1.2.6
				 */
				do_action( 'bp_member_header_actions' ); ?></div><!-- #item-buttons -->

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

						<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>

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
