<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}


// This constant is configuring the foodiepro_esc() function
define(
	'ALLOWED_TAGS',
	array(
		'a' => array(
			'href' => true,
			'class' => true,
			'id' => true,
			'title' => true,
		),
		'abbr' => array(
			'title' => true,
		),
		'acronym' => array(
			'title' => true,
		),
		'b' => array(),
		'br' => array(),
		'blockquote' => array(
			'cite' => true,
		),
		'cite' => array(),
		'code' => array(),
		'del' => array(
			'datetime' => true,
		),
		'em' => array(),
		'i' => array(),
		'img' => array(
			'src' => true,
			'class' => true,
			'id' => true,
			'title' => true,
		),
		'p' => array(
			'class' => true,
		),
		'q' => array(
			'cite' => true,
		),
		'strike' => array(),
		'strong' => array(),
	)
);



/* =================================================================*/
/* =              Custom admin notice
/* =================================================================*/
class foodiepro_admin_notice
{
	private $_message;
	public function __construct($message)
	{
		$this->_message = $message;
		add_action('admin_notices', array($this, 'render'));
	}
	public function render()
	{
		//notice-error, notice-warning, notice-success, or notice-info
		ob_start();
?>
		<div class="notice notice-error is-dismissible">
			<p><?= $this->_message ?></p>
		</div>
	<?php
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
	}
}

/* =================================================================*/
/* =              DYNAMIC TEMPLATE
/* =================================================================*/

/**
 * foodiepro_replace_token
 *
 * @param  mixed $html
 * @param  mixed $token
 * @param  mixed $data
 * @return void
 */
function foodiepro_replace_token($html, $token, $data)
{
	$pattern = '/' . $token . '(.*?)' . $token . '/i';
	// if (preg_match_all("/$tag(.*?)$tag/i", $html, $m)) {
	if (preg_match_all($pattern, $html, $m)) {
		foreach ($m[1] as $i => $varname) {
			$html = str_replace($m[0][$i], sprintf('%s', $data[strtolower($varname)]), $html);
		}
	}
	return $html;
}


/* =================================================================*/
/* =              PERMALINKS
/* =================================================================*/

/**
 * foodiepro_get_author_base
 *
 * @return void
 */
function foodiepro_get_author_base()
{
	return 'auteur';
}


/**
 * foodiepro_get_permalink
 *
 * @param  mixed $atts array of :
 *
 * 	    Input parameters
 * *	'id' 		=> post or page ID
 * *	'slug' 		=> post or page slug
 * *	'tax' 		=> taxonomy slug
 * *	'wp' 		=> false (home, login, register)
 * *	'user' 		=> false (current, view, author, any user ID)
 * *	'community' => false (members, register, myfriends)
 * *	'google' 	=> false (search terms separated by spaces)
 *
 *		Display parameters
 * *	'text' 		=> false (html link is output if not empty), accepts %s usage, to be replaced by $token at output
 * *	'class' 	=> ''
 * *	'display' 	=> false (archive, profile)
 * *	'type' 		=> 'post'(post type : post, recipe OR peepso profile tab : about, activity, friends...)
 * *	'target' 	=> ''	 ('_blank' for new tab)
 *
 *		Google Analytics parameters
 * *	'data' 		=> false ("attr1 val1 attr2 val2  ..." separate with spaces)
 * *	'ga' 		=> false (ga('send', 'event', [eventCategory], [eventAction], [eventLabel], [eventValue] ); separate by spaces)
 *
 * @param  mixed $content
 * @return void
 */
