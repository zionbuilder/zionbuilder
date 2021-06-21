<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;
use ZionBuilder\Options\Schemas\StyleOptions;
use ZionBuilder\Options\Schemas\Typography;
use ZionBuilder\Options\Schemas\Advanced;
use ZionBuilder\Options\Schemas\Video;
use ZionBuilder\Options\Schemas\BackgroundImage;
use ZionBuilder\Options\Schemas\Shadow;
use ZionBuilder\Localization;

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
		add_action( 'zionbuilder/editor/before_scripts', [ $this, 'on_enqueue_scripts' ], 9 );
		add_action( 'zionbuilder/preview/before_load_scripts', [ $this, 'on_enqueue_scripts' ], 9 );
		add_action( 'zionbuilder/admin/before_admin_scripts', [ $this, 'on_enqueue_scripts' ], 9 );
	}

	public function on_enqueue_scripts() {
		self::register_scripts();
	}

	public static function register_scripts() {
		Plugin::instance()->scripts->register_style(
			'zb-components',
			'css/components.css',
			[
				'media-views',
			],
			Plugin::instance()->get_version()
		);

		wp_add_inline_style( 'zb-components', Plugin::instance()->icons->get_icons_css() );

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

		wp_localize_script(
			'zb-i18n',
			'ZnI18NStrings',
			Localization::get_strings()
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
			]
		);

		Plugin::instance()->scripts->register_script(
			'zb-components',
			'js/components.js',
			[
				'zb-vue',
				'zb-hooks',
				'zb-rest',
				'zb-utils',
				'zb-i18n',
			],
			Plugin::instance()->get_version(),
			true
		);

		wp_localize_script(
			'zb-components',
			'ZnPbComponentsData',
			[
				'schemas' => apply_filters(
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

	}
}
