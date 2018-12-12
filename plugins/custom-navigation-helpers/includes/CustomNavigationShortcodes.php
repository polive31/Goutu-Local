<?php 


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
class CustomNavigationShortcodes extends CustomNavigationHelpers {
	
	public function __construct() {
		parent::__construct();
		
		// Shortcodes
		// add_shortcode('index-link', array($this,'add_index_link')); 
		// add_shortcode('tooltip', array($this,'output_tooltip')); 
		add_shortcode('ct-terms-menu', array($this,'list_taxonomy_terms')); 
		add_shortcode('tags-menu', array($this,'list_tags')); 
		add_shortcode('ct-terms', array($this,'list_terms_taxonomy'));
		// add_shortcode('ct-dropdown', array($this,'custom_categories_dropdown_shortcode'));
		add_shortcode('share-title', array($this,'display_share_title')); 
		add_shortcode('registration', array($this,'output_registation_url')); 
		add_shortcode('wp-page-link', array($this,'display_wordpress_page_link') );	
		add_shortcode('taxonomy-terms', array($this,'simple_list_taxonomy_terms'));	


		// Helpful shortcodes within recipes or posts
		add_shortcode('permalink', array($this,'get_permalink'));
		add_shortcode('glossary', array($this,'search_glossary') );	
		add_shortcode('search', array($this,'search_posts') );

		// Social shortcodes
		add_shortcode('site-logo', array($this, 'get_site_logo_path'));

		// Misc 	
		add_shortcode('debug', array($this,'show_debug_html') );	
	
	}


