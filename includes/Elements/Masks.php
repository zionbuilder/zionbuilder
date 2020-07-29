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
			'top'    => [
				'top-mask_01' => Utils::get_file_url( 'assets/masks/top-mask_01.svg' ),
				'top-mask_02' => Utils::get_file_url( 'assets/masks/top-mask_02.svg' ),
				'top-mask_05' => Utils::get_file_url( 'assets/masks/top-mask_05.svg' ),
				'top-mask_06' => Utils::get_file_url( 'assets/masks/top-mask_06.svg' ),
				'top-mask_07' => Utils::get_file_url( 'assets/masks/top-mask_07.svg' ),
				'top-mask_14' => Utils::get_file_url( 'assets/masks/top-mask_14.svg' ),

			],
			'bottom' => [
				'bottom-mask_01' => Utils::get_file_url( 'assets/masks/bottom-mask_01.svg' ),
				'bottom-mask_02' => Utils::get_file_url( 'assets/masks/bottom-mask_02.svg' ),
				'bottom-mask_05' => Utils::get_file_url( 'assets/masks/bottom-mask_05.svg' ),
				'bottom-mask_07' => Utils::get_file_url( 'assets/masks/bottom-mask_07.svg' ),
				'bottom-mask_14' => Utils::get_file_url( 'assets/masks/bottom-mask_14.svg' ),
			],

		];
		return apply_filters( 'zionbuilder/masks', $shapes );
	}

	public static function get_kses_extended_ruleset() {
		$kses_defaults = wp_kses_allowed_html( 'post' );

		$svg_args = [
			'svg'   => [
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true, // <= Must be lower case!
			],
			'g'     => [ 'fill' => true ],
			'title' => [ 'title' => true ],
			'path'  => [
				'd'    => true,
				'fill' => true,
			],
		];
		return array_merge( $kses_defaults, $svg_args );
	}
	/*
	 * Returns string from shape id
	 *
	 * @param string $shape The shape id for which the attributes will be retrieved
	 * @param mixed  $mask
	 */
	public static function get_mask( $mask = '', $position ) {
		// bail if we do not have any attributes

		if ( empty( $mask ) ) {
			return;
		}
		$specific_array = self::getshapes()[$position];
		$returned_value = $specific_array[$mask];
		$svg_file       = FileSystem::get_file_system()->get_contents( $returned_value );
		$sanitizer      = new Sanitizer();
		echo wp_kses( $sanitizer->sanitize( $svg_file ), self::get_kses_extended_ruleset() );
	}
}
