<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomContactFormShortcode extends CustomContactForm {

	const LINEBREAK = '%0D%0A%0D%0A';

	public function __construct() {
		// add_action( 'wp_enqueue_scripts', array($this, 'enqueue_contact_scripts' ) );
		add_shortcode( 'custom-contact-form', array($this, 'output_contact_form' ) );			
	}

	// public function enqueue_contact_scripts() {
	// 	global $post;
	// 	if ( !has_shortcode( $post->post_content, 'custom-contact-form'  ) ) return;

	// 	// $js_uri = CCF_URI . '/assets/js/';
	// 	// $js_path = CCF_PATH . '/assets/js/';
	// 	// custom_enqueue_script( 'contact-form', $js_uri, $js_path, 'contact-form.js', array( 'jquery' ), CHILD_THEME_VERSION, true);

	// 	if (class_exists('CustomGoogleRecaptcha'))
	// 		new CustomGoogleRecaptcha(); // makes sure that the related plugin assets are loaded

	// }

	public function output_contact_form ( $atts ) {
		$atts = shortcode_atts( array(
	        // 'arg' => 'value',
	    ), $atts );

		// wp_enqueue_script( 'g-recaptcha' );
		CustomGoogleRecaptcha::enqueue_scripts();

	    ob_start();
	    $this->ccf_output_form();
	    $out = ob_get_contents();
	    ob_clean();
	    return $out;
    }    	

	public function ccf_output_form() {

		if (class_exists('CustomGoogleRecaptcha'))
			$gCaptcha = new CustomGoogleRecaptcha(); 
		else
			$gCaptcha = false;

		$hasError = false;
		$captchaError = false;

		// FORM SUBMISSION
		//If the form is submitted
		if(isset($_POST['submitted'])) {

			//Check to make sure that the name field is not empty
			if(trim($_POST['contactName']) === '') {
				$nameMissing = __('You forgot to enter your name.', 'foodiepro');
				$hasError = true;
			} else
				$name = esc_attr(strip_tags($_POST['contactName']));
			
			//Get mail subject
			// if(trim($_POST['ccfSubject']) === '') {
			// 	$subjectError = __('You forgot to enter a subject.', 'foodiepro');
			// 	$hasError = true;
			// } else
				$subject = esc_attr(strip_tags($_POST['ccfSubject']));

			//Check to make sure sure that a valid email address is submitted
			if(trim($_POST['email']) === '')  {
				$emailMissing = __('You forgot to enter your email address.', 'foodiepro');
				$hasError = true;
			} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$emailMissing = __('You entered an invalid email address.','foodiepro');
				$hasError = true;
			} else
				$email = esc_attr(strip_tags($_POST['email']));
				
			//Check to make sure comments were entered	
			if(trim($_POST['comments']) === '') {
				$commentMissing = __('You forgot to enter your comments.','foodiepro');
				$hasError = true;
			} else
				$comments = esc_attr(strip_tags($_POST['comments']));

			//Check to make sure privacy notice was accepted	
			if(!isset($_POST['privacyNotice'])) {
				$privacyMissing = __('You must accept the privacy notice for this form to be submitted.','foodiepro');
				$hasError = true;
			};	

			// Check Captcha
			if ($gCaptcha) {
				$captchaError = ($gCaptcha->verify() != 'success');
			}
			else {
				$captchaError = !( CustomContactForm::pdscaptcha($_POST) );
			}
			if ($captchaError) {	
				$captchaMissing = __('Please complete the captcha verification.','foodiepro');;
				$hasError=true;
			}

			if(!$hasError) {
				// Submit Custom Contact Post
				$args = array(
					'post_title' => $subject,
					'post_type' => 'contact',
					'post_content' => $comments,
					'post_status' => 'pending'
				);
				$post_id = wp_insert_post($args);

				if($post_id) {
					// Update Post Meta
					update_post_meta($post_id, 'ccf_name', $name);
					update_post_meta($post_id, 'ccf_email', $email);

					// Send Mail
		            // $to = get_option( 'admin_email' );
		            $to = get_option( 'contact_email' );

		            if( $to ) {
		                $edit_link = admin_url( 'post.php?action=edit&post=' . $post_id );
						$mailSubject = sprintf( __('%s : Contact request from %s', 'foodiepro'), get_bloginfo('name'), $name );
						$headers = 'From: '. $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";
						$sendCopy = trim($_POST['sendCopy']);
						if ($sendCopy == 'true') 
							$headers .= 'Bcc:' . $email . "\r\n";

		                // $message .= "\r\n\r\n";
		                $message .= self::LINEBREAK;
		                $message .= 'Here is the message content : ';
		                $message .= $comments;
		                // $message .= "\r\n\r\n";
		                $message .= self::LINEBREAK;
		                // $message .= 'Sent by : <a href="mailto:' . $email . '">' . $name . '</a>' . "\r\n";
		                $message .= 'Sent by : <a href="mailto:' . $email . '">' . $name . '</a>' . self::LINEBREAK;
		                $message .= 'Check out this contact request:' . $edit_link;
		                wp_mail( $to, $mailSubject, $message, $headers );
		                $emailSent = true;
		            }
		        }			
			}
		} // Post Submitted 

		// FORM DISPLAY 
		if (isset($emailSent) && $emailSent == true) { 
			$homelink = do_shortcode('[wp-page-link target="home" text="' . __('Home page', 'foodiepro') . '"]');
				?>
				<p class="successbox">
					<?php echo sprintf( __('Thanks %s,','foodiepro'), $name); ?><br>
					<?php //echo sprintf( __('Message sent to %s, we will answer you shortly.','foodiepro'), $to); ?>
					<?php echo sprintf( __('Your message was sent, we will answer you shortly.','foodiepro'), $to); ?>
				</p>
				<p>
					<?php echo '<p>← ' . sprintf( __( 'Back to %s', 'foodiepro' ), $homelink ) . '<p>'; ?>
				</p> 			
		<?php 
			return;	
		}
		if ($hasError) { ?>
			<p class="errorbox">
				<?php echo __('Please provide the required information.','foodiepro'); ?>
			</p>
		<?php } 
		else { ?>
			<p>
				Vous souhaitez poser une question ou faire une suggestion ? <br>
				N'hésitez pas à nous envoyer un message en renseignant les informations ci-dessous !
			</p>	
		<?php }?>

		<form action="<?php the_permalink(); ?>" id="contactForm" method="post">

		<!-- <ul class="forms"> -->
			<p class="fieldset inline"><?php echo __('Fields marked with an asterisk (<span class="error">*</span>) are mandatory.', 'foodiepro'); ?></p>

			<div class="fieldset aligned" >
				<label for="contactName" class="requiredField"><?php echo __('Name','foodiepro'); ?></label>
				<input type="text" name="contactName" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" />
				<?php if($nameMissing != '') { ?>
					<span class="error"><?=$nameMissing;?></span> 
				<?php } ?>
			</div>
			
			<div class="fieldset aligned" >
				<label class="aligned requiredField" for="email">Email</label>
				<input type="text" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="requiredField email" />
				<?php if($emailMissing != '') { ?>
					<span class="error"><?=$emailMissing;?></span>
				<?php } ?>
			</div>
			
			<div class="fieldset aligned" >
				<label class="aligned" for="ccfSubject"><?php echo __('Subject','foodiepro'); ?></label>
				<input type="text" name="ccfSubject" id="ccfSubject" value="<?php if(isset($_POST['ccfSubject'])) echo $_POST['ccfSubject'];?>" />
				<?php if($subjectMissing != '') { ?>
					<span class="error"><?=$subjectMissing;?></span> 
				<?php } ?>
			</div>

			<div class="fieldset textarea">
				<div class="clearfix">
					<label for="commentsText" class="requiredField"><?php echo __('Message','foodiepro'); ?></label>
				<?php if($commentMissing != '') { ?>
					<span class="error"><?=$commentMissing;?></span> 
				<?php } ?>
				</div>	
				<textarea name="comments" id="commentsText" rows="10" cols="20" ><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
			</div>

			<div class="fieldset inline">
			<?php 
			if ($gCaptcha) {
				$gCaptcha->display( 'alignleft' );
			}
			else {
				echo CustomContactForm::pdscaptcha("ask");
			}
			?>	
			<?php if ($captchaMissing != '') { ?>
				<span class="error"><?=$captchaMissing;?></span> 
			<?php } ?>
			</div>

			<div class="fieldset inline"><input type="checkbox" name="sendCopy" id="sendCopy" value="true" <?php if(isset($_POST['sendCopy']) && $_POST['sendCopy'] == true) echo 'checked'; ?> /><label for="sendCopy"><?php echo __('Send a copy of this email to yourself','foodiepro'); ?></label>
			</div>		

			<div class="fieldset inline"><input type="checkbox" name="privacyNotice" id="privacyNotice" value="true" <?php if(isset($_POST['privacyNotice']) && $_POST['privacyNotice'] == true) echo 'checked'; ?> /><label for="privacyNotice" class="requiredField"><?php echo __('I accept that the data entered on this form is stored on the present website.','foodiepro'); ?></label>
			<?php if($privacyError != '') { ?>
				<span class="error">  <?=$privacyError;?></span> 
			<?php } ?>										
			</div>	
				
			<div class="fieldset buttons"><input type="hidden" name="submitted" id="submitted" value="true" /><button type="submit"><?php echo __('Send Message', 'foodiepro'); ?></button>
			</div>


		<!-- </ul> -->
		</form>
		
		<?php 
		// } //Check successful submission 

	}


}	

	