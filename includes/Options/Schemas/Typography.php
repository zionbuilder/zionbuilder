<?php

namespace ZionBuilder\Options\Schemas;

use ZionBuilder\Options\Options;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Typography
 *
 * @package ZionBuilder\Options\Schemas
 */
class Typography extends BaseSchema {
	/**
	 * @return array
	 */
	public static function get_schema() {
		$typography_options = new Options( 'zionbuilder/schema/typography_options' );

		$typography_options->add_option(
			'text-align',
			[
				'type'        => 'text_align',
				'width'       => 80,
				'title'       => esc_html__( 'Align', 'zionbuilder' ),
				'description' => esc_html__( 'Select the desired text align.', 'zionbuilder' ),
			]
		);

		$typography_options->add_option(
			'white-space',
			[
				'type'      => 'custom_selector',
				'width'     => 20,
				'css_class' => 'znpb-input-wrapper--valign-end',
				'options'   => [
					[
						'icon' => 'line-break',
						'id'   => 'nowrap',
					],
				],
			]
		);

		$typography_options->add_option(
			'font-family',
			[
				'title'       => esc_html__( 'Font Family', 'zionbuilder' ),
				'type'        => 'select',
				'data_source' => 'fonts',
				'width'       => 50,
				'style_type'  => 'font-select',
			]
		);

		$typography_options->add_option(
			'font-weight',
			[
				'title'       => esc_html__( 'Font Weight', 'zionbuilder' ),
				'description' => esc_html__( 'Font weight allows you to set the text thickness.', 'zionbuilder' ),
				'type'        => 'select',
				'default'     => '400',
				'width'       => 50,
				'options'     => [
					[
						'id'   => '100',
						'name' => '100',
					],
					[
						'id'   => '200',
						'name' => '200',
					],
					[
						'id'   => '300',
						'name' => '300',
					],
					[
						'id'   => '400',
						'name' => '400',
					],
					[
						'id'   => '500',
						'name' => '500',
					],
					[
						'id'   => '600',
						'name' => '600',
					],
					[
						'id'   => '700',
						'name' => '700',
					],
					[
						'id'   => '800',
						'name' => '800',
					],
					[
						'id'   => '900',
						'name' => '900',
					],
					[
						'id'   => 'bolder',
						'name' => esc_html__( 'Bolder', 'zionbuilder' ),
					],
					[
						'id'   => 'lighter',
						'name' => esc_html__( 'Lighter', 'zionbuilder' ),
					],
					[
						'id'   => 'inherit',
						'name' => esc_html__( 'Inherit', 'zionbuilder' ),
					],
					[
						'id'   => 'initial',
						'name' => esc_html__( 'Initial', 'zionbuilder' ),
					],
					[
						'id'   => 'unset',
						'name' => esc_html__( 'Unset', 'zionbuilder' ),
					],
				],
			]
		);

		$typography_options->add_option(
			'color',
			[
				'title' => esc_html__( 'Font Color', 'zionbuilder' ),
				'type'  => 'colorpicker',
				'width' => 50,
			]
		);

		$typography_options->add_option(
			'font-size',
			[
				'title'       => esc_html__( 'Font size', 'zionbuilder' ),
				'description' => esc_html__( 'The font size option sets the size of the font in various units', 'zionbuilder' ),
				'type'        => 'number_unit',
				'width'       => 50,
				'min'         => 0,
				'units'       => self::get_units(),
			]
		);
		$typography_options->add_option(
			'line-height',
			[
				'type'        => 'number_unit',
				'title'       => esc_html__( 'Line height', 'zionbuilder' ),
				'description' => esc_html__( 'Line height sets the distance between lines of text.', 'zionbuilder' ),
				'width'       => 50,
				'min'         => 0,
				'units'       => self::get_units(),
			]
		);

		$typography_options->add_option(
			'letter-spacing',
			[
				'type'        => 'number_unit',
				'title'       => esc_html__( 'Letter Spacing', 'zionbuilder' ),
				'description' => esc_html__( 'Letter spacings sets the width between letters.', 'zionbuilder' ),
				'width'       => 50,
				'units'       => self::get_units(),
			]
		);

		$typography_options->add_option(
			'text-decoration',
			[
				'type'          => 'checkbox_group',
				'direction'     => 'horizontal',
				'display-style' => 'buttons',
				'width'         => 50,
				'columns'       => 3,
				'options'       => [
					[
						'icon' => 'italic',
						'id'   => 'italic',
					],
					[
						'icon' => 'underline',
						'id'   => 'underline',
					],
					[
						'icon' => 'strikethrough',
						'id'   => 'line-through',
					],
				],
			]
		);

		$typography_options->add_option(
			'text-transform',
			[
				'type'    => 'custom_selector',
				'columns' => 3,
				'width'   => 50,
				'options' => [
					[
						'id'   => 'uppercase',
						'icon' => 'uppercase',
						'name' => esc_html__( 'uppercase', 'zionbuilder' ),
					],
					[
						'id'   => 'lowercase',
						'icon' => 'lowercase',
						'name' => esc_html__( 'lowercase', 'zionbuilder' ),
					],
					[
						'id'   => 'capitalize',
						'icon' => 'capitalize',
						'name' => esc_html__( 'capitalize', 'zionbuilder' ),
					],
				],
			]
		);

		return $typography_options->get_schema();
	}
}
