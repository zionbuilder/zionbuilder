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
use ZionBuilder\Utils;

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
	public static $enqueued_responsive_devices = false;

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
			'components',
			[
				'wp-codemirror',
				'media-views',
			],
			Plugin::instance()->get_version()
		);

		wp_add_inline_style( 'zb-components', Plugin::instance()->icons->get_icons_css() );

		Plugin::instance()->scripts->register_script(
			'zb-vue',
			'vue',
			[],
			Plugin::instance()->get_version(),
			false
		);

		Plugin::instance()->scripts->register_script(
			'zb-hooks',
			'hooks',
			[],
			Plugin::instance()->get_version(),
			false
		);

		Plugin::instance()->scripts->register_script(
			'zb-rest',
			'rest',
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
			'zb-utils',
			'utils',
			[
				'zb-rest',
				'zb-hooks',
			],
			Plugin::instance()->get_version(),
			false
		);

		Plugin::instance()->scripts->register_script(
			'zb-i18n',
			'i18n',
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
			'zb-components',
			'components',
			[
				'wp-codemirror',
				'zb-vue',
				'zb-i18n',
				'zb-hooks',
				'zb-rest',
				'zb-utils',
			],
			Plugin::instance()->get_version(),
			true
		);

		wp_localize_script(
			'zb-components',
			'ZnPbComponentsData',
			[
				'schemas'       => apply_filters(
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
				'breakpoints'   => Responsive::get_breakpoints(),
				'is_pro_active' => Utils::is_pro_active(),
			]
		);

	}

	public static function enqueue_responsive_devices( $handle ) {
		if ( self::$enqueued_responsive_devices ) {
			return;
		}

		wp_localize_script( $handle, 'zbFrontendResponsiveDevicesMobileFirst', Responsive::get_breakpoints_mobile_first() );

		self::$enqueued_responsive_devices = true;
	}
}
