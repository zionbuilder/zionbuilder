<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;
use ZionBuilder\Options\Schemas\StyleOptions;
use ZionBuilder\Options\Schemas\Typography;
use ZionBuilder\Options\Schemas\Advanced;
use ZionBuilder\Options\Schemas\Video;
use ZionBuilder\Options\Schemas\BackgroundImage;
use ZionBuilder\Options\Schemas\Shadow;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Cache
 *
 * @package ZionBuilder
 */
class CommonJS {
	public function __construct() {
		// Actions
		add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'on_enqueue_scripts' ] );
	}

	public function on_enqueue_scripts() {
		self::register_scripts();
	}

	public static function register_scripts() {
		// Plugin::instance()->scripts->register_style(
		// 	'zb-common',
		// 	'css/common.css',
		// 	[],
		// 	Plugin::instance()->get_version()
		// );

		Plugin::instance()->scripts->register_script(
			'zb-vue',
			'js/vue.js',
			[],
			Plugin::instance()->get_version(),
			false
		);

		Plugin::instance()->scripts->register_script(
			'zb-utils',
			'js/utils.js',
			[],
			Plugin::instance()->get_version(),
			false
		);

		Plugin::instance()->scripts->register_script(
			'zb-i18n',
			'js/i18n.js',
			[],
			Plugin::instance()->get_version(),
			false
		);

		Plugin::instance()->scripts->register_script(
			'zb-rest',
			'js/rest.js',
			[],
			Plugin::instance()->get_version(),
			false
		);

		Plugin::instance()->scripts->register_script(
			'zb-hooks',
			'js/hooks.js',
			[],
			Plugin::instance()->get_version(),
			false
		);

		wp_localize_script(
			'zb-rest',
			'ZnRestConfig',
			[
				'nonce'     => Nonces::generate_nonce( Nonces::REST_API ),
				'rest_root' => esc_url_raw( rest_url() ),
				'schemas'   => apply_filters(
					'zionbuilder/commonjs/schemas',
					[
						'styles'           => StyleOptions::get_schema(),
						'element_advanced' => Advanced::get_schema(),
						'typography'       => Typography::get_schema(),
						'video'            => Video::get_schema(),
						'background_image' => BackgroundImage::get_schema(),
						'shadow'           => Shadow::get_schema(),
					]
				),
			]
		);

		Plugin::instance()->scripts->register_script(
			'zb-components',
			'js/components.js',
			[
				'zb-vue',
				'zb-hooks',
				'zb-utils',
				'zb-i18n',
				'zb-rest',
			],
			Plugin::instance()->get_version(),
			true
		);

		// Register common script
		// Plugin::instance()->scripts->register_script(
		// 	'zb-common',
		// 	'js/common.js',
		// 	[
		// 		'zb-vue',
		// 	],
		// 	Plugin::instance()->get_version(),
		// 	true
		// );
	}
}
