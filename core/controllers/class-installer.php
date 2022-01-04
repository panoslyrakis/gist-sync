<?php
/**
 * The installer class of the plugin.
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

// use Gist_Sync\Core\Helpers\Cache;
use Gist_Sync\Core\Helpers\General;
use Gist_Sync\Core\Utils\Abstracts\Base;

/**
 * Class Installer
 *
 * @package Gist_Sync\Core\Controllers
 */
class Installer extends Base {

	/**
	 * Run plugin activation scripts.
	 *
	 * If plugin is activated for the first time, setup the
	 * version details, and other flags.
	 * If the Pro version is being activated, check if free version is
	 * active and then deactivate it.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		// Current plugin version.
		if ( $this->is_network() ) {
			$version = get_site_option( 'pluginbase_version', '1.0.0' );
		} else {
			$version = get_option( 'pluginbase_version', '1.0.0' );
		}

		// Set plugin owner.
		$this->set_plugin_owner();
		// Assign capabilities.
		$this->assign_caps();

		/**
		 * Action hook to execute after activation.
		 *
		 * @param int Old version.
		 * @param int New version.
		 *
		 * @since 1.0.0
		 */
		do_action( 'pluginbase/after_activate', $version, GISTSYNC_VERSION );
	}

	/**
	 * Set a user meta field to identify who activated the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function set_plugin_owner() {
		// Get current user.
		$user = get_current_user_id();

		if ( ! empty( $user ) ) {
			// If network activated in multisite.
			if ( $this->is_network() ) {
				update_site_option( 'pluginbase_owner_user', $user );
			} else {
				// Single site.
				update_option( 'pluginbase_owner_user', $user );
			}
		}
	}

	/**
	 * Set our custom capability to admin user by default.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function assign_caps() {
		// Not needed in network admin.
		if ( ! $this->is_network() ) {
			global $wp_roles;

			// Get the role object.
			$role_object = $wp_roles->get_role( 'administrator' );

			// Assign settings and analytics caps.
			if ( ! empty( $role_object ) ) {
				$role_object->add_cap( Capability::SETTINGS_CAP );
			}
		}
	}

	/**
	 * On upgrade call this if you want to show the welcome modal.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function show_welcome() {
		// Set welcome modal.
		gist_sync()->settings->update(
			'show_welcome',
			true,
			'misc',
			General::is_networkwide()
		);
	}
}
