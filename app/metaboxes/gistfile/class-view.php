<?php
/**
 * The output for Gistfile MetaBox.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Metaboxes\gistfile
 */

namespace Gist_Sync\App\Metaboxes\gistfile;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Utils\Abstracts\Base;


/**
 * Class View
 *
 * @package Gist_Sync\App\Metaboxes\gistfile
 */
class View extends Base {

	/**
	 * Render the output.
	 *
	 * @since 1.0.0
	 * 
	 * @param array $params The parameters passed from Controller.
	 *
	 * @return void Render the output.
	 */
	public function render( $params = array() ) {
		$unique_id = isset( $params['unique_id'] ) ? $params['unique_id'] : null;

		?>
		<div class="gistfile-metabox-wrap">
			<div id="<?php echo $unique_id; ?>"></div>
		</div>
		<?php
	}

}
