<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Utils;
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
	 */
	public static function get_shapes() {
		$shapes = [
			'bottom-free-mask_01' => [
				'url'  => Utils::get_file_url( 'assets/masks/bottom-free-mask_01.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-free-mask_01.svg' ),
			],
			'bottom-free-mask_02' => [
				'url'  => Utils::get_file_url( 'assets/masks/bottom-free-mask_02.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-free-mask_02.svg' ),
			],
			'bottom-free-mask_03' => [
				'url'  => Utils::get_file_url( 'assets/masks/bottom-free-mask_03.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-free-mask_03.svg' ),
			],
			'bottom-free-mask_04' => [
				'url'  => Utils::get_file_url( 'assets/masks/bottom-free-mask_04.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-free-mask_04.svg' ),
			],
			'bottom-free-mask_05' => [
				'url'  => Utils::get_file_url( 'assets/masks/bottom-free-mask_05.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-free-mask_05.svg' ),
			],
			'bottom-free-mask_06' => [
				'url'  => Utils::get_file_url( 'assets/masks/bottom-free-mask_06.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-free-mask_06.svg' ),
			],
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
			$shape_style .= $shape_color;
			$flip_class   = isset( $shape_config['flip'] ) && $shape_config['flip'] ? 'zb-mask-pos--flip' : '';
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
	 * @param string $shape_id The shape id for which the attributes will be retrieved
	 *
	 * @return void
	 */
	public static function get_mask( $shape_id ) {
		// bail if we do not have any attributes
		if ( empty( $shape_id ) ) {
			return;
		}
		$all_shapes = self::get_shapes();

		// Old system where the shape was saved with url
		if ( strrpos( $shape_id, '.svg' ) ) {
			echo wp_kses( FileSystem::get_file_system()->get_contents( $shape_id ), self::get_kses_extended_ruleset() );
		} else {
			$shape_config = $all_shapes[$shape_id];

			if ( isset( $shape_config['path'] ) ) {
				echo wp_kses( FileSystem::get_file_system()->get_contents( $shape_config['path'] ), self::get_kses_extended_ruleset() );
			}
		}
	}
}
