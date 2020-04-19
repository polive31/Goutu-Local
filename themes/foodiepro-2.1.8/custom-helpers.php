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
 * foodiepro_get_picture
 *
 * @param  array $args
 * * src : url of original image
 * * dir (optional) : path of the image
 * * id
 * * class
 * * width
 * * height
 * * alt
 * * lazy : if true then image will be lazyloaded (default true)
 * * fallback : src of fallback image
 * @return void
 */
function foodiepro_get_picture($args)
{
	$src = false;
	$dir = false;
	$alt = '';
	$id = '';
	$class = '';
	$lazy = true;
	$width = false;
	$height = false;
	$fallback = false;
	extract($args);

	if ( empty($src) ) {
		if ( !empty($fallback) ) {
			$src=$fallback;
		}
		else {
			return '';
		}
	}


	/* Generates a picture tag including .webp format, based the specified original image file (jpg, png, or other non-webp standard format) url */
	$image = pathinfo($src);
	$filename = $image['filename'];
	$extension = $image['extension'];
	$srcext = ($extension == 'jpg' || $extension == 'jpeg') ? 'jpeg' : $extension;
	$dirname = $image['dirname'];
	$nolazy_markup = $lazy ? '' : 'data-skip-lazy';
	$width_markup = is_int($width) ? sprintf('width="%s"', $width) : '';
	$height_markup = is_int($height) ? sprintf('height="%s"', $height) : '';

	$webp_markup ='';
	if ( $dir ) {
		$ewww_webp_path = trailingslashit($dir) . $filename . '.' . $extension . '.webp';
		$ewww_webp_uri = trailingslashit($dirname) . $filename . '.' . $extension . '.webp';
		if ( file_exists($ewww_webp_path) ) {
			$webp_markup = '<source srcset="' . $ewww_webp_uri . '" type="image/webp">';
		}
		else {
			$ewww_webp_path = trailingslashit($dir) . $filename . '.webp';
			$ewww_webp_uri = trailingslashit($dirname) . $filename . '.webp';
			if (file_exists($ewww_webp_path)) {
				$webp_markup = '<source srcset="' . $ewww_webp_uri . '" type="image/webp">';
			}
		}
	}

	ob_start();
	?>

	<picture id="<?= $id; ?>" class="<?= $class; ?>" alt="<?= $alt; ?>" <?= $nolazy_markup; ?> <?= $width_markup; ?> <?= $height_markup; ?>>
		<?= $webp_markup; ?>
		<source srcset="<?= trailingslashit($dirname) . $filename . '.' . $extension; ?>" type="image/<?= $srcext ?>">
		<img <?= $nolazy_markup; ?> class="<?= $class; ?>"  src="<?= trailingslashit($dirname) . $filename . '.' . $extension; ?>" alt="<?= $alt; ?>">
	</picture>

<?php
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
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
	wp_register_script($handle, $uri . $file, $deps, $version, $footer);

	if (!empty($data)) {
		$name = $data['name'];
		unset($data['name']);
		wp_localize_script($handle, $name, $data);
	}
}

/**
 * foodiepro_enqueue_script
 *
 * @param  mixed $handle
 * @param  mixed $file
 * @param  mixed $uri
 * @param  mixed $dir
 * @param  mixed $deps
 * @param  mixed $version
 * @param  mixed $footer
 * @return void
 */
function foodiepro_enqueue_script($handle, $file = '', $uri = CHILD_THEME_URL, $dir = CHILD_THEME_PATH, $deps = array(), $version = CHILD_THEME_VERSION, $footer = false)
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
	wp_enqueue_script($handle, $uri . $file, $deps, $version, $footer);

	if (!empty($data)) {
		$name = $data['name'];
		unset($data['name']);
		wp_localize_script($handle, $name, $data);
	}
}


/**
 * foodiepro_remove_script
 *
 * @param  mixed $script
 * @return void
 */
function foodiepro_remove_script($script)
{
	wp_deregister_script($script);
	wp_dequeue_script($script);
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
	wp_register_style($handle, $uri . $file, $deps, $version, $media);
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
	wp_enqueue_style($handle, $uri . $file, $deps, $version, $media);
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

	wp_dequeue_style($style);
	wp_deregister_style($style);
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
 * @param  mixed $haystack
 * @param  mixed $needles
 * @return void
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
