<?php


class Custom_Recipe_Submission_Template extends Custom_Recipe_Templates {

	
	public function __construct() {

		/* Customize User Submission form */
		add_filter ( 'wpurp_user_submissions_form', array($this, 'wpurp_custom_submission_form_template'), 15, 2 );

	}

	public function wpurp_custom_submission_form_template( $form, $recipe ) {
		
		// HTML for complete submission form
		ob_start();
		include 'partials/submission-form-main.php';
	  $html = ob_get_contents();
	  ob_end_clean();
		return $html;

	}	


	public function add_helper_buttons( $form, $recipe ) {
		
		// HTML for Helper Buttons 
		ob_start();
		?>
		<div class="wpurp-user-submissions button-area">
		
		<!-- Recipe Timer Button -->
		<input class="user-submissions-button" id="add-timer" type="button" value="<?php _e('Format as Duration','foodiepro'); ?>" />
		<input class="user-submissions-button" id="add-ingredient" type="button" value="<?php _e('Format as Ingredient','foodiepro'); ?>" />
		
		<script type="text/javascript">
		jQuery(document).ready(function() {	
			console.log("In Buttons Script !");
			jQuery('.user-submissions-button').on('click', function() {
				console.log("Button Click Detected !");
				buttonType = 'timer';
				if (buttonType=="timer") {
	    		console.log("Add Recipe Timer");
				}
				else 
	    		console.log("Add Ingredient");
    	});
    });
		</script>

		</div>
		<?php
	  $html = ob_get_contents();
	  ob_end_clean();

		$html .= $form;
		
		return $html;
	}
	
}