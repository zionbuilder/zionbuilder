<?php

namespace ZionBuilder\Options\Schemas;

use ZionBuilder\Options\Options;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Admin Library
 *
 * @package ZionBuilder\Options\Schemas
 */
class AdminLibrary extends BaseSchema {
	/**
	 * @return array
	 */
	public static function get_schema() {
		$options = new Options( 'zionbuilder/schema/admin_library' );

		$library_sources = $options->add_option(
			'library_sources',
			[
				'type'               => 'repeater',
				'title'              => __( 'Library sources', 'zionbuilder' ),
				'add_button_text'    => __( 'Add external libraries.', 'zionbuilder' ),
				'item_title'         => 'name',
				'default_item_title' => 'Source %s',
			]
		);

		$library_sources->add_option(
			'name',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Remote library name', 'zionbuilder' ),
				'description' => esc_html__( 'Set a name that will appear in the templates library for this remote source.', 'zionbuilder' ),
			]
		);

		$library_sources->add_option(
			'url',
			[
				'type'  => 'text',
				'title' => esc_html__( 'Remote library url', 'zionbuilder' ),
			]
		);

		$library_sources->add_option(
			'password',
			[
				'type'  => 'password',
				'title' => esc_html__( 'Remote library password', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'enable_library_share',
			[
				'type'        => 'custom_selector',
				'columns'     => 2,
				'default'     => false,
				'title'       => __( 'Enable library external access' ),
				'description' => __( 'If enabled, the templates saved in this website will be accessible on a different website' ),
				'options'     => [
					[
						'name' => __( 'Enable template library sharing', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'Disable template library sharing', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'password',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Library access password', 'zionbuilder' ),
				'description' => esc_html__( 'Set a password in order to access the template library', 'zionbuilder' ),
				'dependency'  => [
					[
						'option' => 'enable_library_share',
						'value'  => [ true ],
					],
				],
			],
		);

		return $options->get_schema();
	}
}
