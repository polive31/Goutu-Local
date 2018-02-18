<?php
/**
 * BuddyPress - Members Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<div id="buddypress">


<!-- <h1>Je suis dans /www/wp-content/themes/foodiepro-2.1.8/buddypress/members/single/home.php</h1>-->

<?php //if ( is_user_logged_in()  ) : 
?> 

	<?php

	/**
	 * Fires before the display of member home content.
	 *
	 * @since 1.2.0
	 */
	//do_action( 'bp_before_member_home_content' ); ?>

	<!--

	<div id="item-header" role="complementary">

		<?php
		/**
		 * If the cover image feature is enabled, use a specific header
		 */

		// if (!bp_is_home() ) : /* Do not dispay header if my profile */
		// 	if ( bp_displayed_user_use_cover_image_header() ) :
		// 		//echo '<h1>Appel à cover-image-header</h1>';
		// 		bp_get_template_part( 'members/single/cover-image-header' );
		// 	else :
		// 		bp_get_template_part( 'members/single/member-header' );
		// 	endif;
		// endif;
			?>

	</div>

	-->

	<!-- #item-header -->

	<!-- <div class="item-list-tabs no-ajax" id="object-nav">
	  <ul>
	   <?php //bp_get_displayed_user_nav(); ?>
	   <?php //do_action( 'bp_member_options_nav' ); ?>
	  </ul>
	</div>-->

	<div id="item-body">

		<?php

		/**
		 * Fires before the display of member body content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_before_member_body' );

		if ( bp_is_user_front() ) :
			bp_displayed_user_front_template_part();

		elseif ( bp_is_user_activity() ) :
			bp_get_template_part( 'members/single/activity' );

		elseif ( bp_is_user_blogs() ) :
			bp_get_template_part( 'members/single/blogs'    );

		elseif ( bp_is_user_friends() ) :
			bp_get_template_part( 'members/single/friends'  );

		elseif ( bp_is_user_groups() ) :
			bp_get_template_part( 'members/single/groups'   );

		elseif ( bp_is_user_messages() ) :
			bp_get_template_part( 'members/single/messages' );

		elseif ( bp_is_user_profile() ) :
			bp_get_template_part( 'members/single/profile'  );

		elseif ( bp_is_user_forums() ) :
			bp_get_template_part( 'members/single/forums'   );

		elseif ( bp_is_user_notifications() ) :
			bp_get_template_part( 'members/single/notifications' );

		elseif ( bp_is_user_settings() ) :
			bp_get_template_part( 'members/single/settings' );

		// If nothing sticks, load a generic template
		else :
			bp_get_template_part( 'members/single/plugins'  );

		endif;

		/**
		 * Fires after the display of member body content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_after_member_body' ); ?>

	</div><!-- #item-body -->

	
<?php //else : ?>
	<?php //echo '<p> Vous devez être <a href="http://goutu.org/wp-login.php">connecté</a> pour consulter les profils des utilisateurs.</p>'; ?>
	<?php //echo '<p>Pas encore inscrit ? <a href="http://goutu.org/gestion-utilisateurs/bp-register-captcha">N\'hésitez pas</a>, c\'est rapide et gratuit !</p>'; ?>
<?php //endif; ?>



	<?php

	/**
	 * Fires after the display of member home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_member_home_content' ); ?>
	

</div><!-- #buddypress -->
