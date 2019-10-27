<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CCF_Admin {

	public function add_ccf_options() {
		add_options_page('Custom Contact Form', 'Custom Contact Form', 'manage_options', 'ccf_options', array($this, 'ccf_options'));
	}

	public function ccf_options() {
	?>
	    <div class="wrap">
	        <h2>Custom Contact Form Options</h2>
	        <form method="post" action="options.php">
	            <?php wp_nonce_field('update-options') ?>
	            <p><strong>Contact Email</strong><br />
	                <input type="text" name="contact_email" size="45" value="<?php echo get_option('contact_email'); ?>" />
	            </p>
	            <p><input type="submit" name="Submit" value="<?= __('Save Options','foodiepro') ?>" /></p>
	            <input type="hidden" name="action" value="update" />
	            <input type="hidden" name="page_options" value="contact_email" />
	        </form>
	    </div>
	<?php
	}

	public function create_contact_post_type() {
		/* Property */
		$labels = array(
			'name'                => _x('Contact Requests', 'Post Type General Name', 'textdomain'),
			'singular_name'       => _x('Contact Request', 'Post Type Singular Name', 'textdomain'),
			'menu_name'           => __('Contacts', 'textdomain'),
			'name_admin_bar'      => __('Contacts', 'textdomain'),
			'parent_item_colon'   => __('Parent Item:', 'textdomain'),
			'all_items'           => __('All Items', 'textdomain'),
			'add_new_item'        => __('Add New Item', 'textdomain'),
			'add_new'             => __('Add New', 'textdomain'),
			'new_item'            => __('New Item', 'textdomain' ),
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

}
