<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomContactFormShortcode extends ContactFormPostType {

	public function __construct() {	
		//add_action( 'wp_loaded', array($this,'hydrate'));		
		parent::__construct();
		add_action('admin_post_ccf_submission', array($this, 'ccf_submission_process'));
		add_action('admin_post_nopriv_ccf_submission', array($this, 'ccf_submission_process'));
		// add_action( 'wp_enqueue_scripts', array($this, 'add_recaptcha'));
		add_shortcode('contact-form', array($this, 'custom_create_post_form'));
	}
	
	public function add_recaptcha() {
		wp_enqueue_script('google_recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '', true);
	}

	public function custom_create_post_form() {
		ob_start(); 
		if(isset($_GET['post'])) {
			switch($_GET['post']) {
				case 'successfull':
					echo '<p class="successbox">' . __('Message sent, we will answer you shortly.', 'foodiepro') . '</p>';
					return;
				case 'required_missing' :
					echo '<p class="errorbox">' . __('Please fill in all required fields.', 'foodiepro') . '</p>';
					break;
				case 'captcha_failed' :
					echo '<p class="errorbox">' . __('Please enter the correct response.', 'foodiepro') . '</p>';
					break;					
			}
		}
		?>
		<form id="custom-contact-form" action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
			<input type="hidden" name="action" value="ccf_submission" />

			<label for="contact_name"><?php echo __('Name', 'foodiepro') . '<span class="required_field">*</span>'; ?></label>
			<input name="contact_name" id="contact_name" type="text"/>

			<label for="contact_email" class="required"><?php echo __('Email Address', 'foodiepro') . '<span class="required_field">*</span>'; ?></label>
			<input name="contact_email" id="contact_email" type="email"/>

			<label for="contact_subject" class="required"><?php echo __('Subject', 'foodiepro'); ?></label>
			<input name="contact_subject" id="contact_subject" type="text"/>

			<label for="contact_msg" class="required"><?php echo __('Message', 'foodiepro') . '<span class="required_field">*</span>'; ?></label>
			<textarea rows="10" name="contact_msg" id="contact_msg"></textarea>

			<label for="contact_captcha" class="required">2+5=?</label>
			<input name="contact_captcha" id="contact_captcha" type="text"/>	

			<!-- Google Recaptcha -->
			<!-- <div class="g-recaptcha" data-sitekey="6LeIb84SAAAAALIrAdEQoV5GUsuc5WzMfP4Z5ctc"></div> -->

			<?php wp_nonce_field('contact_form_submit', 'ccf_nonce'); ?>
			<input type="submit" name="contact_submit" value="<?php _e('Send Message', 'foodiepro'); ?>"/>

		</form>


		<?php 
		return ob_get_clean();
	}
	 
	public function ccf_submission_process() {
	 
		if ((isset($_POST['ccf_nonce'])) && wp_verify_nonce($_POST['ccf_nonce'], 'contact_form_submit')) {
			
			if(strlen(trim($_POST['contact_name'])) < 1 || strlen(trim($_POST['contact_email'])) < 1 || strlen(trim($_POST['contact_msg'])) < 1 ) {
				$redirect = add_query_arg('post', 'required_missing', home_url($_POST['_wp_http_referer']));
			} 
			elseif ( trim($_POST['contact_captcha']) != '7') {
				$redirect = add_query_arg('post', 'captcha_failed', home_url($_POST['_wp_http_referer']));
			}
			else {		
				$args = array(
					'post_title' => esc_attr(strip_tags($_POST['contact_subject'])),
					'post_type' => 'contact',
					'post_content' => esc_attr(strip_tags($_POST['contact_msg'])),
					'post_status' => 'pending'
				);
				$post_id = wp_insert_post($args);
	 
				if($post_id) {
					$name = esc_attr(strip_tags($_POST['contact_name']));
					$email = esc_attr(strip_tags($_POST['contact_email']));
					update_post_meta($post_id, 'ccf_name', $name);
					update_post_meta($post_id, 'ccf_email', $email);
					$redirect = add_query_arg('post', 'successfull', home_url($_POST['_wp_http_referer']));

					//php mailer variables
	                $to = get_option( 'admin_email' );
	                if( $to ) {
	                    $edit_link = admin_url( 'post.php?action=edit&post=' . $post_id );
						$subject = sprintf( __('%s : Contact request from %s', 'foodiepro'), get_bloginfo('name'), $name );
						$headers = 'From: '. $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";
	                    $message = 'Here is the message content : ';
	                    $message .= "\r\n\r\n";
	                    $message .= esc_attr(strip_tags($_POST['contact_msg']));
	                    $message .= "\r\n\r\n";
	                    $message .= 'Go to this contact request:' . $edit_link;

	                    wp_mail( $to, $subject, $message, $headers );
	                }


				}
			}
			wp_redirect($redirect); 
			exit;
		}
	}


}
