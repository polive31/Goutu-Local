<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSN_Mails {

	private $headers=array();
	private $target;

	// const CONTACT_NAME = "Goutu.org";
	// const CONTACT_EMAIL = "contact@goutu.org";
	const PROVIDER = 'mailchimp';
	const TAG = '%%';

	/* $target argument is used by the [custom-functions-debug] debug shortcode in functions.php */
	public function __construct( $target='production' ) {
		$this->headers[] = 'Bcc: ' . get_bloginfo('admin_email');
		$this->target = $target;
	}

	/* *****************************************************************
						CALLBACKS
	********************************************************************/

	public function published_post_notification_callback( $post ) {
		$this->send_mail_publish_post( $post );
	}

	public function pending_post_notification_callback ( $post ) {
		// Send notification email to administrator
		$to = get_option('admin_email');
		if ($to) {
			$edit_link = admin_url('post.php?action=edit&post=' . $post->ID);

			$subject = sprintf(__('New user submission:%s', 'foodiepro'), get_the_title($post->ID));
			$message = 'A new ' . $post->post_type . ' has been submitted on your website.';
			$message .= "\r\n\r\n";
			$message .= 'Edit this post: ' . $edit_link;

			wp_mail($to, $subject, $message);
		}
	}

	public function insert_comment_callback( $comment_ID, $comment_approved, $commentdata) {
		$comment_author = $commentdata['user_id'];
		$post_author =  get_post_field('post_author',  $commentdata['comment_post_ID']);
		if ($comment_author == $post_author ) return;

		if ($comment_approved ) {
			$this->send_mail_publish_comment( $commentdata );
		}
	}

	public function transition_comment_callback( $new_status, $old_status, $comment ) {
		if ($new_status!='approved') return;

		$commentdata = (array)$comment;
		$this->send_mail_publish_comment( $commentdata);

	}


	/* *****************************************************************
						MAIL PUBLISHERS
	********************************************************************/
	public function send_mail_publish_post( $post ) {
		$subject = CPM_Assets::get_label( $post->post_type, 'post_publish_title');
		$content = CPM_Assets::get_label( $post->post_type, 'post_publish_content');
		$content1 = CPM_Assets::get_label( $post->post_type, 'post_publish_content1');

		$to_id = $post->post_author;

		$to = get_the_author_meta('user_email', $to_id);

		if ( !$to ) return false;

		$to_name = ucfirst(get_the_author_meta('first_name', $to_id));
		$headline = $this->hello($to_name);

		$content = sprintf( $content, get_permalink($post), $post->post_title);
		$content = wpautop( $content );

		$content1 = sprintf( $content1, foodiepro_get_permalink(array('user' => $to_id, 'display' => 'profile', 'type' => 'blogposts') ));
		$content1 = wpautop( $content1 );

		$img_url = get_the_post_thumbnail_url($post, 'post-thumbnail');

		$data = array(
			'title' 		=> $post->post_title,
			'headline' 		=> $headline,
			'image_url' 	=> $img_url,
			'content' 		=> $content . $content1,
		);

		$message = self::populate_template($data, $to_id, $post );
		$this->send_mail( $to, $subject, $message );
	}

	public function send_mail_publish_comment( $commentdata ) {

		if ( !empty($commentdata['comment_parent']) ) {
			$args=array(
				'post_ID'			=> $commentdata['comment_post_ID'],
				'parent_ID' 		=> $commentdata['comment_parent'],
				'responder_ID'		=> $commentdata['user_ID'],
				'responder_name'	=> $commentdata['comment_author'],
				'responder_email'	=> $commentdata['comment_author_email'],
			);
			$this->send_mail_comment_response( $args );
		}

		$post_id = $commentdata['comment_post_ID'];
		$post = get_post( $post_id );

		if ( !empty($commentdata['user_ID'])) {
			$user= get_userdata( $commentdata['user_ID'] );
			$comment_author = $user->user_nicename;
		}
		else
			$comment_author = $commentdata['comment_author'];

		$subject = CPM_Assets::get_label( $post->post_type, 'comment_publish_title');
		$subject = sprintf( $subject, ucfirst($comment_author));

		$content_original = CPM_Assets::get_label( $post->post_type, 'comment_publish_content');
		$content = sprintf( $content_original, ucfirst($comment_author), get_permalink($post), $post->post_title);

		$to = get_the_author_meta('user_email', $post->post_author);

		if ( !$to ) return false;

		$to_id = $post->post_author;
		$to_name = ucfirst(get_the_author_meta('display_name', $to_id));

		$headline = $this->hello($to_name);

		$content = sprintf( $content, get_permalink($post), $post->post_title);
		$content = wpautop( $content );
		$content .= '<p>' . __('See you soon !', 'foodiepro') . '<p>';

		$data = array(
			'title' 		=> $post->post_title,
			'headline' 		=> $headline,
			'image_url' 	=> false,
			'content' 		=> $content,
		);

		$message = self::populate_template($data, $to_id, $post );
		$this->send_mail( $to, $subject, $message );

		/* Send Peepso notification */
		if ( !$to_id ) return;

		$commenter_id = $commentdata['user_ID'];
		do_action('csi_send_notification', 'comment', $commenter_id, $to_id, $post_id);

	}

	public function send_mail_comment_response( $responsedata ) {
		$post_id = $responsedata['post_ID'];
		$post = get_post( $post_id );

		if ( !empty($responsedata['responder_ID'])) {
			$user= get_userdata( $responsedata['responder_ID'] );
			$response_author = $user->user_nicename;
		}
		else
			$response_author = $responsedata['responder_name'];

		$subject = __( '%s answered one of your comments', 'foodiepro');
		$subject = sprintf( $subject, ucfirst($response_author));

		$content = __( '%s answered your comment on post <a href="%s">%s</a>.', 'foodiepro');
		$content = sprintf( $content, ucfirst($response_author), get_permalink($post), $post->post_title);
		$content .= '<p>' . __( 'See you soon !' , 'foodiepro') . '<p>';

		$parent = get_comment( $responsedata['parent_ID']);
		$to = $parent->comment_author_email;

		if ( !$to ) return false;

		$to_id = $parent->user_id;
		$to_name = ucfirst( $parent->comment_author );
		$headline = $this->hello($to_name);
		$content = wpautop( $content );
		// $content .= wpautop( '<div style="padding:5px;background:#f1f1f1;font-family:serif;font-style:italic;">' . $commentdata['comment_content'] . '</div>');

		$data = array(
			'title' 		=> $post->post_title,
			'headline' 		=> $headline,
			'image_url' 	=> false,
			'content' 		=> $content,
		);

		$message = self::populate_template($data, $to_id, $post );
		$this->send_mail( $to, $subject, $message );

		/* Send notification */
		$responder_id = $responsedata['responder_ID'];
		do_action('csi_send_notification', 'comment_respond', $responder_id, $to_id, $post_id);

	}

	/* *****************************************************************
								HELPERS
	********************************************************************/

	public function send_mail( $to, $subject, $message ) {
		$headers = $this->headers();
		if ( $this->target == 'debug' ) {
			echo "From name : " . $this->site_name() . " <br>";
			echo "From address : " . $this->contact_address() . " <br>";
			echo "To : $to<br>";
			echo "Subject : $subject<br>";
			echo "Message : $message<br>";
		}
		else
			wp_mail( $to, $subject, $message, $headers );
	}


	public static function populate_template( $mail_data, $user_id, $post, $template=self::PROVIDER.'_generic' ) {
		$logo = CSN_Assets::plugin_url() . 'assets/img/logo.png';

		$facebook_url = CustomSocialButtons::facebookURL($post);
		$twitter_url = CustomSocialButtons::twitterURL($post);
		$pinterest_url = CustomSocialButtons::pinterestURL($post);
		$mail_url = CustomSocialButtons::mailURL($post, $post->post_type);
		$whatsapp_url = CustomSocialButtons::whatsappURL($post, $post->post_type);

		$signature = self::signature();
		$contact = self::contact();
		$copyright = self::copyright();
		$unsubscribe = self::unsubscribe( $user_id );

		$facebook_text 	= __('Share this recipe on Facebook','foodiepro');
		$twitter_text 	= __('Share this recipe on Twitter','foodiepro');
		$pinterest_text = __('Share this recipe on Pinterest','foodiepro');
		$mail_text 		= __('Share this recipe by email','foodiepro');
		$whatsapp_text 	= __('Share this recipe on Whatsapp','foodiepro');

		extract($mail_data);

		$path = CSN_Assets::plugin_path() . 'public/mails/partials/' . $template . '.php';
		// $html = file_get_contents( $path );

        ob_start();
        include( $path );
        $html .= ob_get_contents();
        ob_end_clean();


		// $pattern = '/' . self::TAG . '(.*?)' . self::TAG . '/i';
		// // if (preg_match_all("/$tag(.*?)$tag/i", $html, $m)) {
		// if (preg_match_all($pattern, $html, $m)) {
		//     foreach ($m[1] as $i => $varname) {
		//         $html = str_replace($m[0][$i], sprintf('%s', $data[strtolower($varname)]), $html);
		//     }
		// }
		// return do_shortcode($html);
		return $html;
	}


	/* *****************************************************************
							GETTERS
	********************************************************************/

	public function headers() {
		return $this->headers;
	}

	public function html_mail_content_type() {
	    return "text/html";
	}

	public function hello( $name ) {
		$html = __('Hello %s,', 'foodiepro');
		$html = wpautop( sprintf($html, $name) );
		return $html;
	}

	public function connect() {
		$out =  __( '<a href="%s">Log yourself in</a> to respond.', 'foodiepro');
		// $out = sprintf( $out, do_shortcode('[permalink wp="login"]') );
		$out = sprintf( $out, foodiepro_get_permalink(array('wp'=>'login')) );
		return $out;
	}

	public static function signature() {
		$signature =  __('The <a href="%s">Goûtu.org</a> Team.','foodiepro');
		$signature = sprintf($signature,get_bloginfo('url'));
		return wpautop($signature);
	}

	public static function contact() {
		$contact =  __('Any problem or question ? Contact us <a href="%s">here</a>','foodiepro');
		$contact = sprintf($contact, 'mailto:' . self::contact_address() );
		return wpautop($contact);
	}

	public static function copyright() {
		return do_shortcode('[footer_copyright before="' . __('All rights reserved','foodiepro') . ' " first="2015"]');
	}

	public static function unsubscribe( $user_id ) {
		if ( empty($user_id) || !class_exists('PeepsoHelpers') ) return '';

		$unsubscribe = __('Want to change how you receive these emails?','foodiepro');
		$unsubscribe1 = __('You can <a href="%s">update your preferences</a> on %s.','foodiepro');
		$link=foodiepro_get_permalink(array(
			'user'		=>$user_id,
			'display'	=>'profile',
			'type'		=>'about'
		));
		$unsubscribe1 = sprintf( $unsubscribe1, $link, get_bloginfo());
		return $unsubscribe . '<br>' . $unsubscribe1;
	}

	public static function site_name( $from_name='' ) {
		$from_name = get_bloginfo( 'name' );
	    return $from_name;
	}

	public static function contact_address( $from_address='' ) {
		$domain = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $domain, 0, 4 ) == 'www.' ) {
			$domain = substr( $domain, 4 );
		}
		$from_address = 'contact@' . $domain;
	    return $from_address;
	}

}
