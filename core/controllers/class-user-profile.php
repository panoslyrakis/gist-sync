<?php
/**
 * An Abstract class for User Profile.
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

use Gist_Sync\Core\Utils\Abstracts\Base;
use Gist_Sync\Core\Traits\Enqueue;

/**
 * Class Core
 *
 * @package Gist_Sync\Core\User_Profile
 */
abstract class User_Profile extends Base {
	/**
	* Use the Enqueue Trait.
	*
	* @since 1.0.0
	*/
	use Enqueue;

	/**
	 * Usermeta keys.
	 *
	 * @var array $usermeta_keys The usermeta keys.
	 *
	 * @since 1.0.0
	 */
	protected $usermeta_fields = array();

	/**
	 * Usermeta keys.
	 *
	 * @var string $usermeta_heading The usermeta heading.
	 *
	 * @since 1.0.0
	 */
	protected $usermeta_heading = '';

	/**
	 * Instance of the User_Profile Model.
	 *
	 * @since 1.0.0
	 *
	 * @var object Instance of the User_Profile Model.
	 */
	protected $model;

	/**
	 * Instance of the User_Profile View.
	 *
	 * @since 1.0.0
	 *
	 * @var object Instance of the User_Profile View.
	 */
	protected $view;

	/**
	 * Init Metabox.
	 *
	 * @since 1.0.0
	 *
	 * @return void Init the metabox.
	 */
	public function init() {
		if ( ! $this->can_boot() ) {
			return;
		}

		$this->setup();
		$this->set_usermeta_heading();
		$this->set_usermeta_fields();

		/**
		 * Admininstrator actions for user profile.
		 */
		\add_action( 'show_user_profile', array( $this, 'show_profile' ) );
		\add_action( 'personal_options_update', array( $this, 'update_profile' ) );

		/**
		 * User actions for his profile.
		 */
		\add_action( 'edit_user_profile', array( $this, 'show_profile' ) );
		\add_action( 'edit_user_profile_update', array( $this, 'update_profile' ) );
	}

	/**
	 * To boot or not to boot?
	 *
	 * @since 1.0.0
	 *
	 * @return boolean Checks if admin page actions/scripts should load. Useful for enqueing scripts.
	 */
	protected function can_boot() {
		global $pagenow;

		return (
			is_admin() &&
			in_array( $pagenow, array( 'user-edit.php', 'user-new.php' ) )
		);
	}
	/** Set up params for metaboxes that will be used to generate HTML in View.
	 *
	 * @since 1.0.0
	 * 
	 * @param object $user The WP_User object.
	 *
	 * @return void
	 */
	public function show_profile( \WP_User $user ){
		$profile_fields = array(
			'heading' => $this->get_usermeta_heading(),
			'fields'  => $this->get_usermeta_fields(),
		);

		$this->view->render( $user->ID, $profile_fields );
	}

	/**
	 * Set the metabox args.
	 *
	 * @since 1.0.0
	 * 
	 * @param int $user_id The user id
	 *
	 * @return void
	 */
	public function update_profile( ?int $user_id = null ) {
		$user_meta_keys = $this->get_usermeta_keys();

		if ( ! empty( $user_meta_keys ) && current_user_can( 'edit_user', $user_id ) ) {
			$user_meta = array();

			foreach ( $user_meta_keys as $user_meta_key ) {
				if ( isset( $_POST[ $user_meta_key ] ) ) {
					$user_meta[ $user_meta_key ] = $_POST[ $user_meta_key ];
				}
			}

			return $this->model->update_user_meta( $user_id, $user_meta );
		}
	}


	/**
	 * Set usermeta keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array Set the list of usermeta keys.
	 */
	protected function set_usermeta_fields() {}

	/**
	 * Get usermeta keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array Get the list of usermeta keys.
	 */
	protected function get_usermeta_keys() {
		if ( empty( $this->usermeta_fields ) ) {
			return $this->usermeta_fields;
		}

		return array_keys( $this->usermeta_fields );
	}

	/**
	 * Set usermeta heading.
	 *
	 * @since 1.0.0
	 *
	 * @return array Set usermeta heading.
	 */
	protected function set_usermeta_heading() {}

	/**
	 * Get usermeta heading.
	 *
	 * @since 1.0.0
	 *
	 * @return array Get usermeta heading.
	 */
	protected function get_usermeta_heading() {
		return $this->usermeta_heading;
	}

	/**
	 * Get usermeta fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array Get the list of usermeta fields.
	 */
	protected function get_usermeta_fields() {
		return $this->usermeta_fields;
	}

	protected abstract function setup();
}
