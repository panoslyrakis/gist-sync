<?php
/**
 * The output for Profile metabox.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Views
 */

namespace Gist_Sync\Core\Views;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Utils\Abstracts\Base;

/**
 * Class View
 *
 * @package Gist_Sync\Core\Views
 */
abstract class User_Profile extends Base {
	/**
	 * Instance of the User_Profile model.
	 *
	 * @since 1.0.0
	 *
	 * @var object Instance of the User_Profile model.
	 */
	protected $model;

	/**
	 * Render the output.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $user_id The user id.
	 *
	 * @param array $params The parameters passed from Controller.
	 *
	 * @param bool  $return Return or print.
	 *
	 * @return void Render the output.
	 */
	public function render( int $user_id, array $profile_fields = array(), bool $return = false ) {
		$this->set_model();

		$heading_html = '';
		$fields_html  = '';

		if ( isset( $profile_fields['heading'] ) ) {
			$heading_html = $this->heading_renderer( $profile_fields['heading'] );
		}

		if ( isset( $profile_fields['fields'] ) ) {
			$fields_html = $this->field_renderer( $user_id, $profile_fields['fields'] );
		}

		if ( $return ) {
			return $heading_html . $fields_html;
		}

		echo $heading_html . $fields_html;
	}

	/**
	 * Render the heading.
	 *
	 * @since 1.0.0
	 *
	 * @param string $heading_content The content of the heading.
	 *
	 * @return string Heading HTML.
	 */
	protected function heading_renderer( string $heading_content = '' ) {
		$dom     = new \DOMDocument();
		$heading = $dom->createElement( 'h2' );
		$heading->appendChild( $dom->createTextNode( $heading_content ) );
		$dom->appendChild( $heading );

		return $dom->saveHTML();
	}

	/**
	 * Render the fields html.
	 *
	 * @since 1.0.0
	 * 
	 * @param int   $user_id The user id.
	 *
	 * @param array $fields An array with fields attributes to be displayed.
	 *
	 * @return string Fields HTML.
	 */
	protected function field_renderer( int $user_id, array $fields = array() ) {
		$dom = new \DOMDocument();

		if ( ! empty( $fields ) ) {
			$user_meta      = $this->model->get_user_meta( $user_id, array_keys( $fields ) );
			$table          = $dom->createElement( 'table' );
			$table->setAttribute( 'class', 'form-table' );
			$table->setAttribute( 'role', 'presentation' );

			foreach ( $fields as $field_key => $field_props ) {
				if ( ! isset( $field_props['input_type'] ) || ! in_array( $field_props['input_type'], array( 'text', 'submit', 'textarea' ) ) ) {
					continue;
				}

				// @todo: Add cases for rest of field types (radio/checkboxes...)
				switch ( $field_props['input_type'] ) {
					case 'text':
						$field = $dom->createElement( 'input' );
						$field->setAttribute( 'type', 'text' );
						$field->setAttribute( 'value', isset( $user_meta[ $field_key ] ) ? \esc_html( $user_meta[ $field_key ] ) : '' );
						break;
					case 'submit':
						$field = $dom->createElement( 'input' );
						$field->setAttribute( 'type', 'submit' );
						$field->setAttribute( 'value', isset( $user_meta[ $field_key ] ) ? \esc_html( $user_meta[ $field_key ] ) : '' );
						break;
					case 'textarea':
						$field = $dom->createElement( 'textarea' );
						// Set field value
						if ( isset( $user_meta[ $field_key ] ) ) {
							$field_content = $dom->createTextNode( \esc_html( $user_meta[ $field_key ] ) );
							$field->appendChild( $field_content );
						}
						break;
				}

				if ( isset( $field_props['attributes'] ) && ! empty( $field_props['attributes'] ) ) {
					foreach ( $field_props['attributes'] as $attribute_key => $attribtue_value ) {
						$field->setAttribute( $attribute_key, $attribtue_value );
					}
				}

				$row = $dom->createElement( 'tr' );
				$row->setAttribute( 'class', $field_key );

				$col_label = $dom->createElement( 'th' );
				if ( isset( $field_props['label'] ) ) {
					$label         = $dom->createElement( 'label' );
					$label_content = $dom->createTextNode( $field_props['label'] );
					$label->appendChild( $label_content );
					$col_label->appendChild( $label );
				}

				$col_field = $dom->createElement( 'td' );
				$col_field->appendChild( $field );

				if ( isset( $field_props['description'] ) && ! empty( $field_props['description'] ) ) {
					$field_description_wrap = $dom->createElement( 'p' );
					$field_description_wrap->setAttribute( 'class', 'description' );
					$field_description_fragment = $dom->createDocumentFragment();
					$field_description_fragment->appendXML( $field_props['description'] );
					$field_description_wrap->appendChild( $field_description_fragment );
					$col_field->appendChild( $field_description_wrap );
				}

				$row->appendChild( $col_label );
				$row->appendChild( $col_field );

				$table->appendChild( $row );
				$dom->appendChild( $table );
			}
		}

		return $dom->saveHTML();
	}

	protected abstract function set_model();
}