function foodiepro_get_permalink($atts)
{

	// Initialize optional display variables which won't be tested prior to be used
	$rel = '';
	$id = '';
	$class = '';
	$type = '';
	$text = '';
	$target = '';
	$token = ''; /* Replacement token for displayed text */

	extract($atts);
	$text = esc_html($text);
	$data = empty($data) ? false : explode(' ', $data);
	$ga = empty($ga) ? false : explode(' ', $ga);

	$url = '#';
	if (!empty($id)) {
		$url = get_permalink($id);
	} elseif (!empty($tax)) {
		if (!empty($slug))
			$url = get_term_link((string) $slug, (string) $tax);
	} elseif (!empty($slug)) {
		// $url=get_permalink(get_page_by_path($slug));
		$url = foodiepro_get_page_by_slug($slug);
	} elseif (!empty($google)) {
		// $url=get_permalink(get_page_by_path($slug));
		$url = 'https://www.google.com/search?q=' . urlencode(remove_accents($google));
	} elseif (!empty($user)) {
		// Define user
		if ($user == 'current') {
			$user_id = get_current_user_id();
		} elseif ($user == 'author') {
			$user_id = get_the_author_meta('ID');
		} elseif ($user == 'view' && class_exists('Peepso')) {
			$user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
		} else {
			$user_id = $user;
		}

		// Define display url
		if ($display == 'archive') {
			$user = get_user_by('id', $user_id);
			if (!$user) return;
			$token = $user->data->user_nicename;
			// $url = get_site_url( null, foodiepro_get_author_base() . '/' . $token);
			$url = get_author_posts_url($user_id, $token);
			$url = esc_url(add_query_arg('post_type', $type, $url));
			$rel = 'author';
		} elseif ($display == 'profile' && class_exists('Peepso')) {
			$peepso_user = PeepsoUser::get_instance($user_id);
			$url = $peepso_user->get_profileurl();
			$url .= $type;
			$token = $peepso_user->get_nicename();
		}

	} elseif (!empty($wp)) {
		if ($wp == 'home')
			$url = get_home_url();
		elseif ($wp == 'login')
			$url = wp_login_url();
		elseif ($wp == 'register')
			$url = wp_registration_url();

	}
	elseif (!empty($community)) {
		if (!class_exists('Peepso')) return;
		$url = PeepSo::get_page($community);
	}
	else {
		// Current URL is supplied by default
		$url = $_SERVER['REQUEST_URI'];
	}

	if ($text)
		return '<a class="' . $class . '" rel="' . $rel . '" id="' . $id . '" ' . foodiepro_get_data($data) . ' href="' . $url . '" target="' . $target . '" onclik="' . foodiepro_get_ga($ga) . '">' . sprintf($text, $token) . '</a>';
	else
		return $url;
}

/* =================================================================*/
/* =              SECURITY
/* =================================================================*/


/**
 * Secures translation strings outputs, while allowing some html attributes to be displayed
 *
 * @param  mixed $text
 * @return void
 */
function foodiepro_esc($text)
{
	return wp_kses($text, ALLOWED_TAGS);
}


/* =================================================================*/
/* =    IMAGE & ICON OUTPUT
/* =================================================================*/
/**
 * foodiepro_get_icon_link
 *
 * @param  mixed $url
 * @param  mixed $slug
 * @param  mixed $id
 * @param  mixed $class
 * @param  mixed $title
 * @param  mixed $data
 * @return void
 */
function foodiepro_get_icon_link($url, $slug, $id = '', $class = '', $title = '', $data = array())
{
	$html = '<a href="' . $url . '" id="' . $id . '" class="' . $class . '" ';
	foreach ($data as $key => $value) {
		$html .= 'data-' . $key . '="' . $value . '" ';
	}
	$html .=  '>' . foodiepro_get_icon($slug, '', '', $title) . '</a>';
	return $html;
}

/**
 * foodiepro_get_icon
 *
 * @param  mixed $main
 * @param  mixed $class
 * @param  mixed $id
 * @param  mixed $title
 * @return void
 */
function foodiepro_get_icon($main, $class = '', $id = '', $title = '')
{
	switch ($main) {
		case 'remove':
			$html = '✘';
			break;
		default:
			$html = '<i class="' . foodiepro_get_icon_class($main) . ' ' . $class . '" id="' . $id . '" title="' . $title . '"></i>';
	}
	return $html;
}

/**
 * foodiepro_get_icon_class
 *
 * @param  mixed $slug
 * @return void
 */
