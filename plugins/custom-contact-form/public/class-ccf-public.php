<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class CCF_Public
{

	const LINEBREAK = '%0D%0A%0D%0A';

	private static $CCF_PATH;
	private static $CCF_URI;

	public function __construct() {
		self::$CCF_PATH = plugin_dir_path(dirname(__FILE__));
		self::$CCF_URI = plugin_dir_url(dirname(__FILE__));
	}

	public function ccf_shortcode($atts)
	{
		$atts = shortcode_atts(array(
			// 'arg' => 'value',
		), $atts);

		if (class_exists('Custom_Google_Recaptcha')) {
			$gCaptcha = new CGR_Public();
			$gCaptcha->enqueue_scripts();
		} else
			$gCaptcha = false;

		ob_start();
		$this->ccf_output_form($gCaptcha);
		$out = ob_get_contents();
		ob_clean();
		return $out;
	}

	public function ccf_output_form($gCaptcha)
	{

		$hasError = false;
		$captchaError = false;

		// FORM SUBMISSION
		//If the form is submitted
		if (isset($_POST['submitted'])) {

			//Check to make sure that the name field is not empty
			if (trim($_POST['contactName']) === '') {
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
			if (trim($_POST['email']) === '') {
				$emailMissing = __('You forgot to enter your email address.', 'foodiepro');
				$hasError = true;
			} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$emailMissing = __('You entered an invalid email address.', 'foodiepro');
				$hasError = true;
			} else
				$email = esc_attr(strip_tags($_POST['email']));

			//Check to make sure comments were entered
			if (trim($_POST['comments']) === '') {
				$commentMissing = __('You forgot to enter your comments.', 'foodiepro');
				$hasError = true;
			} else
				$comments = esc_attr(strip_tags($_POST['comments']));

			//Check to make sure privacy notice was accepted
			if (!isset($_POST['privacyNotice'])) {
				$privacyMissing = __('You must accept the privacy notice for this form to be submitted.', 'foodiepro');
				$hasError = true;
			};

			// Check Captcha
			if ($gCaptcha) {
				$captchaError = ($gCaptcha->verify() != 'success');
			} else {
				$captchaError = !($this->pdscaptcha($_POST));
			}
			if ($captchaError) {
				$captchaMissing = __('Please complete the captcha verification.', 'foodiepro');;
				$hasError = true;
			}

			if (!$hasError) {
				// Submit Custom Contact Post
				$args = array(
					'post_title' => $subject,
					'post_type' => 'contact',
					'post_content' => $comments,
					'post_status' => 'pending'
				);
				$post_id = wp_insert_post($args);

				if ($post_id) {
					// Update Post Meta
					update_post_meta($post_id, 'ccf_name', $name);
					update_post_meta($post_id, 'ccf_email', $email);

					// Send Mail
					// $to = get_option( 'admin_email' );
					$to = get_option('contact_email');

					if ($to) {
						$edit_link = admin_url('post.php?action=edit&post=' . $post_id);
						$mailSubject = sprintf(__('%s : Contact request from %s', 'foodiepro'), get_bloginfo('name'), $name);
						$headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";
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
						wp_mail($to, $mailSubject, $message, $headers);
						$emailSent = true;
					}
				}
			}
		} // Post Submitted

		// FORM DISPLAY
		if (isset($emailSent) && $emailSent == true) {
			$homelink = do_shortcode('[permalink wp="home" text="' . __('Home page', 'foodiepro') . '"]');
			?>
			<p class="successbox">
				<?php echo sprintf(__('Thanks %s,', 'foodiepro'), $name); ?><br>
				<?php //echo sprintf( __('Message sent to %s, we will answer you shortly.','foodiepro'), $to);
							?>
				<?php echo sprintf(__('Your message was sent, we will answer you shortly.', 'foodiepro'), $to); ?>
			</p>
			<p>
				<?php echo '<p>← ' . sprintf(__('Back to %s', 'foodiepro'), $homelink) . '<p>'; ?>
			</p>
		<?php
					return;
				}
				if ($hasError) { ?>
			<p class="errorbox">
				<?php echo __('Please provide the required information.', 'foodiepro'); ?>
			</p>
		<?php } else { ?>
			<p>
				Vous souhaitez poser une question ou faire une suggestion ? <br>
				N'hésitez pas à nous envoyer un message en renseignant les informations ci-dessous !
			</p>
		<?php }

		include(self::$CCF_PATH . 'templates/contact-form.php' );
	}


	/*pds_captcha.php - un captcha mathématique bidouillé par passeurs de savoirs<br>
	plus d'infos sur http://passeurs-de-savoirs.fr/lab/lab2015/captcha-math.php
	*/
	public function pdscaptcha($step)
	{
		if ($step == "ask") {
			$msg = __('For security reasons, and to avoid spam, please solve the following operation : ', 'foodiepro');
			$tchiffres = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
			$tlettres = array(
				__('zero', 'foodiepro'),
				__('one', 'foodiepro'),
				__('two', 'foodiepro'),
				__('three', 'foodiepro'),
				__('four', 'foodiepro'),
				__('five', 'foodiepro'),
				__('six', 'foodiepro'),
				__('seven', 'foodiepro'),
				__('eight', 'foodiepro'),
				__('nine', 'foodiepro'),
				__('ten', 'foodiepro'),
				__('eleven', 'foodiepro'),
				__('twelve', 'foodiepro')
			);
			$premier = rand(0, count($tchiffres) - 1);
			$second = rand(0, count($tchiffres) - 1);

			if ($second <= $premier) {
				$resultat = $tchiffres[$premier] - $tchiffres[$second];
				$operation = "Combien font " . $tlettres[$premier] . " moins " . $tlettres[$second] . " (en chiffres) ?";
			} else if ($second > $premier) {
				$resultat = $tchiffres[$second] - $tchiffres[$premier];
				$operation = "Combien font " . $tlettres[$second] . " moins " . $tlettres[$premier] . " (en chiffres) ?";
			} else {
				$resultat = $tchiffres[$premier] + $tchiffres[$second];
				$operation = "Combien font " . $tlettres[$premier] . " plus " . $tlettres[$second] . " (en chiffres) ?";
			}
			// echo 'resultat de reference avant md5 : ' . $resultat . "<br>";
			$resultat = md5($resultat);
			// echo 'resultat de reference après md5 : ' . $resultat . "<br>";
			$o = "";
			foreach (str_split(utf8_decode($operation)) as $obj) {
				$o .= "&#" . ord($obj) . ";";
			}

			$html = '<p><label for="reponsecap">' . $msg;
			$html .= '<span class="mathquestion">' . $o . '</span></label>';
			$html .= '<input type="text" name="reponsecap" value="" />';
			$html .= '<input name="reponsecapcode" type="hidden" value="' . $resultat . '" /></p>';
			return $html;
		} else {
			// echo 'reponse utilisateur' . $step["reponsecap"] . "<br>";
			// echo 'MD5 de reference' . $step["reponsecapcode"] . "<br>";
			if (md5(htmlspecialchars($step["reponsecap"])) == htmlspecialchars($step["reponsecapcode"]))
				return true;
			else
				return false;
		}
	}


}
