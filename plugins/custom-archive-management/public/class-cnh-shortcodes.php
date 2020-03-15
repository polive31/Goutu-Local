<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

class CNH_Shortcodes {

    public function get_site_logo_path( $atts ) {
    	$url = get_stylesheet_directory_uri();
    	$url = $url . '\images\fb-app-icon-512x512.png';
		return $url;
    }

	/* Pending Posts Count
	--------------------------------------------- */
	public function get_post_count( $atts ) {
		//Let's not loose time if user doesn't have the rights
		if( !current_user_can('editor') && !current_user_can('administrator') ) return;

	    $atts = shortcode_atts( array(
	        'status' => 'pending', //draft, publish, auto-draft, private, separated by " "
	        'type' => 'post', //recipe
	        'category_name' => '', //recipe
		), $atts );

		$status = explode(' ', $atts['status']);

		$args = array(
			// 'author' => 1, // user ID here
			'posts_per_page' => -1, // retrieve all
			'post_type' => $atts['type'],
			'category_name' => $atts['category_name'],// list of category IDs to be included, separate by commas
			// 'post_status' => 'any' // any status
			'post_status' => $status
		);

		$posts = get_posts( $args );
		$html = (count($posts)>0)?'<span class="post-count-indicator">('.count($posts).')</span>':'';

		return $html;
	}


	/* Share Title Output
	--------------------------------------------- */
	public function display_share_title() {
		if (is_singular()) {
			if (is_singular('recipe'))
				$msg=__('Share this recipe','foodiepro');
			else
				$msg=__('Share this post','foodiepro');
			$html = '<h3 class="share-title">' . $msg . '</h3>';
		}
		return $html;
	}

	/* =================================================================*/
	/* = IF SHORTCODE
	/* =================================================================*/
	public function display_conditionnally( $atts, $content ) {
		$atts = shortcode_atts( array(
			'user' => '', //logged-in, logged-out
		), $atts );

		$display=true;
		$user=$atts['user'];

		if ( $user=='logged-out' )
			$display=$display && !is_user_logged_in();
		elseif ( $user=='logged-in' )
			$display=$display && is_user_logged_in();

		return $display?do_shortcode($content):'';
	}



	/* =================================================================*/
	/* = DEBUG SHORTCODE
	/* =================================================================*/
	public function show_debug_html( $atts, $content ) {
		return WP_DEBUG?$content:'';
	}


	/* =================================================================*/
	/* = LIST TAXONOMY TERMS SHORTCODE
	/* =================================================================*/
	public function simple_list_taxonomy_terms($args) {
	    $args = shortcode_atts( array(
	        'taxonomy' => 'post_tag',
	        'orderby' => 'description',
	        'groupby' => ''
	    ), $args );

	    $terms = get_categories($args);

	    $output = '';

	    // Exit if there are no terms
	    if (! $terms) {
	        return $output;
	    }

	    // Start list
	    $output .= '<ul>';

	    // Add terms
	    foreach($terms as $term) {
	        $output .= '<li><a href="'. get_term_link($term) .'">'. esc_html($term->cat_name) .'</a></li>';
	    }

	    // End list
	    $output .= '</ul>';

	    return $output;
	}


	/* =================================================================*/
	/* = GLOSSARY SHORTCODE
	/* =================================================================*/
	public function search_glossary( $atts, $content ) {
		$atts = shortcode_atts( array(
			'glossaryslug' => 'lexique-de-cuisine',
			'searchkey' => 'name-directory-search-value',
			'dir' => '2',
			'term' => '',
		), $atts );

		$term = !empty($atts['term'])? $atts['term']:$content;
		$glossary_url = $this->get_page_by_slug($atts['glossaryslug']);
		$url=add_query_arg(
			array(
				$atts['searchkey'] 	=> strip_tags($term),
				'dir'				=> $atts['dir'],
			),
			$glossary_url
		);

		// if ( get_page_by_path($url,'post') )
			$html='<a href="' . $url . '">' . $content . '</a>';
		// else
		// 	$html=$content;

		return $html;
	}

	/* =================================================================*/
	/* = SEARCH SHORTCODE
	/* =================================================================*/
	public function search_posts( $atts, $content ) {
		$atts = shortcode_atts( array(
			'searchkey' => 's',
			), $atts );

		$html=add_query_arg( $atts['searchkey'], $content, get_site_url());
		$html='<a href="' . $html . '">' . $content . '</a>';

		return $html;
	}