function foodiepro_get_icon_class($slug)
{
	switch ($slug) {
		case 'drag-updown':
			$class= 'fas fa-exchange-alt fa-rotate-90';
			break;
		case 'checkbox':
			$class = 'far fa-square';
			break;
		case 'delete':
			$class = 'far fa-trash-alt';
			break;
		case 'hand':
			$class = 'far fa-hand-paper';
			break;
		case 'spinner-arrows':
			$class = 'fas fa-sync fa-spin';
			break;
		case 'spinner-dots':
			$class = 'fas fa-spinner fa-spin';
			break;
		case 'arrows-updown':
			$class = "fas fa-arrows-alt-v";
			break;
		case 'liked':
			$class = "fas fa-thumbs-up";
			break;
		case 'like':
			$class = "far fa-thumbs-up";
			break;
		case 'read':
			$class = "fas fa-volume-up";
			break;
		default:
			$class = 'fas fa-' . $slug; //heart, book, thumbtack, edit, print, chevron-right, chevron-left, tag
	}
	return $class;
}

/**
 * Generates a picture tag including .webp format,
 * based on the url of the specified original image file
 * (jpg, png, or other non-webp standard image format)
 *
 * @param  array $args
 * * src (required) : url of original image
 * * dir (optional) : path of the image
 * * id
 * * class
 * * filter_max_width : int|false displays all existing files within specified max width, as <source> tags
 * * filter_ext : false|array('jpg', 'jpeg', 'png',...) displays all existing files matching with one of the extensions
 * * width
 * * height
 * * alt
 * * lazy : if true then image will be lazyloaded (default true)
 * * fallback : src of fallback image
 * @return void
 */
function foodiepro_get_picture($args)
{
	do_action('qm/start', 'foodiepro_get_picture');

	$src = false;
	$dir = false;
	$alt = '';
	$id = '';
	$class = '';
	$lazy = true;
	$filter_max_width = false;
	$filter_ext = array();
	$width = false;
	$height = false;
	$fallback = false;
	extract($args);

	/* Initial setup */
	$src=empty($src)?$fallback:$src;
	if ( empty($src ) ) return '';

	/* Main image url parts */
	$img = pathinfo($src);
	$img_filename = isset($img['filename'])? $img['filename']:'';
	$img_ext = isset($img['extension'])?$img['extension']:'';
	// $img_format = ($img_ext == 'jpg' || $img_ext == 'jpeg') ? 'jpeg' : $img_ext;
	$img_dir_uri = isset($img['dirname'])? $img['dirname']:'';

	if ( $filter_max_width && $dir ) {
		$files = glob( trailingslashit($dir) . $img_filename . '-*', GLOB_NOSORT  );
		usort($files, 'usort_source_files_cb');
	}
	else
	$files=array();

	/* <picture> tag markup */
	$nolazy_markup = $lazy ? '' : 'data-skip-lazy';
	$width_markup = is_int($width) ? sprintf('width="%s"', $width) : '';
	$height_markup = is_int($height) ? sprintf('height="%s"', $height) : '';
	$html = '<picture id="' .  $id . '" class="' .  $class . '" alt="'. $alt . '" ' . $nolazy_markup . ' ' . $width_markup . ' ' . $height_markup . '>';

	/* <source> tags markup */
	foreach ($files as $file) {
		$size_img = pathinfo($file);
		$size_img_ext = isset($size_img['extension']) ? $size_img['extension'] : '';
		// Check if extension is allowed
		if ( !in_array($size_img_ext, $filter_ext) ) continue;
		$size_img_filename = isset($size_img['filename']) ? $size_img['filename'] : '';
		// Check if width  is allowed
		$match = preg_match('/(\d+)x.*/', $size_img_filename, $size);
		if ( $match!==1 || $size[1]>$filter_max_width ) continue;
		// Add <source> tag for this file
		$size_img_format = ($size_img_ext == 'jpg' || $size_img_ext == 'jpeg') ? 'jpeg' : $size_img_ext;
		$html .= '<source media="(min-width: ' . $size[1] . 'px)" class="skip-lazy" srcset="' . trailingslashit($img_dir_uri) . $size_img_filename . '.' . $size_img_ext . '" type="image/' . $size_img_format . '">';
	}

	/* <img> & closing </picture> tag markup */
	if ( in_array('webp', $filter_ext) ) {
		$html .= foodiepro_get_webp_source_tag( $img_filename, $img_ext, $dir, $img_dir_uri );
	}
	$html .= '<img ' . $nolazy_markup . ' class="' . $class . '"  src="' . $src . '" ' . $width_markup . ' ' . $height_markup . ' alt="' . $alt . '">';
	$html .= '</picture>';

	do_action('qm/stop', 'foodiepro_get_picture');
	return $html;
}

