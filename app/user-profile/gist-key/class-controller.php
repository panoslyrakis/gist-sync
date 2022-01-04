<?php
/**
 * Settings page
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\User_Profile\Gist_Key
 */

namespace Gist_Sync\App\User_Profile\Gist_Key;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Controllers\User_Profile;
use Gist_Sync\App\User_Profile\Gist_Key\View;
use Gist_Sync\App\User_Profile\Gist_Key\Model;
use Gist_Sync\Core\Models\Settings as Settings_Api;

/**
 * Class Controller
 *
 * @package Gist_Sync\App\User_Profile\Gist_Key
 */
class Controller extends User_Profile {
	/**
	 * Set usermeta keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of usermeta keys.
	 */
	protected function set_usermeta_fields() {
		$this->usermeta_fields = array(
			'gist_sync_token' => array(
				'label'       => __( 'Personal token', 'gist_sync' ),
				'input_type'  => 'text',
				'attributes'  => array(
					'id'          => 'gist_sync_token',
					'name'        => 'gist_sync_token',
					'class'       => 'regular-text',
					'placeholder' => __( 'Insert your Personal token', 'gist_sync' ),
				),
				'description' => __( 'To set a user access token you can follow <a target="_blank" href="https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token">these instructions</a>.', 'gist_sync' ),
			),
		);
	}

	/**
	 * Set usermeta heading.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of usermeta heading.
	 */
	protected function set_usermeta_heading() {
		$this->usermeta_heading = __( 'Sync to Gist', 'gist_sync' );
	}

	/** Set up params for metaboxes that will be used to call "add_meta_box" for users.
	 *
	 * @since 1.0.0
	 *
	 * @param object $user The WP_User object.
	 *
	 * @return void
	 */
	public function show_profile( \WP_User $user ) {
		$settings = Settings_API::instance()->get( 'globalTokenStatus' );

		if ( $settings ) {
			return;
		}

		$profile_fields = array(
			'heading' => __( 'Sync to Gist', 'gist_sync' ),
			'fields'  => $this->get_usermeta_fields(),
		);

		View::instance()->render( $user->ID, $profile_fields );
	}

	/** Prepare parent Core User_Profile.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setup() {
		$this->model = Model::instance();
		$this->view  = View::instance();
	}
}
