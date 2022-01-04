<?php
/**
 * A Shortcode blue print.
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
 * Class Shortcode
 *
 * @package Gist_Sync\Core\Controllers
 */
abstract class Shortcode extends Base {
	/**
	 * Use the Enqueue Trait.
	 *
	 * @since 1.0.0
	 */
	use Enqueue;

	/**
	 * The Shotcode Tag.
	 *
	 * @var string $tag Tag of shortcode.
	 *
	 * @since 1.0.0
	 */
	protected $tag;

	/**
	 * A unique id. Useful for loclizing js vars when calling same shortcode multiple times in samme page.
	 *
	 * @var string $tag Unique id for shortcode.
	 *
	 * @since 1.0.0
	 */
	protected $unique_id;

	/**
	 * The Shotcode Tag.
	 *
	 * @var bool $can_enqueue To enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	 protected $can_enqueue;



	/**
	 * Init Shortcode
	 *
	 * @since 1.0.0
	 *
	 * @return void Initialize the Shortcode.
	 */
	public function init() {
		$this->add_shortcode();
		$this->prepare_scripts();

		// $this->register_styles();
		//$this->register_scripts();
	}

	/**
	 * Get Shortcode Tag.
	 *
	 * @since 1.0.0
	 *
	 * @return void Get shortcode tag
	 */
	public function get_tag() : string {
		/**
		 * Filter the shortcode tag.
		 *
		 * @since 1.0.0
		 */
		return apply_filters(
			'pluginbase/shotcode/tag',
			$this->tag,
			self::instance()
		);
	}

	/**
	 * Add Shortcode
	 *
	 * @since 1.0.0
	 *
	 * @return void Add the Shortcode.
	 */
	public function add_shortcode() {
		add_shortcode( $this->get_tag(), array( $this, 'callback' ) );
	}

	/**
	 * Shortcode Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void The callback function of the shortcode.
	 */
	abstract public function callback();

	/**
	 * Register Shortcode Styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void Register shrotcode styles.
	 */
	public function register_styles() {}

	/**
	 * Enqueue Shortcode Styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void Enqueue styles that will be enqueued in front end.
	 */
	public function enqueue_styles() {}

	/**
	 * Enqueue Shortcode Scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void Enqueue scripts that will be enqueued in front end.
	 */
	public function enqueue_scripts() {}

	/**
	 * Shortcode Scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void Scripts that will be enqueued in front end.
	 */
	public function can_enqueue() {
		global $post;

		if ( $post instanceof WP_Post && has_shortcode( $post->post_content, $this->get_tag() ) ) {
			$this->can_enqueue = true;
		}
		return $this->can_enqueue;
	}


}
