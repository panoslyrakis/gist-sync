<?php
/**
 * File Description:
 * Base abstract class to be inherited by other classes
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * 
 */

namespace Gist_Sync\Core\Utils\Abstracts;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Base
 * 
 * @package Gist_Sync\Core\Utils\Abstracts
 */
abstract class Base extends Singleton {
    /**
	 * Setter method.
	 *
	 * Set property and values to class.
	 *
	 * @param string $key   Property to set.
	 * @param mixed  $value Value to assign to the property.
	 *
	 * @since 1.0.0
	 */
	public function __set( $key, $value ) {
		$this->{$key} = $value;
	}

	/**
	 * Getter method.
	 *
	 * Allows access to extended site properties.
	 *
	 * @param string $key Property to get.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Value of the property. Null if not available.
	 */
	public function __get( $key ) {
		// If set, get it.
		if ( isset( $this->{$key} ) ) {
			return $this->{$key};
		}

		return null;
	}

	/**
	 * Get network admin flag.
	 *
	 * When called from an ajax request using admin-ajax, we will
	 * check for network flag in $_REQUEST.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_network() {
		// If called from Ajax, check request.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			// phpcs:ignore
			$network = ! empty( $_REQUEST['network'] );
		} else {
			$network = is_network_admin();
		}

		/**
		 * Filter to change network admin flag.
		 *
		 * @param bool $network Is network.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'pluginbase/is_network', $network );
	}
}