<?php 


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
class CustomNavigationShortcodes extends CustomArchive {
	
	public function __construct() {
		parent::__construct();
		// add_shortcode('index-link', array($this,'add_index_link')); 
		add_shortcode('ct-terms-menu', array($this,'list_taxonomy_terms')); 
		add_shortcode('tags-menu', array($this,'list_tags')); 
		add_shortcode('ct-terms', array($this,'list_terms_taxonomy'));
		add_shortcode('permalink', array($this,'add_permalink_shortcode'));
		add_shortcode('share-title', array($this,'display_share_title')); 
		add_shortcode('registration', array($this,'output_registation_url')); 
		add_shortcode( 'wp-page-link', array($this,'display_wordpress_page_link') );		
		add_filter( 'query_vars', array($this,'archive_filter_queryvars') );		
		add_filter('terms_clauses', array($this,'add_terms_clauses'), 10, 3 );
	}

	public function add_terms_clauses($clauses, $taxonomy, $args) {
	  global $wpdb;
	  if ($args['tags_post_type']) {
	    $post_types = $args['tags_post_type'];
	    // allow for arrays
	    if ( is_array($args['tags_post_type']) ) {
	      $post_types = implode("','", $args['tags_post_type']);
	    }
	    $clauses['join'] .= " INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id";
	    $clauses['where'] .= " AND p.post_type IN ('". esc_sql( $post_types ). "') GROUP BY t.term_id";
	  }
	  return $clauses;
	}


