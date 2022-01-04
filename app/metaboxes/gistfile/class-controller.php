<?php
/**
 * The Post Type for Gists. Each Gist CPT contains it's data and will be parent to GistFile CPTs.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Metabox\Gist_File
 */

namespace Gist_Sync\App\Metaboxes\Gistfile;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Controllers\Metabox;
use Gist_Sync\App\Metaboxes\gistfile\View;

/**
 * Class Controller
 *
 * @package Gist_Sync\App\Metabox\Gist_File
 */
class Controller extends Metabox {
	/**
	 * A unique id.
	 *
	 * @since 1.0.0
	 *
	 * @var int A unique id to be used with React and JS in general.
	 */
	private $unique_id;

	/**
	 * Set Metabox args.
	 *
	 * @since 1.0.0
	 *
	 * @return void Set the args of the Metabox.
	 */
	protected function prepare_metabox_args() {
		$this->unique_id = $this->get_unique_id();
		$this->id        = 'gistfile';
		$this->title     = 'Files';
		$this->screen    = 'gist';
	}

	/**
	 * Metabox callback function.
	 *
	 * @since 1.0.0
	 *
	 * @return void The function that the metabox will use as a callback.
	 */
	public function callback() {
		View::instance()->render(
			array(
				'unique_id' => $this->unique_id,
			)
		);
	}

	/**
	 * Register scripts for the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return void Register scripts for the admin page.
	 */
	public function set_scripts() {


		return array(
			'gistfile_metabox' => array(
				'src'       => $this->scripts_dir . 'metaboxes/gistfile/main.js',
				'deps'      => array( 'react', 'wp-element', 'wp-i18n', 'wp-is-shallow-equal', 'wp-polyfill' ),
				'ver'       => GISTSYNC_SCIPTS_VERSION,
				'in_footer' => true,
				'localize'  => array(
					'gist_sync_files_metabox' => array(
						'data'   => array(
							'rest_url'       => \esc_url_raw( \rest_url() ),
							'rest_namespace' => '/gist_sync/v1/gistfile',
							'unique_id'      => $this->unique_id,
							'nonce'          => \wp_create_nonce( 'wp_rest' ),
							'gist_id'        => ( $this->post instanceof \WP_Post ) ? intval( $this->post->ID ) : null,
						),
						'labels' => array(
							'title'              => $this->title,
							'add_file_btn_title' => __( 'Add file', 'gist-sync' ),
							'add_btn_title'      => __( 'Add', 'gist-sync' ),
							'update_btn_title'   => __( 'Update', 'gist-sync' ),
							'cancel_btn_title'   => __( 'Cancel', 'gist-sync' ),
							'error_messages' => array(
								'general' => __( 'Something went wrong here.', 'gist-sync' ),
							),
						),
					),
				),
			),
		);
	}

}
