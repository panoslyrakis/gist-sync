<?php
/**
 * The output for Profile metabox.
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

use Gist_Sync\App\User_Profile\Gist_Key\Model;
use Gist_Sync\Core\Views\User_Profile;

/**
 * Class View
 *
 * @package Gist_Sync\App\User_Profile\gistfile
 */
class View extends User_Profile {

	protected function set_model() {
		$this->model = Model::instance();
	}

}