	/* Custom query variable for taxonomy filter
	--------------------------------------------- */		
	public function archive_filter_queryvars($vars) {
	  $vars[] = 'filter';
	  $vars[] .= 'filter_term';
	  return $vars;
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
	/* = LOGIN PAGE LINK SHORTCODE    
	/* =================================================================*/

	public function display_wordpress_page_link( $atts ) {
		$atts = shortcode_atts( array(
			'target' => 'home', // home, login
			'markup' => 'full', // url, full
			'text' => '',
			), $atts );

		if ($atts['target'] == 'home')
			$url = get_home_url();
		else 
			$url = wp_login_url();

		if ($atts['markup'] == 'full') {
			$html .= '<a href="' .  $url . '">' . $atts['text'] . '</a>';
			return $html;
		}
		else 
			return $url;
	}


	// /* =================================================================*/
	// /* =                   ADD INDEX LINK  
	// /* =================================================================*/


	// public function add_index_link($atts) {
	// 	 // Get shortcode parameters
	// 	$atts = shortcode_atts( array(
	// 		'back' => 'false',
	// 	), $atts );
		
	// 	//PHP_Debug::log(' In index-link shortcode');
		
	// 	$url='';				
	// 	$msg='';				
	
	// 	if ( ($back!='true') && !is_search() ) {
			
	// 		$obj = get_queried_object();
	// 		$author = isset($obj->data->user_login);				
	// 		$tax_id = $author?'author':$obj -> taxonomy;
	// 		$parent_id = $obj -> parent;
	// 		$parent = get_term_by('id', $parent_id,'cuisine');
	// 		////PHP_Debug::log(array('Parent id = ', $parent_id));
	// 		$parent_slug = ($parent)?$parent->slug:'';
	// 		$parent_name = ($parent)?$parent->name:'';
	// 		////PHP_Debug::log(array('Parent slug = ', $parent_slug));
	// 		////PHP_Debug::log(array('Parent name = ', $parent_name));
	// 		$current_slug = $obj -> slug;
	// 		////PHP_Debug::log(array('Current taxonomy = ', $tax_id));
	// 		////PHP_Debug::log(array('Current slug = ', $current_slug));

	// 		switch ($tax_id) {
	// 	    case 'author':
	// 				$url = "/social/membres";
	// 				$msg = __('members', 'foodiepro');
	// 				break;				
	// 	    case 'course':
	// 				$url = "/accueil/recettes/plats";
	// 				$msg = __('Courses', 'foodiepro');
	// 				break;
	// 	    case 'season':
	// 				$url = "/accueil/recettes/saisons";
	// 				$msg = __('Seasons', 'foodiepro');
	// 				break;
	// 	    case 'occasion':
	// 				$url = "/accueil/recettes/occasions";
	// 				$msg = __('Occasions', 'foodiepro');
	// 				break;
	// 	    case 'diet':
	// 				$url = "/accueil/recettes/regimes";
	// 				$msg = __('Diets', 'foodiepro');
	// 				break;
	// 	    case 'cuisine':
	// 	    	if ( $current_slug=='france' ) {
	// 	    		$url = "/accueil/recettes/regions";
	// 					$msg = __('France', 'foodiepro');
	// 				}
	// 	    	elseif ( $parent_slug=='france' ) {
	// 	    		$url = '/origine/france';
	// 					$msg = __('France', 'foodiepro');
	// 				}					
	// 	    	elseif (!empty($parent_slug)) {
	// 	    		$url = '/origine/' . $parent_slug;
	// 	    		$msg = $parent_name;
	// 	    	}
	// 	    	else {
	// 	    		$url = "/accueil/recettes/monde";
	// 					$msg = __('World', 'foodiepro');
	// 				}
	// 	    	break;
	// 	    case 'category':
	// 				$url = "/accueil/articles";
	// 				$msg = __('All posts', 'foodiepro');
	// 				break;	
	// 		}
	// 	}
			
	// 	else {
	// 			$url = 'javascript:history.back()';
	// 			$msg = __('Previous page','foodiepro');
	// 	}
		
	// 	$output = '<ul class="menu"> <li> <a class="back-link" href="' . $url . '">' . $msg . '</a> </li> </menu>';
	// 	return $output;
	// }


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
			'count' => 'false'
			), $atts );

		$drill = $atts['drill']=='true';
		$count = $atts['count']=='true';
		$tax = $atts['tax'];
		$html = '<div class="tax-container">';
			
		if ( $atts['title']=='' ) {
			$tax_details = get_taxonomy( $tax );
			$title = $tax_details->labels->name;
		}
		else 
			$title = $atts['title'];

		$html .= '<h3>' . $title . '</h3>';
		$html .= '<div class="subnav" id="' . $tax . '" style="display:none">';

		$terms = get_categories( array(
			'taxonomy' => $tax,
			'exclude' => $atts['exclude'],
			'parent' => $atts['parent'],
			'author' => 0,		
			// 'author' => $atts['author'],		
			'hide_empty' => true,		
			'orderby' => 'slug',
			'order'   => 'ASC'
		) );
		
		foreach ( $terms as $term ) {
			$post_count='';
			if  ( $count ) {
				if ( $drill ) {
					$subterms = get_categories( array(
						'taxonomy' => $tax,
						'parent' => $term->term_id,		
					) );
					// echo '<pre>' . $term->name . '</pre>';
					foreach ($subterms as $subterm) {
						// echo '<pre>' . print_r($subterm) . '</pre>';
						$post_count += $subterm->count;
					}
				}
				$post_count += $term->count;
				$post_count = ' (' . $post_count . ')';
			}
			$html .= '<li><a href="' . get_term_link( $term, $tax ) . '">' . $term->name . $post_count . '</a></li>';
		}	
		
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
			'depth' => 1,
			'exclude' => '',
			'index_title' => '',
			'index_path' => ''
		), $atts );

		$html = '';
		$depth = $atts['depth'];
		$exclude = $atts['exclude'];
		$dropdown = $atts['dropdown'];
		$index_title = $atts['index_title'];
		$index_path = $atts['index_path'];
		
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
			
			
	// Output taxonomy and parent term			
		if ($tax_slug == 'cuisine') { // $tax_slug will stay cuisine
			if ($obj->parent != 0) // term has a parent => either country or region archive
				$child_of = $obj->parent; // wp_list_categories will use parent to filter
			else // term has no parent => either continent or france
				$child_of = $obj->term_id; // wp_list_categories will use current term to filter
		}
		else {
			$child_of='';
		}
	

	// Arguments for wp_list_categories	/ wp_list_authors
		$args = array( 
			'taxonomy'			=> $tax_slug,
			'child_of'			=> $child_of,
			'depth' 			=> $depth,
			'exclude' 			=> $exclude,
			'orderby' 			=> 'slug',
			'echo' 				=> false,
			'role__not_in'		=> array('administrator','pending'),
			'show'				=> user_login
		);
		
		
		if ($dropdown=='true') {	
			$dropdown_id = $taxonomy . ++$dropdown_cnt;
			
			$html = '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '"> . $label . </label>';

			$args['show_option_none'] = $select_msg;
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

	public function add_permalink_shortcode($atts) {
		$a = shortcode_atts(array(
			'id' => false,
			'slug' => false,
			'html' => false, // html markup or url only
			'text' => ""  // default value if none supplied
	    ), $atts);
	
		$id=$a['id'];
		$slug=$a['slug'];
		$html=$a['html'];
		$text=esc_html($a['text']);
	
		if ($id) 
			$url=get_permalink($id);
		elseif ($slug) {
			// $url=get_permalink(get_page_by_path($slug));			
			$url=$this->get_page_by_slug($slug);			
		}
		else {
			$url=$_SERVER['REQUEST_URI'];			
		}

    if ($html) return '<a href=' . $url . '>' . $text . '</a>';
    else return $url;
	}

	public function get_page_by_slug($page_slug, $post_type = 'page' ) { 
		global $wpdb; 
		$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s AND post_status = 'publish'", $page_slug, $post_type ) ); 
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



