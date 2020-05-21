<?php

namespace ZionBuilder\Elements;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Style
 *
 * @package ZionBuilder\Elements
 */
class Style {
	/**
	 * @param $css_selector
	 * @param array $style_options
	 *
	 * @return string
	 */
	public static function get_styles( $css_selector, $style_options = [] ) {
		$compiled_styles = '';
		$devices_order   = [
			'default',
			'laptop',
			'tablet',
			'mobile',
		];

		foreach ( $devices_order as $responsive_device_id ) {
			if ( isset( $style_options[$responsive_device_id] ) ) {
				$pseudo_selectors = $style_options[$responsive_device_id];
				$pseudo_styles    = self::handle_pseudo_selectors( $css_selector, $pseudo_selectors );
				$compiled_styles .= self::handle_media_query( $responsive_device_id, $pseudo_styles );
			}
		}

		return $compiled_styles;
	}

	/**
	 * @param $responsive_device_id
	 * @param $styles
	 *
	 * @return bool|string
	 */
	public static function handle_media_query( $responsive_device_id, $styles ) {
		// Don't proceed if we do not have
		if ( ! $responsive_device_id || ! $styles ) {
			return false;
		}

		$media_queries = [
			'laptop' => '991.98px',
			'tablet' => '767.98px',
			'mobile' => '575.98px',
		];

		$start = 'default' !== $responsive_device_id ? '@media (max-width: ' . $media_queries[$responsive_device_id] . ') {' : '';
		$end   = 'default' !== $responsive_device_id ? '}' : '';

		return sprintf( '%s%s%s', $start, $styles, $end );
	}

	/**
	 * @param $css_selector
	 * @param $pseudo_selectors
	 *
	 * @return string
	 */
	public static function handle_pseudo_selectors( $css_selector, $pseudo_selectors ) {
		$compiled_css = '';
		if ( is_array( $pseudo_selectors ) ) {
			foreach ( $pseudo_selectors as $pseudo_selector => $selector_config ) {
				$compiled_css .= self::handle_pseudo_selector( $css_selector, $pseudo_selector, $selector_config );
			}
		}

		return $compiled_css;
	}

	/**
	 * @param $css_selector
	 * @param $pseudo_selector
	 * @param $style_options
	 *
	 * @return string
	 */
	public static function handle_pseudo_selector( $css_selector, $pseudo_selector, $style_options ) {
		$append          = 'default' !== $pseudo_selector ? $pseudo_selector : '';
		$compiled_styles = self::compile_styles( $style_options );
		$content         = isset( $style_options['content'] ) ? $style_options['content'] : false;
		$content_style   = ! empty( $content ) ? sprintf( 'content: "%s";', $content ) : '';

		if ( $compiled_styles || $content_style ) {
			return sprintf( '%s%s {%s%s}', $css_selector, $append, $content_style, $compiled_styles );
		}

		return '';
	}

	public static function compile_gradient( $config ) {
		if ( ! is_array( $config ) ) {
			return '';
		}

		$gradients = [];

		foreach ( $config as $gradient_config ) {
			$colors   = [];
			$position = '90deg';
			if ( ! empty( $gradient_config['colors'] ) ) {
				usort(
					$gradient_config['colors'],
					function( $a, $b ) {
						return $a['position'] > $b['position'];
					}
				);

				foreach ( $gradient_config['colors'] as $value ) {
					$colors[] = sprintf( '%s %s%%', $value['color'], $value['position'] );
				}

				if ( $gradient_config['type'] === 'radial' ) {
					$position_x = isset( $gradient_config['position']['x'] ) ? $gradient_config['position']['x'] : 50;
					$position_y = isset( $gradient_config['position']['y'] ) ? $gradient_config['position']['y'] : 50;
					$position   = sprintf( 'circle at %s%% %s%%', $position_x, $position_y );
				} else {
					$position = sprintf( '%sdeg', $gradient_config['angle'] );
				}

				$gradients[] = sprintf( '%s-gradient(%s, %s)', $gradient_config['type'], $position, implode( ', ', $colors ) );
			}
		}
		$reversed = array_reverse( $gradients );
		return implode( ', ', $reversed );
	}

