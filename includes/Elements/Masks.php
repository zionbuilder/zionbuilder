<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Utils;
use enshrined\svgSanitize\Sanitizer;


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
		$shapes = apply_filters(
			'zionbuilder/masks',
			[
				'shape-oblique'            => Utils::get_file_url( 'assets/masks/shape-oblique.svg' ),
				'shape-double'             => Utils::get_file_url( 'assets/masks/shape-double.svg' ),
				'shape-oblique-mirror'     => Utils::get_file_url( 'assets/masks/shape-oblique-mirror.svg' ),
				'shape-curved-mirror'      => Utils::get_file_url( 'assets/masks/shape-curved-mirror.svg' ),
				'shape-split'              => Utils::get_file_url( 'assets/masks/shape-split.svg' ),
				'shape-wavy'               => Utils::get_file_url( 'assets/masks/shape-wavy.svg' ),
				'shape-oblique-top'        => Utils::get_file_url( 'assets/masks/shape-oblique-top.svg' ),
				'shape-double-top'         => Utils::get_file_url( 'assets/masks/shape-double-top.svg' ),
				'shape-oblique-mirror-top' => Utils::get_file_url( 'assets/masks/shape-oblique-mirror-top.svg' ),
				'shape-curved-mirror-top'  => Utils::get_file_url( 'assets/masks/shape-curved-mirror-top.svg' ),
				'shape-split-top'          => Utils::get_file_url( 'assets/masks/shape-split-top.svg' ),
				'shape-wavy-top'           => Utils::get_file_url( 'assets/masks/shape-wavy-top.svg' ),
			]
		);
		return $shapes;
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
	public static function get_mask( $mask = '' ) {
		// bail if we do not have any attributes

		if ( empty( $mask ) ) {
			return;
		}

		$returned_value = self::getshapes()[$mask];
		$request        = wp_remote_get( $returned_value );
		$response       = wp_remote_retrieve_body( $request );
		$sanitizer      = new Sanitizer();
		echo wp_kses( $sanitizer->sanitize( $response ), self::get_kses_extended_ruleset() );
	}
}
