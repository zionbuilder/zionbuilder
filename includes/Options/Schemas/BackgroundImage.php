<?php

namespace ZionBuilder\Options\Schemas;

use ZionBuilder\Options\Options;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class StyleOptions
 *
 * @package ZionBuilder\Editor
 */
class BackgroundImage extends BaseSchema {
	/**
	 * @return array
	 */
	public static function get_schema() {
		$options = new Options( 'zionbuilder/schema/background_image' );

		$options->add_option(
			'background-repeat',
			[
				'title'      => esc_html__( 'Background repeat', 'zionbuilder' ),
				'filterable' => true,
				'type'       => 'select',
				'default'    => 'repeat',
				'options'    => [
					[
						'id'   => 'repeat',
						'name' => 'repeat',
					],
					[
						'id'   => 'repeat-x',
						'name' => 'repeat-x',
					],
					[
						'id'   => 'repeat-y',
						'name' => 'repeat-y',
					],
					[
						'id'   => 'no-repeat',
						'name' => 'no-repeat',
					],
					[
						'id'   => 'space',
						'name' => 'space',
					],
					[
						'id'   => 'round',
						'name' => 'round',
					],
					[
						'id'   => 'initial',
						'name' => 'initial',
					],
					[
						'id'   => 'inherit',
						'name' => 'inherit',
					],
				],
			]
		);

		$options->add_option(
			'background-attachment',
			[
				'title'      => esc_html__( 'Background attachment', 'zionbuilder' ),
				'filterable' => true,
				'type'       => 'select',
				'default'    => 'initial',
				'options'    => [
					[
						'id'   => 'scroll',
						'name' => 'scroll',
					],
					[
						'id'   => 'fixed',
						'name' => 'fixed',
					],
					[
						'id'   => 'local',
						'name' => 'local',
					],
					[
						'id'   => 'initial',
						'name' => 'initial',
					],
					[
						'id'   => 'inherit',
						'name' => 'inherit',
					],
				],
			]
		);

		$options->add_option(
			'background-position-x',
			[
				'title' => esc_html__( 'Horizontal position', 'zionbuilder' ),
				'type'  => 'number_unit',
				'width' => 50,
			]
		);

		$options->add_option(
			'background-position-y',
			[
				'title' => esc_html__( 'Vertical position', 'zionbuilder' ),
				'type'  => 'number_unit',
				'width' => 50,
			]
		);

		$options->add_option(
			'background-size',
			[
				'title'   => esc_html__( 'Background size', 'zionbuilder' ),
				'type'    => 'select',
				'default' => 'initial',
				'options' => [
					[
						'id'   => 'auto',
						'name' => 'auto',
					],
					[
						'id'   => 'cover',
						'name' => 'cover',
					],
					[
						'id'   => 'contain',
						'name' => 'contain',
					],
					[
						'id'   => 'initial',
						'name' => 'initial',
					],
					[
						'id'   => 'inherit',
						'name' => 'inherit',
					],
					[
						'id'   => 'custom',
						'name' => 'custom',
					],
				],
			]
		);

		$bg_size_units_group = $options->add_option(
			'background-size-units',
			[
				'title'      => esc_html__( 'Background Size Units', 'zionbuilder' ),
				'type'       => 'group',
				'dependency' => [
					[
						'option' => 'background-size',
						'value'  => [ 'custom' ],
					],
				],
			]
		);

		$bg_size_units_group->add_option(
			'x',
			[
				'label'       => esc_html__( 'Width', 'zionbuilder' ),
				'label-align' => 'center',
				'type'        => 'number_unit',
			]
		);

		$bg_size_units_group->add_option(
			'y',
			[
				'label'        => esc_html__( 'Height', 'zionbuilder' ),
				'type'         => 'number_unit',
				'label-align'  => 'center',
				'default_unit' => '%',
			]
		);

		$options->add_option(
			'background-clip',
			[
				'title'   => esc_html__( 'Background Clip', 'zionbuilder' ),
				'type'    => 'select',
				'default' => 'content-box',
				'options' => [
					[
						'id'   => 'border-box',
						'name' => 'border-box',
					],
					[
						'id'   => 'padding-box',
						'name' => 'padding-box',
					],
					[
						'id'   => 'content-box',
						'name' => 'content-box',
					],
					// Text is not properly supported in browsers
					// [
					//  'id'   => 'text',
					//  'name' => 'text',
					// ],
				],
			]
		);

		$options->add_option(
			'background-blend-mode',
			[
				'title'   => esc_html__( 'Background blend mode', 'zionbuilder' ),
				'type'    => 'select',
				'default' => 'normal',
				'options' => [
					[
						'id'   => 'normal',
						'name' => 'normal',
					],
					[
						'id'   => 'multiply',
						'name' => 'multiply',
					],
					[
						'id'   => 'screen',
						'name' => 'screen',
					],
					[
						'id'   => 'overlay',
						'name' => 'overlay',
					],
					[
						'id'   => 'darken',
						'name' => 'darken',
					],
					[
						'id'   => 'lighten',
						'name' => 'lighten',
					],
					[
						'id'   => 'color-dodge',
						'name' => 'color-dodge',
					],
					[
						'id'   => 'saturation',
						'name' => 'saturation',
					],
					[
						'id'   => 'color',
						'name' => 'color',
					],
					[
						'id'   => 'luminosity',
						'name' => 'luminosity',
					],
				],
			]
		);

		return $options->get_schema();
	}
}
