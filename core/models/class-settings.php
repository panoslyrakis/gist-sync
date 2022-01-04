<?php
/**
 * The Settings model
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
class Settings extends Base {
	/**
	* Use the Sanitize Trait.
	*
	* @since 1.0.0
	*/
	use Sanitize;

	private $option_key = 'gist-sync';
	/**
	 * Save Options in DB.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $options The options to save.
	 *
	 * @param bool   $override Optional
	 *
	 * @param string $option_key Optional nullable option_key.
	 * 
	 * @param bool $autoload To autoload option or not. Default is false
	 *
	 * @return bool True if saved succesfully.
	 */
	public function save( array $options = array(), bool $override = false, ?string $option_key = null, ?bool $autoload = false ) {
		$option_key = ! \is_null( $option_key ) ? $option_key : $this->option_key;

		if ( ! $override ) {
			$current_options = $this->get( null, $option_key );

			if ( json_last_error() !== JSON_ERROR_NONE ) {
				$current_options = array();
			}

			$options = wp_parse_args( $options, $current_options );
		}
		
		return update_option(
			$option_key,
			wp_json_encode( $this->sanitize_array( $options ) ),
			$autoload
		);
	}

	/**
	 * Get Options from DB.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $settings_key Specific settings key. If null it returns all options.
	 *
	 * @param string      $option_key Optional nullable option_key.
	 *
	 * @return array Returns an array with options.
	 */
	public function get( ?string $settings_key = null, ?string $option_key = null ) {
		$option_key = ! \is_null( $option_key ) ? $option_key : $this->option_key;
		// Options are expected to be stored as json.
		$options = json_decode( get_option( $option_key ), true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$options = array();
		}

		if ( ! \is_null( $settings_key ) ) {
			$settings_key = sanitize_key( $settings_key );
			$options      = isset( $options[ $settings_key ] ) ? $options[ $settings_key ] : array();
		}

		return $options;
	}
}
