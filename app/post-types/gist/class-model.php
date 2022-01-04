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

use Gist_Sync\Core\Models\Post_Type;
use Gist_Sync\App\Post_Types\Gist\Controller;
use Gist_Sync\App\Modules\Gist_Api;
use Gist_Sync\App\Post_Types\Gist\Controller as Gist;
use Gist_Sync\App\Post_Types\GistFile\Controller as GistFile;

/**
 * Class Controller
 *
 * @package Gist_Sync\App\Post_Types\Gist
 */
class Model extends Post_Type {
	/**
	 * Gist link meta key.
	 *
	 * @since 1.0.0
	 *
	 * @var string The gist link meta key.
	 */
	private $gist_meta_key = 'gist_sync_meta';

	/**
	 * Gist id meta key.
	 *
	 * @since 1.0.0
	 *
	 * @var string The gist id meta key.
	 */
	private $gist_id_meta_key = 'gist_sync_post_gist_id';


	/**
	 * Save Post Type. Usually triggered on save_post action.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_ID The post ID.
	 * @param WP_Post $post The WP_Post object.
	 * @param bool    $update Whether this is an existing post being updated or a new one.
	 *
	 * @return void Save Post Type.
	 */
	public function save_gist( array $files = array() ) {
		$files        = $this->format_files( $files );
		$synced_files = $this->sync( $files );

		// TODO
		// We might need to delete files.

		// if ( ! $synced_files ) {
			// Logger::log( __( 'Could not sync files on gist.github.com ) );
		// }
	}

	/**
	 * Prepare files array structure to be gist api compatible.
	 *
	 * @since 1.0.0
	 *
	 * @param array $files The files list.
	 *
	 * @return array $files Formatted files ready for gist api
	 */
	public function format_files( array $files = array() ) {
		if ( empty( $files ) ) {
			return $files;
		}

		$prepared_files = array();

		foreach ( $files as $file_name => $file_content ) {
			$file_content                 = sanitize_textarea_field( $file_content );
			$file_name                    = sanitize_text_field( $file_name );
			$prepared_files[ $file_name ] = array( 'content' => $file_content );
		}

		return $prepared_files;
	}

	/**
	 * Sync Post Type. Usually triggered on save_post action.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WP_Post object.
	 * @param Array   $files The list of files to be synced in gist.github.com.
	 *
	 * @return bool|WP_Error True if synced succesfully or WP_Error if synced failed.
	 */
	protected function sync( array $files = array() ) {
		$post          = Gist::instance()->__get( 'wp_post' );
		$api           = Gist_Api::init( 'username', 'token' );
		$gist_meta     = $this->get_gist_meta( $post );
		$gist_link     = isset( $gist_meta['url'] ) ? $gist_meta['url'] : '';
		$gist_id       = isset( $gist_meta['id'] ) ? $gist_meta['id'] : '';
		$api_response  = null;
		$error_message = esc_html__( 'Something is wrong', 'gist-sync' );
		$current_files = array_keys( $api->get_gist_files( $gist_id ) );
		$remove_files  = array_diff( $current_files, array_keys( $files ) );

		if ( ! empty( $remove_files ) ) {
			foreach ( $remove_files as $file_to_be_removed ) {
				/**
				 * To delete a gist file, set it's content to empty:
				 * https://github.community/t/deleting-or-renaming-files-in-a-multi-file-gist-using-github-api/170967
				 */
				$files[ $file_to_be_removed ] = array( 'content' => '' );
			}
		}

		$api_response = ( ! empty( $gist_id ) ) ?
			$api->update_gist( $gist_id, $post->post_title, $files ) :
			$api->add_gist( $post->post_title, $files );

		if ( ! $api_response instanceof \WP_HTTP_Response ) {
			return new \WP_Error(
				'gist-sync-failled',
				$error_message
			);
		}

		$response_data = \json_decode( $api_response->get_data(), true );

		if ( 201 !== $api_response->get_status() ) {
			$error_message = isset( $response_data['message'] ) ? esc_html( $response_data['message'] ) : $error_message;

			return new \WP_Error(
				'gist-sync-failled',
				$error_message
			);
		}

		$gist_url  = isset( $response_data['url'] ) ? esc_html( $response_data['url'] ) : '';
		$gist_id   = isset( $response_data['id'] ) ? esc_html( $response_data['id'] ) : '';
		$gist_meta = \json_encode(
			array(
				'url' => $gist_url,
				'id'  => $gist_id,
			)
		);

		$this->set_gist_meta( $post, $gist_meta );


		// We might need to add/update/delete files locally and remotely.
		return $this->adjust_local_files( $api_response );
	}

	protected function adjust_local_files( $response_data ) {
		// @TODO: When option to store local files is add we need to adjust local storage in DB.
		// We need to check if insert or update

		// If insert simply insert gist files for gist

		// If update:
		// 1. update local files
		// 2. Find if any files have been removed and remove them from local (might need to remove from remote as well)

		// Then we need to check if there are any files to delete or update
	}

	protected function update_gist_local() {

	}

	protected function insert_gist_local() {

	}

	/**
	 * Sync Post Type. Usually triggered on save_post action.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WP_Post object.
	 * @param Array   $files The list of files to be synced in gist.github.com.
	 *
	 * @return void Save Post Type.
	 */
	protected function get_files_to_remove( \WP_Post $post, array $files = array() ) {
		global $wpdb;
		$gistfile = new GistFile();

		$post_files = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_title FROM {$wpdb->posts} WHERE post_parent=%d AND post_type=%s",
				$post->ID,
				$gistfile->get_slug()
			),
			$gistfile
		);

		if ( empty( $post_files ) ) {
			return array();
		}
	}

	/**
	 * Set the gist meta..
	 *
	 * @param WP_Post     $post The WP_Post object.
	 *
	 * @param string|json $meta The gist meta. Includes gist id and url.
	 *
	 * @return void
	 */
	protected function set_gist_meta( \WP_Post $post, string $meta = '' ) {
		\update_post_meta( $post->ID, $this->gist_meta_key, \sanitize_text_field( $meta ) );
	}

	/**
	 * Get the gist meta from post meta.
	 *
	 * @param WP_Post $post The WP_Post object.
	 *
	 * @return Array An array if found else empty.
	 */
	protected function get_gist_meta( \WP_Post $post ) {
		return \json_decode(
			\get_post_meta( $post->ID, $this->gist_meta_key, true ),
			true
		);
	}
}
