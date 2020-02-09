<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class CCF_Admin
{
	private $requester_name;
	private $requester_email;
	private $post_id;

	private static $CCF_PATH;
	private static $CCF_URI;

	public function __construct()
	{
		self::$CCF_PATH = plugin_dir_path(dirname(__FILE__));
		self::$CCF_URI = plugin_dir_url(dirname(__FILE__));
	}

	public function add_ccf_options()
	{
		add_options_page('Custom Contact Form', 'Custom Contact Form', 'manage_options', 'ccf_options', array($this, 'ccf_options'));
	}

	public function ccf_options()
	{
?>
		<div class="wrap">
			<h2>Custom Contact Form Options</h2>
			<form method="post" action="options.php">
				<?php wp_nonce_field('update-options') ?>
				<p><strong>Contact Email</strong><br />
					<input type="text" name="contact_email" size="45" value="<?php echo get_option('contact_email'); ?>" />
				</p>
				<p><input type="submit" name="Submit" value="<?= __('Save Options', 'foodiepro') ?>" /></p>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="contact_email" />
			</form>
		</div>
	<?php
	}

	public function enqueue_ccf_admin_js()
	{

		$args = array(
			'uri'		=> self::$CCF_URI,
			'dir'		=> self::$CCF_PATH,
			'deps'		=> array('jquery'),
			'version'	=> CHILD_THEME_VERSION,
			'footer'	=> true,
		);

		// Enqueue scripts
		$args['handle'] = 'ccf-admin';
		$args['file'] = '/assets/js/ccf-admin.js';
		custom_enqueue_script($args);

		wp_localize_script(
			'ccf-admin',
			'ccf_admin',
			array(
				'ajaxurl' 	=> admin_url('admin-ajax.php'),
				'postid'	=> get_the_id(),
				'nonce' 	=> wp_create_nonce('_ccf_send_mail_nonce')
			)
		);
	}

	// add_action( 'init', 'create_tag_taxonomies', 0 );

	// 	//create two taxonomies, genres and tags for the post type "tag"
	// 	public function create_contact_type_tag_taxonomy()
	// 	{
	// 	// Add new taxonomy, NOT hierarchical (like tags)
	// 	$labels = array(
	// 		'name' => _x( 'Tags', 'taxonomy general name' ),
	// 		'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
	// 		'search_items' =>  __( 'Search Tags' ),
	// 		'popular_items' => __( 'Popular Tags' ),
	// 		'all_items' => __( 'All Tags' ),
	// 		'parent_item' => null,
	// 		'parent_item_colon' => null,
	// 		'edit_item' => __( 'Edit Tag' ),
	// 		'update_item' => __( 'Update Tag' ),
	// 		'add_new_item' => __( 'Add New Tag' ),
	// 		'new_item_name' => __( 'New Tag Name' ),
	// 		'separate_items_with_commas' => __( 'Separate tags with commas' ),
	// 		'add_or_remove_items' => __( 'Add or remove tags' ),
	// 		'choose_from_most_used' => __( 'Choose from the most used tags' ),
	// 		'menu_name' => __( 'Tags' ),
	// 	);

	// 	register_taxonomy('tag','portfolio',array(
	// 		'hierarchical' => false,
	// 		'labels' => $labels,
	// 		'show_ui' => true,
	// 		'update_count_callback' => '_update_post_term_count',
	// 		'query_var' => true,
	// 		'rewrite' => array( 'slug' => 'tag' ),
	// 	));
	// 	}

	public function create_contact_post_type()
	{
		/* Property */
		$labels = array(
			'name'                => _x('Contact Forms', 'Post Type General Name', 'textdomain'),
			'singular_name'       => _x('Contact Form', 'Post Type Singular Name', 'textdomain'),
			'menu_name'           => __('Contacts', 'textdomain'),
			'name_admin_bar'      => __('Contacts', 'textdomain'),
			'parent_item_colon'   => __('Parent Item:', 'textdomain'),
			'all_items'           => __('All Items', 'textdomain'),
			'add_new_item'        => __('Add New Item', 'textdomain'),
			'add_new'             => __('Add New', 'textdomain'),
			'new_item'            => __('New Item', 'textdomain'),
			'edit_item'           => __('Edit Item', 'textdomain'),
			'update_item'         => __('Update Item', 'textdomain'),
			'view_item'           => __('View Item', 'textdomain'),
			'search_items'        => __('Search Item', 'textdomain'),
			'not_found'           => __('Not found', 'textdomain'),
			'not_found_in_trash'  => __('Not found in Trash', 'textdomain'),
		);
		$rewrite = array(
			'slug'                => _x('contact', 'contact', 'textdomain'),
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => false,
		);
		$args = array(
			'label'               => __('contact', 'textdomain'),
			'description'         => __('Contacts', 'textdomain'),
			'labels'              => $labels,
			'supports'            => array('title', 'editor', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
			'taxonomies'          => array(''),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-testimonial',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => 'contact',
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);
		register_post_type('contact', $args);
	}

	public function hydrate()
	{
		$this->post_id = get_the_ID();
		$this->requester_name = get_post_meta($this->post_id, 'ccf_name', true);
		$this->requester_email = get_post_meta($this->post_id, 'ccf_email', true);
	}


	/* Custom Columns */
	// Add the custom columns to the book post type:
	//
	public function set_contact_forms_columns($columns)
	{
		unset($columns['thumb']);
		$columns['contact-type'] = __('Contact Type', 'foodiepro');
		$columns['requester'] = __('Requester', 'foodiepro');

		return $columns;
	}

	// Add the data to the custom columns for the book post type:
	//
	public function contact_define_columns($column, $post_id)
	{
		switch ($column) {

			case 'contact-type':
				// $terms = get_the_term_list($post_id, 'book_author', '', ',', '');
				$email = get_post_meta($post_id, 'ccf_email', true);
				if (empty($email))
					echo __('Template', 'foodiepro');
				else
					// _e('Unable to get contact types(s)', 'your_text_domain');
					echo __('Request', 'foodiepro');
				break;

			case 'requester':
				echo get_post_meta($post_id, 'ccf_name', true);
				break;
		}
	}


	/* ADMIN MENU LAYOUT */

	public function add_ccf_submenus()
	{
		add_submenu_page('edit.php?post_type=contact', 'Contact : Requests', 'All Requests', 'manage_options', 'edit.php?contact_type=template&post_type=contact');
		add_submenu_page('edit.php?post_type=contact', 'Contact : Templates', 'All Templates', 'manage_options', 'edit.php?contact_type=request&post_type=contact');
	}

	public function add_ccf_queryvars($qvars)
	{
		$qvars[] = 'contact_type';
		return $qvars;
	}

	public function customize_ccf_post_query($query)
	{
		global $pagenow;
		// Get the post type
		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
		$contact_type = isset($_GET['contact_type']) ? $_GET['contact_type'] : '';
		if (is_admin() && $pagenow == 'edit.php' && $post_type == 'contact') {
			if ($contact_type == 'template') {
				$query->query_vars['meta_key'] = 'ccf_email';
				$query->query_vars['meta_value'] = '@';
				$query->query_vars['meta_compare'] = 'LIKE';
			} elseif ($contact_type == 'request') {
				$query->query_vars['meta_key'] = 'ccf_email';
				$query->query_vars['meta_value'] = 'null';
				$query->query_vars['meta_compare'] = 'NOT EXISTS';
			}
		}
	}


	public function restrict_events_by_meta($q)
	{
		if (
			$q->is_main_query()
			&& is_admin()
			&& 'event' == $q->get('post_type')
			&& isset($_GET['meta_key'])
			&& isset($_GET['meta_value'])
		) {
			$q->set('meta_key', $_GET['meta_key']);
			$q->set('meta_value', $_GET['meta_value']);
			$q->set('orderby', 'meta_key');
		}
	}

	/* Custom Meta Box in Contact Form edit screen */
	/* One meta box for sending contact form */
	/* One meta box for list of sent mails for this contact */

	public function tokens_legend_meta_box()
	{
		add_meta_box(
			'tokens',
			__('Available replacement tokens', 'foodiepro'),
			array($this, 'tokens_legend_callback')
		);
	}

	public function send_mail_meta_box()
	{
		add_meta_box(
			'mail',
			__('Send as a mail', 'foodiepro'),
			array($this, 'send_mail_meta_box_callback')
		);
	}

	public function requester_meta_box()
	{
		add_meta_box(
			'requester',
			__('Request author', 'foodiepro'),
			array($this, 'request_author_meta_box_callback')
		);
	}

	public function mail_history_meta_box()
	{
		add_meta_box(
			'mail_history',
			__('Mail History', 'foodiepro'),
			array($this, 'mail_history_meta_box_callback')
		);
	}

	/* META BOXES CALLBACKS */
	public function mail_history_meta_box_callback()
	{
		$sentmails=get_post_meta( $this->post_id, '_mail_sent_to');
		$html = '';
		echo '<ul>';

		foreach($sentmails as $mail) {
			$user_id = $mail['user_id'];
			$recipient = get_user_by('id', $user_id);
			$date = $mail['date'];
			$html = '<li><strong>' . $date . '</strong> : <span>' . $recipient->data->user_nicename .  '</span></li>' . $html;
		}
		echo $html;
		echo '</ul>';
	}

	public function tokens_legend_callback()
	{
	?>
		<p>%%firstname%%</p>
		<?php
	}

	public function send_mail_meta_box_callback()
	{
		if (!empty($this->requester_name) || !empty($this->requester_email)) {
			echo __('This is a user request, it cannot be resent to another user.', 'foodiepro');
			return;
		}

		if (get_post_status() == 'publish') {

			$users = get_users();

		?>
			<div class="ccf-send-mail-ajax-form">
				<p>
					<label>
						<?php _e('User', 'foodiepro'); ?>
					</label>
					<br>
					<select class="full-width" id="" name="userid">
						<option value="" selected disabled hidden><?= __('Choose a recipient', 'foodiepro'); ?></option>
						<?php foreach ($users as $user) { ?>
							<option value="<?= $user->ID; ?>"><?= $user->data->user_nicename ?></option>
						<?php } ?>
					</select>
				</p>

				<p>
					<label for="headline"><?php _e('Email headline', 'foodiepro'); ?></label>
					<input type="text" name="headline">
				</p>
				<button id="ccf_send_mail_submit"><?php _e('Send Mail', 'foodiepro'); ?></button>
			</div>
		<?php
		} else {
			echo __('Your contact post must be published prior to sending mail', 'foodiepro');
		}
	}

	public function request_author_meta_box_callback()
	{
		if (empty($this->requester_name) && empty($this->requester_email)) {
			echo __('Not available on a template page', 'foodiepro');
			return;
		}

		echo sprintf(__('<p><strong>Requester name : </strong>%s</p>', 'foodiepro'), $this->requester_name);
		echo sprintf(__('<p><strong>Requester email : </strong>%s</p>', 'foodiepro'), $this->requester_email);
		echo sprintf(__('<p><strong>Requester received on : </strong>%s</p>', 'foodiepro'), get_the_date('d-m-Y', $this->post_id));
	}


	/* AJAX CALLBACKS */
	public function ajax_send_contact_as_mail_cb()
	{
		// This is a secure process to validate if this request comes from a valid source.
		check_ajax_referer('_ccf_send_mail_nonce', 'security');

		$post_id = $_POST['post_id'];
		$user_id = $_POST['user_id'];
		$subject = $_POST['subject'];

		$user = get_user_by('id', $user_id);
		$peepso_user = PeepsoHelpers::get_user($user_id);
		$to_name = PeepsoHelpers::get_field($peepso_user, 'firstname');
		$to_email = $user->data->user_email;

		$message = wpautop(do_shortcode(get_the_content(null, false, $post_id)));
		$email = get_option('contact_email');

		if ($to_email) {

			$headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";
			$headers .= 'Bcc: ' . get_bloginfo('admin_email');

			$data = array(
				'title' 		=> '',
				'headline' 		=> '',
				'image_url' 	=> false,
				'content' 		=> $message,
			);
			$message = CustomSiteMails::populate_template($data, $user_id);
			$message = foodiepro_replace_token($message, '%%', array('firstname' => $to_name));

			wp_mail($to_email, $subject, $message, $headers);
			$emailSent = true;
			$maildata = array(
				'user_id' => $user_id,
				'date' => date("d-m-Y")
			);
			$result = add_post_meta($post_id, '_mail_sent_to', $maildata);
			echo "Send message successfully";
		}
	}



	/* DEPRECATED */
	function contacts_filter_restrict_posts()
	{

		global $wpdb;
		$result = $wpdb->get_results("SELECT DISTINCT meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key='contact_type'");
		echo '<select name="template">';
		echo '<option value="">All Contact Forms</option>';
		?>
		<option value="template" <?php if (isset($_GET['contact_type']) && ($_GET['contact_type'] == 'template')) {
										echo 'selected="selected"';
									} ?>><?php echo 'Template' ?></option>
		<option value="request" <?php if (isset($_GET['contact_type']) && ($_GET['contact_type'] == 'request')) {
									echo 'selected="selected"';
								} ?>><?php echo 'Request' ?></option>
<?php
		echo '</select>';
	}

	public function contact_sortby($columns)
	{
		$columns['contact-type'] = 'contact-type';
		$columns['requester'] = 'requester';
		return $columns;
	}

	public function contact_orderby($vars)
	{
		if (isset($vars['orderby']) && 'contact-type' == $vars['orderby']) {
			$vars = array_merge($vars, array(
				'meta_key' => 'ccf_email',
				'orderby' => 'meta_value'
			));
		}
		if (isset($vars['orderby']) && 'requester' == $vars['orderby']) {
			$vars = array_merge($vars, array(
				'meta_key' => 'ccf_name',
				'orderby' => 'meta_value'
			));
		}
		return $vars;
	}
}
