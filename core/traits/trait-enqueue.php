<?php
/**
 * Wrapper class for registering and enqueueing scripts and styles.
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
use function method_exists;

/**
 * Class Core
 *
 * @package Gist_Sync\Core\Traits
 */
trait Enqueue {

	/**
	 * Styles to be registered.
	 *
	 * @return void Styles to be registered.
	 * @since 1.0.0
	 *
	 */
	public static $styles = array();

	/**
	 * JS assets url.
	 *
	 * @return void JS assets url.
	 * @since 1.0.0
	 *
	 */
	public $scripts_dir = GISTSYNC_ASSETS_URL . 'scripts/';

	/**
	 * CSS assets url.
	 *
	 * @return void CSS assets url.
	 * @since 1.0.0
	 *
	 */
	public $style_dir = GISTSYNC_ASSETS_URL . 'styles/';

	/**
	 * Set scripts.
	 *
	 * @return void Set scripts.
	 * @since 1.0.0
	 *
	 */
	public function set_scripts() {
	}

	/**
	 * Prepare scripts.
	 *
	 * @return void Prepare scripts.
	 * @since 1.0.0
	 *
	 */
	public function prepare_scripts() {
		if ( method_exists( $this, 'set_scripts' ) ) {
			Core::$scripts = array_merge( Core::$scripts, $this->set_scripts() );
		}
	}

	/**
	 * Register Style.
	 *
	 * @param string $handle The handle.
	 * @param string $src The src.
	 * @param array $deps The deps.
	 * @param bool $ver The ver.
	 * @param string $media The media.
	 *
	 * @return void Register style.
	 * @since 1.0.0
	 *
	 */
	public function register_styles(
		string $handle, string $src, array $deps = array(), bool $ver = false, string $media =
	'all'
	) {
		error_log( 'Trait Enqueue register_style called' );
	}

	/**
	 * Generate random id. Useful for creating element ids in scripts
	 *
	 * @param string $prefix Optional. A prefix
	 *
	 * @return string Generate unique id. Not completely random, it is predictable, so it should not cause issues in
	 * cache.
	 * @since 1.0.0
	 *
	 */
	public function get_unique_id( $prefix = null ): string {
		if ( is_null( $prefix ) ) {
			$prefix = uniqid() . '_';
		}

		return wp_unique_id( $prefix );
	}

}
