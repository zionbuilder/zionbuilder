<?php

namespace ZionBuilder;

use ZionBuilder\Environment;
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
 * Class Settings
 *
 * @package ZionBuilder
 */
class Scripts {

	/**
	 * Keeps a reference to the project assets root URL
	 *
	 * This folder is automatically created during build process
	 *
	 * @var string The asset root folder URL for this project
	 */
	private $assets_root_url = null;

	/**
	 * Keeps a reference to the project assets root
	 *
	 * This folder is automatically created during build process
	 *
	 * @var string The asset root folder for this project
	 */
	private $assets_root_path = null;

	/**
	 * Holds the value of the debug flag
	 *
	 * @var boolean
	 */
	private $is_debug = false;

	public function __construct() {
		$this->setup_environment();

		$this->is_debug = Environment::is_debug();

		if ( $this->is_debug ) {
			add_filter( 'script_loader_src', [ $this, 'remove_script_version' ], 15, 1 );
			add_filter( 'style_loader_src', [ $this, 'remove_script_version' ], 15, 1 );
			add_filter( 'script_loader_tag', [ $this, 'add_module_attribute' ], 10, 3 );
		}
	}

	public function remove_script_version( $src ) {
		$parts = explode( '?ver', $src );
		return $parts[0];
	}

	public function add_module_attribute( $tag, $handle, $src ) {
		// if not your script, do nothing and return original $tag
		if ( $this->is_debug ) {

			$scripts_map = Environment::get_value( 'devScripts', [] );
			if ( in_array( $src, $scripts_map, true ) ) {
				// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
				$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
			}
		}

		// change the script tag by adding type="module" and return it.
		return $tag;
	}

	/**
	 * Sets the root path and url for the assets
	 *
	 * @return void
	 */
	public function setup_environment() {
		// Get the project root
		$output_directory       = Environment::get_value( 'outputDir' );
		$this->assets_root_url  = trailingslashit( Utils::get_file_url( $output_directory ) );
		$this->assets_root_path = trailingslashit( Utils::get_file_path( $output_directory ) );
	}

	public static function enqueue_common() {
		wp_enqueue_media();
		wp_print_styles( 'media-views' );
		self::enqueue_common_css();
		self::enqueue_common_js();
	}

	public static function enqueue_common_css() {
		// Load google font used in builder
		wp_enqueue_style(
			'znpb-roboto-font',
			'https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i&display=swap&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese',
			[],
			Plugin::instance()->get_version()
		);

		Plugin::instance()->scripts->enqueue_style(
			'zb-common',
			'common',
			[
				'wp-codemirror',
			],
			Plugin::instance()->get_version()
		);

		// Load icon fonts
		wp_add_inline_style( 'zb-common', Plugin::instance()->icons->get_icons_css() );
	}

