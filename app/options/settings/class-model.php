<?php
/**
 * The Settings model
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Options
 */

namespace Gist_Sync\App\Options\Settings;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Models\Option;

/**
 * Class Installer
 *
 * @package Gist_Sync\Core\Models
 */
class Model extends Option {
	/**
	 * The settings option key.
	 *
	 * @since 1.0.0
	 *
	 * @var string The settings option key.
	 */
	protected $option_key = 'gist-sync';

	/**
	 * Get Options from DB.
	 *
	 * @param string|null $settings_key Specific settings key. If null it returns all options.
	 *
	 * @param string|null $option_key Optional nullable option_key.
	 *
	 * @return array Returns an array with options.
	 * @since 1.0.0
	 *
	 */
	public function get_creds() {
		$settings = $this->get();
		$credentials = array();
		$credentials[ 'username' ] = $settings[ 'username' ] ?? '';
		$credentials[ 'token' ] = '';
		$use_global = isset( $settings['globaltokenstatus'] ) && $settings['globaltokenstatus'];

		if ( $use_global ) {
			$credentials[ 'token' ] = $settings[ 'globaltoken' ] ?? '';
		} else {
			$roles_allowed = $settings[ 'userrolesallowed' ] ?? '';

			if ( ! empty( $roles_allowed ) && $this->user_role_allowed( json_decode( $roles_allowed, true ) ) ) {
				$credentials[ 'token' ] = $this->get_user_token();
			}
		}

		return $credentials;
	}

	/**
	 * Check if current user in allowed user roles.
	 *
	 * @param array $roles_allowed An array specified in plugin settings that hold allowed user roles.
	 *
	 * @param int|null $user_id Optional nullable user id. If null function will use current user id.
	 *
	 * @return bool Returns an array with options.
	 * @since 1.0.0
	 *
	 */
	public function user_role_allowed( array $roles_allowed = array(), ?int $user_id = null ) : bool {
		$user = null;
		if ( ! is_null( $user_id ) ) {
			$user = get_user_by( 'id', $user_id );
		} elseif( is_user_logged_in() ) {
			$user = wp_get_current_user();
		}

		if ( ! $user instanceof \WP_User ) {
			return false;
		}

		if ( ! empty( $user->roles ) ) {
			foreach( $user->roles as $role ) {
				if ( isset( $roles_allowed[ $role ] ) && $roles_allowed[ $role ] ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get user token from gist_sync_token user meta.
	 *
	 * @param int|null $user_id Optional nullable user id. If null function will use current user id.
	 *
	 * @return string Returns user token.
	 * @since 1.0.0
	 *
	 */
	public function get_user_token( ?int $user_id = null ) {
		if ( is_null( $user_id ) ) {
			if ( ! is_user_logged_in() ) {
				return '';
			}

			$user_id = get_current_user_id();
		}

		return get_user_meta( $user_id, 'gist_sync_token', true );
	}

}

