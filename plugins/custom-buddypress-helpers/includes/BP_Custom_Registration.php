<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =================================================================*/
/* =            REGISTRATION
/* =================================================================*/


class BP_Custom_Registration {


	public function __construct() {
		// $this->bp_register_set_full_layout();
		// add_action( 'wp', array($this, 'bp_register_set_full_layout' ) );
		// add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

		add_action( 'bp_before_registration_submit_buttons', array($this, 'add_privacy_statement'), 11 );
		add_action( 'bp_signup_validate', array($this, 'add_signup_validate') );

	}


	// // Apply Full Width Content to registration page
	// public function bp_register_set_full_layout() {
	// 	if ( !bp_is_register_page() ) return;
	// }

	public function add_privacy_statement() {?>
		<div class="bp-reg-field">
			<?php do_action( 'bp_privacy_policy_errors' ); ?>
			<label for="agree_to_privacy_policy" class="required-field"> Protection de la vie privée </label>
			<div class="alignleft">
			<!-- <div class="alignleft legal-mention"> -->
			<input type="checkbox" name="agree_to_privacy_policy">
			<?php echo __('On submitting this form, I agree that my email address be used by <a href="goutu.org">goutu.org</a> for contacting me, and that my first name, sex and pseudonym be visible to all visitors of this website.', 'foodiepro'); ?>
			</div>
		</div>
		<?php
	}

	public function add_signup_validate() {
		global $bp;
		if ( ! isset( $_POST['agree_to_privacy_policy'] ) || $_POST['agree_to_privacy_policy'] !== 'on' ) {
		  $bp->signup->errors['privacy_policy'] = __('Please confirm that you agree with our privacy policy','foodiepro');
		}
	}


}
