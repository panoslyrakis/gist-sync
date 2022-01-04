<?php
/**
 * An Abstract class for Post Type.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Models
 */

namespace Gist_Sync\Core\Models;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Utils\Abstracts\Base;

/**
 * Class Installer
 *
 * @package Gist_Sync\Core\Models
 */
class Post_Type extends Base {
	/**
	 * The WP_Post Object.
	 *
	 * @var string $wp_post The WP_Post object.
	 *
	 * @since 1.0.0
	 */
	public $wp_post;

	/**
	 * Save Post Type. Usually triggered on save_post action.
	 *
	 * @since 1.0.0
	 *
	 * @return void Save Post Type.
	 */
	public function save_post( $post_ID, $post, $update ) {}
}
