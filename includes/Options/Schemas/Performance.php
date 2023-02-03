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
class Performance extends BaseSchema {
	/**
	 * @return array
	 */
	public static function get_schema() {
		$options = new Options( 'zionbuilder/schema/admin/performance' );

		$options->add_option(
			'local_google_fonts',
			[
				'type'        => 'custom_selector',
				'columns'     => 2,
				'default'     => false,
				'title'       => __( 'Load Google fonts locally', 'zionbuilder' ),
				'description' => __( 'If enabled, Google fonts used by the builder will be downloaded and served from your server.', 'zionbuilder' ),
				'options'     => [
					[
						'name' => __( 'Load Google fonts locally', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'Load Google fonts from Google servers', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'fonts_display',
			[
				'type'    => 'select',
				'default' => 'auto',
				'title'   => __( 'Font display', 'zionbuilder' ),
				'options' => [
					[
						'name' => 'auto',
						'id'   => 'auto',
					],
					[
						'name' => 'block',
						'id'   => 'block',
					],
					[
						'name' => 'swap',
						'id'   => 'swap',
					],
					[
						'name' => 'fallback',
						'id'   => 'fallback',
					],
					[
						'name' => 'optional',
						'id'   => 'optional',
					],
				],
			]
		);

		$options->add_option(
			'enable_video_lazy_load',
			[
				'type'        => 'custom_selector',
				'columns'     => 2,
				'default'     => false,
				'title'       => esc_html__( 'Enable lazy load for videos', 'zionbuilder' ),
				'description' => esc_html__( 'By lazy loading videos, they will be added in the page when they become visible.', 'zionbuilder' ),
				'options'     => [
					[
						'name' => esc_html__( 'Enable video lazy load', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => esc_html__( 'no', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'disable_normalize_css',
			[
				'type'        => 'custom_selector',
				'columns'     => 2,
				'default'     => false,
				'title'       => esc_html__( 'Disable Normalize.css', 'zionbuilder' ),
				'description' => esc_html__( 'This option allows you do unload normalize.css script that is loaded by the builder. If your theme already loads this script, it is recommended to disable it from here.', 'zionbuilder' ),
				'options'     => [
					[
						'name' => esc_html__( 'Disable Normalize.css', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => esc_html__( 'no', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'disable_jquery_migrate',
			[
				'type'    => 'custom_selector',
				'columns' => 2,
				'default' => false,
				'title'   => esc_html__( 'Disable jQuery migrate', 'zionbuilder' ),
				'options' => [
					[
						'name' => esc_html__( 'Disable jQuery migrate', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => esc_html__( 'no', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'disable_emojis',
			[
				'type'    => 'custom_selector',
				'columns' => 2,
				'default' => false,
				'title'   => esc_html__( 'Disable WordPress emojis', 'zionbuilder' ),
				'options' => [
					[
						'name' => __( 'Disable WordPress emojis', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'no', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'disable_area_wrappers',
			[
				'type'    => 'custom_selector',
				'default' => false,
				'title'   => __( 'Disable area wrappers', 'zionbuilder' ),
				'options' => [
					[
						'name' => __( 'Disable builder areas wrapper div', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'no', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		return $options->get_schema();
	}
}
