<?php

namespace ZionBuilder;

use ZionBuilder\Elements\Style;

use ZionBuilder\Responsive;

class CustomCSS {
	private $css_selector = null;

	/**
	 * Holds a reference to the css styles for responsive devices
	 *
	 * @var array{default: array<mixed>, laptop: array<mixed>, tablet: array<mixed>, mobile: array<mixed>}
	 */
	private $custom_css_config = [
		'default' => [],
		'laptop'  => [],
		'tablet'  => [],
		'mobile'  => [],
	];

	/**
	 * Holds a reference of the responsive devices css classes modifiers
	 *
	 * @var array{default: string, laptop: string, tablet: string, mobile: string}
	 */
	private static $responsive_devices_map = null;

	/**
	 * Holds a reference to the raw CSS
	 *
	 * @var string
	 */
	private $raw_css = '';

	/**
	 * Main class constructor
	 *
	 * @param string $css_selector The css selector for which the styles will be applied
	 */
	public function __construct( $css_selector = '' ) {
		$this->css_selector = $css_selector;

		if ( null === self::$responsive_devices_map ) {
			self::$responsive_devices_map = Responsive::get_breakpoints_as_device_width();
		}
	}


	/**
	 * Sets the CSS selector
	 *
	 * @param string $selector
	 * @return void
	 */
	public function set_css_selector( $selector ) {
		$this->css_selector = $selector;
	}

	/**
	 * Returns the css selector that will be used for style configurations
	 *
	 * @return string
	 */
	public function get_css_selector() {
		return $this->css_selector;
	}

	/**
	 * Will check if the given option schema has styles that needs to be extracted
	 *
	 * @param \ZionBuilder\Options\Option $option_schema The option configuration
	 * @param mixed                       $option_value  The saved option value
	 * @param int|null                    $index         The index of the value in case it is inside a repeater
	 *
	 * @return void
	 */
	public function parse_options_schema( $option_schema, $option_value, $index ) {
		if ( isset( $option_schema->css_style ) && is_array( $option_schema->css_style ) ) {
			// Check for custom css
			foreach ( $option_schema->css_style as $css_style_config ) {
				if ( isset( $option_schema->responsive_options ) && $option_schema->responsive_options ) {
					$this->extract_responsive_option_css( $option_schema->type, $css_style_config, $option_value, $index );
				} else {
					$this->extract_option_css( 'default', $option_schema->type, $css_style_config, $option_value, $index );
				}
			}
		} else {
			if ( $option_schema->type === 'shape_dividers' ) {
				if ( is_array( $option_value ) ) {

					foreach ( $option_value as $position => $value ) {
						$shape  = isset( $value['shape'] ) ? $value['shape'] : false;
						$height = isset( $value['height'] ) ? $value['height'] : false;

						if ( $shape && $height ) {
							// Normalize height
							if ( ! is_array( $value['height'] ) ) {
								$height = [
									'default' => $value['height'],
								];
							}

							$selector          = sprintf( 'zb-mask-pos--%s', $position );
							$custom_css_config = [
								'selector' => sprintf( '%s .%s', $this->css_selector, $selector ),
								'value'    => 'height: {{VALUE}}',
							];

							$this->extract_responsive_option_css( $option_schema->type, $custom_css_config, $height, $index );
						}
					}
				}
			}
		}
	}

	/**
	 * Extracts the css from a given option
	 *
	 * @param string                $device       The device id
	 * @param string                $option_type  The option type
	 * @param array<string, string> $style_config Configuration for the css
	 * @param mixed                 $saved_value  The value to use when parsing the option
	 * @param int|null              $index        In case of repeaters, the index of the saved value
	 *
	 * @return string|void
	 */
	public function extract_option_css( $device, $option_type, $style_config, $saved_value, $index ) {
		if ( ! isset( $style_config['selector'] ) || ! isset( $style_config['value'] ) ) {
			return;
		}

		$selector = $style_config['selector'];
		$value    = $style_config['value'];

		$selector = str_replace( '{{ELEMENT}}', $this->css_selector, $selector );
		$selector = str_replace( '{{INDEX}}', strval( $index ), $selector );
		$value    = str_replace( '{{VALUE}}', $saved_value, $value );

		if ( $option_type === 'element_styles' ) {
			$styles = Style::get_css_from_selector( [ $selector ], $saved_value );
			if ( ! empty( $styles ) ) {
				return $styles;
			}
		} else {
			$this->add_custom_css( $device, $selector, $value );
		}
	}


	/**
	 * Extracts a responsive option css
	 *
	 * @param string                $option_type  The option type
	 * @param array<string, string> $style_config Configuration for the css
	 * @param mixed                 $value        The value to use when parsing the option
	 * @param int|null              $index        In case of repeaters, the index of the saved value
	 *
	 * @return void
	 */
	public function extract_responsive_option_css( $option_type, $style_config, $value, $index ) {
		if ( ! empty( $value ) && is_array( $value ) ) {
			foreach ( $value as $device => $css_value ) {
				$this->extract_option_css( $device, $option_type, $style_config, $css_value, $index );
			}
		}
	}

	/**
	 * Adds a custom css rule to the custom css configuration
	 *
	 * @param string $device   The device id
	 * @param string $selector The css selector
	 * @param string $value    The css rule that will be added to the device->selector configuration
	 *
	 * @return void
	 */
	public function add_custom_css( $device, $selector, $value ) {
		if ( ! isset( $this->custom_css_config[$device] ) ) {
			return;
		}

		if ( ! isset( $this->custom_css_config[$device][$selector] ) ) {
			$this->custom_css_config[$device][$selector] = [];
		}
		$this->custom_css_config[$device][$selector][] = $value;
	}

	public function add_raw_css( $css ) {
		$this->raw_css .= $css;
	}

	/**
	 * Returns the custom css build from the custom css config
	 *
	 * @return string
	 */
	public function get_css() {
		$returned_css = $this->raw_css;

		foreach ( $this->custom_css_config as $device => $selectors_config ) {
			$extracted_css = $this->extract_styles( $selectors_config );

			if ( empty( $extracted_css ) ) {
				continue;
			}

			if ( $device === 'default' ) {
				$returned_css .= $extracted_css;
			} else {
				$returned_css .= sprintf( '@media (max-width: %spx) { %s }', self::$responsive_devices_map[$device], $extracted_css );
			}
		}

		return $returned_css;
	}

	/**
	 * Converts a style configuration to CSS
	 *
	 * @param array<string, array<string>> $selectors_config The style configuration
	 *
	 * @return string
	 */
	public function extract_styles( $selectors_config ) {
		$returned_css = '';

		if ( is_array( $selectors_config ) ) {
			foreach ( $selectors_config as $selector => $value ) {
				$returned_css .= sprintf( '%s { %s }', $selector, implode( ';', $value ) );
			}
		}

		return $returned_css;
	}
}
