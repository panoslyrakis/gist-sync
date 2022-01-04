<?php
/**
 * The Admin page for managing images.
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

use Gist_Sync\Core\Utils\Abstracts\Base;


/**
 * Class View
 *
 * @package Gist_Sync\App\Admin_Pages\Settings
 */
class View extends Base {

	/**
	 * Render the output.
	 *
	 * @since 1.0.0
	 *
	 * @return void Render the output.
	 */
	public function render( $params = array() ) {
		$unique_id = isset( $params['unique_id'] ) ? $params['unique_id'] : null;
		?>
		<div class="wrap">
			<div id="<?php echo $unique_id; ?>"></div>
		</div>
		<?php
	}

}
