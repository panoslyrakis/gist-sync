<?php
/**
 * An Abstract class for Post Type.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Controllers
 */

namespace Gist_Sync\Core\Controllers;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Utils\Abstracts\Base;
use Gist_Sync\Core\Traits\Enqueue;

/**
 * Class Core
 *
 * @package Gist_Sync\Core\Post_Type
 */
abstract class Post_Type extends Base {
	/**
	* Use the Enqueue Trait.
	*
	* @since 1.0.0
	*/
	use Enqueue;

	/**
	 * The Post Type slug.
	 *
	 * @var string $slug The name/slug of the post type.
	 *
	 * @since 1.0.0
	 */
	public $slug;

	/**
	 * The WP_Post Object.
	 *
	 * @var string $wp_post The WP_Post object.
	 *
	 * @since 1.0.0
	 */
	public $wp_post;

	/**
	 * The Post Type labels.
	 *
	 * @var array $labels The labels of the post type.
	 *
	 * @since 1.0.0
	 */
	public $labels;

	/**
	 * The Post Type args.
	 *
	 * @var array $args The args of the post type.
	 *
	 * @since 1.0.0
	 */
	public $args;

	/**
	 * The Metaboxes.
	 *
	 * @var array $metaboxes The metaboxes of the post type.
	 *
	 * @since 1.0.0
	 */
	public $metaboxes = array();

	/**
	 * Init Post Type. Register and add metaboxes
	 *
	 * @since 1.0.0
	 *
	 * @return void Initialize the post type.
	 */
	public function init() {
		$this->set_slug();
		$this->set_labels();
		$this->set_args();
		$this->set_metaboxes();
		$this->hooks();

		\add_action( 'init', array( $this, 'register_post_type' ) );
		\add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ), 10, 2 );
	}

	/**
	 * Post Type Slug/Name
	 *
	 * @since 1.0.0
	 *
	 * @return string Slug of post type.
	 */
	public function get_slug() : string {
		return $this->slug;
	}

	/**
	 * Method to utilize custom hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void A general purpose method to add custom hooks.
	 */
	public function hooks(){}

	/**
	 * Set Post Type slug
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type Slug.
	 */
	abstract protected function set_slug();

	/**
	 * Get Post Type Labels
	 *
	 * @since 1.0.0
	 *
	 * @return array Labels of post type.
	 */
	public function get_labels() : array {
		return $this->labels;
	}

	/**
	 * Set Post Type labels
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type Labels.
	 */
	abstract protected function set_labels();

	/**
	 * Get Post Type Args
	 *
	 * @since 1.0.0
	 *
	 * @return array Args of post type.
	 */
	public function get_args() : array {
		return $this->args;
	}

	/**
	 * Set Post Type Args
	 *
	 * @since 1.0.0
	 *
	 * @return void Set Post Type Args.
	 */
	abstract protected function set_args();

	/**
	 * Register the Post Type
	 *
	 * @since 1.0.0
	 *
	 * @return void Register Post Type.
	 */
	public function register_post_type() {
		register_post_type( $this->get_slug(), $this->get_args() );
	}

	/**
	 * Register the Post Type
	 *
	 * @since 1.0.0
	 *
	 * @return void Register Post Type.
	 */
	public function add_metaboxes( $post_type, $post ) {
		$metaboxes = $this->get_metaboxes();

		if ( ! empty( $metaboxes ) ) {
			foreach ( $metaboxes as $metabox ) {
				if ( ! method_exists( $metabox, 'init' ) ) {
					continue;
				}

				$metabox->init( $post_type, $post );
			}
		}
	}

	/**
	 * Get Post Type Metaboxes
	 *
	 * @since 1.0.0
	 *
	 * @return array Array with the mateboxes.
	 */
	public function get_metaboxes() : array {
		return $this->metaboxes;
	}

	/**
	 * Add the Post Type Metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void Set Metaboxes.
	 */
	public function set_metaboxes() {}

}
