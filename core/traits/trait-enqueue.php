<?php
/**
 * Wrapper class for egistering and enqueueing scripts and styles.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Traits
 */

namespace Gist_Sync\Core\Traits;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Loader as Core;

/**
 * Class Core
 *
 * @package Gist_Sync\Core\Traits
 */
trait Enqueue {

	/**
	 * Styles to be registered.
	 *
	 * @since 1.0.0
	 *
	 * @return void Styles to be registered.
	 */
	public static $styles = array();

	/**
	 * JS assets url.
	 *
	 * @since 1.0.0
	 *
	 * @return void JS assets url.
	 */
	public $scripts_dir = GISTSYNC_ASSETS_URL . 'scripts/';

	/**
	 * CSS assets url.
	 *
	 * @since 1.0.0
	 *
	 * @return void CSS assets url.
	 */
	public $style_dir = GISTSYNC_ASSETS_URL . 'styles/';

	/**
	 * Set scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void Set scripts.
	 */
	public function set_scripts() {}

	/**
	 * Pepare scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void Prepare scripts.
	 */
	public function prepare_scripts() {
		if ( \method_exists( $this, 'set_scripts' ) ){
			Core::$scripts = array_merge( Core::$scripts, $this->set_scripts() );
		}
	}

	/**
	 * Register Style.
	 *
	 * @since 1.0.0
	 * 
	 * @param string $handle The handle.
	 * @param string $src The src.
	 * @param array $deps The deps.
	 * @param bollean $ver The ver.
	 * @param string $media The media.
	 *
	 * @return void Register style.
	 */
	public function register_styles( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
		error_log( 'Trait Enqueue register_style called' );
	}

	/**
	 * Generate random id. Usefull for creating element ids in scripts
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix Optional. A prefix
	 *
	 * @return string Generate unique id. Not completelly random, it is predictable so it should not cause issues in cache.
	 */
	public function get_unique_id( $prefix = null ) : string {
		if ( is_null( $prefix ) ) {
			$prefix = uniqid() . '_';
		}
		return wp_unique_id( $prefix );
	}

}
