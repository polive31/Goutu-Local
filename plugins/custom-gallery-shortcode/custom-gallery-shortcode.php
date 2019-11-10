<?php
/*
Plugin Name: Custom Gallery Shortcode
Plugin URI: wwww.goutu.org
Description: Provides customized gallery shortcode
Version: 1.0
Author: Pascal Olive
Author URI: www.goutu.org
License: GPL
*/

// Block direct requests
if (!defined('ABSPATH'))
	die('-1');


class Custom_Gallery_Shortcode
{

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

	public function __construct()
	{
		self::$PLUGIN_PATH = plugin_dir_path(__FILE__);
		self::$PLUGIN_URI = plugin_dir_url(__FILE__);

		add_filter('use_default_gallery_style', '__return_false');
		// Load stylesheet, with fallback in case the class is called at enqueue_styles hook level
		add_action('wp_enqueue_scripts', 	array($this, 'custom_gallery_stylesheet'));
		add_shortcode('custom-gallery', 	array($this, 'custom_gallery_shortcode'));

	}

	public function custom_gallery_stylesheet()
	{
		custom_register_style('custom-gallery', 'assets/css/custom-gallery.css', self::$PLUGIN_URI, self::$PLUGIN_PATH, array(), CHILD_THEME_VERSION);
	}


	/**
	 * Builds the Gallery shortcode output.
	 *
	 * This implements the functionality of the Gallery Shortcode for displaying
	 * WordPress images on a post.
	 *
	 * @since 2.5.0
	 *
	 * @staticvar int $instance
	 *
	 * @param array $attr {
	 *     Attributes of the gallery shortcode.
	 *
	 *     @type string       $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
	 *     @type string       $orderby    The field to use when ordering the images. Default 'menu_order ID'.
	 *                                    Accepts any valid SQL ORDERBY statement.
	 *     @type int          $id         Post ID.
	 *     @type string|array $size       Size of the images to display. Accepts any valid image size, or an array of width
	 *                                    and height values in pixels (in that order). Default 'thumbnail'.
	 *     @type string       $ids        A comma-separated list of IDs of attachments to display. Default empty.
	 *     @type string       $include    A comma-separated list of IDs of attachments to include. Default empty.
	 *     @type string       $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
	 *     @type string       $link       What to link each image to. Default empty (links to the attachment page).
	 *                                    Accepts 'file', 'none'.
	 * }
	 * @return string HTML content to display gallery.
	 */
	public function custom_gallery_shortcode($attr)
	{
		$post = get_post();

		static $instance = 0;
		$instance++;

		custom_enqueue_style('custom-gallery');

		if (!empty($attr['ids'])) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if (empty($attr['orderby'])) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		/**
		 * Filters the default gallery shortcode output.
		 *
		 * If the filtered output isn't empty, it will be used instead of generating
		 * the default gallery template.
		 *
		 * @since 2.5.0
		 * @since 4.2.0 The `$instance` parameter was added.
		 *
		 * @see gallery_shortcode()
		 *
		 * @param string $output   The gallery output. Default empty.
		 * @param array  $attr     Attributes of the gallery shortcode.
		 * @param int    $instance Unique numeric ID of this gallery shortcode instance.
		 */
		$output = apply_filters('post_gallery', '', $attr, $instance);
		if ($output != '')
			return $output;

		$html5 = current_theme_supports('html5', 'gallery');
		$atts = shortcode_atts(array(
			'order'      	=> 'ASC',
			'orderby'    	=> 'menu_order ID',
			'gallery-id' 	=> '',
			'id'         	=> $post ? $post->ID : 0,
			'size'       	=> 'thumbnail',
			'include'    	=> '',
			'exclude'    	=> '',
			'link'       	=> ''
		), $attr, 'gallery');

		$id = intval($atts['id']);
		$gallery_id = $atts['gallery-id'];

		/* CSS style output */
		$selector = $gallery_id;
		/* $selector = "gallery-{$instance}"; original */

		/* Retrieve attachments */
		if (!empty($atts['include'])) {
			$_attachments = get_posts(array('include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));

			$attachments = array();
			foreach ($_attachments as $key => $val) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif (!empty($atts['exclude'])) {
			$attachments = get_children(array('post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
		} else {
			$attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
		}
		$attachments = apply_filters('cgs_media', $attachments, $id);


		/* Gallery content output */
		$button_id = is_user_logged_in() ? 'upload-picture' : '';
		$size_class = sanitize_html_class($atts['size']);

		$gallery_style = '';
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-size-{$size_class}'>";
		$output .= apply_filters('gallery_style', $gallery_style . $gallery_div);


		ob_start();
		?>

		<div class="add-picture-button">
			<button class="tooltip-onclick" id="<?= $button_id; ?>" data-tooltip-id="<?php echo is_user_logged_in() ? '' : 'join_us'; ?>" title="<?= __('You cooked this recipe ? Upload your own picture here', 'foodiepro'); ?>"><?= __('Add a picture', 'foodiepro'); ?></button>
			<?php
					if (is_user_logged_in()) {
						$args = array(
							'content' 	=> $this->get_file_upload_form(),
							'valign' 	=> 'above',
							'halign'	=> 'left',
							'action'	=> 'click',
							'class'		=> 'fu-form modal',
							'title'		=> __('Upload your picture', 'foodiepro'),
							'img'		=> CHILD_THEME_URL . '/images/popup-icons/add_pic.png'
						);
						Tooltip::display($args);
					}
					?>
		</div>

		<?php
				if (!empty($attachments)) {
					// Loop through gallery pictures
					foreach ($attachments as $id => $attachment) {
						$attr = (trim($attachment->post_excerpt)) ? array('aria-describedby' => "$selector-$id") : '';
						$image = wp_get_attachment_image($id, $atts['size'], false, $attr);
						$url = wp_get_attachment_url($id);
						$title = get_the_title( $id );
						?>
				<div class="gallery-item">
					<div class="gallery-icon">
						<a href="<?= $url; ?>" title="<?= $title; ?>" id="lightbox"><?= $image; ?></a>
					</div>
				</div>
				<?php }
				}
				?>

</div>
<?php
		$output .= ob_get_contents();
		ob_clean();

		return $output;
	}

	public function get_file_upload_form()
	{
		$html = do_shortcode('
		[fu-upload-form title="" suppress_default_fields="true" append_to_post="true"]' .
		// Vous avez aimé ce plat ? Envoyez votre plus belle photo pour en illustrer la recette !
		// __('Did you like this plate ? Upload your own picture here','foodiepro') .
			'[input type="text" name="fu_title" id="title" class="" description="' . __('Picture title', 'foodiepro') . '"]
			[input type="file" name="photo" id="ug_photo" class="required" description=""]
			[input type="submit" class="btn" value="Envoyer"]
			[/fu-upload-form]');

			return $html;
	}





}
