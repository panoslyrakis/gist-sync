<?php
/**
 * The Post Type for Gists. Each Gist CPT contains it's data and will be parent to GistFile CPTs.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Post_Types\Gist
 */

namespace Gist_Sync\App\Post_Types\Gist;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Controllers\Post_Type;
use Gist_Sync\App\Post_Types\Gist\Model;
use Gist_Sync\App\Metaboxes;

/**
 * Class Controller
 *
 * @package Gist_Sync\App\Post_Types\Gist
 */
class Controller extends Post_Type {
	/**
	 * The Files list.
	 *
	 * @var array $files An array containing all gist files.
	 *
	 * @since 1.0.0
	 */
	protected $files = array();

	/**
	 * Set Post Type slug
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type Slug.
	 */
	protected function set_slug() {
		$this->slug = 'gist';
	}

	/**
	 * Set Gist files
	 *
	 * @since 1.0.0
	 * 
	 * @param array Files list array.
	 *
	 * @return void Set the Gist files.
	 */
	protected function set_files( Array $files = array() ) {
		$this->files = $files;
	}

	/**
	 * Get Gist files
	 *
	 * @since 1.0.0
	 * 
	 * @return array Files list.
	 */
	protected function get_files() {
		return $this->files;
	}

	/**
	 * Set Post Type labels
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type Labels.
	 */
	protected function set_labels() {
		$this->labels = array(
			'name'                  => _x( 'Gists', 'Post type general name', 'gist-sync' ),
			'singular_name'         => _x( 'Gist', 'Post type singular name', 'gist-sync' ),
			'menu_name'             => _x( 'Gists', 'Admin Menu text', 'gist-sync' ),
			'name_admin_bar'        => _x( 'Gist', 'Add New on Toolbar', 'gist-sync' ),
			'add_new'               => __( 'Add New', 'gist-sync' ),
			'add_new_item'          => __( 'Add New gist', 'gist-sync' ),
			'new_item'              => __( 'New gist', 'gist-sync' ),
			'edit_item'             => __( 'Edit gist', 'gist-sync' ),
			'view_item'             => __( 'View gist', 'gist-sync' ),
			'all_items'             => __( 'Gists', 'gist-sync' ),
			'search_items'          => __( 'Search gists', 'gist-sync' ),
			'parent_item_colon'     => __( 'Parent gists:', 'gist-sync' ),
			'not_found'             => __( 'No gists found.', 'gist-sync' ),
			'not_found_in_trash'    => __( 'No gists found in Trash.', 'gist-sync' ),
			'featured_image'        => _x( 'Gist Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'gist-sync' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'gist-sync' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'gist-sync' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'gist-sync' ),
			'archives'              => _x( 'Gist archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'gist-sync' ),
			'insert_into_item'      => _x( 'Insert into gist', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'gist-sync' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this gist', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'gist-sync' ),
			'filter_items_list'     => _x( 'Filter gists list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'gist-sync' ),
			'items_list_navigation' => _x( 'Gists list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'gist-sync' ),
			'items_list'            => _x( 'Gists list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'gist-sync' ),
		);
	}

	/**
	 * Set Post Type args.
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type args.
	 */
	protected function set_args() {
		$this->args = array(
			'labels'             => $this->get_labels(),
			'description'        => __( 'Gist post type that holds Gist data and GistFiles CPTs', 'gist-sync' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			//'show_in_menu'       => 'gist_sync',
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'gist' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			// From https://github.com/Rush/Font-Awesome-SVG-PNG/blob/master/black/svg/github.svg?short_path=9aa472a. To match colors in admin menu, add `fill="black"` in the `<path>` tag.
			'menu_icon'          => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M896 128q209 0 385.5 103t279.5 279.5 103 385.5q0 251-146.5 451.5t-378.5 277.5q-27 5-40-7t-13-30q0-3 .5-76.5t.5-134.5q0-97-52-142 57-6 102.5-18t94-39 81-66.5 53-105 20.5-150.5q0-119-79-206 37-91-8-204-28-9-81 11t-92 44l-38 24q-93-26-192-26t-192 26q-16-11-42.5-27t-83.5-38.5-85-13.5q-45 113-8 204-79 87-79 206 0 85 20.5 150t52.5 105 80.5 67 94 39 102.5 18q-39 36-49 103-21 10-45 15t-57 5-65.5-21.5-55.5-62.5q-19-32-48.5-52t-49.5-24l-20-3q-21 0-29 4.5t-5 11.5 9 14 13 12l7 5q22 10 43.5 38t31.5 51l10 23q13 38 44 61.5t67 30 69.5 7 55.5-3.5l23-4q0 38 .5 88.5t.5 54.5q0 18-13 30t-40 7q-232-77-378.5-277.5t-146.5-451.5q0-209 103-385.5t279.5-279.5 385.5-103zm-477 1103q3-7-7-12-10-3-13 2-3 7 7 12 9 6 13-2zm31 34q7-5-2-16-10-9-16-3-7 5 2 16 10 10 16 3zm30 45q9-7 0-19-8-13-17-6-9 5 0 18t17 7zm42 42q8-8-4-19-12-12-20-3-9 8 4 19 12 12 20 3zm57 25q3-11-13-16-15-4-19 7t13 15q15 6 19-6zm63 5q0-13-17-11-16 0-16 11 0 13 17 11 16 0 16-11zm58-10q-2-11-18-9-16 3-14 15t18 8 14-14z"/></svg>' ),
			// 'supports'           => array( 'title', 'author' ),
			'show_in_rest'       => true,
		);
	}

	/**
	 * Set Post Type meta boxes
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type Metaboxes.
	 */
	public function set_metaboxes() {
		$this->metaboxes = array(
			Metaboxes\Gistfile\Controller::instance(),
		);
	}

	/**
	 * Set Post Type custom hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type Hooks.
	 */
	public function hooks() {
		\add_action( 'admin_menu', array( $this, 'add_submenus' ) );
		\add_action( 'save_post_gist', array( $this, 'save_gist_files' ), 10, 3 );
	}

	/**
	 * Save and sync the gist files in meta using Model.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_ID The post ID.
	 * @param WP_Post $post The WP_Post object.
	 * @param bool    $update Whether this is an existing post being updated or a new one.
	 *
	 * @return void Save the gist files.
	 */
	public function save_gist_files( int $post_ID, \WP_Post $post = object, bool $update = false ) {
		if (
			! isset( $_REQUEST[ 'action' ] ) ||
			! current_user_can( 'edit_post', $post_ID ) ||
			empty( $_REQUEST[ 'action' ] ) ||
			\wp_is_post_revision( $post_ID ) ||
			\wp_is_post_autosave( $post_ID ) ||
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			) {
			return;
		}

		$formated_files = array();
		$files          = filter_input( INPUT_POST, 'wpgist-filedata', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$files = ! is_array( $files ) ? array() : $files;

		$this->__set( 'wp_post', $post );
		Model::instance()->save_gist( $files, $update );
	}

	/**
	 * Set Post Type Submenus.
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type Submenus at Dashboard admin menu.
	 */
	public function add_submenus() {
		// No need to add the Gists submenu as we're using it in show_in_menu
		
		\add_submenu_page(
			'gist_sync',
			__( 'Gists', 'gist-sync' ),
			__( 'Gists', 'gist-sync' ),
			'manage_options',
			"edit.php?post_type={$this->slug}"
		);
		

		\add_submenu_page(
			'gist_sync',
			__( 'Add Gist', 'gist-sync' ),
			__( 'Add Gist', 'gist-sync' ),
			'manage_options',
			"post-new.php?post_type={$this->slug}"
		);
	}
}