	public static function enqueue_common_js() {
		Plugin::instance()->scripts->enqueue_script(
			'zb-vue',
			'vue',
			[],
			Plugin::instance()->get_version(),
			true
		);

		Plugin::instance()->scripts->register_script(
			'zb-vue-router',
			'vue-router',
			[],
			Plugin::instance()->get_version(),
			true
		);

		Plugin::instance()->scripts->enqueue_script(
			'zb-pinia',
			'pinia',
			[],
			Plugin::instance()->get_version(),
			true
		);

		Plugin::instance()->scripts->enqueue_script(
			'zb-common',
			'common',
			[
				'wp-codemirror',
				'csslint',
				'htmlhint',
				'jshint',
				'jsonlint',
				'zb-vue',
				'zb-pinia',
			],
			Plugin::$instance->get_version(),
			true
		);

		wp_set_script_translations( 'zb-common', 'zionbuilder' );

		// Get info for zion builder plugins
		$plugin_updates     = get_site_transient( 'update_plugins' );
		$free_plugin_update = null;
		$pro_plugin_update  = null;
		if ( isset( $plugin_updates->response ) && is_array( $plugin_updates->response ) ) {
			if ( isset( $plugin_updates->response['zionbuilder/zionbuilder.php'] ) ) {
				$free_plugin_update = $plugin_updates->response['zionbuilder/zionbuilder.php'];
			}
		}

		if ( isset( $plugin_updates->response ) && is_array( $plugin_updates->response ) ) {
			if ( isset( $plugin_updates->response['zionbuilder-pro/zionbuilder-pro.php'] ) ) {
				$pro_plugin_update = $plugin_updates->response['zionbuilder-pro/zionbuilder-pro.php'];
			}
		}

		wp_localize_script(
			'zb-common',
			'ZBCommonData',
			apply_filters(
				'zionbuilder/js/common/initial_data',
				[
					'rest'        => [
						'nonce'     => Nonces::generate_nonce( Nonces::REST_API ),
						'rest_root' => esc_url_raw( rest_url() ),
					],
					'environment' => [
						'plugin_name' => Whitelabel::get_title(),
						'urls'        => [
							'zion_dashboard'    => admin_url( sprintf( 'admin.php?page=%s', Whitelabel::get_id() ) ),
							'logo'              => Whitelabel::get_logo_url(),
							'pro_logo'          => Utils::get_pro_png_url(),
							'plugin_root'       => Utils::get_file_url(),
							'updates_page'      => admin_url( 'update-core.php' ),
							'purchase_url'      => 'https://zionbuilder.io/pricing/',
							'documentation_url' => 'https://zionbuilder.io/help-center/',
							'free_changelog'    => 'https://zionbuilder.io/changelog-free-version/',
							'pro_changelog'     => 'https://zionbuilder.io/changelog-pro-version/',
						],
						'plugin_free' => [
							'is_active'      => true,
							'version'        => Plugin::instance()->get_version(),
							'update_version' => $free_plugin_update ? $free_plugin_update->new_version : null,
						],
						'plugin_pro'  => [
							'is_active'      => Utils::is_pro_active(),
							'is_installed'   => Utils::is_pro_installed(),
							'version'        => class_exists( 'ZionBuilderPro\Plugin' ) ? \ZionBuilderPro\Plugin::instance()->get_version() : null,
							'update_version' => $pro_plugin_update ? $pro_plugin_update->new_version : null,
						],
					],
					'library'     => [
						'sources' => Plugin::$instance->library->get_sources(),
						'types'   => Plugin::$instance->templates->get_template_types(),
					],
					'breakpoints' => Responsive::get_breakpoints(),
					'schemas'     => apply_filters(
						'zionbuilder/js/common/schemas',
						[
							'styles'           => StyleOptions::get_schema(),
							'element_advanced' => Advanced::get_schema(),
							'typography'       => Typography::get_schema(),
							'video'            => Video::get_schema(),
							'background_image' => BackgroundImage::get_schema(),
							'shadow'           => Shadow::get_schema(),
						]
					),
					'wp_editor'   => self::get_wp_editor(),
				]
			)
		);
	}

	/**
	 * Returns the HTML content for the WP editor
	 *
	 * @return string|false The editor HTML content or false on failure
	 */
	public static function get_wp_editor() {
		// Remove all TinyMCE plugins.
		remove_all_filters( 'mce_buttons', 10 );
		remove_all_filters( 'mce_external_plugins', 10 );

		ob_start();
		wp_editor(
			'%%ZNPB_EDITOR_CONTENT%%',
			'znpbwpeditorid',
			[
				'editor_height' => '150',
				'wpautop'       => false,
				'tinymce'       => [
					'forced_root_block' => '',
				],
			]
		);
		$wp_editor = ob_get_clean();

		return $wp_editor;
	}

	/**
	 * Register a script.
	 *
	 * Registers the script if $src provided (does NOT overwrite).
	 *
	 * @since 1.0.0
	 *
	 * @param string           $handle    Name of the script. Should be unique.
	 * @param string           $src       relative URL of the script, or path of the script relative to the Project root directory.
	 *                                    Default empty.
	 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param string|bool|null $ver       Optional. String specifying script version number, if it has one, which is added to the URL
	 *                                    as a query string for cache busting purposes. If version is set to false, a version
	 *                                    number is automatically added equal to current installed WordPress version.
	 *                                    If set to null, no version is added.
	 * @param bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
	 *                                    Default 'false'.
	 *
	 * @return void
	 */
	public function register_script( $handle, $src = '', $deps = [], $ver = false, $in_footer = false ) {
		wp_register_script( $handle, $this->get_script_url( $src, 'js' ), $deps, $ver, $in_footer );
	}

