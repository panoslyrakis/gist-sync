<?php
/**
 * The Schema for Rest endpoint
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\App\Rest_Endpoints\Settings
 */

namespace Gist_Sync\App\Rest_Endpoints\Settings\Includes;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Schema {
	/**
	 * Get Schema for Rest Endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action A string that contains action that endpoint performs.
	 * 
	 * @param array $args An array containing several options that we can use for returning specific schema properties.
	 * 
	 * @return array An array containing Schema.
	 */
	public static function get_schema( ?string $action = null, array $args = array() ) {
		if ( \is_null( $action ) ) {
			return array();
		}

		$poperties_keys = array();
		$schema         = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => isset( $args['rest_base'] ) ? $args['rest_base'] : '',
			'type'       => 'object',
			'properties' => array(),
		);

		switch ( $action ) {
			case 'save':
				$poperties_keys = array( 'message', 'status_code' );
				break;
			case 'get':
				$poperties_keys = array( 'message', 'status_code', 'settings' );
				break;
		}

		$schema['properties'] = self::get_schema_properties( $poperties_keys );

		return $schema;
	}

	/**
	 * Get Schema properties for Rest Response.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties_keys An array containing field keys for properties needed.
	 *
	 * @return array An array of schema properties.
	 */
	protected static function get_schema_properties( array $properties_keys = array() ) {
		$return_properties = array();
		$schema_properties = array(
			'settings'          => array(
				'description' => esc_html__( 'All settings to sync to Github Gists.', 'gist_sync' ),
				'type'        => 'object',
				'properties'  => array(
					'username'          => array(
						'description' => esc_html__( 'Github username.', 'gist_sync' ),
						'type'        => 'string',
					),
					'globalTokenStatus' => array(
						'description' => esc_html__( 'Global token for all users status.', 'gist_sync' ),
						'type'        => 'boolean',
					),
					'globalToken'       => array(
						'description' => esc_html__( 'Global token for all site users.', 'gist_sync' ),
						'type'        => 'string',
					),
					'userRolesAllowed'  => array(
						'type'       => 'object',
						'properties' => array(
							'name'  => array(
								'description' => esc_html__( 'User role name', 'gist_sync' ),
								'type'        => 'string',
							),
							'label' => array(
								'description' => esc_html__( 'User role label', 'gist_sync' ),
								'type'        => 'string',
							),
						),
					),
				),
			),

			'username'          => array(
				'description' => esc_html__( 'Github username.', 'gist_sync' ),
				'type'        => 'string',
			),

			'globalTokenStatus' => array(
				'description' => esc_html__( 'Global token for all users status.', 'gist_sync' ),
				'type'        => 'boolean',
			),

			'globalToken'       => array(
				'description' => esc_html__( 'Global token for all site users.', 'gist_sync' ),
				'type'        => 'string',
			),

			'userRolesAllowed'  => array(
				'type'       => 'object',
				'properties' => array(
					'name'  => array(
						'description' => esc_html__( 'User role name', 'gist_sync' ),
						'type'        => 'string',
					),
					'label' => array(
						'description' => esc_html__( 'User role label', 'gist_sync' ),
						'type'        => 'string',
					),
				),
			),

			'message'           => array(
				'description' => esc_html__( 'Response message.', 'gist_sync' ),
				'type'        => 'string',
			),

			'status_code'       => array(
				'description' => esc_html__( 'Response status code.', 'gist_sync' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array(
					'200',
					'400',
					'401',
					'403',
				),
				'readonly'    => true,
			),
		);

		if ( empty( $properties_keys ) ) {
			$return_properties = $schema_properties;
		} else {
			$return_properties = \array_filter(
				$schema_properties,
				function( string $property_key = '' ) use ( $properties_keys ) {
					return in_array( $property_key, $properties_keys );
				},
				ARRAY_FILTER_USE_KEY
			);
		}

		return apply_filters( 'gist_sync_rest_enpoints_settings_schema_properties', $return_properties );
	}
}
