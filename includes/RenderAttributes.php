<?php

namespace ZionBuilder;

class RenderAttributes {
	/**
	 * Holds a reference of the responsive devices css classes modifiers
	 *
	 * @var array<string, string>
	 */
	private static $responsive_devices_map = [
		'default' => '',
		'laptop'  => '--lg',
		'tablet'  => '--md',
		'mobile'  => '--sm',
	];

	/**
	 * These are attributes that will be applied to various dom nodes
	 *
	 * @var array
	 */
	private $render_attributes = [];


	/**
	 * Main class constructor
	 *
	 * @param array $render_attributes Render attributes that will be populated as default
	 */
	public function __construct( $render_attributes = [] ) {
		$this->render_attributes = $render_attributes;
	}

	/**
	 * Will return the string of HTML attributes for a tag
	 *
	 * @param string|array $tag_ids          The tag id for which we need to build the attributes string
	 * @param array        $extra_attributes
	 *
	 * @return string
	 */
	public function get_attributes_as_string( $tag_ids, $extra_attributes = [] ) {
		$attributes = [];
		if ( is_array( $tag_ids ) ) {
			foreach ( $tag_ids as $tag_id ) {

				$attributes = $this->combine_attributes( $attributes, $this->get_attributes( $tag_id ) );
			}
		} else {
			$attributes = $this->get_attributes( $tag_ids );
		}

		$returned_attributes = [];
		$combined_attributes = $this->combine_attributes( $attributes, $extra_attributes );

		foreach ( $combined_attributes as $attribute_key => $attribute_values ) {
			if ( ! empty( $attribute_values ) ) {
				$returned_attributes[] = sprintf( '%s="%s"', $attribute_key, esc_attr( implode( ' ', $attribute_values ) ) );
			}
		}

		return implode( ' ', $returned_attributes );
	}

	/**
	 * Combines two list of attributes
	 *
	 * @param array $attributes       The first list of attributes
	 * @param array $extra_attributes The second list of attributes
	 *
	 * @return array The combined list of attributes
	 *
	 * @since 1.1.0
	 */
	public function combine_attributes( $attributes, $extra_attributes ) {
		foreach ( $extra_attributes as $key => $value ) {
			if ( ! isset( $attributes[$key] ) ) {
				$attributes[$key] = [];
			}
			$attributes[$key][] = is_array( $value ) ? implode( ' ', $value ) : $value;
		}

		return $attributes;
	}

	/**
	 * Combines a list of attributes with strings
	 *
	 * @param string $tag_id       The tag
	 * @param array $extra_attributes The second list of attributes
	 *
	 * @return array The combined list of attributes
	 *
	 * @since 1.2.0
	 */
	public function get_combined_attributes( $tag_id = 'wrapper', $extra_attributes = [] ) {

		$string_attr   = $this->get_attributes( $tag_id );
		$combined_attr = $this->combine_attributes( $string_attr, $extra_attributes );

		return $combined_attr;
	}

		/**
	 * Combines a list of attributes with strings
	 *
	 * @param string $tag_id       The tag
	 * @param array $extra_attributes The second list of attributes
	 *
	 * @return array The combined list of attributes
	 *
	 * @since 1.2.0
	 */
	public function get_combined_attributes_as_key_value( $tag_id, $extra_attributes = [] ) {
		$returned_value = [];
		$string_attr    = $this->get_attributes( $tag_id );
		$combined_attr  = $this->combine_attributes( $string_attr, $extra_attributes );

		foreach ( $combined_attr as $key => $value ) {
			if ( is_array( $value ) ) {
				$returned_value[$key] = esc_attr( implode( ' ', $value ) );
			} else {
				$returned_value[$key] = $value;
			}
		}

		return $returned_value;
	}

	/**
	 * Returns all registered attributes for a given tag id
	 *
	 * @param string         $tag_id The tag id for which the attributes will be retrieved
	 * @param boolean|string $type   The type of attribute to return for the given tag id. If not specified, all tags will be returned
	 *
	 * @return array
	 */
	public function get_attributes( $tag_id = 'wrapper', $type = false ) {
		// bail if we do not have any attributes
		if ( ! $tag_id || ! isset( $this->render_attributes[$tag_id] ) ) {
			return [];
		}

		if ( ! $type ) {
			return $this->render_attributes[$tag_id];
		}

		if ( ! isset( $this->render_attributes[$tag_id][$type] ) ) {
			return [];
		}

		return $this->render_attributes[$tag_id][$type];
	}