	/**
	 * Enqueue a script.
	 *
	 * Registers the script if $src provided (does NOT overwrite), and enqueues it.
	 *
	 * @since 1.0.0
	 *
	 * @param string           $handle    Name of the script. Should be unique.
	 * @param string           $src       relative URL of the script, or path of the script relative to the Project root directory.
	 *                                    Default empty.
	 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param string|bool|null $ver       Optional. String specifying script version number, if it has one, which is added to the URL
	 *                                    as a query string for cache busting purposes. If version is set to false, a version
	 *                                    number is automatically added equal to current installed WordPress version.
	 *                                    If set to null, no version is added.
	 * @param bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
	 *                                    Default 'false'.
	 *
	 * @return void
	 */
	public function enqueue_script( $handle, $src = '', $deps = [], $ver = false, $in_footer = false ) {
		wp_enqueue_script( $handle, $this->get_script_url( $src, 'js' ), $deps, $ver, $in_footer );
	}

	/**
	 * Register a style.
	 *
	 * Registers the style if $src provided (does NOT overwrite).
	 *
	 * @since 1.0.2
	 *
	 * @param string           $handle Name of the style. Should be unique.
	 * @param string           $src    relative URL of the style, or path of the style relative to the Project root directory.
	 *                                 Default empty.
	 * @param string[]         $deps   Optional. An array of registered style handles this style depends on. Default empty array.
	 * @param string|bool|null $ver    Optional. String specifying style version number, if it has one, which is added to the URL
	 *                                 as a query string for cache busting purposes. If version is set to false, a version
	 *                                 number is automatically added equal to current installed WordPress version.
	 *                                 If set to null, no version is added.
	 * @param string           $media  (Optional) The media for which this stylesheet has been defined. Accepts media types like
	 *                                 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and
	 *                                 '(max-width: 640px)'.
	 *
	 * @return void
	 */
	public function register_style( $handle, $src = '', $deps = [], $ver = false, $media = 'all' ) {
		wp_register_style( $handle, $this->get_script_url( $src, 'css' ), $deps, $ver, $media );
	}


	/**
	 * Enqueue a style.
	 *
	 * Registers the style if $src provided (does NOT overwrite), and enqueues it.
	 *
	 * @since 1.0.0
	 *
	 * @param string           $handle Name of the script. Should be unique.
	 * @param string           $src    relative URL of the script, or path of the script relative to the Project root directory.
	 *                                 Default empty.
	 * @param string[]         $deps   Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param string|bool|null $ver    Optional. String specifying script version number, if it has one, which is added to the URL
	 *                                 as a query string for cache busting purposes. If version is set to false, a version
	 *                                 number is automatically added equal to current installed WordPress version.
	 *                                 If set to null, no version is added.
	 * @param string           $media  Optional. The media for which this stylesheet has been defined.
	 *                                 Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
	 *                                 '(orientation: portrait)' and '(max-width: 640px)'.
	 *
	 *
	 * @return void
	 */
	public function enqueue_style( $handle, $src = '', $deps = [], $ver = false, $media = 'all' ) {
		wp_enqueue_style( $handle, $this->get_script_url( $src, 'css' ), $deps, $ver, $media );
	}


	/**
	 * Returns the script url to be used in enqueue functions
	 *
	 * If the project is in development mode, it will use webpack dev server host
	 * instead of the dist folder public path
	 *
	 * @param string $path The script relative path
	 *
	 * @return string
	 */
	public function get_script_url( $path, $extension ) {
		if ( $this->is_debug && $extension === 'js' ) {
			$scripts_map = Environment::get_value( 'devScripts', [] );

			if ( isset( $scripts_map[$path] ) ) {
				return $scripts_map[$path];
			}
		}

		return $this->assets_root_url . $path . '.' . $extension;
	}

	/**
	 * Returns the script path
	 *
	 * @param string $path The script relative path
	 *
	 * @return string
	 */
	public function get_script_path( $path ) {
		return $this->assets_root_path . $path;
	}
}
