<?php
/**
 * The endpoint for Settings
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Rest_Endpoints\Settings
 */

namespace Gist_Sync\App\Rest_Endpoints\Settings;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Controllers\Rest_Api;
use Gist_Sync\Core\Models\Settings as Settings_Api;
use Gist_Sync\App\Rest_Endpoints\Settings\Includes\Schema;

/**
 * Class Controller
 *
 * @package Gist_Sync\App\Rest_Endpoints\gist_sync
 */

class Controller extends \WP_REST_Controller {

	protected $request_action;
	/**
	 * The version.
	 *
	 * @var string
	 */
	protected $version = 'v1';

	/**
	 * The namespace.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Rest base for the current object.
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Settings keys.
	 *
	 * @var array
	 */
	protected $settings_keys = array(
		'username',
		'globalTokenStatus',
		'globalToken',
		'userRolesAllowed',
	);

	/**
	 * Constructor.
	 */
	private function __construct() {
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

	public function init() {
		$this->settings_keys = \array_map(
			function( $settings_key ) {
				return \sanitize_key( $settings_key );
			},
			$this->settings_keys
		);

		$this->namespace = "gist_sync_settings/{$this->version}";
		$this->rest_base = 'save';

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(

				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'get_settings_permissions' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'save_settings' ),
					'permission_callback' => array( $this, 'save_settings_permissions' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Formatting the response
	 *
	 * @since 1.0.0
	 *
	 * @param array           $item my-thing.
	 * @param WP_REST_Request $request request object.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		$fields          = $this->get_fields_for_response( $request );
		$data            = array();
		$request_expects = $request->get_param( 'expects' );

		foreach ( $fields as $field_key ) {
			if ( rest_is_field_included( $field_key, $fields ) ) {
				$data[ $field_key ] = isset( $item[ $field_key ] ) ? $item[ $field_key ] : '';
			}
		}

		return $data;
	}

	/**
	 * Update category order
	 *
	 * @since 1.0.0
	 *
	 * @param object $request WP_REST_Request get data from request.
	 *
	 * @return mixed WP_REST_Response|WP_Error|WP_HTTP_Response|mixed $response
	 */
	public function save_settings( \WP_REST_Request $request ) {
		$this->request_action = $request->get_param( 'action' );
		$settings             = $this->filter_settings_keys( $request->get_params() );
		$saved_response       = Settings_Api::instance()->save( $settings );
		$response_data        = array(
			'message'     => __( 'Settings saved', 'gist_sync' ),
			'status_code' => 200,
		);

		if ( \is_wp_error( $saved_response ) ) {
			$response_data = array(
				'message'     => $saved_response->get_error_message(),
				'status_code' => 500,
			);
		}

		$response = $this->prepare_item_for_response( $response_data, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Accepts a given array of settings and returns only settings with specific array keys.
	 *
	 * @since 1.0.0
	 *
	 * @param array Settings array.
	 *
	 * @return array
	 */
	protected function filter_settings_keys( array $setting_keys = array() ) {
		return array_filter(
			$setting_keys,
			function( $value, $key ) {
				return in_array( \sanitize_key( $key ), $this->settings_keys );
			},
			ARRAY_FILTER_USE_BOTH
		);
	}

	/**
	 * Check permissions for saving options.
	 *
	 * @since 1.0.0
	 *
	 * @param object $request get data from request.
	 *
	 * @return bool|object Boolean or WP_Error.
	 */
	public function get_settings_permissions( \WP_REST_Request $request ) {
		if ( ! current_user_can( 'read' ) ) {
			return new WP_Error(
				'rest_forbidden',
				esc_html__( 'You cannot view settings.', 'gist_sync' ),
				array( 'status' => $this->authorization_status_code() )
			);
		}

		return true;
	}
	/**
	 * Check permissions for the update
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request get data from request.
	 *
	 * @return bool|WP_Error
	 */
	public function save_settings_permissions( \WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				esc_html__( 'Action forbidden.', 'gist_sync' ),
				array( 'status' => $this->authorization_status_code() )
			);
		}

		return true;
	}

	/**
	 * Grabs all the category list.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request get data from request.
	 *
	 * @return mixed|WP_REST_Response
	 */
	public function get_settings( \WP_REST_Request $request ) {
		// Return settings fetched from Settings_Api.
		return rest_ensure_response( Settings_Api::instance()->get() );
	}

	/**
	 * Sets up the proper HTTP status code for authorization.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function authorization_status_code() {

		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}

	/**
	 * Set Schema for rest responnse.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function set_response_schema() {

	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = Schema::get_schema( $this->request_action, array( 'rest_base' => $this->rest_base ) );
		return $this->add_additional_fields_schema( $this->schema );
	}
}
