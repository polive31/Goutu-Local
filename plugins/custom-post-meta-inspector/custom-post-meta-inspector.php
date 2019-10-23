<?php
/**
 * Plugin Name: Custom Post Meta Inspector
 * Plugin URI: http://wordpress.org/extend/plugins/custom-post-meta-inspector/
 * Description: Peer inside your post meta. Admins can view post meta for any post from a simple meta box.
 * Author: Pascal Olive
 * Version: 1.1.1
 * Author URI: http://automattic.com/
 */

define( 'CUSTOM_POST_META_INSPECTOR_VERSION', '1.1.1' );

class Custom_Post_Meta_Inspector
{
	const MAX_COLUMNS = 4;

	private static $instance;

	public $view_cap;

	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Custom_Post_Meta_Inspector;
			self::setup_actions();
		}
		return self::$instance;
	}

	private function __construct() {
		/** Do nothing **/
	}

	private static function setup_actions() {

		add_action( 'init', array( self::$instance, 'action_init') );
		add_action( 'add_meta_boxes', array( self::$instance, 'action_add_meta_boxes' ) );
	}

	/**
	 * Init i18n files
	 */
	public function action_init() {
		load_plugin_textdomain( 'custom-post-meta-inspector', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add the post meta box to view post meta if the user has permissions to
	 */
	public function action_add_meta_boxes() {

		$this->view_cap = apply_filters( 'pmi_view_cap', 'manage_options' );
		if ( ! current_user_can( $this->view_cap ) || ! apply_filters( 'pmi_show_post_type', '__return_true', get_post_type() ) )
			return;

		add_meta_box( 'custom-post-meta-inspector', __( 'Custom Post Meta Inspector', 'custom-post-meta-inspector' ), array( self::$instance, 'custom_post_meta_inspector' ), get_post_type() );
	}

	public function custom_post_meta_inspector() {
		$toggle_length = apply_filters( 'pmi_toggle_long_value_length', 0 );
		$toggle_length = max( intval($toggle_length), 0);
		$toggle_el = '<a href="javascript:void(0);" class="pmi_toggle">' . __( 'Click to show&hellip;', 'custom-post-meta-inspector' ) . '</a>';
		?>
		<style>
			#post-meta-inspector table {
				text-align: left;
				width: 100%;
			}
			#post-meta-inspector table .key-column {
				display: inline-block;
				width: 20%;
			}
			#post-meta-inspector table .value-column {
				display: inline-block;
				width: 79%;
			}
			#post-meta-inspector code {
				word-wrap: break-word;
			}
		</style>

		<?php $post_ID=get_the_ID(); ?>
		<?php $custom_fields = get_post_meta( $post_ID ); ?>


		<p>
			<?php
				$hook_name = 'wp_insert_post';
				global $wp_filter;
				$vardump=print_r( $wp_filter[$hook_name], true );
				// var_dump( $wp_filter[$hook_name] );
			?>
			<pre>
				<?php echo $vardump ;?>
			</pre>
		</p>

		<table>
			<thead>
				<tr>
					<th class="key-column"><?php _e( 'Key', 'post-meta-inspector' ); ?></th>
					<th class="value-column"><?php _e( 'Value', 'post-meta-inspector' ); ?></th>
				</tr>
			</thead>
			<tbody>


		<?php foreach( $custom_fields as $key => $values ) :
				if ( apply_filters( 'pmi_ignore_post_meta_key', false, $key ) )
					continue;
				$values=get_post_meta( $post_ID, $key, true );
				if (!is_array($values)) {
					$values=array(0=>$values);
				}
		?>

			<tr>
			<td class="key-column"><?php echo esc_html( $key ); ?></td>
			<?php
				$i=0;
				foreach( $values as $value ) : ?>
			<?php
				// $value = unserialize( $value );
				$value = var_export( $value, true );
			?>
				<!-- <td class="value-column"><?php if( $toggled ) echo $toggle_el; ?><code <?php if( $toggled ) echo ' style="display: none;"'; ?>><?php echo esc_html( $value ); ?></code></td> -->
				<!-- <td class="value-column"><?php if( $toggled ) echo $toggle_el; ?><pre <?php if( $toggled ) echo ' style="display: none;"'; ?>><?php echo esc_html( $value ); ?></pre></td> -->
				<!-- <td class="value-column"><pre><?php echo esc_html( $i . ' => ' . $value ); ?></pre></td> -->
				<td class="value-column"><pre><?php echo esc_html( $value ); ?></pre></td>
			<?php
				if (++$i == self::MAX_COLUMNS) {?>
					</tr>
					<tr>
						<td class="key-column"><?php echo esc_html( $key ) . ' (cont\'d)'; ?></td>
			<?php
					$i=0;
					};
				endforeach; ?>
			</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
		<script>
		jQuery(document).ready(function() {
			jQuery('.pmi_toggle').click( function(e){
				jQuery('+ code', this).show();
				jQuery(this).hide();
			});
		});
		</script>
		<?php
	}

}

function Custom_Post_Meta_Inspector() {
	return Custom_Post_Meta_Inspector::instance();
}
add_action( 'plugins_loaded', 'Custom_Post_Meta_Inspector' );