	/**
	 * @param array $style_options
	 *
	 * @return string
	 */
	public static function compile_styles( $style_options ) {
		$compiled_css            = '';
		$background_position_x   = false;
		$background_position_y   = false;
		$background_image_config = [];
		$text_decoration_value   = [];

		foreach ( $style_options as $attribute => $value ) {
			switch ( $attribute ) {
				case 'background-gradient':
					$gradient_config = self::compile_gradient( $value );
					if ( ! empty( $gradient_config ) ) {
						$background_image_config[0] = $gradient_config;
					}
					break;

				case 'background-image':
					$background_image_config[1] = sprintf( 'url(%s)', $value );
					break;

				case 'background-size':
				case 'background-size-units':
					if ( isset( $style_options['background-size'] ) && $style_options['background-size'] !== 'custom' ) {
						$compiled_css .= sprintf( '%s: %s;', $attribute, $value );
					} else {
						if ( isset( $style_options['background-size'] ) && $style_options['background-size'] === 'custom' ) {
							if ( $attribute === 'background-size-units' && isset( $value['x'] ) || isset( $value['y'] ) ) {
								$x             = isset( $value['x'] ) ? $value['x'] : '50%';
								$y             = isset( $value['y'] ) ? $value['y'] : '50%';
								$compiled_css .= sprintf( 'background-size: %s %s;', $x, $y );
							}
						}
					}
					break;
				case 'background-position-x':
					$background_position_x = $value;
					break;
				case 'background-position-y':
					$background_position_y = $value;
					break;
				case 'text-decoration':
					if ( is_array( $value ) ) {
						if ( in_array( 'underline', $value, true ) ) {
							$text_decoration_value[] = 'underline';
						}

						if ( in_array( 'line-through', $value, true ) ) {
							$text_decoration_value[] = 'line-through';
						}

						if ( in_array( 'italic', $value, true ) ) {
							$compiled_css .= sprintf( 'font-style: italic;' );
						}
					}
					break;
				case 'text-shadow':
					$text_shadow = self::compile_box_shadow( $value );
					if ( ! empty( $text_shadow ) ) {
						$compiled_css .= sprintf( 'text-shadow: %s;', $text_shadow );
					}
					break;
				case 'border':
					$border = self::compile_border( $value );
					if ( ! empty( $border ) ) {
						$compiled_css .= $border;
					}
					break;
				case 'border-radius':
					$border_radius = self::compile_border_radius( $value );
					if ( ! empty( $border_radius ) ) {
						$compiled_css .= $border_radius;
					}
					break;
				case 'box-shadow':
					$box_shadow = self::compile_box_shadow( $value );
					if ( ! empty( $box_shadow ) ) {
						$compiled_css .= sprintf( 'box-shadow: %s;', $box_shadow );
					}
					break;
				case 'transition-property':
				case 'transition-delay':
				case 'transition-duration':
				case 'transition-timing-function':
					break;
				case 'transform':
					$compiled_css .= self::compile_transform( $value );
					break;
				default:
					if ( ! empty( $value ) && ! is_array( $value ) ) {
						$compiled_css .= sprintf( '%s: %s;', $attribute, $value );
					}
					break;
			}
		}

		// Background image
		if ( ! empty( $background_image_config ) ) {
			sort( $background_image_config );
			$background_value = implode( ', ', $background_image_config );
			$compiled_css    .= sprintf( 'background-image: %s;', $background_value );
		}

		// Background position
		if ( $background_position_x || $background_position_y ) {
			$compiled_css .= sprintf( 'background-position: %s %s;', $background_position_x, $background_position_y );
		}

		// Text decoration
		if ( ! empty( $text_decoration_value ) ) {
			$text_decoration_value_string = implode( ' ', $text_decoration_value );
			$compiled_css                .= sprintf( 'text-decoration: %s;', $text_decoration_value_string );
		}

		// Check for transitions
		if ( ! empty( $style_options['transition-duration'] ) ) {
			$property        = isset( $style_options['transition-property'] ) ? $style_options['transition-property'] : 'all';
			$duration        = isset( $style_options['transition-duration'] ) ? $style_options['transition-duration'] : 0;
			$timing_function = isset( $style_options['transition-timing-function'] ) ? $style_options['transition-timing-function'] : 'ease';
			$delay           = isset( $style_options['transition-delay'] ) ? $style_options['transition-delay'] : 0;

			$compiled_css .= sprintf( 'transition: %s %sms %s %sms;', $property, $duration, $timing_function, $delay );
		}

		return $compiled_css;
	}

