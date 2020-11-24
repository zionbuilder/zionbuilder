<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Utils;
use enshrined\svgSanitize\Sanitizer;
use ZionBuilder\FileSystem;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Masks
 *
 * @package ZionBuilder\Elements
 */
class Masks {
	final protected function __construct() {
	}
	/**
	 * Holds a list of masks
	 *
	 * @var array<int, string>
	 */
	public static function getshapes() {
		$shapes = [
			'bottom-free-mask_01' => Utils::get_file_url( 'assets/masks/bottom-free-mask_01.svg' ),
			'bottom-free-mask_02' => Utils::get_file_url( 'assets/masks/bottom-free-mask_02.svg' ),
			'bottom-free-mask_03' => Utils::get_file_url( 'assets/masks/bottom-free-mask_03.svg' ),
			'bottom-free-mask_04' => Utils::get_file_url( 'assets/masks/bottom-free-mask_04.svg' ),
			'bottom-free-mask_05' => Utils::get_file_url( 'assets/masks/bottom-free-mask_05.svg' ),
			'bottom-free-mask_06' => Utils::get_file_url( 'assets/masks/bottom-free-mask_06.svg' ),
		];
		return apply_filters( 'zionbuilder/masks', $shapes );
	}
	/*
	 * Returns ruleset for svg
	 */
	public static function get_kses_extended_ruleset() {
		$kses_defaults = wp_kses_allowed_html( 'post' );

		$svg_args = [
			'svg'   => [
				'class'               => true,
				'aria-hidden'         => true,
				'aria-labelledby'     => true,
				'role'                => true,
				'xmlns'               => true,
				'width'               => true,
				'height'              => true,
				'viewbox'             => true, // <= Must be lower case!
				'style'               => true,
				'preserveaspectratio' => true,
			],
			'g'     => [ 'fill' => true ],
			'title' => [ 'title' => true ],
			'path'  => [
				'd'            => true,
				'fill'         => true,
				'fill-opacity' => true,
			],
		];
		return array_merge( $kses_defaults, $svg_args );
	}

	/*
	 * Returns mask markup
	 *
	 * @param string $masks The mask options saved for the element
	 *
	 * @return void
	 */
	public static function render_masks( $masks ) {
		foreach ( $masks as $position => $shape_config ) {
			// Don't proceed if we do not have a valid shape
			if ( empty( $shape_config['shape'] ) ) {
				continue;
			}

			$shape_style  = '';
			$shape_path   = $shape_config['shape'];
			$shape_color  = ( ! empty( $shape_config['color'] ) ) ? sprintf( 'color: %s;', $shape_config['color'] ) : '';
			$shape_height = ( ! empty( $shape_config['height'] && $shape_config['height'] !== 'auto' ) ) ? sprintf( ' height: %s;', $shape_config['height'] ) : '';
			$shape_style .= $shape_color;
			$shape_style .= $shape_height;
			$flip         = $shape_config['flip'];
			$flip_class   = $flip ? 'zb-mask-pos--flip' : '';
			$pos_class    = 'zb-mask-pos--' . esc_attr( $position ); ?>
			<span class="zb-mask <?php echo esc_attr( $pos_class ) . ' ' . esc_attr( $flip_class ); ?>"
				<?php
				if ( ! empty( $shape_style ) ) {
					echo sprintf( 'style="%s"', esc_attr( $shape_style ) );
				}
				?>
			>
			<?php
				self::get_mask( $shape_path );
			?>
			</span>
			<?php
		}
	}

	/*
	 * Returns string from shape id
	 *
	 * @param string $shape The shape id for which the attributes will be retrieved
	 * @param mixed  $mask
	 */
	public static function get_mask( $shape_path ) {
		// bail if we do not have any attributes
		if ( empty( $shape_path ) ) {
			return;
		}
		$all_shapes = self::getshapes();
		$shape_path = strrpos( $shape_path, '.svg' ) ? $shape_path : $all_shapes[$shape_path];
		$svg_file   = FileSystem::get_file_system()->get_contents( $shape_path );
		$sanitizer  = new Sanitizer();
		echo wp_kses( $sanitizer->sanitize( $svg_file ), self::get_kses_extended_ruleset() );
	}
}
