<?php

namespace ZionBuilder\Options;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Typography
 *
 * @package ZionBuilder\Options\Schemas
 */
class BaseSchema {
	/**
	 * @param array $before
	 * @param array $after
	 *
	 * @return array
	 */
	public static function get_units( $before = [], $after = [] ) {
		return array_merge(
			$before,
			[
				'px',
				'%',
				'vh',
				'vw',
				'rem',
				'em',
				'pt',
			],
			$after
		);
	}
}
