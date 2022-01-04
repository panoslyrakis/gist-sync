<?php
/**
 * Wrapper class for sanitizing data.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Traits
 */

namespace Gist_Sync\Core\Traits;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Core
 *
 * @package Gist_Sync\Core\Traits
 */
trait Sanitize {
	/**
	 * Sanitize an array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options The options to sanitize.
	 *
	 * @return array Returns the sanitized array.
	 */
	protected function sanitize_array( array $options = array() ) {
		if ( ! is_array( $options ) ) {
			return $this->sanitize_single( $options );
		}

		$sanitized_options = array();

		foreach ( $options as $key => $value ) {
			$sanitized_options[ sanitize_key( $key ) ] = is_array( $value ) ? $this->sanitize_array( $value ) : $this->sanitize_single( $value );
		}

		return $sanitized_options;
	}

	/**
	 * Sanitize an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option The option to sanitize.
	 *
	 * @return string Returns the sanitized string.
	 */
	protected function sanitize_single( string $option = '' ) {
		if ( is_email( $option ) ) {
			$option = sanitize_email( $option );
		} elseif ( preg_match( '/\R/', $option ) ) {
			$option = sanitize_textarea_field( $option );
		} elseif ( $option !== \strip_tags( $option ) ) {
			$option = wp_kses_post( $option );
		} else {
			if ( ! \is_numeric( $option ) && ! \is_bool( $option ) ) {
				$option = sanitize_text_field( $option );
			}
		}

		return $option;
	}
}