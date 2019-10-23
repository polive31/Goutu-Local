<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomContactForm {

	public static $CCF_PATH;
	public static $CCF_URI;
	public static $CONTACT_EMAIL;

	public function __construct() {	
		add_action('init', array($this, 'ccf_create_contact_post_type'), 10);
		// add_filter ('theme_page_templates', array($this,'add_ccf_template'));
		// add_filter ('template_include', array($this, 'redirect_ccf_template'));
		
		// Create options page
		add_action('admin_menu', array($this, 'add_ccf_options'));	
	
		self::$CCF_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$CCF_URI = plugin_dir_url( dirname( __FILE__ ) );
	}



	public function add_ccf_options() {
		add_options_page('Custom Contact Form', 'Custom Contact Form', 'manage_options', 'functions', array($this, 'ccf_options'));
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

	public function add_ccf_template($templates) {
	    $templates['contact-form.php'] = 'Contact Form';
	    return $templates;
    }

	// public function redirect_ccf_template ($template) {
	//     // if ('contact-form.php' == basename ($template))
	//     if ( is_page( get_option('contact_page_slug') ))
	//         $template = self::$CCF_PATH . '/templates/contact-form.php';
	//     return $template;
 //    }


	public function ccf_create_contact_post_type() {

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


	/*pds_captcha.php - un captcha mathématique
	bidouillé par passeurs de savoirs<br>
	plus d'infos sur 
	http://passeurs-de-savoirs.fr/lab/lab2015/captcha-math.php
	*/
	public function pdscaptcha($step) {
		if ($step=="ask") {
			$msg=__('For security reasons, and to avoid spam, please solve the following operation : ', 'foodiepro');
			$tchiffres=array(0,1,2,3,4,5,6,7,8,9,10,11,12);
			$tlettres=array(
				__('zero','foodiepro'),
				__('one','foodiepro'),
				__('two','foodiepro'),
				__('three','foodiepro'),
				__('four','foodiepro'),
				__('five','foodiepro'),
				__('six','foodiepro'),
				__('seven','foodiepro'),
				__('eight','foodiepro'),
				__('nine','foodiepro'),
				__('ten','foodiepro'),
				__('eleven','foodiepro'),
				__('twelve','foodiepro'));
			$premier=rand ( 0 , count($tchiffres)-1 );
			$second=rand ( 0 , count($tchiffres)-1 );

			if($second<=$premier ) {
				$resultat=$tchiffres[$premier]-$tchiffres[$second];
				$operation="Combien font ".$tlettres[$premier]." moins ".$tlettres[$second]." (en chiffres) ?";
			}
			else if($second>$premier ) {
				$resultat=$tchiffres[$second]-$tchiffres[$premier];
				$operation="Combien font ".$tlettres[$second]." moins ".$tlettres[$premier]." (en chiffres) ?";
			}
			else {
				$resultat=$tchiffres[$premier]+$tchiffres[$second];
				$operation="Combien font ".$tlettres[$premier]." plus ".$tlettres[$second]." (en chiffres) ?";
			}
			// echo 'resultat de reference avant md5 : ' . $resultat . "<br>";
			$resultat=md5($resultat);
			// echo 'resultat de reference après md5 : ' . $resultat . "<br>";
			$o="";
			foreach (str_split(utf8_decode($operation)) as $obj) {
				$o .= "&#".ord($obj).";";
			}
			    
			$html='<p><label for="reponsecap">' . $msg;
			$html.= '<span class="mathquestion">' . $o . '</span></label>';
			$html.= '<input type="text" name="reponsecap" value="" />';
			$html.= '<input name="reponsecapcode" type="hidden" value="' . $resultat . '" /></p>';
			return $html;
		}
		else {
			// echo 'reponse utilisateur' . $step["reponsecap"] . "<br>";
			// echo 'MD5 de reference' . $step["reponsecapcode"] . "<br>";
			if (md5(htmlspecialchars($step["reponsecap"]))==htmlspecialchars($step["reponsecapcode"]))
				return true;
			else
				return false;
		}

	}

}