function usort_source_files_cb( $a, $b ) {
	$size=array();
	$match_a = preg_match('/(\d+)x(\d+)[.a-z]*\.(\w+)/', $a, $size);
	if ($match_a!==1) return -1;
	$width_a = $size[1];
	// $webp_a = strpos($size[3],'.webp');
	$webp_a = $size[3] == 'webp';
	$match_b = preg_match('/(\d+)x.*/', $b, $size);
	if ($match_b!==1) return 1;
	$width_b = $size[1];

	if ($width_a==$width_b)
		$result=($webp_a)?-1:1;
	else
		$result=($width_a<$width_b)?1:-1;

	return $result;
}

/**
 * Returns <source> tag for the .webp version of the file if it exists
 *
 * @param  mixed $dir
 * @param  mixed $filename
 * @param  mixed $extension
 * @param  mixed $dirname
 * @return string
 */
function foodiepro_get_webp_source_tag($filename, $extension, $dir_path, $dir_uri ) {
	$webp_markup = '';
	$webp_uri = foodiepro_get_webp_uri($filename, $extension, $dir_path, $dir_uri );
	$webp_markup = '<source srcset="' . $webp_uri . '" type="image/webp">';
	return $webp_markup;
}


/**
 * Returns <source> tag for the .webp version of the file if it exists
 *
 * @param  mixed $dir
 * @param  mixed $filename
 * @param  mixed $extension
 * @param  mixed $dirname
 * @return string
 */
function foodiepro_get_webp_uri($filename, $extension, $dir_path, $dir_uri)
{
	$uri = '';
	$path = trailingslashit($dir_path) . $filename . '.' . $extension . '.webp';
	if (is_file($path))
		$uri = trailingslashit($dir_uri) . $filename . '.' . $extension . '.webp';
	else {
		$path = trailingslashit($dir_path) . $filename . '.webp';
		if (is_file($path))
			$uri = trailingslashit($dir_uri) . $filename . '.webp';
	}
	return $uri;
}


/**
 * foodiepro_get_term_image
 *
 * @param  mixed $term
 * @param  mixed $size
 * @param  mixed $class
 * @param  mixed $imgclass
 * @param  mixed $fallback_url
 * @param  mixed $fallback_html
 * @return void
 */
function foodiepro_get_term_image($term = false, $size = 'full', $class = '', $imgclass = '', $fallback_url = false, $fallback_html = false)
{
	$html = '';
	if (class_exists('WPCustomCategoryImage')) {
		$id = is_object($term) ? $term->term_id : false;
		$name = is_object($term) ? $term->name : false;
		$atts = array(
			'size'       => $size,
			'term_id'    => $id,
			'alt'        => $name,
			'class'      => $imgclass,
			'onlysrc'    => false,
		);
		$html = WPCustomCategoryImage::get_category_image($atts);
	}
	if (empty($html) && $id) {
		$parent_term = get_term_by('id', $term->parent, $term->taxonomy);
		if ($parent_term) {
			$atts['term_id'] = $parent_term->term_id;
			$atts['alt'] = $parent_term->name;
			$html = WPCustomCategoryImage::get_category_image($atts);
		}
	}

	if (empty($html)) {
		if ($fallback_url)
			$html = foodiepro_get_picture(array(
				'src' 	=> $fallback_url,
			));
		elseif ($fallback_html)
			$html = $fallback_html;
	}
	$html = "<div class='$class'>$html</div>";
	return $html;
}


