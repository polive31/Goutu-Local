<?php
/*
Template Name: Contact Form
*/
?>



<?php 

// add_action( 'wp_enqueue_scripts', 'enqueue_contact_script' );
function enqueue_contact_script() {
	$js_uri = CCF_URI . '/assets/js/';
	$js_path = CCF_PATH . '/assets/js/';
	custom_enqueue_script( 'contact-form', $js_uri, $js_path, 'contact-form.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
}

//If the form is submitted
if(isset($_POST['submitted'])) {

	//Check to see if the honeypot captcha field was filled in
	if (!CustomContactForm::pdscaptcha($_POST))
		$captchaError = true;
	
	//Check to make sure that the name field is not empty
	if(trim($_POST['contactName']) === '') {
		$nameError = __('You forgot to enter your name.', 'foodiepro');
		$hasError = true;
	} else
		$name = esc_attr(strip_tags($_POST['contactName']));
	
	//Get mail subject
	if(trim($_POST['ccfSubject']) === '') {
		$subjectError = __('You forgot to enter a subject.', 'foodiepro');
		$hasError = true;
	} else
		$subject = esc_attr(strip_tags($_POST['ccfSubject']));

	//Check to make sure sure that a valid email address is submitted
	if(trim($_POST['email']) === '')  {
		$emailError = __('You forgot to enter your email address.', 'foodiepro');
		$hasError = true;
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$emailError = __('You entered an invalid email address.','foodiepro');
		$hasError = true;
	} else
		$email = esc_attr(strip_tags($_POST['email']));
		
	//Check to make sure comments were entered	
	if(trim($_POST['comments']) === '') {
		$commentError = __('You forgot to enter your comments.','foodiepro');
		$hasError = true;
	} else
		$comments = esc_attr(strip_tags($_POST['comments']));
	
	//If there is no error, save the post and send notification
	if(!isset($hasError) && !isset($captchaError)) {
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
            $to = get_option( 'admin_email' );

            if( $to ) {
                $edit_link = admin_url( 'post.php?action=edit&post=' . $post_id );
				$mailSubject = sprintf( __('%s : Contact request from %s', 'foodiepro'), get_bloginfo('name'), $name );
				$headers = 'From: '. $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";
				$sendCopy = trim($_POST['sendCopy']);
				if ($sendCopy == 'true') 
					$header .= 'Cc:' . $email . "\r\n";
                $message = 'Here is the message content : ';
                $message .= "\r\n\r\n";
                $message .= $comments;
                $message .= "\r\n\r\n";
                $message .= 'Check out this contact request:' . $edit_link;
                wp_mail( $to, $mailSubject, $message, $headers );
                $emailSent = true;
            }
        }			
	}
} // Post Submitted ?>


<?php get_header(); ?>

	<?php if (have_posts()) : ?>
	
	<?php while (have_posts()) : the_post(); ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php the_content(); ?>
		
	<?php 
	if (isset($emailSent) && $emailSent == true) { 
	$homelink = do_shortcode('[wp-page-link target="home" text="' . __('Home page', 'foodiepro') . '"]');
		?>
		<p class="successbox">
			<?php echo sprintf( __('Thanks,%s','foodiepro'), $name); ?><br>
			<?php echo __('Message sent, we will answer you shortly.','foodiepro'); ?>
		</p>
		<p>
			<?php echo '<p>← ' . sprintf( __( 'Back to %s', 'foodiepro' ), $homelink ) . '<p>'; ?>
		</p> 			
	<?php }
	else {
		if (isset($hasError)) { ?>
			<p class="errorbox">
				<?php echo __('Please provide the required information.','foodiepro'); ?>
			</p>
		<?php } elseif (isset($captchaError)) {?>
			<p class="errorbox">
				<?php echo __('Please provide the correct operation result.','foodiepro'); ?>
			</p>
		<?php } ?>

		<form action="<?php the_permalink(); ?>" id="contactForm" method="post">

		<!-- <ul class="forms"> -->
			<div class="fieldset aligned" >
				<label for="contactName"><?php echo __('Name','foodiepro'); ?></label>
				<input type="text" name="contactName" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="requiredField" />
				<?php if($nameError != '') { ?>
					<span class="error"><?=$nameError;?></span> 
				<?php } ?>
			</div>
			
			<div class="fieldset aligned" >
				<label class="aligned" for="email">Email</label>
				<input type="text" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="requiredField email" />
				<?php if($emailError != '') { ?>
					<span class="error"><?=$emailError;?></span>
				<?php } ?>
			</div>
			
			<div class="fieldset aligned" >
				<label class="aligned" for="ccfSubject"><?php echo __('Subject','foodiepro'); ?></label>
				<input type="text" name="ccfSubject" id="ccfSubject" value="<?php if(isset($_POST['ccfSubject'])) echo $_POST['ccfSubject'];?>" class="requiredField" />
				<?php if($subjectError != '') { ?>
					<span class="error"><?=$subjectError;?></span> 
				<?php } ?>
			</div>

			<div class="fieldset textarea"><label for="commentsText"><?php echo __('Message','foodiepro'); ?></label>
				<textarea name="comments" id="commentsText" rows="10" cols="20" class="requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
				<?php if($commentError != '') { ?>
					<span class="error"><?=$commentError;?></span> 
				<?php } ?>
			</div>

			<div class="screenReader">
				<?php echo CustomContactForm::pdscaptcha("ask"); ?>
			</div>	

			<div class="fieldset inline"><input type="checkbox" name="sendCopy" id="sendCopy" value="true"<?php if(isset($_POST['sendCopy']) && $_POST['sendCopy'] == true) echo ' checked="checked"'; ?> /><label for="sendCopy"><?php echo __('Send a copy of this email to yourself','foodiepro'); ?></label>
			</div>				
				
			<div class="fieldset buttons"><input type="hidden" name="submitted" id="submitted" value="true" /><button type="submit"><?php echo __('Send Message', 'foodiepro'); ?></button>
			</div>
		<!-- </ul> -->
		</form>
	
			<?php } //Check successful submission ?> 
		<?php endwhile; ?>
	<?php endif; ?>


<?php get_footer(); ?>
	