<?php

namespace ZionBuilder\Elements;

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
	public function getshapes() {
		$shapes = [
			'shape-oblique'        => [
				'viewbox' => '0 0 1440 180',
				'paths'   => [ 'M0 0l1440 180H0V0z' ],
			],
			'shape-double'         => [
				'viewbox' => '0 0 1440 180',
				'paths'   => [
					'M0 0l1440 180H0V0z',
					'M0 50l1440 130H0V50z',
				],
			],
			'shape-oblique-mirror' => [
				'viewbox' => '0 0 1440 180',
				'paths'   => [ 'M1440 0v181H0V0l720 179L1440 0z' ],
			],
			'shape-curved-mirror'  => [
				'viewbox' => '0 0 1440 180',
				'paths'   => [ 'M720 179c260.2 0 505.27-64.75 720-179v180H0V0c214.73 114.25 459.8 179 720 179z' ],
			],
			'shape-split'          => [
				'viewbox' => '0 0 1440 50',
				'paths'   => [ 'M770 50H670l50-50 50 50z' ],
			],
			'shape-wavy'           => [
				'viewbox' => '0 0 1440 50',
				'paths'   => [ 'M1260 10.15c-90-10.43-90-10.43-180-2.4s-90 8.03-180 0-90-8.03-180 2.4-90 10.43-180 4.42-90-6.01-180 5.74-90 11.75-180 6.16c-90-5.6-90-5.6-180 5.6V52h1440V10.16c-90 10.42-90 10.42-180-.01z' ],
			],
		];
		return $shapes;
	}
	/*
	 * Returns string from shape id
	 *
	 * @param string $shape The shape id for which the attributes will be retrieved
	 * @param mixed  $mask
	 */
	public static function get_mask_paths( $mask = '' ) {
		// bail if we do not have any attributes

		if ( empty( $mask ) ) {
			return;
		}

		$returned_value = self::getshapes()[$mask]['paths'];
		return $returned_value;
	}

	/*
	 * Returns string from shape id
	 *
	 * @param string $shape The shape id for which the attributes will be retrieved
	 * @param mixed  $mask
	 */
	public static function get_viewbox( $mask = '' ) {
		// bail if we do not have any attributes

		if ( empty( $mask ) ) {
			return;
		}

		$shapes        = self::getshapes();
		$shape_viewbox = $shapes[$mask];

		return $shape_viewbox['viewbox'];
	}
}