	/* =================================================================*/
	/* = DISPLAY TOOLTIP SHORTCODE
	/* =================================================================*/

  //   public function output_tooltip( $atts ) {
  //       // $path = self::$_PluginPath . 'assets/img/callout_'. $position . '.png';
		// $atts = shortcode_atts( array(
		// 	'text' => '',
		// 	'pos' => 'top',
		// 	), $atts );

		// $content = $atts['text'];
		// $position = $atts['pos'];

  //       $uri = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/callout_'. $position . '.png';

  //       $html ='<div class="tooltip-content">';
  //       $html.='<div class="wrap">';
  //       $html.= $content;
  //       $html.='<img class="callout" data-no-lazy="1" src="' . $uri . '">';
  //       $html.='</div>';
  //       $html.='</div>';

  //       return $html;
  //   }



	/* =================================================================*/
	/* = TAXONOMIES LIST SHORTCODE
	/* =================================================================*/

	public function list_taxonomy_terms( $atts ) {
		$atts = shortcode_atts( array(
			'tax' => '',
			'title' => '',
			'class' => '',
			'post_type' => '',
			'parent' => '',
			'author' => '',
			'exclude' => '',
			'drill'	=> 'false',
			'count' => 'false',
			'page_slug' => '', // no custom page entry if empty
			'page_title' => '',
			'page_order' => 'last', //first, last
			), $atts );

		$drill = $atts['drill']=='true';
		$count = $atts['count']=='true';
		$tax = $atts['tax'];
		$html = '<div class="tax-container">';

		if ( empty($atts['title']) ) {
			$tax_details = get_taxonomy( $tax );
			$title = $tax_details->labels->name;
		}
		else
			$title = $atts['title'];

		$page_link = '';
		if ($atts['page_slug'] != '') {
			$page_url = $this->get_permalink(array('slug'=>$atts['page_slug']));
			$page_link .= '<li class="accordion-page-link"><a href="' . $page_url . '">' . $atts['page_title'] . '</a></li>';
		}

		$html .= '<h3>' . $title . '</h3>';

		$html .= '<div class="subnav" id="' . $tax . '" style="display:none">';

		$terms = get_categories( array(
			'taxonomy' => $tax,
			'exclude' => $atts['exclude'],
			'parent' => $atts['parent'],
			'author' => 0,
			// 'author' => $atts['author'],
			'hide_empty' => true,
			'orderby' => CNH_Assets::get_orderby($tax),
			'order'   => 'ASC'
		) );


		$html .= ($atts['page_order']=='first')?$page_link:'';
		foreach ( $terms as $term ) {
			$post_count=0;
			if  ( $count ) {
				if ( $drill ) {
					$subterms = get_categories( array(
						'taxonomy' => $tax,
						'parent' => $term->term_id,
					) );
					// echo '<pre>' . $term->name . '</pre>';
					foreach ($subterms as $subterm) {
						// echo '<pre>' . print_r($subterm) . '</pre>';
						$post_count += (int)$subterm->count;
					}
				}
				$post_count += (int)$term->count;
				$post_count = ' (' . $post_count . ')';
			}
			$html .= '<li><a href="' . get_term_link( $term, $tax ) . '">' . $term->name . $post_count . '</a></li>';
		}

		$html .= ($atts['page_order']=='last')?$page_link:'';
		$html .= '</div></div>';

		return $html;
	}

	/* =================================================================*/
	/* = TAGS LIST SHORTCODE
	/* =================================================================*/

	public function list_tags( $atts ) {
		$atts = shortcode_atts( array(
			'title' => '',
			'class' => '',
			'post_type' => '',
			'exclude' => '',
			'count' => 'false'
			), $atts );

		$count = $atts['count']=='true';

		$html = '<div class="tax-container">';

		$title = $atts['title'];
		$html .= '<h3>' . $title . '</h3>';
		$html .= '<div class="subnav" id="tags" style="display:none">';

		$tags = get_tags( array(
			// New clause "tags_post_type" added to the WP_Query function
			// see req_clauses filter above
			'tags_post_type' => $atts['post_type'],
			'hide_empty' => true,
			'exclude' => $atts['exclude'],
			'orderby' => 'name',
			'order'   => 'ASC'
		) );

		foreach ( $tags as $tag ) {
			$post_count = $count?' (' . $tag->count . ')':'';
			$url = get_tag_link($tag->term_id);
			$url = add_query_arg( 'post_type', 'recipe', $url );
			$html .= '<li><a href="' . $url . '">' . $tag->name . $post_count . '</a></li>';
		}

		$html .= '</div></div>';

		return $html;
	}


