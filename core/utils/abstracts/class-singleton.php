<?php
/**
 * Singleton class for all classes.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Utils\Abstracts
 */

namespace Gist_Sync\Core\Utils\Abstracts;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Singleton
 *
 * @package Gist_Sync\Core\Utils\Abstracts
 */
abstract class Singleton {

	/**
	 * Singleton constructor.
	 *
	 * Protect the class from being initiated multiple times.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		// Protect class from being initiated multiple times.
	}

	/**
	 * Instance obtaining method.
	 *
	 * @since 1.0.0
	 *
	 * @return static Called class instance.
	 */
	public static function instance() {
		static $instances = array();

		// @codingStandardsIgnoreLine Plugin-backported
		$called_class_name = get_called_class();

		if ( ! isset( $instances[ $called_class_name ] ) ) {
			$instances[ $called_class_name ] = new $called_class_name();
		}

		return $instances[ $called_class_name ];
	}
}
