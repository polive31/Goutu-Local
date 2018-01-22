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
			add_action('wp_footer','show_uploaded_cover_image'); // too late for header, adds Javascript code into footer
			bp_get_template_part( 'members/single/profile/change-cover-image' );
			break;

		// Compose
		case 'public' :?>
		
			<div class="blog-title"><?php echo wpautop(xprofile_get_field_data( 'Description du blog', bp_displayed_user_id() ) ); ?>	</div>
			
			<?php
			// Displays widgeted public profile content
	    genesis_widget_area( 'social-content', array(
	        'before' => '<div class="social-content widget-area">',
	        'after'  => '</div>',
	  	));
	  		  	
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
do_action( 'bp_after_profile_content' ); 


//* Hook category widget area after profile content
add_action( 'genesis_before_footer', 'add_social_footer_widgeted_area', 1 );
function add_social_footer_widgeted_area() {
  genesis_widget_area( 'social-footer', array(
      'before' => '<div class="social-footer widget-area">',
      'after'  => '</div>',
	));
}


//* Display Uploaded Cover Image following Ajax call completion
function show_uploaded_cover_image() {
	?>
	<script type="text/javascript">
		bp.CoverImage.Attachment.on( 'change:url', function( data ) {
			url = data.attributes['url'];
			jQuery("picture#cover-image img").attr('src', url);
		} );
	</script>
	<?php
}
?>

