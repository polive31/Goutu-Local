<?php 


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
class CustomNavigationShortcodes extends CustomArchive {
	
	public function __construct() {
		parent::__construct();
		add_shortcode('index-link', array($this,'add_index_link')); 
		add_shortcode('ct-terms', array($this,'list_terms_taxonomy'));
		add_shortcode('permalink', array($this,'add_permalink_shortcode'));
		add_shortcode('share-title', array($this,'display_share_title')); 
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
	/* =                   ADD INDEX LINK  
	/* =================================================================*/


	public function add_index_link($atts) {
		 //Inside the function we extract parameter of our shortcode
		extract( shortcode_atts( array(
			'back' => 'false',
		), $atts ) );
		
		//PHP_Debug::log(' In index-link shortcode');
		
		$url='';				
		$msg='';				
	
		if ( ($back!='true') && !is_search() ) {
			
			$obj = get_queried_object();
			$tax_id = $obj -> taxonomy;
			$parent_id = $obj -> parent;
			$parent = get_term_by('id', $parent_id,'cuisine');
			////PHP_Debug::log(array('Parent id = ', $parent_id));
			$parent_slug = ($parent)?$parent->slug:'';
			$parent_name = ($parent)?$parent->name:'';
			////PHP_Debug::log(array('Parent slug = ', $parent_slug));
			////PHP_Debug::log(array('Parent name = ', $parent_name));
			$current_slug = $obj -> slug;
			////PHP_Debug::log(array('Current taxonomy = ', $tax_id));
			////PHP_Debug::log(array('Current slug = ', $current_slug));

			switch ($tax_id) {
		    case 'course':
					$url = "/accueil/recettes/plats";
					$msg = __('Courses', 'foodiepro');
					break;
		    case 'season':
					$url = "/accueil/recettes/saisons";
					$msg = __('Seasons', 'foodiepro');
					break;
		    case 'occasion':
					$url = "/accueil/recettes/occasions";
					$msg = __('Occasions', 'foodiepro');
					break;
		    case 'diet':
					$url = "/accueil/recettes/regimes";
					$msg = __('Diets', 'foodiepro');
					break;
		    case 'cuisine':
		    	if ( $current_slug=='france' || $parent_slug=='france' ) {
		    		$url = "/accueil/recettes/regions";
						$msg = __('France', 'foodiepro');
					}
		    	elseif (!empty($parent_slug)) {
		    		$url = '/origine/' . $parent_slug;
		    		$msg = $this->get_cuisine_caption($parent_name);
		    	}
		    	else {
		    		$url = "/accueil/recettes/monde";
						$msg = __('World', 'foodiepro');
					}
		    	break;
		    case 'category':
					$url = "/accueil/articles";
					$msg = __('All posts', 'foodiepro');
					break;	
			}
		}
			
		else {
				$url = 'javascript:history.back()';
				$msg = __('Previous page','foodiepro');
		}
		
		$output = '<ul class="menu"> <li> <a class="back-link" href="' . $url . '">' . $msg . '</a> </li> </menu>';
		return $output;
	}


	/* =================================================================*/
	/* =                    TAXONOMY LIST SHORTCODE     
	/* =================================================================*/

	public function list_terms_taxonomy( $atts ) {
		static $dropdown_cnt;
		extract( shortcode_atts( array(
			'dropdown' => 'false',
			'taxonomy' => 'category',
			'label' => '',
			'select_msg' => __( 'Select...', 'foodiepro' ),
			'all_msg' => '',
			'depth' => 1,
			'child_of' => 0,
			'exclude' => '',
			'index_title' => '',
			'index_path' => ''
		), $atts ) );


		$html = '';
	// Extraction of taxonomy from current url
		$all_url='#';
		if ($taxonomy == 'url') {
			$obj = get_queried_object();
			$taxonomy = $obj -> taxonomy;
			if ($taxonomy == 'cuisine') {
				// extract term of depth = 1
				$parent = $obj -> parent;
				$current = $obj -> term_id;
				if ($parent==0) {
					$child_of = $current;}
				else {
					$child_of = $parent;
					$parent_meta = get_term_by('id', $parent, 'cuisine');
					//if ($parent_meta != false) $all_msg = $parent_meta->name;
					//$all_url = add_query_arg( 'cuisine', $parent_meta->slug, home_url() );
				}
			}
		}

	 //arguments for function wp_list_categories
		$args = array( 
			'taxonomy' => $taxonomy,
			'child_of' => $child_of,
			'depth' => $depth,
			'exclude' => $exclude,
			'orderby'  => 'slug',
			//'title_li' => '',
			'echo' => false
		);
		
		if ($dropdown=='true') {	
			$dropdown_id = $taxonomy . ++$dropdown_cnt;
			
			$html = '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $label . '</label>';

			$args['show_option_none'] = $select_msg;
			//$args['show_option_all'] = $all_msg;
			$args['show_option_all'] = '';
			$args['option_none_value'] = 'none';
			$args['selected'] = 'none';
			$args['id'] = $dropdown_id;
			$args['name'] = $dropdown_id;
			$args['value_field'] = 'slug';
			
			$html .= wp_dropdown_categories( $args );
			
			// Get taxonomy slug from taxonomy ID
			$tax_meta = get_taxonomy( $taxonomy );
			if ($tax_meta != false) 
				$tax_slug = $tax_meta->rewrite['slug'];
				
			ob_start();
			?>
			
			<script type='text/javascript'>
			/* <![CDATA[ */
			(function() {
			 var <?php echo $dropdown_id;?>_dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id );?>" );
			 function on_<?php echo $dropdown_id;?>_Change() {
			  var choice = <?php echo $dropdown_id;?>_dropdown.options[ <?php echo $dropdown_id;?>_dropdown.selectedIndex ].value;
				if ( choice !="none" ) {
					  location.href = "<?php echo home_url() . '/' . $tax_slug . '/';?>" + choice;
				}
				if ( choice =="0" ) {
					  location.href = "<?php echo $all_url;?>";
				}
			 }
				<?php echo $dropdown_id;?>_dropdown.onchange = on_<?php echo $dropdown_id;?>_Change;
			})();
			/* ]]> */
			</script>
			
			<?php
			$html .= ob_get_contents();
	    ob_end_clean();

		}
		
		else {

		 	$html = '<ul class="menu">';
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
		extract(shortcode_atts(array(
			'id' => 1,
			'text' => ""  // default value if none supplied
	    ), $atts));
	    
	    if ($text) {
	        $url = get_permalink($id);
	        return "<a href='$url'>$text</a>";
	    } else {
		   return get_permalink($id);
		}
	}








}



