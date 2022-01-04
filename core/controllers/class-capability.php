<?php
/**
 * The capability class of the plugin.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Controllers
 */

namespace Gist_Sync\Core\Controllers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_User;
use Gist_Sync\Core\Helpers\Permission;
use Gist_Sync\Core\Utils\Abstracts\Base;

/**
 * Class Capability
 *
 * @package Gist_Sync\Core\Controllers
 */
class Capability extends Base {

	/**
	 * Custom capability for settings.
	 *
	 * @since 1.0.0
	 *
	 * @var string $settings_cap
	 */
	const SETTINGS_CAP = 'pluginbase_manage_settings';

	/**
	 * Initialize the class by registering hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		/**
		 * TODO
		 * Use a hooks trait for adding actions/filters
		 */
		// Set capability to roles after settings update.
		add_action( 'pluginbase_settings_update', array( $this, 'set_settings_capability' ), 10, 3 );

		// Filter settings capabilities.
		add_filter( 'user_has_cap', array( $this, 'filter_settings_role_cap' ), 10, 3 );
		add_filter( 'user_has_cap', array( $this, 'filter_settings_user_cap' ), 11, 3 );
	}

	/**
	 * Update the role capabilities based on the settings.
	 *
	 * When settings are updated, we need to re-assign the settings
	 * capability to the selected roles and users in settings.
	 *
	 * @param array $options Old values.
	 * @param array $values  New values.
	 * @param bool  $network Network flag.
	 *
	 * @global      $wp_roles
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function set_settings_capability( $options, $values, $network ) {
		// Chill. It's network admin.
		if ( $network && is_multisite() ) {
			return;
		}

		// Can subsites overwrite?.
		$can_overwrite = Permission::can_overwrite( 'settings' );

		// Get enabled roles.
		$enabled_roles = (array) gist_sync()->settings->get( 'settings_roles', 'permissions', ! $can_overwrite, array() );

		global $wp_roles;

		// Make sure admin user has the capability in single installations.
		if ( ! in_array( 'administrator', $enabled_roles, true ) ) {
			$enabled_roles = array_merge( array( 'administrator' ), $enabled_roles );
		}

		// Loop through each roles.
		foreach ( $wp_roles->get_names() as $role => $label ) {
			// Get the role object.
			$role_object = $wp_roles->get_role( $role );

			// Role not found.
			if ( empty( $role_object ) ) {
				continue;
			}

			if ( in_array( $role, $enabled_roles, true ) ) {
				// Role is enabled in settings, so add capability.
				$role_object->add_cap( self::SETTINGS_CAP );
			} else {
				// Remove the capability if not enabled.
				$role_object->remove_cap( self::SETTINGS_CAP );
			}
		}

		// Now process users.
		$this->set_settings_capability_user();
	}

	/**
	 * Update the user capabilities based on the settings.
	 *
	 * Admins can specifically add or remove users from permission.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function set_settings_capability_user() {
		// Can subsites overwrite?.
		$can_overwrite = Permission::can_overwrite( 'settings' );

		// Get enabled and disabled users.
		$included_users = (array) gist_sync()->settings->get( 'settings_include_users', 'permissions', ! $can_overwrite, array() );
		$excluded_users = (array) gist_sync()->settings->get( 'settings_exclude_users', 'permissions', ! $can_overwrite, array() );

		// Loop through all allowed users.
		foreach ( $included_users as $user_id ) {
			$user = get_userdata( $user_id );

			// Grant settings capability to the user.
			if ( $user instanceof WP_User ) {
				$user->add_cap( self::SETTINGS_CAP, true );
			}
		}

		// Loop through all denied users.
		foreach ( $excluded_users as $user_id ) {
			$user = get_userdata( $user_id );

			// Deny settings capability to the user.
			if ( $user instanceof WP_User ) {
				$user->add_cap( self::SETTINGS_CAP, false );
			}
		}
	}

	/**
	 * Filter a user's capabilities so they can be altered at runtime.
	 *
	 * This is used to grant  the 'pluginbase_manage_settings' capability
	 * to the user if they have the ability to manage options.
	 * This does not get called for Super Admins because super admin has all capabilities.
	 *
	 * @param bool[]   $user_caps     Array of key/value pairs where keys represent a capability name and boolean values
	 *                                represent whether the user has that capability.
	 * @param string[] $required_caps Required primitive capabilities for the requested capability.
	 * @param array    $args          Arguments that accompany the requested capability check.
	 *
	 * @since 1.0.0
	 *
	 * @return bool[] Concerned user's capabilities.
	 */
	public function filter_settings_role_cap( $user_caps, $required_caps, $args ) {
		// Our custom settings capability is not being checked.
		if ( self::SETTINGS_CAP !== $args[0] ) {
			return $user_caps;
		}

		$override = Permission::can_overwrite( 'settings' );

		// Get enabled roles.
		$roles = (array) gist_sync()->settings->get( 'settings_roles', 'permissions', ! $override, array() );

		// Make sure admin user has the capability in single installations and subsites.
		$roles = array_merge( array( 'administrator' ), $roles );

		// Get user object.
		$user = get_userdata( $args[1] );

		// Make sure it exists.
		if ( empty( $user->roles ) ) {
			return $user_caps;
		}

		// Get user roles.
		$user_roles = get_userdata( $args[1] )->roles;

		// Get common roles.
		$common_roles = array_intersect( $roles, $user_roles );

		// Get allowed roles.
		if ( count( $common_roles ) > 0 ) {
			$user_caps[ self::SETTINGS_CAP ] = true;
		} else {
			$user_caps[ self::SETTINGS_CAP ] = false;
		}

		return $user_caps;
	}

	/**
	 * Filter a user's capabilities so they can be altered at runtime.
	 *
	 * Forcefully include granted individual users and deny excluded individual
	 * users on run-time.
	 *
	 * @param bool[]   $user_caps     Array of key/value pairs where keys represent a capability name and boolean values
	 *                                represent whether the user has that capability.
	 * @param string[] $required_caps Required primitive capabilities for the requested capability.
	 * @param array    $args          Arguments that accompany the requested capability check.
	 *
	 * @since 1.0.0
	 *
	 * @return bool[] Concerned user's capabilities.
	 */
	public function filter_settings_user_cap( $user_caps, $required_caps, $args ) {
		// Our custom settings capability is not being checked.
		if ( self::SETTINGS_CAP !== $args[0] ) {
			return $user_caps;
		}

		// Can subsites overwrite?.
		$can_overwrite = Permission::can_overwrite( 'settings' );

		// Get enabled and disabled users.
		$included_users = (array) gist_sync()->settings->get( 'settings_include_users', 'permissions', ! $can_overwrite, array() );
		$excluded_users = (array) gist_sync()->settings->get( 'settings_exclude_users', 'permissions', ! $can_overwrite, array() );

		// Grant included user.
		// phpcs:ignore
		if ( in_array( $args[1], $included_users ) ) {
			$user_caps[ self::SETTINGS_CAP ] = true;
		}

		// Deny excluded user.
		// phpcs:ignore
		if ( in_array( $args[1], $excluded_users ) ) {
			$user_caps[ self::SETTINGS_CAP ] = false;
		}

		return $user_caps;
	}

}
