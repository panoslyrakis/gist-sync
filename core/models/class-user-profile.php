<?php
/**
 * An Abstract class for User Profile.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Models
 */

namespace Gist_Sync\Core\Models;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Utils\Abstracts\Base;
use Gist_Sync\Core\Traits\Sanitize;

/**
 * Class Installer
 *
 * @package Gist_Sync\Core\Models
 */
class User_Profile extends Base {
	/**
	* Use the Sanitize Trait.
	*
	* @since 1.0.0
	*/
	use Sanitize;

	/** Update usermeta.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user id.
	 *
	 * @return bool True on successful update, false on failure or if the value passed to the function is the same as the one that is already in the database.
	 */
	public function update_user_meta( ?int $user_id = null, array $user_meta = array() ) {
		$result = true;

		if ( ! empty( $user_meta ) && \current_user_can( 'edit_user', $user_id ) ) {
			foreach( $user_meta as $user_meta_key => $user_meta_value ) {
				if ( 
					! \update_user_meta( 
						\intval( $user_id ),
						\sanitize_key( $user_meta_key ),
						$this->sanitize_single( $user_meta_value )
					)
				) {
					$result = false;
				}
			}
		}

		return $result;
	}

	/** Update usermeta.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user id.
	 *
	 * @return mixed Meta value(s) or null.
	 */
	public function get_user_meta( ?int $user_id = null, ?array $user_meta_keys = array() ) {
		$user_meta = array();

		if ( is_null( $user_id ) ) {
			if ( ! is_user_logged_in() ) {
				return null;
			}

			$user_id = get_current_user_id();
		}

		if ( empty( $user_meta_keys ) || \is_null( $user_meta_keys ) ) {
			return \get_user_meta( \intval( $user_id ) );
		}

		foreach ( $user_meta_keys as $user_meta_key ) {
			$user_meta[ $user_meta_key ] = \get_user_meta( \intval( $user_id ), \sanitize_key( $user_meta_key ), true );
		}

		return $user_meta;
	}
}
