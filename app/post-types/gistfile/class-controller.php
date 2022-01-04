<?php
/**
 * The Post Type for GistFiles. Each GistFile CPT contains it's data and will be parent to GistFile CPTs.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Post_Types\GistFile
 */

namespace Gist_Sync\App\Post_Types\GistFile;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Controllers\Post_Type;
// use Gist_Sync\App\Post_Types\GistFile\View;

/**
 * Class Controller
 *
 * @package Gist_Sync\App\Post_Types\GistFile
 */
class Controller extends Post_Type {
	/**
	 * Set Post Type slug
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the Post Type Slug.
	 */
	protected function set_slug() {
		$this->slug = 'gistfile';
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
			'name'                  => _x( 'GistFiles', 'Post type general name', 'gist-sync' ),
			'singular_name'         => _x( 'GistFile', 'Post type singular name', 'gist-sync' ),
			'menu_name'             => _x( 'GistFiles', 'Admin Menu text', 'gist-sync' ),
			'name_admin_bar'        => _x( 'GistFile', 'Add New on Toolbar', 'gist-sync' ),
			'add_new'               => __( 'Add New', 'gist-sync' ),
			'add_new_item'          => __( 'Add New gistfile', 'gist-sync' ),
			'new_item'              => __( 'New gist', 'gist-sync' ),
			'edit_item'             => __( 'Edit gist', 'gist-sync' ),
			'view_item'             => __( 'View gist', 'gist-sync' ),
			'all_items'             => __( 'All gists', 'gist-sync' ),
			'search_items'          => __( 'Search gists', 'gist-sync' ),
			'parent_item_colon'     => __( 'Parent gists:', 'gist-sync' ),
			'not_found'             => __( 'No gists found.', 'gist-sync' ),
			'not_found_in_trash'    => __( 'No gists found in Trash.', 'gist-sync' ),
			'featured_image'        => _x( 'GistFile Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'gist-sync' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'gist-sync' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'gist-sync' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'gist-sync' ),
			'archives'              => _x( 'GistFile archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'gist-sync' ),
			'insert_into_item'      => _x( 'Insert into gist', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'gist-sync' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this gist', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'gist-sync' ),
			'filter_items_list'     => _x( 'Filter gists list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'gist-sync' ),
			'items_list_navigation' => _x( 'GistFiles list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'gist-sync' ),
			'items_list'            => _x( 'GistFiles list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'gist-sync' ),
		);
	}

	protected function set_args() {
		$this->args = array(
			'labels'             => $this->get_labels(),
			'description'        => __( 'GistFile post type which is the file of the Gist CPT. A Gist CPT can be parent to one or multiple GistFiles', 'gist-sync' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'gistfile' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'supports'           => array( 'title', 'author' ),
			'show_in_rest'       => true,
		);
	}
}
