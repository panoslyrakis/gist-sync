<?php
/**
 * Helper class for remote requests.
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

/**
 * Class Installer
 *
 * @package Gist_Sync\Core\Models
 */
class Request extends Base {


	/**
	 * Request max timeout
	 *
	 * @var int
	 */
	private $timeout = 25;

	/**
	 * Response object
	 *
	 * @var object
	 */
	private $response;

	/**
	 * Response code
	 *
	 * @var int
	 */
	private $response_code = null;

	/**
	 * Response body
	 *
	 * @var mixed|object|array
	 */
	private $response_body = null;

	/**
	 * Header arguments
	 *
	 * @var array
	 */
	private $headers = array();

	/**
	 * Make Remote Request
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The url.
	 * @param string $method The method (post, get, request).
	 * @param array  $args Arguments.
	 *
	 * @return void
	 */
	public function process( $url = '', $method = 'post', $args = array() ) {

		$defaults = array(
			'headers'   => $this->headers,
			'sslverify' => false,
			'method'    => strtoupper( $method ),
			'timeout'   => $this->timeout,
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! $args['timeout'] ) {
			$args['blocking'] = false;
		}

		$this->response = \wp_remote_request( \esc_url_raw( $url ), $args );
	}

	/**
	 * Get Response.
	 *
	 * @since 1.0.0
	 *
	 * @return object The request response.
	 */
	public function response() {
		return $this->response;
	}

	/**
	 * Get Response Body
	 *
	 * @since 1.0.0
	 *
	 * @return object Get Response Body
	 */
	public function response_body() {
		if ( \is_null( $this->response_body ) ) {
			$this->response_body = \json_decode( \wp_remote_retrieve_body( $this->response ) );
		}

		return $this->response_body;
	}

	/**
	 * The response code.
	 *
	 * @since 1.0.0
	 *
	 * @return int|string The response code.
	 */
	public function response_code() {
		if ( \is_null( $this->response_code ) ) {
			$this->response_code = \wp_remote_retrieve_response_code( $this->response );
		}

		return $this->response_code;
	}

	/**
	 * Set Request Headers
	 *
	 * @since 1.0.0
	 *
	 * @return void Set Request Headers
	 */
	public function set_headers( $headers = array() ) {
		$this->headers = $headers;
	}

}
