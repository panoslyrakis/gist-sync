<?php
/**
 * The Admin page for listing images data.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Admin_Pages\Gist_Sync
 */

namespace Gist_Sync\App\Admin_Pages\Gist_Sync;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Controllers\Admin_Page;
use Gist_Sync\App\Admin_Pages\Gist_Sync\View;


/**
 * Class Controller
 *
 * @package Gist_Sync\App\Admin_Pages\Gist_Sync
 */
class Controller extends Admin_Page {

	/**
	 * A unique id.
	 *
	 * @since 1.0.0
	 *
	 * @var int A unique id to be used with React and JS in general.
	 */
	private $unique_id;

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 1.0.0
	 *
	 * @return void Prepares properties of the Admin page.
	 */
	public function prepare_props() {
		$this->unique_id  = $this->get_unique_id();
		$this->page_title = __( 'Gists Dashboard', 'gist-sync' );
		$this->menu_title = __( 'Gists Dashboard', 'gist-sync' );
		$this->capability = 'manage_options';
		$this->menu_slug  = 'gist_sync';
		// From https://github.com/Rush/Font-Awesome-SVG-PNG/blob/master/black/svg/github.svg?short_path=9aa472a. To match colors in admin menu, add `fill="black"` in the `<path>` tag.
		$this->icon_url   = 'data:image/svg+xml;base64,' . base64_encode('<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M896 128q209 0 385.5 103t279.5 279.5 103 385.5q0 251-146.5 451.5t-378.5 277.5q-27 5-40-7t-13-30q0-3 .5-76.5t.5-134.5q0-97-52-142 57-6 102.5-18t94-39 81-66.5 53-105 20.5-150.5q0-119-79-206 37-91-8-204-28-9-81 11t-92 44l-38 24q-93-26-192-26t-192 26q-16-11-42.5-27t-83.5-38.5-85-13.5q-45 113-8 204-79 87-79 206 0 85 20.5 150t52.5 105 80.5 67 94 39 102.5 18q-39 36-49 103-21 10-45 15t-57 5-65.5-21.5-55.5-62.5q-19-32-48.5-52t-49.5-24l-20-3q-21 0-29 4.5t-5 11.5 9 14 13 12l7 5q22 10 43.5 38t31.5 51l10 23q13 38 44 61.5t67 30 69.5 7 55.5-3.5l23-4q0 38 .5 88.5t.5 54.5q0 18-13 30t-40 7q-232-77-378.5-277.5t-146.5-451.5q0-209 103-385.5t279.5-279.5 385.5-103zm-477 1103q3-7-7-12-10-3-13 2-3 7 7 12 9 6 13-2zm31 34q7-5-2-16-10-9-16-3-7 5 2 16 10 10 16 3zm30 45q9-7 0-19-8-13-17-6-9 5 0 18t17 7zm42 42q8-8-4-19-12-12-20-3-9 8 4 19 12 12 20 3zm57 25q3-11-13-16-15-4-19 7t13 15q15 6 19-6zm63 5q0-13-17-11-16 0-16 11 0 13 17 11 16 0 16-11zm58-10q-2-11-18-9-16 3-14 15t18 8 14-14z"/></svg>');
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void The callback function of the Admin Menu Page.
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
	 * @return array Register scripts for the admin page.
	 */
	public function set_scripts() {
		return array(
			'gist_sync_admin_page' => array(
				'src'       => $this->scripts_dir . 'admin-pages/gist-sync/main.js',
				'deps'      => array( 'react', 'wp-element', 'wp-i18n', 'wp-is-shallow-equal', 'wp-polyfill' ),
				'ver'       => GISTSYNC_SCIPTS_VERSION,
				//'ver'       => time(),
				'in_footer' => true,
				'localize'  => array(
					'gist_sync' => array(
						'data'   => array(
							'rest_url'       => esc_url_raw( rest_url() ),
							'rest_namespace' => '/gist_sync/v1/fetch_images',
							'unique_id'      => $this->unique_id,
							'nonce'          => wp_create_nonce( 'wp_rest' ),
						),
						'labels' => array(
							'page_title'     => $this->page_title,
							'fetch_images'   => __( 'Fetch Images', 'gist-sync' ),
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