	/* Output permalink of any page
	------------------------------------------------------*/

	public function get_permalink($atts, $content='') {
		$atts = shortcode_atts(array(
			/* Source parameters */
			'id' 	=> '',
			'slug' 	=> false,
			'tax' 	=> false,
			'wp' 	=> false, // home, login, register
			'user' 	=> false, // current, view, author, any user ID
			'peepso' => false, // members, register

			/* Display parameters */
			'class' => '',
			'display' => false, // archive, profile
			'type' => 'post', // post type : post, recipe OR peepso profile tab : about, activity...
			'text' 	=> false,  // html link is output if not empty
			'target' 	=> '',  // link target

			/* Google Analytics parameters */
			'data' 	=> false, // "attr1 val1 attr2 val2  ..." separate with spaces
			'ga' 	=> false, // ga('send', 'event', [eventCategory], [eventAction], [eventLabel], [eventValue] ); separate by spaces

	    ), $atts);

		extract( $atts );
		$text=$text?esc_html($text):'';
		$content=esc_html($content);
		$data=$data?explode(' ', $data):false;
		$ga=$ga?explode(' ', $ga):false;
		$rel='';

		$url='#';
		$token=''; /* Replacement token for display text */
		if ($id) {
			$url=get_permalink($id);
		}
		elseif ($tax) {
			if (!empty($slug))
				$url=get_term_link((string) $slug, (string) $tax);
		}
		elseif ($slug) {
			// $url=get_permalink(get_page_by_path($slug));
			$url=$this->get_page_by_slug($slug);
		}
		elseif ($user) {
			// Define user
			if ($user=='current') {
				$user_id = get_current_user_id();
			}
			elseif ($user=='author') {
				$user_id = get_the_author_meta('ID');
			}
			elseif ($user=='view' && class_exists('Peepso') ) {
				$user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
			}
			else {
				$user_id = $user;
			}
			// Define display url
			if ($display=='archive') {
				$user = get_user_by('id', $user_id);
				if (!$user) return;
				$token = $user->data->user_nicename;
				// $url = get_site_url( null, foodiepro_get_author_base() . '/' . $token);
				$url = get_author_posts_url($user_id, $token);
				$url = esc_url(add_query_arg('post_type', $type, $url));
				$rel='author';
			}
			elseif ( $display=='profile' && class_exists('Peepso') ) {
				$peepso_user = PeepsoUser::get_instance( $user_id );
				$url = $peepso_user->get_profileurl();
				$url .= $type;
				$token = $peepso_user->get_nicename();
			}
		}
		elseif ($wp) {
			if ( $wp=='home' )
				$url = get_home_url();
			elseif ( $wp=='login' )
				$url = wp_login_url();
			elseif ( $wp=='register' )
				$url = wp_registration_url();
		}
		elseif ($peepso) {
			if (!class_exists('Peepso')) return;
			if ($peepso=='members' ) {
				$url = PeepSo::get_page('members');
			}
			elseif ($peepso=='register') {
				$url= PeepSo::get_page('register');
			}
		}
		else {
			// Current URL is supplied by default
			$url=$_SERVER['REQUEST_URI'];
		}

		if ( $content || $text )
			return '<a class="' . $class . '" rel="' . $rel . '" id="' . $id . '" ' . $this->get_data( $data ) . ' href="' . $url . '" target="' . $target . '" onclik="' . $this->get_ga( $ga ) . '">' . sprintf( $text . $content, $token ) . '</a>';
		else
			return $url;
	}


	/* =================================================================*/
	/* = PERMALINK HELPERS
	/* =================================================================*/

	public function get_ga( $ga ) {
		if ( !$ga || !is_array($ga) ) return;
		$html = "ga('send', 'event' ";
		foreach ( $ga as $field ) {
			$html .= ",'$field' ";
		}
		$html .= ");";
		return $html;
	}

	public function get_data( $data ) {
		if ( !$data || ( count($data) % 2 != 0) ) return;
		$html = '';
		$i = 0;
		while ( isset($data[$i]) ) {
			$html .= 'data-' . $data[$i] . '="' . $data[$i+1] . '" ';
			$i=$i+2;
		}
		return $html;
	}

	public function get_page_by_slug($page_slug ) {
		global $wpdb;
		$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_status = 'publish'", $page_slug ) );
		if ( $page )
		    return get_permalink($page);
		return null;
  	}


}