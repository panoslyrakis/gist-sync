<?php
/**
 * Settings page
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Admin_Pages\Settings
 */

namespace Gist_Sync\App\Admin_Pages\Settings;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Controllers\Admin_Page;
use Gist_Sync\App\Admin_Pages\Settings\View;
use Gist_Sync\Core\Models\Settings as Settings_Api;

/**
 * Class Controller
 *
 * @package Gist_Sync\App\Admin_Pages\Settings
 */
class Controller extends Admin_Page {

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 1.0.0
	 *
	 * @return void Prepares properties of the Admin page.
	 */
	public function prepare_props() {
		$this->is_submenu  = true;
		$this->parent_slug = 'gist_sync';
		$this->unique_id   = $this->get_unique_id();
		$this->page_title  = __( 'Settings', 'gist-sync' );
		$this->menu_title  = __( 'Settings', 'gist-sync' );
		$this->capability  = 'manage_options';
		$this->menu_slug   = 'settings';
		$this->position    = 99999;
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
			'gist_sync_settings_page' => array(
				'src'       => $this->scripts_dir . 'admin-pages/settings/main.js',
				'deps'      => array( 'react', 'wp-element', 'wp-i18n', 'wp-is-shallow-equal', 'wp-polyfill' ),
				'ver'       => GISTSYNC_SCIPTS_VERSION,
				'in_footer' => true,
				'localize'  => array(
					'gist_sync_settings' => array(
						'data'   => array(
							'rest_url'       => esc_url_raw( rest_url() ),
							'rest_namespace' => '/gist_sync_settings/v1/save',
							'unique_id'      => $this->unique_id,
							'nonce'          => wp_create_nonce( 'wp_rest' ),
							'user_roles'     => wp_json_encode( $this->get_user_roles() ),
							'settings'       => wp_json_encode( Settings_Api::instance()->get() ),
						),
						'labels' => array(
							'page_title'     => $this->page_title,
							'error_messages' => array(
								'general' => __( 'Something went wrong here.', 'gist-sync' ),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Get user roles.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of all site roles.
	 */
	protected function get_user_roles() {
		global $wp_roles;

		$roles = array();

		if ( ! empty( $wp_roles->roles ) ) {
			foreach ( $wp_roles->roles as $role_key => $role_data ) {
				$roles[] = array(
					'name'  => $role_key,
					'label' => $role_data['name'],
				);
			}
		}

		return apply_filters( 'gist_sync_admin_pages_settings_user_roles', $roles );
	}

}
