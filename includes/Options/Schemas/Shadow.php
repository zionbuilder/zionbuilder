<?php

namespace ZionBuilder\Options\Schemas;

use ZionBuilder\Options\Options;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Shadow
 *
 * @package ZionBuilder\Options\Schemas
 */
class Shadow extends BaseSchema {
	/**
	 * @return array
	 */
	public static function get_schema() {
		$shadow_options = new Options( 'zionbuilder/schema/shadow' );

		$shadow_options->add_option(
			'offset-x',
			[
				'type'           => 'number_unit',
				'layout'         => 'inline',
				'width'          => 50,
				'label-position' => 'left',
				'label-icon'     => 'horizontal',
				'label-title'    => esc_html__( 'Horizontal distance', 'zionbuilder' ),
				'min'            => -999,
				'max'            => 999,
				'units'          => self::get_units(),
			]
		);

		$shadow_options->add_option(
			'offset-y',
			[
				'type'           => 'number_unit',
				'layout'         => 'inline',
				'width'          => 50,
				'label-position' => 'right',
				'label-icon'     => 'vertical',
				'label-title'    => esc_html__( 'Vertical distance', 'zionbuilder' ),
				'min'            => -999,
				'max'            => 999,
				'units'          => self::get_units(),
			]
		);

		$shadow_options->add_option(
			'blur',
			[
				'type'           => 'number_unit',
				'layout'         => 'inline',
				'width'          => 50,
				'label-position' => 'left',
				'label-icon'     => 'blur',
				'label-title'    => esc_html__( 'Blur', 'zionbuilder' ),
				'min'            => 0,
				'max'            => 999,
				'units'          => self::get_units(),
			]
		);

		$shadow_options->add_option(
			'spread',
			[
				'type'           => 'number_unit',
				'layout'         => 'inline',
				'width'          => 50,
				'label-position' => 'right',
				'label-icon'     => 'spread',
				'label-title'    => esc_html__( 'Spread', 'zionbuilder' ),
				'min'            => -999,
				'max'            => 999,
				'units'          => self::get_units(),
			]
		);

		$shadow_options->add_option(
			'color',
			[
				'type'      => 'colorpicker',
				'width'     => 50,
				'title'     => __( 'Shadow color', 'zionbuilder' ),
				'min'       => -999,
				'max'       => 999,
				'units'     => self::get_units(),
				'css_class' => 'znpb-shadow-option--ptop znpb-shadow-option__colorpicker',
			]
		);

		$shadow_options->add_option(
			'inset',
			[
				'type'      => 'checkbox_switch',
				'width'     => 50,
				'title'     => __( 'Inset?', 'zionbuilder' ),
				'css_class' => 'znpb-shadow-option--ptop',
			]
		);

		return $shadow_options->get_schema();
	}
}