	public static function compile_box_shadow( $options ) {
		$offset_x = isset( $options['offset-x'] ) ? $options['offset-x'] : 0;
		$offset_y = isset( $options['offset-y'] ) ? $options['offset-y'] : 0;

		if ( isset( $options['offset-x'] ) || isset( $options['offset-y'] ) || isset( $options['blur'] ) || isset( $options['spread'] ) || isset( $options['color'] ) || isset( $options['inset'] ) ) {
			$shadow_list = [ $offset_x, $offset_y ];

			if ( isset( $options['blur'] ) ) {
				$shadow_list[] = $options['blur'];
			}

			if ( isset( $options['spread'] ) ) {
				$shadow_list[] = $options['spread'];
			}

			if ( isset( $options['color'] ) ) {
				$shadow_list[] = $options['color'];
			}

			if ( isset( $options['inset'] ) ) {
				$shadow_list[] = 'inset';
			}

			return implode( ' ', $shadow_list );
		}

		return false;
	}

	public static function compile_border( $border_config ) {
		$css = '';
		if ( is_array( $border_config ) ) {
			foreach ( $border_config as $border_position => $border_value ) {
				if ( ! empty( $border_value['width'] ) && isset( $border_value['width'] ) ) {
					$color = isset( $border_value['color'] ) ? $border_value['color'] : false;
					$width = isset( $border_value['width'] ) ? $border_value['width'] : false;
					$style = isset( $border_value['style'] ) ? $border_value['style'] : 'solid';

					if ( $border_position === 'all' ) {
						$css .= sprintf( 'border: %s %s %s;', $width, $style, $color );
					} else {
						$css .= sprintf( 'border-%s: %s %s %s;', $border_position, $width, $style, $color );
					}
				}
			}
		}

		return $css;
	}

	public static function compile_transform( $value ) {
		$transform_string = '';
		$origin_string    = '';
		$combined_styles  = '';

		if ( is_array( $value ) ) {
			foreach ( $value as $transform_config ) {
				$property                = isset( $transform_config['property'] ) ? $transform_config['property'] : 'translate';
				$current_property_config = isset( $transform_config[$property] ) ? $transform_config[$property] : [];

				if ( is_array( $current_property_config ) ) {
					foreach ( $current_property_config as $property_id => $property_value ) {
						if ( $property === 'transform-origin' ) {
							$origin_string .= $property_value . ' ';
						} elseif ( $property !== 'perspective' ) {
							$transform_string .= sprintf( '%s(%s)', $property_id, $property_value );
						} else {
							$perspective_string = $property_value;
						}
					}
				}
			}

			if ( ! empty( $transform_string ) ) {
				$combined_styles .= sprintf( 'transform: %s;', $transform_string );
			}

			if ( ! empty( $perspective_string ) ) {
				$combined_styles .= sprintf( 'perspective: %s;', $perspective_string );
			}

			if ( ! empty( $origin_string ) ) {
				$combined_styles .= sprintf( 'transform-origin: %s;', $origin_string );
			}
		}

		return $combined_styles;
	}

	public static function compile_border_radius( $border_radius_config ) {
		$css = '';

		if ( is_array( $border_radius_config ) ) {
			$border_top_left_radius     = ! empty( $border_radius_config['border-top-left-radius'] ) ? $border_radius_config['border-top-left-radius'] : 0;
			$border_top_right_radius    = ! empty( $border_radius_config['border-top-right-radius'] ) ? $border_radius_config['border-top-right-radius'] : 0;
			$border_bottom_right_radius = ! empty( $border_radius_config['border-bottom-right-radius'] ) ? $border_radius_config['border-bottom-right-radius'] : 0;
			$border_bottom_left_radius  = ! empty( $border_radius_config['border-bottom-left-radius'] ) ? $border_radius_config['border-bottom-left-radius'] : 0;

			$borders_array = [ $border_top_left_radius, $border_top_right_radius, $border_bottom_right_radius, $border_bottom_left_radius ];
			$equal_borders = count( array_unique( $borders_array ) ) === 1;

			if ( $equal_borders ) {
				$css .= sprintf( 'border-radius: %s;', $border_top_left_radius );
			} else {
				$css .= sprintf( 'border-radius: %s %s %s %s;', $border_top_left_radius, $border_top_right_radius, $border_bottom_right_radius, $border_bottom_left_radius );
			}
		}

		return $css;
	}
}
