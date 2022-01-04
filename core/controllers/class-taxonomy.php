<?php
/**
 * An Abstract calls for Taxonomies.
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

// use Gist_Sync\Core\Endpoints;
// use Gist_Sync\Core\Controllers as Core_controllers;
use Gist_Sync\Core\Utils\Abstracts\Base;

/**
 * Class Core
 *
 * @package Gist_Sync\Core\Post_Type
 */
abstract class Taxonomy extends Base {

	/**
	 * The Taxonomy slug.
	 *
	 * @var slug
	 *
	 * @since 1.0.0
	 */
	public $slug;

	/**
	 * The Taxonomy Post Type(s).
	 *
	 * @var object_type
	 *
	 * @since 1.0.0
	 */
	public $object_type;

	/**
	 * Init Taxonomy. Register and add metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void Initialize the taxonomy.
	 */
	public function init() {
		$this->register_taxonomy();
	}

	/**
	 * Taxonomy Slug/Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Slug of taxonomy.
	 */
	public function get_slug() : string {
		return $this->slug;
	}

	/**
	 * Taxonomy Object Type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The Post Type associated with Taxonomy.
	 */
	public function get_object_type() : array {
		if ( ! is_array( $this->object_type ) ) {
			return array();
		}

		return $this->object_type;
	}

	/**
	 * Taxonomy labels.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array with the Labels.
	 */
	abstract public function get_labels() : array;

	/**
	 * Taxonomy Args
	 *
	 * @since 1.0.0
	 *
	 * @return array Array with the Args.
	 */
	abstract public function get_args() : array;

	/**
	 * Get Taxonomy Metaboxes
	 *
	 * @since 1.0.0
	 *
	 * @return array Array with the mateboxes.
	 */
	public function get_metaboxes() : array{}

	/**
	 * Register the Taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @return void Register Taxonomy.
	 */
	public function register_taxonomy() {
		register_taxonomy( $this->get_slug(), $this->get_object_type(), $this->get_args() );
	}

	/**
	 * Add the Post Type Metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void Add Metaboxes.
	 */
	public function add_metaboxes() {
		$metaboxes = $this->get_metaboxes();
	}

}