/* =================================================================*/
/* =              CUSTOM SCRIPTS HELPERS
/* =================================================================*/

/**
 * foodiepro_register_script
 *
 * @param  mixed $handle
 * @param  mixed $file
 * @param  mixed $uri
 * @param  mixed $dir
 * @param  mixed $deps
 * @param  mixed $version
 * @param  mixed $footer
 * @param  mixed $data
 * @return void
 */
function foodiepro_register_script($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $footer = false, $data = array())
{
	if (is_array($handle)) {
		$uri = CHILD_THEME_URL;
		$dir = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$footer = false;
		extract($handle);
	}

	if (!strpos($file, '.min.js')) {
		$minfile = str_replace('.js', '.min.js', $file);
		if (file_exists($dir . $minfile) && WP_MINIFY) {
			$file = $minfile;
		}
	}
	$result_register=wp_register_script($handle, $uri . $file, $deps, $version, $footer);

	$result_localize=true;
	if (!empty($data)) {
		$name = $data['name'];
		unset($data['name']);
		$result_localize=wp_localize_script($handle, $name, $data);
	}
	return $result_register && $result_localize;
}

/**
 * foodiepro_enqueue_script
 *
 * @param  string $handle
 * @param  string $uri (child theme url)
 * this must contain the path towards external scripts
 * @param  string $dir (child theme path)
 * @param  string $file ('')
 * @param  array $deps ( array() )
 * @param  string $version ( child theme version)
 * @param  boolean $footer ( false)
 * @return void
 */
function foodiepro_enqueue_script($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $footer = false)
{
	if (is_array($handle)) {
		$uri = CHILD_THEME_URL;
		$file = '';
		$dir = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$footer = false;
		extract($handle);
	}

	if ( !empty($file) && !foodiepro_contains($file, '.min.js') ) {
		$minfile = str_replace('.js', '.min.js', $file);
		if (file_exists($dir . $minfile) && WP_MINIFY) {
			$file = $minfile;
		}
	}

	$result_enqueue = wp_enqueue_script($handle, $uri . $file, $deps, $version, $footer);

	$result_localize=true;
	if (!empty($data)) {
		$name = $data['name'];
		unset($data['name']);
		wp_localize_script($handle, $name, $data);
	}

	return $result_enqueue && $result_localize;
}


/**
 * foodiepro_remove_script
 *
 * @param  mixed $script
 * @return void
 */
function foodiepro_remove_script($script)
{
	$result_deregister = wp_deregister_script($script);
	$result_dequeue = wp_dequeue_script($script);
}


/* =================================================================*/
/* =              CUSTOM STYLES HELPERS
/* =================================================================*/

/**
 * foodiepro_register_style
 *
 * @param  mixed $handle
 * @param  mixed $file
 * @param  mixed $uri
 * @param  mixed $dir
 * @param  mixed $deps
 * @param  mixed $version
 * @param  mixed $media
 * @return void
 */
function foodiepro_register_style($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $media = 'all')
{
	if (is_array($handle)) {
		$uri = CHILD_THEME_URL;
		$dir = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$media = 'all';
		extract($handle);
	}

	if (!strpos($file, '.min.css')) {
		$minfile = str_replace('.css', '.min.css', $file);
		if (file_exists($dir . $minfile) && WP_MINIFY) {
			$file = $minfile;
		}
	}
	return wp_register_style($handle, $uri . $file, $deps, $version, $media);
}

/**
 * foodiepro_enqueue_style
 *
 * @param  mixed $handle
 * @param  mixed $file
 * @param  mixed $uri
 * @param  mixed $dir
 * @param  mixed $deps
 * @param  mixed $version
 * @param  mixed $media
 * @return void
 */
