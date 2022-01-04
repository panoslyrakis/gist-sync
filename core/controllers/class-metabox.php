<?php
/**
 * An Abstract class for Metabox.
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
abstract class Metabox extends Base {
	/**
	* Use the Enqueue Trait.
	*
	* @since 1.0.0
	*/
	use Enqueue;

	/**
	 * The Metabox id.
	 *
	 * @var string $id The name/id of the metabox.
	 *
	 * @since 1.0.0
	 */
	public $id;

	/**
	 * The Post.
	 *
	 * @var \WP_Post $post The post that uses this metabox.
	 *
	 * @since 1.0.0
	 */
	public $post;

	/**
	 * The Post Type.
	 *
	 * @var string $post_type The Post's Type of this metabox.
	 *
	 * @since 1.0.0
	 */
	public $post_type;

	/**
	 * The Metabox title.
	 *
	 * @var string $title The title of the metabox.
	 *
	 * @since 1.0.0
	 */
	public $title;

	/**
	 * The Metabox screen.
	 *
	 * @var string $screen The screen of the metabox.
	 *
	 * @since 1.0.0
	 */
	public $screen;

	/**
	 * The Metabox context.
	 *
	 * @var string $context The context of the metabox.
	 *
	 * @since 1.0.0
	 */
	public $context;

	/**
	 * The Metabox priority.
	 *
	 * @var string $priority The priority of the metabox.
	 *
	 * @since 1.0.0
	 */
	public $priority;

	/**
	 * The Metabox callback args.
	 *
	 * @var array $callback_args The callback_args of the metabox.
	 *
	 * @since 1.0.0
	 */
	public $callback_args;

	/**
	 * Init Metabox.
	 *
	 * @since 1.0.0
	 *
	 * @return void Init the metabox.
	 */
	public function init( string $post_type = null, ?\WP_Post $post = null ) {
		$this->post_type = $post_type;
		$this->post      = $post;
		$this->context   = null;
		$this->priority  = null;
		$this->context   = null;
		$this->prepare_metabox_args();

		if ( $this->can_boot() ) {
			$this->prepare_scripts();
			$this->add_meta_box();
		}
	}
	/**
	 * Set the metabox args.
	 *
	 * @since 1.0.0
	 *
	 * @return void Metabox args.
	 */
	abstract protected function prepare_metabox_args();

	/**
	 * The metabox callback function.
	 *
	 * @since 1.0.0
	 *
	 * @return void Metabox callback.
	 */
	abstract public function callback();

	/**
	 * To boot or not to boot?
	 *
	 * @since 1.0.0
	 *
	 * @return boolean Checks if admin page actions/scripts should load. Useful for enqueing scripts.
	 */
	protected function can_boot() {
		return (
			is_admin() &&
			is_callable( '\get_current_screen' ) &&
			isset( \get_current_screen()->id ) &&
			$this->screen === \get_current_screen()->id
		);
	}

	/**
	 * Register the Post Type
	 *
	 * @since 1.0.0
	 *
	 * @return void Register Post Type.
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->id,
			$this->title,
			array( $this, 'callback' ),
			$this->screen,
			( ! is_null( $this->context ) ) ? $this->context : 'advanced',
			( ! is_null( $this->priority ) ) ? $this->priority : 'default',
			( ! is_null( $this->callback_args ) ) ? $this->callback_args : null
		);
	}

}
