<?php
/*
Plugin Name: Display File Sizes
Plugin URI: http://wordpress.org/extend/plugins/display-file-sizes
Description: Shows file sizes for attachments on the attachment edit screen.
Version: 1.2
Author: desrosj
Author URI: http://jonathandesrosiers.com
License: GPLv2 or later
Text Domain: display-file-sizes
Domain Path: /languages
*/

class Display_File_Sizes {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'attachment_submitbox_misc_actions', array( $this, 'attachment_submitbox_misc_actions' ), 20 );
	}

	/**
	 * Load the plugin text domain.
	 */
	function plugins_loaded() {
		load_plugin_textdomain( 'display-file-sizes', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * This plugin is no longer needed after version 3.7.
	 */
	function admin_init() {
		global $wp_version;

		if ( version_compare( '3.7', $wp_version, '<=' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
	}

	/**
	 * When the plugin is disabled, show a message in the admin.
	 */
	function admin_notices() {
		?>
		<div class="notice notice-warning is-dismissible">
        	<p><?php printf( __( 'The Display File Sizes plugin has been disabled. This functionality was merged into WordPress in version 3.7 (<a href="%s">#25170</a>).', 'display-file-sizes' ), 'https://core.trac.wordpress.org/ticket/25170' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Display the file size on the attachment edit screen.
	 *
	 * @access public
	 * @return void
	 */
	function attachment_submitbox_misc_actions() {
		global $post, $_wp_additional_image_sizes;

		if ( $original = get_attached_file( $post->ID ) ) : ?>
			<div class="misc-pub-section file-size">
				<span><?php _e( 'File Size', 'display-file-sizes' ); ?>: <strong><?php echo $this->get_file_size( $original ); ?></strong></span>
			</div>
			<?php
		endif;
	}

	/**
	 * Returns the file size of the given URL.
	 *
	 * @access public
	 * @param string $image_url (default: '')
	 * @return void
	 */
	function get_file_size( $file_url = '' ) {
		if ( empty( $file_url ) )
			return;

		$sizes = apply_filters( 'dfs_size_types', array( 'Bytes', 'KB', 'MB', 'GB' ) );
		$file_size = filesize( esc_url( $file_url ) );

		$i = floor( log( $file_size, 1024 ) );

		if ( $i > 1 )
			return round( $file_size / pow( 1024, $i ), 1 ) . ' ' . $sizes[ $i ];
		else
			return round( $file_size / pow( 1024, $i ) ) . ' ' . $sizes[ $i ];
	}
}
$display_file_sizes = new Display_File_Sizes();