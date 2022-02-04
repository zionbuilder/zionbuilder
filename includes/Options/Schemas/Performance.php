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
				'title'       => __( 'Load Google fonts locally' ),
				'description' => __( 'If enabled, Google fonts used by the builder will be downloaded and served from your server.' ),
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
				'title'   => __( 'Font display' ),
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
			'disable_jquery_migrate',
			[
				'type'    => 'custom_selector',
				'columns' => 2,
				'default' => false,
				'title'   => __( 'Disable jQuery migrate' ),
				'options' => [
					[
						'name' => __( 'Disable jQuery migrate', 'zionbuilder' ),
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
			'disable_emojis',
			[
				'type'    => 'custom_selector',
				'columns' => 2,
				'default' => false,
				'title'   => __( 'Disable WordPress emojis' ),
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

		return $options->get_schema();
	}
}