	/**
	 * Checks if a tag id has a specific attribute
	 *
	 * @param string $tag_id
	 * @param string $type
	 *
	 * @return boolean
	 */
	public function has_attribute( $tag_id, $type ) {
		// bail if we do not have any attributes
		if ( ! $tag_id || ! isset( $this->render_attributes[$tag_id] ) ) {
			return false;
		}

		if ( ! isset( $this->render_attributes[$tag_id][$type] ) ) {
			return false;
		}

		return (bool) $this->render_attributes[$tag_id][$type];
	}

	/**
	 * Add render attribute
	 *
	 * Will register a new attribute for render
	 *
	 * @param string     $tag_id  The unique identifier for the render attribute
	 * @param mixed      $value   The value to add to the attribute list
	 * @param mixed      $type
	 *
	 * @param bool|false $replace
	 *
	 * @return void
	 */
	public function add( $tag_id, $type, $value, $replace = false ) {
		if ( ! isset( $this->render_attributes[$tag_id] ) ) {
			$this->render_attributes[$tag_id] = [];
		}

		$current_attributes = $this->render_attributes[$tag_id];

		if ( ! isset( $current_attributes[$type] ) ) {
			$current_attributes[$type] = [];
		}

		if ( $replace ) {
			$current_attributes[$type] = [ $value ];
		} else {
			$current_attributes[$type][] = $value;
		}

		// Save the new attributes
		$this->render_attributes[$tag_id] = $current_attributes;
	}

	/**
	 * Parse render attributes
	 *
	 * Will check if the option configuration has a render attribute to apply in case the option has a saved value.
	 * Render attributes are automatically bound to the element.
	 *
	 * @param mixed    $option_schema The option schema configuration
	 * @param mixed    $option_value  The saved option value
	 * @param int|null $index         The index of the value in case it is inside a repeater
	 *
	 * @return void
	 */
	public function parse_options_schema( $option_schema, $option_value, $index = null ) {
		if ( isset( $option_schema->render_attribute ) && is_array( $option_schema->render_attribute ) ) {
			foreach ( $option_schema->render_attribute as $attribute_config ) {
				if ( ! empty( $option_value ) ) {
					$tag_id          = isset( $attribute_config['tag_id'] ) ? $attribute_config['tag_id'] : 'wrapper';
					$compiled_tag_id = null !== $index ? $tag_id . $index : $tag_id;
					$attribute       = isset( $attribute_config['attribute'] ) ? $attribute_config['attribute'] : 'class';
					$attribute_value = isset( $attribute_config['value'] ) ? $attribute_config['value'] : '';

					if ( property_exists( $option_schema, 'responsive_options' ) && $option_schema->responsive_options ) {
						$option_value = ! is_array( $option_value ) ? [ 'default' => $option_value ] : $option_value;
						foreach ( $option_value as $device_id => $value ) {
							if ( ! isset( self::$responsive_devices_map[$device_id] ) || ! isset( $value ) ) {
								continue;
							}

							$val = self::$responsive_devices_map[$device_id];

							// Replace placeholders
							$attribute_value = isset( $attribute_config['value'] ) ? $attribute_config['value'] : '';
							$attribute_value = str_replace( '{{RESPONSIVE_DEVICE_CSS}}', $val, $attribute_value );
							$attribute_value = str_replace( '{{VALUE}}', $value, $attribute_value );
							$attribute_value = ! empty( $attribute_value ) ? $attribute_value : $option_value;

							if ( ! empty( $attribute_value ) ) {
								$this->add( $compiled_tag_id, $attribute, $attribute_value );
							}
						}
					} else {
						// Don't add empty values
						if ( empty( $option_value ) ) {
							continue;
						}

						$attribute_value = str_replace( '{{VALUE}}', $option_value, $attribute_value );
						$attribute_value = ! empty( $attribute_value ) ? $attribute_value : $option_value;

						if ( ! empty( $attribute_value ) ) {
							$this->add( $compiled_tag_id, $attribute, $attribute_value );
						}
					}
				}
			}
		}
	}
}