    public function get_site_logo_path( $atts ) {
    	$url = get_stylesheet_directory_uri();
    	$url = $url . '\images\fb-app-icon-512x512.png';
		return $url;    	
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

	    // $args = array(
	    //     'taxonomy' => $attributes['taxonomy'],
	    //     'orderby' => $attributes['orderby'],
	    // );

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
			'searchkey' => 'name-directory-search-value', 
			'slug' => 'lexique-de-cuisine',
			), $atts );

		$glossary_url = $this->get_page_by_slug($atts['slug']);
		$html=add_query_arg( $atts['searchkey'], strip_tags($content), $glossary_url);
		$html='<a href="' . $html . '">' . $content . '</a>';

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
	/* = HOME/LOGIN PAGE LINK SHORTCODE    
	/* =================================================================*/

	public function display_wordpress_page_link( $atts ) {
		$atts = shortcode_atts( array(
			'target' => 'home', // home, login
			'markup' => 'full', // url, full
			'text' => '',
			), $atts );

		if ($atts['target'] == 'home')
			$url = get_home_url();
		elseif ($atts['target'] == 'login') 
			$url = wp_login_url();

		if ($atts['markup'] == 'full') {
			$html .= '<a href="' .  $url . '">' . $atts['text'] . '</a>';
			return $html;
		}
		else 
			return $url;
	}

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
			'orderby' => self::orderby($tax),
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
			$html .= '<li><a href="' .  get_tag_link($tag->term_id) . '">' . $tag->name . $post_count . '</a></li>';
		}	
		
		$html .= '</div></div>';

		return $html;
	}


	/* =================================================================*/
	/* = TAXONOMY LIST SHORTCODE     
	/* =================================================================*/

	public function list_terms_taxonomy( $atts ) {
		static $dropdown_cnt;
		$atts = shortcode_atts( array(
			'taxonomies' => '',
			'dropdown' => 'false',
			'exclude' => '',
			'index_title' => '',
			'index_path' => '',
			'option_none_msg' => __('Choose...','foodiepro'),
		), $atts );

		$html = '';
		$exclude = $atts['exclude'];
		$dropdown = $atts['dropdown'];
		$index_title = $atts['index_title'];
		$index_path = $atts['index_path'];
		$option_none_msg = $atts['option_none_msg'];
		
/* arguments for function wp_list_categories
------------------------------------------------------------------------*/
	// Source taxonomy
		$all_url='#';
		
		$obj = get_queried_object();
		$author = isset($obj->data->user_login);
		//print_r( $obj );
		$tax_slug = $author?'author':$obj->taxonomy;
		//echo $tax_slug;
		$tax = $author?__('authors', 'foodiepro'):get_taxonomy($tax_slug);
		//echo $tax;
		//$term_name = $obj->name;
		$term_slug = $author?$obj->user_login:$obj->slug;		
		//echo $term_slug;
		//echo sprintf( '$tax_slug = %s <br>', $tax_slug);
			
		
		$hierarchical=0;	
		$depth=0;	
	// Output taxonomy and parent term			
		if ($tax_slug == 'cuisine') { // $tax_slug will stay cuisine
			if ($obj->parent != 0) // term has a parent => either country or region archive
				$child_of = $obj->parent; // wp_list_categories will use parent to filter
			else // term has no parent => either continent or france
				$child_of = $obj->term_id; // wp_list_categories will use current term to filter
		}
		elseif ($tax_slug == 'ingredient') {
			$hierarchical = 1; 
		}
		else {
			$child_of='';
		}
	

	// Arguments for wp_dropdown_categories	/ wp_dropdown_users
		$args = array( 
			'taxonomy'			=> $tax_slug,
			'child_of'			=> $child_of,
			'hierarchical'		=> $hierarchical,
			'depth' 			=> $depth,
			'exclude' 			=> $exclude,
			'orderby' 			=> self::orderby($tax_slug),
			'echo' 				=> false,
			'role__not_in'		=> array('administrator','pending'),
			'show'				=> 'user_login'
		);
		
		
		if ($dropdown=='true') {	
			$dropdown_id = $tax_slug . ++$dropdown_cnt;
			
			$html = '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '"> . $label . </label>';

			$args['show_option_none'] = $option_none_msg;
			//$args['show_option_all'] = $all_msg;
			$args['show_option_all'] = '';
			$args['option_none_value'] = 'none';
			$args['selected'] = get_query_var('fterm');
			$args['id'] = $dropdown_id;
			$args['name'] = $dropdown_id;
			$args['class'] = 'dropdown-select';
			$args['value_field'] = 'slug';
			
			$html .= $author?wp_dropdown_users( $args ):wp_dropdown_categories( $args );
				
			ob_start();
			?>
			
			<script type='text/javascript'>
				/* <![CDATA[ */
				(function() {
					var dropdown_<?php echo $dropdown_id;?> = document.getElementById( "<?php echo esc_js( $dropdown_id );?>" );
					function on_<?php echo $dropdown_id;?>_Change() {
						var choice = dropdown_<?php echo $dropdown_id;?>.options[ dropdown_<?php echo $dropdown_id;?>.selectedIndex ].value;
						if ( choice !="none" ) {<?php echo 'location.href = "' . home_url() . '/?' . $tax_slug . '=" + choice'; ?>}
						if ( choice =="0" ) {location.href = "<?php echo $all_url;?>";}
					}
					dropdown_<?php echo $dropdown_id;?>.onchange = on_<?php echo $dropdown_id;?>_Change;
				})();
				/* ]]> */
			</script>
			
			<?php
			$html .= ob_get_contents();
	    ob_end_clean();

		}
		
		else {

		 	$html = '<ul class="menu" id="accordion">';
		 	// wrap it in unordered list 

			$html .= wp_list_categories($args);	

			if ($index_title!='')
				$html .= '<li class="ct-index-url"> <a class="back-link" href="' . site_url($index_path) . '">' . $index_title . '</a></li>';
		 
		 	$html .= '</ul>';
			
		}

	 // Return the output
	 	return $html;
	 
	}


	/* Output permalink of a given post id
	------------------------------------------------------*/

	public function get_permalink($atts, $content=null) {
		$a = shortcode_atts(array(
			'id' => false,
			'slug' => false,
			'tax' => false,
			'text' => ""  // html link is output if not empty
	    ), $atts);
	
		$id=$a['id'];
		$tax=$a['tax'];
		$slug=$a['slug'];
		$text=esc_html($a['text']);
	
		if ($id) 
			$url=get_permalink($id);
		elseif ($tax)
			$url=get_term_link((string) $slug, (string) $tax);			
		elseif ($slug) {
			// $url=get_permalink(get_page_by_path($slug));			
			$url=$this->get_page_by_slug($slug);			
		}
		else {
			// Current URL is supplied by default
			$url=$_SERVER['REQUEST_URI'];			
		}

	    if (!empty($content)) return '<a href="' . $url . '">' . $content . '</a>';
	    if (!empty($text)) return '<a href="' . $url . '">' . $text . '</a>';
	    else return $url;
	}

	public function get_page_by_slug($page_slug ) { 
		global $wpdb; 
		$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_status = 'publish'", $page_slug ) ); 
		 if ( $page ) 
		    return get_permalink($page); 
		return null; 
  	}


	/* Output registration page url
	------------------------------------------------------*/

	public function output_registation_url($atts, $content=null) {
		$a = shortcode_atts(array(
			'html' => true,
			'text' => ""  // default value if none supplied
	    ), $atts);
		$html=$a['html'];
		$text=esc_html($a['text']);
		$content=esc_html($content);
	    
  	$url=wp_registration_url();
    if ($html) return '<a href=' . $url . '>' . $text . $content . '</a>';
    else return $url;
	}



}



