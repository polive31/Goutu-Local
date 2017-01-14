<?php
/**
 * BuddyPress - Users Profile
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php // Prevents profile actions buttons if public profile 
//if ( bp_current_action() != 'public' ) { 
if ( bp_is_my_profile() ) { 
	echo '<div class="item-list-tabs no-ajax" id="subnav" role="navigation">';
		echo '<ul>';
			bp_get_options_nav();
		echo '</ul>';
	echo '</div><!-- .item-list-tabs -->';
} ?>


	<?php

	/**
	 * Fires before the display of member profile content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_profile_content' ); ?>

	<div class="profile">

	<!-- <h1> Je suis dans /www/wp-content/themes/foodiepro-2.1.8/buddypress/members/single/profile.php <h1>  -->

	<?php switch ( bp_current_action() ) :

		// Edit
		case 'edit'   :
			bp_get_template_part( 'members/single/profile/edit' );
			break;

		// Change Avatar
		case 'change-avatar' :
			bp_get_template_part( 'members/single/profile/change-avatar' );
			break;

		// Change Cover Image
		case 'change-cover-image' :
			bp_get_template_part( 'members/single/profile/change-cover-image' );
			break;

		// Compose
		case 'public' :
		
			//echo '<h1> Je suis dans /www/wp-content/themes/foodiepro-2.1.8/buddypress/members/single/profile.php => section "public" </h1>'; 


			// Display XProfile
			if ( bp_is_active( 'xprofile' ) )
				bp_get_template_part( 'members/single/profile/profile-loop' );

			// Display WordPress profile (fallback)
			else
				bp_get_template_part( 'members/single/profile/profile-wp' );
			break;


		// Any other
		//echo '<h1> Je suis dans /www/wp-content/themes/foodiepro-2.1.8/buddypress/members/single/profile.php => section "default" <h1>';  
		default :
			bp_get_template_part( 'members/single/plugins' );
			break;
	endswitch; ?>
	</div><!-- .profile -->


	
<?php
/**
 * Fires after the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_profile_content' ); ?>
