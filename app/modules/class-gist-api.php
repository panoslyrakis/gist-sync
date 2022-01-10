<?php
/**
 * The Gist API.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Modules
 */

namespace Gist_Sync\App\Modules;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Gist_Api
 *
 * @package Gist_Sync\App\Modules
 */
class Gist_Api {
	/**
	 * The Gist api base url.
	 *
	 * @var string
	 */
	protected $url_base = 'https://api.github.com/gists';

	/**
	 * The Gist api full url.
	 *
	 * @var string
	 */
	protected $url = null;

	/**
	 * The Gist account username.
	 *
	 * @var string
	 */
	protected static $username = null;

	/**
	 * The Gist account token.
	 *
	 * @var string
	 */
	protected static $token = null;

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
	 * Initialise api connection
	 *
	 * @since 1.0.0
	 *
	 * @param string $username Gist account username.
	 * @param string $token Gist account personal token.
	 *
	 * @return object An instance of object.
	 */
	public static function init( string $username = '', string $token = '' ) {
		if ( empty( $username ) || empty( $token ) ) {
			return new \WP_Error( 'invalid_call', __( 'Missing information', 'gist-sync' ) );
		}

		static $instance = null;
		self::$username  = $username;
		self::$token     = $token;

		if ( is_null( $instance ) ) {
			$instance = new self( $username, $token );
		}

		return $instance;
	}

	/**
	 * Updates an existing gist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $gist_id The gist id which we need to update the files.
	 * @param string $description The gist description.
	 * @param array  $files The list of files.
	 *
	 * @return object An WP_HTTP_Response object containing the data of the gist.
	 */
	public function update_gist( string $gist_id = '', string $description = '', array $files = array() ) {
		if ( empty( $gist_id ) || empty( $description ) ) {
			return new \WP_Error( 'invalid_request_data', __( 'Missing data', 'gist-sync' ) );
		}

		$this->url    = wp_normalize_path( path_join( $this->url_base, $gist_id ) );
		$request_args = array(
			// (PATCH) https://docs.github.com/en/rest/reference/gists#update-a-gist.
			'method'      => 'PATCH',
			'description' => $description,
			'files'       => $files,
			'public'      => true,
		);

		return $this->request( $request_args );
	}

	/**
	 * Add a new gist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $description The gist description.
	 * @param array  $files The list of files.
	 *
	 * @return object An WP_HTTP_Response object containing the data of the gist.
	 */
	public function add_gist( string $description = '', array $files = array() ) {
		if ( empty( $description ) ) {
			return new \WP_Error( 'invalid_request_data', __( 'Missing gist description.', 'gist-sync' ) );
		}

		$this->url    = $this->url_base;
		$request_args = array(
			// (POST) https://docs.github.com/en/rest/reference/gists#create-a-gist.
			'method'      => 'POST',
			'description' => $description,
			'files'       => $files,
			'public'      => true,
		);

		return $this->request( $request_args );
	}

	/**
	 * Get gist data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $gist_id The gist id.
	 *
	 * @return object An WP_HTTP_Response object containing the data of the gist.
	 */
	public function get_gist( string $gist_id = '' ) {
		if ( empty( $gist_id ) ) {
			return new \WP_Error( 'invalid_request_data', __( 'Missing gist id.', 'gist-sync' ) );
		}

		$this->url    = wp_normalize_path( path_join( $this->url_base, $gist_id ) );
		$request_args = array(
			// (GET) https://docs.github.com/en/rest/reference/gists#get-a-gist.
			'method' => 'GET',
		);

		return $this->request( $request_args );
	}

	/**
	 * Get gist files.
	 *
	 * @since 1.0.0
	 *
	 * @param string $gist_id The gist id.
	 *
	 * @return array|object An array containg gist files or empty array.
	 */
	public function get_gist_files( string $gist_id = '' ) {
		if ( empty( $gist_id ) ) {
			return new \WP_Error( 'invalid_request_data', __( 'Missing gist id.', 'gist-sync' ) );
		}

		$response_data = \json_decode( $this->get_gist( $gist_id )->data, true );
		return isset( $response_data['files'] ) ? $response_data['files'] : array();
	}

	/**
	 * Make API Request.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Request arguments.
	 *
	 * @return object WP_HTTP_Response
	 */
	protected function request( array $args = array() ) {
		$response     = null;
		$default_args = array(
			'method' => 'GET',
		);
		$args         = apply_filters(
			'gist_sync_gist_api_request_args',
			wp_parse_args( $args, $default_args ),
			$this
		);

		if ( ! empty( $args['files'] ) ) {
			$request_args['files'] = $args['files'];
		}

		switch ( $args['method'] ) {
			default:
			case 'POST':
				$headers  = array(
					'Authorization' => 'bearer ' . self::$token,
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				);
				$body     = array(
					'description' => $args['description'],
					'public'      => $args['public'],
					'files'       => $args['files'],
				);
				$response = wp_remote_post(
					$this->url,
					array(
						'body'    => json_encode( $body ),
						'headers' => $headers,
					)
				);

				break;
			case 'PATCH':
				$headers  = array(
					'Authorization' => 'bearer ' . self::$token,
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				);
				$body     = array(
					'description' => $args['description'],
					'public'      => $args['public'],
					'files'       => $args['files'],
				);
				$response = wp_remote_request(
					$this->url,
					array(
						'method'  => 'PATCH',
						'body'    => json_encode( $body ),
						'headers' => $headers,
					)
				);

				break;
			case 'GET':
				$response = wp_remote_get( $this->url );
				break;
		}

		return new \WP_HTTP_Response(
			\wp_remote_retrieve_body( $response ),
			\wp_remote_retrieve_response_code( $response )
		);
	}
}