function foodiepro_enqueue_style($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $media = 'all')
{
	if (is_array($handle)) {
		$uri = CHILD_THEME_URL;
		$dir = CHILD_THEME_PATH;
		$deps = array();
		$version = CHILD_THEME_VERSION;
		$media = 'all';
		extract($handle);
	}

	if (!strpos($file, '.min.css')) {
		$minfile = str_replace('.css', '.min.css', $file);
		if (file_exists($dir . $minfile) && WP_MINIFY) {
			$file = $minfile;
		}
	}
	return wp_enqueue_style($handle, $uri . $file, $deps, $version, $media);
}



/**
 * Optimize page loading by dequeuing specific CSS stylesheets loading actions
 *
 * @param  mixed $style
 * @return void
 */
function foodiepro_remove_style($style)
{
	// global $wp_scripts;
	$result_dequeue = wp_dequeue_style($style);
	$result_deregister = wp_deregister_style($style);
}

/* =================================================================*/
/* =         GENERATE PICTURE MARKUP FOR .WEbp SUPPORT
/* =================================================================*/

/**
 * output_picture_markup
 *
 * @param  mixed $url
 * @param  mixed $path
 * @param  mixed $name
 * @param  mixed $ext
 * @return void
 */
function output_picture_markup($url, $path, $name, $ext = null)
{
	echo '<picture>';
	if (file_exists($path . $name . '.webp'))
		echo '<source srcset="' . $url . $name . '.webp" ' . 'type="image/webp">';
	if (isset($ext)) {
		echo '<img src="' . $url . $name . '.' . $ext . '">';
	} else {
		if (file_exists($path . $name . '.jpg')) {
			echo '<img src="' . $url . $name . '.jpg' . '">';
		} elseif (file_exists($path . $name . '.png')) {
			echo '<img src="' . $url . $name . '.png' . '">';
		}
	}
	echo '</picture>';
}


/* =================================================================*/
/* =              MISC HELPERS
/* =================================================================*/


/**
 * foodiepro_contains
 *
 * @param  string $haystack
 * @param  string $needles (one or more, separated by "|" )
 * @return boolean
 */
function foodiepro_contains( $haystack, $needles ) {
	if ( strpos($needles, '|') ) {
		$contains= preg_match('(' . $needles . ')', $haystack) === 1 ;
	}
	else {
		$contains=strpos($haystack, $needles)!==false;
	}
	return $contains;
}

/**
 * foodiepro_startsWith
 *
 * @param  mixed $string
 * @param  mixed $startString
 * @return void
 */
function foodiepro_startsWith($string, $startString)
{
	$len = strlen($startString);
	return (substr($string, 0, $len) === $startString);
}


/**
 * foodiepro_url_exists
 *
 * @param  mixed $url
 * @return void
 */
function foodiepro_url_exists($url)
{
	$headers = @get_headers($url);
	return (strpos($headers[0], '404') === false);
}

/**
 * initial_is_vowel
 *
 * @param  mixed $expression
 * @return void
 */
function initial_is_vowel($expression)
{
	if (empty($expression)) return false;

	$vowels = array('a', 'e', 'i', 'o', 'u');
	$exceptions = array('huile', 'herbes', 'hiver');

	$name = remove_accents($expression);
	$first_letter = strtolower($name[0]);
	$first_word = strtolower(explode(' ', trim($name))[0]);
	return (in_array($first_letter, $vowels) || in_array($first_word, $exceptions));
}

/**
 * foodiepro_check_initial
 *
 * @param  mixed $expression
 * @return void
 */
function foodiepro_check_initial($expression)
{
	if (empty($expression)) return 'none';
	if (initial_is_vowel($expression))
		$type = 'vowel';
	else
		$type = 'consonant';
	return $type;
}

/**
 * foodiepro_is_plural
 *
 * @param  mixed $word
 * @return void
 */
function foodiepro_is_plural($word)
{
	$last = strtolower($word[strlen($word) - 1]);
	return ($last == 's');
}
