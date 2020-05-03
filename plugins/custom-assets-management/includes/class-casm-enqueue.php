<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CASM_Enqueue {

	private $enqueued_styles;
	private $enqueued_scripts;
	private $footer_styles;

	private $styles_whitelist;
	private $scripts_whitelist;
	private $styles_blacklist;
	private $scripts_blacklist;

	public function __construct() {
		$this->styles_whitelist=array();
		$this->scripts_whitelist=array();
		$this->styles_blacklist=array();
		$this->scripts_blacklist=array();
		$this->footer_styles=array();
	}

	/*  LOAD CONDITIONALLY
	/* ----------------------------------------------------------------*/
	public function build_styles_lists( )
	{
		$inspected_styles = CASM_Assets::css_if();
		foreach ($inspected_styles as $style => $conditions) {
			if ($this->match($conditions)) {
				// All conditions are fulfilled, therefore style(s) will be enqueued, and won't be examined in footer again
				CASM_Assets::css_if_remove($style);
				if (strpos($style,'*')===false)
				$this->styles_whitelist[]=$style; // if the $style handler isn't a regexp, then this style must be added to the white list
			} else {
				$this->styles_blacklist[]=$style;
			}
		}
		$this->styles_whitelist=apply_filters( 'casm_styles_whitelist', $this->styles_whitelist );
		$this->styles_blacklist=apply_filters( 'casm_styles_blacklist', $this->styles_blacklist );
		$this->dequeue_styles();
	}

	public function build_scripts_lists()
	{
		$inspected_scripts = CASM_Assets::js_if();
		foreach ($inspected_scripts as $script => $conditions) {
			if ($this->match($conditions)) {
				// All conditions are fulfilled, therefore script(s) will be enqueued, and won't be examined in footer again
				CASM_Assets::js_if_remove($script);
				if (strpos($script,'*')===false)
				$this->scripts_whitelist[]=$script; // if the $script handler isn't a regexp, then this script must be added to the white list
			} else {
				$this->scripts_blacklist[]=$script;
			}
		}
		$this->scripts_whitelist = apply_filters('casm_scripts_whitelist', $this->scripts_whitelist);
		$this->scripts_blacklist = apply_filters('casm_scripts_blacklist', $this->scripts_blacklist);
		$this->dequeue_scripts();
	}

	public function dequeue_styles() {
		global $wp_styles;
		$this->enqueued_styles= $wp_styles->queue;

		// Replace whitelisted styles which have replacement data
		foreach ( $this->styles_whitelist as $style) {
			$replacement = CASM_Assets::get_style_replacement($style);
			if ( $replacement ) {
				$old= isset($wp_styles->registered[$style])? $wp_styles->registered[$style]:false;

				$new=array('handle'=>$style);
				if (isset($replacement['file'])) {
					$new['file']=$replacement['file'];
				}
				elseif ($old) {
					$new['file']=$old->src;
				}
				else
					continue; // 'file' cannot be given a value, therefore this Style cannot be processed, let's go to next

				foodiepro_remove_style($style);

				// Force dir & uri to be empty, otherwise will be defaulted to CHILD_THEME_URI/PATH once in foodiepro_enqueue/register
				if ( !isset($replacement['file']) ) {
					$new['dir']='';
					$new['uri']='';
				}
				else {
					if ( isset($replacement['uri'] ) )
						$new['uri']= $replacement['uri'];
					if ( isset($replacement['dir'] ) )
						$new['dir']= $replacement['dir'];
				}

				if ( empty($replacement['footer']) ) {
					foodiepro_enqueue_style($new);
				}
				else {
					$result = foodiepro_register_style($new);
					$this->footer_styles[]=$style;
				}
			}
		}

		// Dequeue blacklisted styles
		foreach ($this->styles_blacklist as $style) {
			if (strpos($style,'*')===false) { // not a regexp, dequeue directly
				foodiepro_remove_style($style);
			}
			else {
				$regexp = '/' . str_replace('*', '([\w-]+)', $style) . '/i';
				$matches=preg_grep ($regexp, $this->enqueued_styles);
				foreach ($matches as $style) {
					if (!in_array($style, $this->styles_whitelist)  || in_array($style, $this->styles_blacklist)) {
						foodiepro_remove_style($style);
					}
				}
			}
		}
	}

	public function dequeue_scripts() {
		global $wp_scripts;
		$this->enqueued_scripts= $wp_scripts->queue;

		// Replace whitelisted scripts which have replacement data
		foreach ($this->scripts_whitelist as $script) {
			$replacement = CASM_Assets::get_script_replacement($script);
			if ($replacement) {
				$old = isset($wp_scripts->registered[$script]) ? $wp_scripts->registered[$script] : false;

				$new = array('handle' => $script);
				if (isset($replacement['file'])) {
					$new['file'] = $replacement['file'];
				} elseif ($old) {
					$new['file'] = $old->src;
				} else
					continue; // 'file' cannot be given a value, therefore this script cannot be processed, let's go to next

				foodiepro_remove_script($script);

				// Force dir & uri to be empty, otherwise will be defaulted to CHILD_THEME_URI/PATH once in foodiepro_enqueue/register
				if (!isset($replacement['file'])) {
					$new['dir'] = '';
					$new['uri'] = '';
				} else {
					if (isset($replacement['uri']))
						$new['uri'] = $replacement['uri'];
					if (isset($replacement['dir']))
						$new['dir'] = $replacement['dir'];
				}
				if ( !empty($replacement['footer']) )
					$new['footer']=$replacement['footer'];

				foodiepro_enqueue_script($new);

			}
		}

		// Dequeue blacklisted scripts
		foreach ($this->scripts_blacklist as $script) {

			if (strpos($script,'*')===false) { // not a regexp, dequeue directly
				foodiepro_remove_script($script);
			}
			else {
				$regexp = '/' . str_replace('*', '([\w-]+)', $script) . '/i';
				$matches=preg_grep ($regexp, $this->enqueued_scripts);
				foreach ($matches as $script) {
					if (!(in_array($script, $this->scripts_whitelist) || in_array($script, $this->scripts_blacklist)) ) {
						foodiepro_remove_script($script);
					}
				}
			}
		}
	}

	public function enqueue_footer_styles()
	{
		global $wp_styles;
		foreach ($this->footer_styles as $style) {
			wp_enqueue_style( $style );
		}
	}

	/**
	 * Parse conditions and determine result
	 *
	 * @param  mixed $conditions
	 * @return void
	 */
	public function match($conditions, $or=false )
	{
		$met = $or?false:true;
		foreach ($conditions as $type => $value) {
			$thismet = true;
			switch ($type) {
				case 'or':
					$thismet = $this->match($value, true);
					break;
				case 'true':
					$thismet = true;
					break;
				case 'false':
					$thismet = false;
					break;
				case 'page':
					$thismet = $this->is_page_of_type(explode(' ', $value));
					break;
				case 'notpage':
					$thismet = !$this->is_page_of_type(explode(' ', $value));
					break;
				case 'shortcode':
					$content = '';
					if (is_singular()) {
						$post = get_post();
						$content = $post->post_content;
					} elseif (is_archive()) {
						$term_id = get_queried_object_id();
						$content = get_term_meta($term_id, 'intro_text', true);
					}
					$thismet = empty($content) ? false : has_shortcode($content, $value);
					break;
				case 'single':
					$thismet = is_single();
					break;
				case 'singular':
					$thismet = is_singular(explode(' ', $value));
					break;
				case 'logged-in':
					$thismet = (bool) $value ? is_user_logged_in() : !is_user_logged_in();
					break;
				case 'admin':
					$thismet = (bool) $value ? current_user_can('manage_options') : !current_user_can('manage_options');
					break;
				case 'mobile':
					$thismet = (bool) $value ? wp_is_mobile() : !wp_is_mobile();
					break;
			}
			$met = $or?$met||$thismet:$met&&$thismet;
		}
		return $met;
	}

	public function is_page_of_type($types)
	{
		$met = false;
		foreach ($types as $type) {
			$thismet = false;
			switch ($type) {
				case 'home':
					$thismet = is_front_page();
					break;
				case 'contact':
					$template = get_page_template();
					$thismet = $thismet || (strpos($template, 'contact') !== false);
					break;
				case 'social':
					$url = $_SERVER["REQUEST_URI"];
					$thismet = strpos($url, 'communaute') !== false;
					$template = get_page_template();
					$thismet = $thismet || (strpos($template, 'social') !== false);
					break;
				case 'blog-page':
					$template = get_page_template();
					$thismet = strpos($template, 'social') == false;
					break;
			}
			$met = $met || $thismet;
		}
		return $met;
	}


	/*  ASYNC STYLE & SCRIPTS LOADING
	/* ----------------------------------------------------------------*/

	public function async_load_js($html, $handle, $src)
	{
		if (is_admin()) return $html;
		if ( CASM_Assets::is_deferred('script', $handle) ) {
			$html = '<script src="' . $src . '" async type="text/javascript"></script>' . "\n";
		}
		return $html;
	}


	public function async_load_css($html, $handle, $href, $media)
	{
		if (is_admin()) return $html;
		if ( CASM_Assets::is_deferred('style', $handle) ) {
			// $html = '<link rel="stylesheet" href="' . $href . '" media="async" onload="if(media!=\'all\')media=\'all\'"><noscript><link rel="stylesheet" href="css.css"></noscript>' . "\n";
			$html = '<link rel="stylesheet" href="' . $href . '" media="none" onload="if(media!=\'all\')media=\'all\'"><noscript><link rel="stylesheet" href="css.css"></noscript>' . "\n";
		}
		return $html;
	}

	public function preload_css($html, $handle, $href, $media)
	{
		if (is_admin()) return $html;
		if ( CASM_Assets::is_preloaded($handle) ) {
			$search = "/rel=\"(.*?)\"/i";
			$replace = "rel='preload'";

			$html = preg_replace($search, $replace, $html);
		}
		return $html;
	}


	/**
	 * Allows using a minified version of the style.css main theme stylesheet
	 *
	 * @param  mixed $default_stylesheet_uri
	 * @return void
	 */
	public function enqueue_minified_theme_stylesheet($default_stylesheet_uri)
	{
		$path_parts = pathinfo($default_stylesheet_uri);
		$file = $path_parts['basename'];
		$min_file = str_replace('.css', '.min.css', $file);
		$min_file_path = CHILD_THEME_PATH . '/' . $min_file;
		// echo '<pre>' . "Default stylesheet URI : {$default_stylesheet_uri}" . '</pre>';
		// echo '<pre>' . "Min file : {$min_file}" . '</pre>';
		// echo '<pre>' . "Min file path : { $min_file_path }" . '</pre>';

		if (file_exists($min_file_path) && WP_MINIFY) {
			$default_stylesheet_uri = CHILD_THEME_URL . '/' . $min_file;
		}
		return $default_stylesheet_uri;
	}


	/* UNUSED
	--------------------------------------------------------*/
	/**
	 * Use Google-served version of jQuery
	 *
	 * @return void
	 */
	public function load_jquery_from_google()
	{
		if (!is_admin()) {
			// comment out the next two lines to load the local copy of jQuery
			wp_deregister_script('jquery');
			wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js', false, '1.8.1');
			wp_enqueue_script('jquery');
		}
	}


	/**
	 * Prevent Max Mega Menu to load all google fonts
	 *
	 * @return void
	 */
	public function megamenu_dequeue_google_fonts()
	{
		wp_dequeue_style('megamenu-google-fonts');
	}



}
