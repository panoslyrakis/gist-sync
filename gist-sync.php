<?php
/**
 * @package gist_sync
 *
 * Plugin name: Gist Sync
 * Plugin URI:  https://gist.github.com/panoslyrakis/
 * Description: A base plugin to use as a starter
 * Author:      Panos Lyrakis
 * Version:     1.0.0
 * License:     GNU General Public License (Version 2 - GPLv2)
 * Text Domain: gist-sync
 * Domain Path: /languages
 */

namespace Gist_Sync;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

// Plugin version.
if ( ! defined( 'GISTSYNC_VERSION' ) ) {
	define( 'GISTSYNC_VERSION', '1.0.0' );
}

// Define GISTSYNC_PLUGIN_FILE.
if ( ! defined( 'GISTSYNC_PLUGIN_FILE' ) ) {
	define( 'GISTSYNC_PLUGIN_FILE', __FILE__ );
}

// Plugin directory.
if ( ! defined( 'GISTSYNC_DIR' ) ) {
	define( 'GISTSYNC_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin url.
if ( ! defined( 'GISTSYNC_URL' ) ) {
	define( 'GISTSYNC_URL', plugin_dir_url( __FILE__ ) );
}
// Assets url.
if ( ! defined( 'GISTSYNC_ASSETS_URL' ) ) {
	define( 'GISTSYNC_ASSETS_URL', plugin_dir_url( __FILE__ ) . trailingslashit( 'assets' ) );
}

// Scripts version.
if ( ! defined( 'GISTSYNC_SCIPTS_VERSION' ) ) {
	define( 'GISTSYNC_SCIPTS_VERSION', '1.0.0' );
}

// Autoloader.
require_once plugin_dir_path( __FILE__ ) . '/core/utils/autoloader.php';

/**
 * Run plugin activation hook to setup plugin.
 *
 * @since 1.0.0
 */


// Make sure gist_sync is not already defined.
if ( ! function_exists( 'gist_sync' ) ) {
	/**
	 * Main instance of plugin.
	 *
	 * Returns the main instance of gist_sync to prevent the need to use globals
	 * and to maintain a single copy of the plugin object.
	 * You can simply call Gist_Sync\gist_sync() to access the object.
	 *
	 * @since  1.0.0
	 *
	 * @return Gist_Sync\Core\Loader
	 */
	function gist_sync() {
		return Core\Loader::instance();
	}

	// Init the plugin and load the plugin instance for the first time.
	add_action( 'plugins_loaded', 'Gist_Sync\\gist_sync' );
}
