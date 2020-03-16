<?php
/**
 * Plugin Name:  Custom Recent Post Widget
 * Plugin URI:   https://goutu.org
 * Description:  Enables advanced widget that gives you total control over the output of your site’s most recent Posts.
 * Version:      0.9.9.7
 * Author:       Pascal Olive
 * Author URI:   https://goutu.org
 * Author Email: contact@goutu.org
 * Text Domain:  custom-recent-post-widget
 * Domain Path:  /languages
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package    Custom_Recent_Post_Widget
 * @since      1.0
 * @author     Pascal Olive
 * @copyright  goutu.org
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once 'includes/class-recent-posts-widget.php';
require_once 'public/class-rpwe-customizations.php';

require_once 'includes/resizer.php';
require_once 'includes/functions.php';
require_once 'includes/shortcode.php';
require_once 'includes/helpers.php';

require_once 'classes/widget.php';

add_action('plugins_loaded', 'custom_recent_posts_start');
function custom_recent_posts_start()
{
	new Custom_Recent_Posts_Widget();
}

// Set the constants needed by the plugin.
add_action('plugins_loaded', 'custom_recent_posts_constants');
function custom_recent_posts_constants() {
	// Set constant path to the plugin directory.
	define( 'RPWE_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
	// Set the constant path to the plugin directory URI.
	define( 'RPWE_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
	// Set the constant path to the includes directory.
	define('RPWE_PARTIALS', RPWE_DIR . trailingslashit('partials'));
	// Set the constant path to the includes directory.
	define( 'RPWE_CLASS', RPWE_DIR . trailingslashit( 'classes' ) );
	// Set the constant path to the assets directory.
	define( 'RPWE_ASSETS', RPWE_URI . trailingslashit( 'assets' ) );
}

// Internationalize the text strings used.
add_action('widgets_init', 'custom_recent_posts_i18n');
function custom_recent_posts_i18n() {
	load_plugin_textdomain( 'recent-posts-widget-extended', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
