<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;
use ZionBuilder\Elements\Style;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Cache
 *
 * @since 3.4.0
 *
 * @package ZionBuilder
 */
class Assets {
	/**
	 * Flag to show the regenerate cache to admins
	 */
	const REGENERATE_CACHE_FLAG = 'zionbuilder_regenerate_assets';
	/**
	 * Holds the name of the directory to use by default for assets config
	 */
	const CACHE_FOLDER_NAME = 'cache';

	/**
	 * Holds the name of the dynamic cache file name
	 */
	const DYNAMIC_CSS_FILENAME = 'dynamic_css.css';

	/**
	 * Holds a reference to the cache folder
	 *
	 * @var array{path: string, url: string}
	 */
	private static $cache_directory = null;

	/**
	 * Holds a reference to the element scripts that are already loaded
	 *
	 * @var array
	 */
	private static $loaded_element_assets = [];

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ] );

		// Cache file creation and deletion
		add_action( 'save_post', array( $this, 'generate_post_assets' ) );
		add_action( 'delete_post', array( $this, 'delete_post_assets' ) );
		add_action( 'zionbuilder/settings/after_save', array( $this, 'compile_global_css' ) );

		if ( get_option( self::REGENERATE_CACHE_FLAG, false ) ) {
			add_action( 'admin_notices', [ $this, 'show_regeneration_message' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_notice_scripts' ] );
		}

		// Generate and set the cache directory
		$relative_cache_path       = trailingslashit( self::CACHE_FOLDER_NAME );
		$zionbuilder_upload_folder = FileSystem::get_zionbuilder_upload_dir();

		self::$cache_directory = array(
			'path' => $zionbuilder_upload_folder['basedir'] . $relative_cache_path,
			'url'  => esc_url( set_url_scheme( $zionbuilder_upload_folder['baseurl'] . $relative_cache_path ) ),
		);

		// Create the cache folder
		wp_mkdir_p( self::$cache_directory['path'] );
	}

	public function show_regeneration_message() {
		echo '<div id="znpb-regenerateAssetsNotice"></div>';
	}

	public function register_admin_notice_scripts() {
		Plugin::instance()->scripts->enqueue_script(
			'zb-vue',
			'vue',
			[],
			Plugin::instance()->get_version(),
			true
		);

		wp_enqueue_style( 'znpb-assets-notice', Plugin::instance()->scripts->get_script_url( 'regenerate-assets-notice', 'css' ), [], Plugin::instance()->get_version() );

		wp_enqueue_script( 'znpb-assets-notice', Plugin::instance()->scripts->get_script_url( 'regenerate-assets-notice', 'js' ), [ 'zb-vue' ], Plugin::instance()->get_version(), true );
		wp_localize_script(
			'znpb-assets-notice',
			'ZnRestConfig',
			[
				'nonce'     => Nonces::generate_nonce( Nonces::REST_API ),
				'rest_root' => esc_url_raw( rest_url() ),
			]
		);

		CommonJS::localize_common_js_data( 'znpb-assets-notice' );
	}

	public function on_enqueue_scripts() {
		// #1 register scripts
		$this->register_defaults_scripts();

		// #2 Load global dynamic css file
		$this->load_global_css();

		// #3 Load page elements scripts and styles
		$this->load_page_content_scripts();

		// #4 Load specific pages css files
		$this->load_page_css();
	}

	public function load_page_css() {
		$registered_posts     = Plugin::$instance->renderer->get_registered_areas();
		$registered_areas_ids = array_keys( $registered_posts );

		// If we have registered areas, just generate the assets based on the areas
		if ( count( $registered_areas_ids ) > 0 ) {
			foreach ( $registered_posts as $post_id => $post_data ) {
				self::enqueue_assets_for_post( $post_id );

			}
		}
	}

	public static function enqueue_assets_for_post( $post_id ) {
		$css_file_url  = self::$cache_directory['url'] . "post-{$post_id}.css";
		$css_file_path = self::$cache_directory['path'] . "post-{$post_id}.css";
		$js_file_url   = self::$cache_directory['url'] . "post-{$post_id}.js";
		$js_file_path  = self::$cache_directory['path'] . "post-{$post_id}.js";

		if ( is_file( $css_file_path ) ) {
			wp_enqueue_style( sprintf( 'zionbuilder-post-%s', $post_id ), $css_file_url, [], filemtime( $css_file_path ) );
		}

		if ( is_file( $js_file_path ) ) {
			wp_enqueue_script( sprintf( 'zionbuilder-post-%s', $post_id ), $js_file_url, [], filemtime( $js_file_path ), true );
		}
	}


	public function load_page_content_scripts() {
		$registered_posts = Plugin::$instance->renderer->get_registered_areas();

		foreach ( $registered_posts as $post_id => $template_data ) {
			// Load elements css/js from scripts files
			foreach ( $template_data as $element ) {
				$element_instance = Plugin::$instance->renderer->get_element_instance( $element['uid'] );

				if ( $element_instance ) {
					$this->enqueue_external_files_for_element( $element_instance );
				}
			}
		}
	}

	public function enqueue_external_files_for_element( $element_instance ) {
		$element_instance->enqueue_all_extra_scripts();

		// Check for children
		$children = $element_instance->get_children();
		if ( is_array( $children ) ) {
			foreach ( $children as $element ) {
				$child_element = Plugin::$instance->renderer->get_element_instance( $element['uid'] );

				if ( $child_element ) {
					$this->enqueue_external_files_for_element( $child_element );
				}
			}
		}
	}

	public static function generate_post_assets( $post_id ) {
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post_id );
		$css           = '';
		$js            = '';

		// Clear the loaded element assets
		self::$loaded_element_assets = [];

		if ( ! $post_instance || ! $post_instance->is_built_with_zion() ) {
			return;
		}

		// Get the template data
		$post_template_data = $post_instance->get_template_data();

		foreach ( $post_template_data as $element_uid => $element_data ) {
			$element_instance = Plugin::$instance->elements_manager->get_element_instance_with_data( $element_data );

			if ( $element_instance ) {
				$assets = self::extract_element_assets( $element_instance );
				$css   .= $assets['css'];
				$js    .= $assets['js'];
			}
		}

		// TODO: document this
		$css = apply_filters( 'zionbuilder/assets/page/css', $css, $post_id );

		// TODO: document this
		$js = apply_filters( 'zionbuilder/assets/page/js', $js, $post_id );

		// Save the css to file
		if ( ! empty( $css ) ) {
			$file_path = self::$cache_directory['path'] . "post-{$post_id}.css";
			FileSystem::get_file_system()->put_contents( $file_path, self::minify( $css ), 0644 );
		}

		// Save the css to file
		if ( ! empty( $js ) ) {
			$file_path = self::$cache_directory['path'] . "post-{$post_id}.js";
			FileSystem::get_file_system()->put_contents( $file_path, self::minify( $js ), 0644 );
		}

		return true;
	}

	public static function extract_element_assets( $element_instance ) {
		$css = '';
		$js  = '';

		do_action( 'zionbuilder/element/before_element_extract_assets', $element_instance );

		$element_instance->enqueue_all_extra_scripts();
		$element_styles  = $element_instance->get_element_styles();
		$element_scripts = $element_instance->get_element_scripts();

		// #1 Add element css and js files
		if ( empty( self::$loaded_element_assets[$element_instance->get_type()] ) ) {
			foreach ( $element_styles as $style_url ) {
				$style_path = Utils::get_file_path_from_url( $style_url );
				$css       .= FileSystem::get_file_system()->get_contents( $style_path );
			}

			foreach ( $element_scripts as $script_url ) {
				$script_path = Utils::get_file_path_from_url( $script_url );
				$js         .= FileSystem::get_file_system()->get_contents( $script_path );
			}

			// Set a flag so we only load these files once
			self::$loaded_element_assets[$element_instance->get_type()] = true;
		}

		// Setup the data
		$element_instance->options->parse_data();

		// #2 Add css from style tab
		$styles            = $element_instance->options->get_value( '_styles', array() );
		$registered_styles = $element_instance->get_style_elements_for_editor();

		if ( ! empty( $styles ) && is_array( $registered_styles ) ) {
			foreach ( $registered_styles as $id => $style_config ) {
				if ( ! empty( $styles[$id] ) ) {
					$css_selector = $element_instance->get_css_selector();
					$css_selector = str_replace( '{{ELEMENT}}', $css_selector, $style_config['selector'] );
					$css_selector = apply_filters( 'zionbuilder/element/full_css_selector', array( $css_selector ), $element_instance );

					$css .= Style::get_css_from_selector( $css_selector, $styles[$id] );
				}
			}
		}

		// #3 Add css from options
		$css .= $element_instance->custom_css->get_css();

		// #2 Add element dynamic css
		$css .= $element_instance->css();

		// #4 Add custom css
		$css .= $element_instance->get_custom_css();

		// Check for children css
		$children = $element_instance->get_children();
		if ( is_array( $children ) ) {
			foreach ( $children as $element ) {
				$child_element = Plugin::$instance->elements_manager->get_element_instance_with_data( $element );

				if ( $child_element ) {
					$assets = self::extract_element_assets( $child_element );
					$css   .= $assets['css'];
					$js    .= $assets['js'];
				}
			}
		}

		do_action( 'zionbuilder/assets/after_element_extract_assets', $element_instance );

		return [
			'css' => $css,
			'js'  => $js,
		];
	}

	public static function delete_post_assets( $post_id ) {
		$post_id      = absint( $post_id );
		$cached_files = self::get_cache_files_for_post( $post_id );

		foreach ( $cached_files as $file_path ) {
			FileSystem::get_file_system()->delete( $file_path );
		}
	}

	/**
	 * Will return all the cache files that matches a post id
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	public static function get_cache_files_for_post( $post_id ) {
		$cache_files_found = array();
		$glob_pattern      = sprintf( '%s*.{css,js}', self::$cache_directory['path'] );
		$cached_files      = glob( $glob_pattern, GLOB_BRACE );

		foreach ( $cached_files as $file ) {
			$name = pathinfo( $file, PATHINFO_FILENAME );

			if ( false !== strpos( $name, (string) $post_id ) ) {
				$cache_files_found[] = $file;
			}
		}

		return $cache_files_found;
	}

	public static function generate_post_css( $post_id ) {

	}

	public function register_defaults_scripts() {
		// register styles
		wp_register_style( 'swiper', Utils::get_file_url( 'assets/vendors/swiper/swiper.min.css' ), array(), Plugin::instance()->get_version() );

		// Load animations
		wp_register_style( 'zion-frontend-animations', plugins_url( 'zionbuilder/assets/vendors/css/animate.css' ), array(), Plugin::instance()->get_version() );

		// Register scripts
		wp_register_script( 'zb-modal', Plugin::instance()->scripts->get_script_url( 'ModalJS', 'js' ), array(), Plugin::instance()->get_version(), true );

		// Video
		wp_register_script( 'zb-video', Plugin::instance()->scripts->get_script_url( 'ZBVideo', 'js' ), array(), Plugin::instance()->get_version(), true );
		wp_register_script( 'zb-video-bg', Plugin::instance()->scripts->get_script_url( 'ZBVideoBg', 'js' ), array( 'zb-video' ), Plugin::instance()->get_version(), true );

		// Swiper slider
		wp_register_script( 'swiper', Utils::get_file_url( 'assets/vendors/swiper/swiper.min.js' ), array(), Plugin::instance()->get_version(), true );
		wp_register_script( 'zion-builder-slider', Utils::get_file_url( 'dist/elements/ImageSlider/frontend.js' ), array( 'swiper' ), Plugin::instance()->get_version(), true );

		// Animate JS
		Plugin::instance()->scripts->register_script(
			'zionbuilder-animatejs',
			'animateJS',
			array(),
			Plugin::instance()->get_version(),
			true
		);

		wp_add_inline_script( 'zionbuilder-animatejs', 'animateJS()' );
	}

	public function load_global_css() {
		$dynamic_cache_file     = self::$cache_directory['path'] . self::DYNAMIC_CSS_FILENAME;
		$dynamic_cache_file_url = self::$cache_directory['url'] . self::DYNAMIC_CSS_FILENAME;

		// Create the file if it doesn't exists
		if ( ! is_file( $dynamic_cache_file ) || Environment::is_debug() ) {
			self::compile_global_css();
		}

		$version = (string) filemtime( $dynamic_cache_file );
		wp_enqueue_style( 'zionbuilder-global-css', $dynamic_cache_file_url, array(), $version );
	}


	public static function compile_global_css() {
		$dynamic_cache_file = self::$cache_directory['path'] . self::DYNAMIC_CSS_FILENAME;

		$dynamic_css = '';

		// #1 Add normalize if necessary
		if ( Settings::get_value( 'performance.disable_normalize_css', false ) === false ) {
			$normalize_css = FileSystem::get_file_system()->get_contents( Utils::get_file_path( 'assets/vendors/css/normalize.css' ) );
			if ( $normalize_css ) {
				$dynamic_css .= $normalize_css;
			}
		}

		// #2 Add frontend.css
		$frontend_css = FileSystem::get_file_system()->get_contents( Utils::get_file_path( 'dist/frontend.css' ) );

		// Set proper responsive breakpoints
		if ( $frontend_css ) {
			$dynamic_css .= Responsive::replace_devices_in_css( $frontend_css );
		}

		// #3 Add css classes css
		// TODO: do not load global classes css here and only load where it is used
		$dynamic_css .= CSSClasses::get_css();

		// #4 Allow others to add css to the global css
		$dynamic_css = apply_filters( 'zionbuilder/cache/dynamic_css', $dynamic_css );

		return FileSystem::get_file_system()->put_contents( $dynamic_cache_file, self::minify( $dynamic_css ), 0644 );
	}


	/**
	 * Minify
	 *
	 * Will minify css code by removing comments and whitespace
	 *
	 * @param string $css
	 *
	 * @return string The minified css
	 */
	public static function minify( $css ) {
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $css );

		return $css;
	}
}
