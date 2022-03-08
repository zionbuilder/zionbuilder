<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Responsive;

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
	public static function get_css_from_selector( $selectors, $style_config ) {
		$css = '';

		if ( isset( $style_config['styles'] ) ) {
			$selector = implode( ',', $selectors );
			$css     .= self::get_styles( $selector, $style_config['styles'] );
		}

		if ( isset( $style_config['child_styles'] ) && is_array( $style_config['child_styles'] ) ) {
			foreach ( $style_config['child_styles'] as $child_style_config ) {
				$states   = isset( $child_style_config['states'] ) ? $child_style_config['states'] : [ 'default' ];
				$selector = isset( $child_style_config['selector'] ) ? $child_style_config['selector'] : null;

				if ( $selector ) {
					$child_selectors = [];

					foreach ( $selectors as $main_selector ) {
						foreach ( $states as $state ) {
							if ( $state === 'default' ) {
								$child_selectors[] = sprintf( '%s %s', $main_selector, $selector );
							} else {
								$child_selectors[] = sprintf( '%s%s %s', $main_selector, $state, $selector );
							}
						}
					}

					$css .= self::get_css_from_selector( $child_selectors, $child_style_config );
				}
			}
		}

		return $css;
	}

	/**
	 * @param string               $css_selector
	 * @param array<string, mixed> $style_options
	 *
	 * @return string
	 */
	public static function get_styles( $css_selector, $style_options = [] ) {
		$compiled_styles = '';
		$devices_order   = array_keys( Responsive::get_breakpoints_as_device_width() );

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
	 * @param string $responsive_device_id
	 * @param string $styles
	 *
	 * @return bool|string
	 */
	public static function handle_media_query( $responsive_device_id, $styles ) {
		// Don't proceed if we do not have
		if ( ! $responsive_device_id || ! $styles ) {
			return false;
		}

		$media_queries = Responsive::get_breakpoints_as_device_width();

		$start = 'default' !== $responsive_device_id ? '@media (max-width: ' . $media_queries[$responsive_device_id] . 'px) {' : '';
		$end   = 'default' !== $responsive_device_id ? '}' : '';

		return sprintf( '%s%s%s', $start, $styles, $end );
	}

	/**
	 * @param string                              $css_selector
	 * @param array<string, array<string, mixed>> $pseudo_selectors
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
	 * @param string               $css_selector
	 * @param string               $pseudo_selector
	 * @param array<string, mixed> $style_options
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


	/**
	 * Compiles gradient config to valid gradient css
	 *
	 * @param array<int, mixed> $config
	 *
	 * @return string
	 */
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
						return $a['position'] > $b['position'] ? 1 : -1;
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
	 * @param array<string, mixed> $style_options
	 *
	 * @return string
	 */
	public static function compile_styles( $style_options ) {
		$compiled_css            = '';
		$background_position_x   = false;
		$background_position_y   = false;
		$background_image_config = [];
		$text_decoration_value   = [];
		$filter_properties       = [ 'grayscale', 'sepia', 'blur', 'brightness', 'saturate', 'opacity', 'contrast', 'hue-rotate', 'invert' ];
		$transform_origin_x      = '';
		$transform_origin_y      = '';
		$transform_origin_z      = '';
		$compiled_filter         = '';
		$flex_reverse            = isset( $style_options['flex-reverse'] ) ? $style_options['flex-reverse'] : false;

		foreach ( $style_options as $attribute => $value ) {

			switch ( $attribute ) {
				case in_array( $attribute, $filter_properties, true ):
					switch ( $attribute ) {
						case 'hue-rotate':
							$compiled_filter .= sprintf( '%s(%sdeg) ', $attribute, $value );
							break;

						case 'blur':
							$compiled_filter .= sprintf( '%s(%spx) ', $attribute, $value );
							break;

						default:
							$compiled_filter .= sprintf( '%s(%s%%) ', $attribute, $value );
							break;

					}
					break;

				case 'transform_origin_x_axis':
					$transform_origin_x = $value;
					break;

				case 'transform_origin_y_axis':
					$transform_origin_y = $value;
					break;

				case 'transform_origin_z_axis':
					$transform_origin_z = $value;
					break;

				case 'transform_style':
					$compiled_css .= sprintf( '-ms-transform-style: %s; -webkit-transform-style: %s; transform-style: %s;', $value, $value, $value );
					break;

				case 'flex-reverse':
					break;

				case 'flex-direction':
					if ( ! $flex_reverse ) {
						$compiled_css .= ( $value === 'row' ) ? sprintf( '-webkit-box-orient: horizontal; -webkit-box-direction:normal;  -ms-flex-direction: %s; flex-direction: %s;', $value, $value ) : sprintf( '-webkit-box-orient: vertical; -webkit-box-direction:normal;  -ms-flex-direction:  %s; flex-direction: %s;', $value, $value );
					} else {
						$compiled_css .= ( $value === 'row' ) ? sprintf( '-webkit-box-orient: horizontal; -webkit-box-direction:reverse; -ms-flex-direction: row-reverse; flex-direction: row-reverse; ' ) : sprintf( '-webkit-box-orient: vertical; -webkit-box-direction:reverse; -ms-flex-direction: column-reverse; flex-direction: column-reverse;' );
					}

					break;

				case 'order':
					$compiled_css .= sprintf( '-webkit-order: %s; -ms-flex-order: %s; order: %s;', $value, $value, $value );
					break;

				case 'custom-order':
					$compiled_css .= sprintf( '-webkit-order: %s; -ms-flex-order: %s; order: %s;', $value, $value, $value );
					break;

				case 'align-items':
					$todelete      = 'flex-';
					$clean_value   = str_replace( $todelete, '', $value );
					$compiled_css .= sprintf( '-webkit-box-align: %s; -ms-flex-align: %s; align-items: %s;', $clean_value, $clean_value, $value );
					break;

				case 'justify-content':
					if ( $value === 'space-around' ) {
						$compiled_css .= sprintf( '-ms-flex-pack: distribute; justify-content: space-around;' );
					} else {
						if ( $value === 'space-between' ) {
							$compiled_css .= sprintf( '-webkit-box-pack: justify; -ms-flex-pack: justify; justify-content: space-between;' );
						} else {
							$todelete      = 'flex-';
							$clean_value   = str_replace( $todelete, '', $value );
							$compiled_css .= sprintf( '-webkit-box-pack:  %s; -ms-flex-pack:  %s; justify-content:  %s;', $clean_value, $clean_value, $value );
						}
					}

					break;

				case 'flex-wrap':
					$compiled_css .= sprintf( '-ms-flex-wrap: %s; flex-wrap: %s;', $value, $value );
					break;

				case 'align-content':
					switch ( $value ) {
						case 'space-around':
							$compiled_css .= sprintf( '-ms-flex-line-pack: distribute; align-content: space-around;' );
							break;
						case 'space-between':
							$compiled_css .= sprintf( '-ms-flex-line-pack: justify; align-content: space-between;' );
							break;
						default:
							$todelete      = 'flex-';
							$clean_value   = str_replace( $todelete, '', $value );
							$compiled_css .= sprintf( '-ms-flex-line-pack: %s; align-content: %s;', $clean_value, $value );
							break;
					}
					break;

				case 'flex-grow':
					$compiled_css .= sprintf( '-webkit-box-flex: %s; -ms-flex-positive: %s; flex-grow: %s;', $value, $value, $value );
					break;

				case 'flex-shrink':
					$compiled_css .= sprintf( '-ms-flex-negative: %s; flex-shrink: %s;', $value, $value );
					break;

				case 'flex-basis':
					$compiled_css .= sprintf( '-ms-flex-preferred-size: %s; flex-basis: %s;', $value, $value );
					break;

				case 'align-self':
					$todelete      = 'flex-';
					$clean_value   = str_replace( $todelete, '', $value );
					$compiled_css .= sprintf( '-ms-flex-item-align: %s; align-self: %s;', $clean_value, $value );
					break;

				case 'perspective':
					$compiled_css .= sprintf( '-webkit-%s: %s;', $attribute, $value );
					$compiled_css .= sprintf( '%s: %s;', $attribute, $value );
					break;

				case 'background-gradient':
					$gradient_config = self::compile_gradient( $value );
					if ( ! empty( $gradient_config ) ) {
						$background_image_config[0] = $gradient_config;
					}
					break;

				case 'background-image':
					if ( $value ) {
						$background_image_config[1] = sprintf( 'url(%s)', $value );
					}
					break;

				case 'background-size':
				case 'background-video':
				case 'background-size-units':
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
					if ( ! empty( $border_radius || $value === 0 ) ) {
						$compiled_css .= $border_radius;
					}
					break;
				case 'box-shadow':
					$box_shadow = self::compile_box_shadow( $value );
					if ( ! empty( $box_shadow || $value === 0 ) ) {
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
					if ( ( ! empty( $value ) || $value === 0 || $value === '0' ) && ! is_array( $value ) ) {
						$compiled_css .= sprintf( '%s: %s;', $attribute, $value );
					}
					break;
			}

			switch ( $value ) {
				case 'flex':
					$compiled_css .= sprintf( 'display: -webkit-box; display: -moz-box; display: -ms-flexbox; display: -webkit-flex;' );
					break;
				case 'inline-flex':
					$compiled_css .= sprintf( 'display: -webkit-inline-box; display: -ms-inline-flexbox;' );
					break;
			}
		}

		// Background size
		if ( isset( $style_options['background-size'] ) ) {
			$bg_size_value = $style_options['background-size'];

			if ( $bg_size_value !== 'custom' ) {
				$compiled_css .= sprintf( 'background-size: %s;', $bg_size_value );
			} elseif ( isset( $style_options['background-size-units'] ) ) {
				$bg_size_units = $style_options['background-size-units'];

				if ( isset( $bg_size_units['x'] ) || isset( $bg_size_units['y'] ) ) {
					$x             = isset( $bg_size_units['x'] ) ? $bg_size_units['x'] : 'auto';
					$y             = isset( $bg_size_units['y'] ) ? $bg_size_units['y'] : 'auto';
					$compiled_css .= sprintf( 'background-size: %s %s;', $x, $y );
				}
			}
		}

		// Transform origin
		if ( ! empty( $transform_origin_x ) || ! empty( $transform_origin_y ) || ! empty( $transform_origin_z ) ) {
			$compiled_css .= sprintf( '-webkit-transform-origin: %s %s %s;', $transform_origin_x, $transform_origin_y, $transform_origin_z );
			$compiled_css .= sprintf( 'transform-origin: %s %s %s;', $transform_origin_x, $transform_origin_y, $transform_origin_z );
		}

		// Filters
		if ( ! empty( $compiled_filter ) ) {
			$compiled_css .= sprintf( '-webkit-filter: %s;', $compiled_filter );
			$compiled_css .= sprintf( 'filter: %s;', $compiled_filter );
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

			if ( $delay !== 0 ) {
				$compiled_css .= sprintf( 'transition: %s %sms %s %sms;', $property, $duration, $timing_function, $delay );
			} else {
				$compiled_css .= sprintf( 'transition: %s %sms %s ;', $property, $duration, $timing_function );
			}
		}

		return $compiled_css;
	}


	/**
	 * Compiles box shadow list to valid CSS
	 *
	 * @param array<string, string> $options
	 *
	 * @return string
	 */
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

			if ( isset( $options['inset'] ) && $options['inset'] ) {
				$shadow_list[] = 'inset';
			}

			return implode( ' ', $shadow_list );
		}

		return '';
	}


	/**
	 * Compiles border config to valid CSS
	 *
	 * @param array<string, array<string, string>> $border_config
	 *
	 * @return string
	 */
	public static function compile_border( $border_config ) {
		$css = '';
		if ( is_array( $border_config ) ) {
			foreach ( $border_config as $border_position => $border_value ) {
				if ( isset( $border_value['width'] ) ) {
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


	/**
	 * Compiles transform config to valid css
	 *
	 * @param array<int, array<string, mixed>> $value
	 *
	 * @return string
	 */
	public static function compile_transform( $value ) {
		$transform_string     = '';
		$origin_string        = '';
		$combined_styles      = '';
		$perspective_value    = '';
		$perspective_origin_x = '';
		$perspective_origin_y = '';
		$origin               = '';
		$x_axis               = '';
		$y_axis               = '';

		if ( is_array( $value ) ) {
			foreach ( $value as $transform_config ) {
				$property                = isset( $transform_config['property'] ) ? $transform_config['property'] : 'translate';
				$current_property_config = isset( $transform_config[$property] ) ? $transform_config[$property] : [];

				if ( is_array( $current_property_config ) ) {
					foreach ( $current_property_config as $property_id => $property_value ) {
						if ( $property === 'transform-origin' ) {
							$origin_string .= $property_value . ' ';
						} elseif ( $property === 'perspective' ) {
							if ( $property_id === 'perspective_value' ) {
								$transform_string .= sprintf( 'perspective(%s) ', $property_value );
							}
							if ( $property_id === 'perspective_origin_x_axis' ) {
								$perspective_origin_x .= sprintf( '%s', $property_value );
							}
							if ( $property_id === 'perspective_origin_y_axis' ) {
								$perspective_origin_y .= sprintf( '%s', $property_value );
							}
						} else {
							$transform_string .= sprintf( '%s(%s) ', $property_id, $property_value );
						}
					}
				}
			}

			if ( ! empty( $transform_string ) ) {
				$combined_styles .= sprintf( '-webkit-transform: %s;', $transform_string );
				$combined_styles .= sprintf( 'transform: %s;', $transform_string );
			}
			if ( ! empty( $perspective_value ) || ! empty( $perspective_origin_x ) || ! empty( $perspective_origin_y ) ) {
				if ( ! empty( $perspective_value ) ) {
					$combined_styles .= sprintf( '-webkit-perspective: %s; perspective: %s;', $perspective_value, $perspective_value );
				}
				$x_axis .= ! empty( $perspective_origin_x ) ? $perspective_origin_x : '50%';
				$y_axis .= ! empty( $perspective_origin_y ) ? $perspective_origin_y : '50%';

				$origin          .= sprintf( '%s %s', $x_axis, $y_axis );
				$combined_styles .= sprintf( '-ms-perspective-origin: %s; -moz-perspective-origin: %s; -webkit-perspective-origin: %s; perspective-origin: %s;', $origin, $origin, $origin, $origin );
			}

			if ( ! empty( $origin_string ) ) {
				$combined_styles .= sprintf( '-webkit-transform-origin: %s; transform-origin: %s;', $origin_string, $origin_string );
			}
		}

		return $combined_styles;
	}


	/**
	 * Compiles border radius config to valid CSS
	 *
	 * @param array<string, string> $border_radius_config
	 *
	 * @return string
	 */
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
