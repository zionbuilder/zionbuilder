<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Cache
 *
 * @package ZionBuilder
 */
class Cache {
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
	private $cache_directory_config = null;

	private static $loaded_assets            = [];
	private static $loaded_javascript_assets = [];
	private static $done_areas_js            = [];
	private static $late_scripts             = [];

	private $areas_raw_css = [];
	private $active_areas  = [];


	private $active_area = null;

	private $generate_element_css = false;

	/**
	 * Main class constructor
	 */
	public function __construct() {
		// Delete cache for posts actions
		add_action( 'delete_post', [ $this, 'delete_post_cache' ] );
		add_action( 'save_post', [ $this, 'delete_post_cache' ] );

		// Delete all cache
		add_action( 'after_switch_theme', [ $this, 'delete_all_cache' ] );
		add_action( 'activated_plugin', [ $this, 'delete_all_cache' ] );
		add_action( 'zionbuilder/settings/save', [ $this, 'delete_all_cache' ] );

		// Enqueue styles
		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ] );

			// This is needed so we can load scripts that are not available on WP action ( for example, shortcodes )
			add_action( 'wp_footer', [ $this, 'enqueue_post_assets' ] );

		} else {
			// Register default scripts so we can use them in edit mode
			// TODO: Check if this is still needed
			add_action( 'zionbuilder/editor/before_scripts', [ $this, 'register_default_scripts' ] );
		}
	}

	/**
	 * Enqueues page scripts
	 *
	 * @return void
	 */
	public function on_enqueue_scripts() {
		$this->register_default_scripts();
		$this->enqueue_post_assets();
		$this->enqueue_dynamic_css();
	}

	public function enqueue_post_assets() {
		$in_footer        = did_action( 'wp_enqueue_scripts' ) && ! doing_filter( 'wp_enqueue_scripts' );
		$registered_posts = Plugin::$instance->renderer->get_registered_areas();
		$is_preview       = Plugin::$instance->editor->preview->is_preview_mode();

		$registered_areas_ids = array_keys( $registered_posts );

		// If we have registered areas, just generate the assets based on the areas
		if ( count( $registered_areas_ids ) > 0 ) {
			$page_dynamic_assets = PageAssets::get_instance( $registered_areas_ids, false );
			$page_dynamic_assets->enqueue_element_scripts();

			// Check to see if the file was already generated
			if ( $page_dynamic_assets->is_generated() ) {
				$page_dynamic_assets->enqueue();
			} elseif ( $in_footer ) {
				$page_dynamic_assets->generate_dynamic_assets()->save()->enqueue();
			} else {
				// Set a flag so we can collect the css and js from elements
				$this->generate_element_css = true;
			}
		} else {
			// Create individual assets for each registered area
			foreach ( $registered_posts as $post_id => $post_content ) {
				// Create dynamic files for each area
			}
		}
	}


	public function register_for_late_compile( $handle, $config = [] ) {
		if ( ! isset( self::$late_scripts[$handle] ) ) {
			self::$late_scripts[$handle] = $config;
		}
	}

	/**
	 * Enqueues the assets for a given post
	 *
	 * @param integer $post_id
	 * @param array $cache_file_config
	 *
	 * @return void
	 */
	public function include_assets_for_post( $post_id, $cache_file_config ) {
		// Check to see if the file has content
		if ( 0 !== FileSystem::get_file_system()->size( $cache_file_config['path'] . '.css' ) ) {
			wp_enqueue_style( $cache_file_config['handle'], $cache_file_config['url'] . '.css', [], $this->get_cache_version( $post_id ) );
		}

		if ( 0 !== FileSystem::get_file_system()->size( $cache_file_config['path'] . '.js' ) ) {
			wp_enqueue_script( $cache_file_config['handle'], $cache_file_config['url'] . '.js', [], $this->get_cache_version( $post_id ), true );
		}
	}


	/**
	 * Will enqueue all elements scripts and styles
	 *
	 * Will also look for scripts inside child elements
	 *
	 * @param Element $element The Zion Builder element for which we need to enqueue scripts
	 *
	 * @return void
	 */
	public function enqueue_element_scripts( $element ) {
		$element->do_enqueue_scripts();

		// Check for children
		$children = $element->get_children();

		if ( is_array( $children ) ) {
			foreach ( $children as $element ) {
				$child_element = Plugin::$instance->renderer->get_element_instance( $element['uid'] );
				$this->enqueue_element_scripts( $child_element );
			}
		}
	}

	/**
	 * Returns true if we need to generate CSS for elements
	 *
	 * @return boolean
	 */
	public function should_generate_css() {
		return $this->generate_element_css;
	}

	public function get_active_area() {
		return end( $this->active_areas );
	}

	public function set_active_area( $post_id ) {
		$this->active_areas[] = $post_id;
	}

	public function reset_active_area() {
		array_pop( $this->active_areas );
	}

	public function add_raw_css( $css ) {
		if ( $this->active_area ) {
			$this->active_area->add_raw_css( $css );
		}

		if ( ! isset( $this->areas_raw_css[$this->get_active_area()] ) ) {
			$this->areas_raw_css[$this->get_active_area()] = '';
		}

		$this->areas_raw_css[$this->get_active_area()] .= $css;
	}

	public function compile_assets_for_post( $handle, $config ) {
		$post_id           = $config['post_id'];
		$cache_file_config = $config['file_config'];

		if ( $config['is_main'] ) {
			$css = '';
			$js  = '';

			$registered_posts = Plugin::$instance->renderer->get_registered_areas();

			// Enqueue scripts and styles for elements
			foreach ( $registered_posts as $post_id => $post_content ) {
				$post_assets = $this->get_css_and_js_for_post( $post_id );

				$css .= $post_assets['css'];
				$js  .= $post_assets['js'];
			}

			// Save the CSS
			FileSystem::get_file_system()->put_contents( $cache_file_config['path'] . '.css', $css, 0644 );

			// Save the JS
			FileSystem::get_file_system()->put_contents( $cache_file_config['path'] . '.js', $js, 0644 );
		} else {
			// TODO: partials
		}
		if ( $config['enqueue'] === true ) {
			$this->include_assets_for_post( $post_id, $config['file_config'] );
		}
	}

	public function get_css_and_js_for_post( $post_id ) {
		$css = 'sdfsdf';
		$js  = 'wrwerwe';

		return [
			'css' => $css,
			'js'  => $js,
		];
	}

	/**
	 * Register plugin default scripts
	 *
	 * @return void
	 */
	public function register_default_scripts() {
		// register styles
		wp_register_style( 'swiper', Utils::get_file_url( 'assets/vendors/swiper/swiper.min.css' ), [], Plugin::instance()->get_version() );

		// Load animations
		wp_register_style( 'zion-frontend-animations', plugins_url( 'zionbuilder/assets/vendors/css/animate.css' ), [], Plugin::instance()->get_version() );

		// Register scripts
		wp_register_script( 'zb-modal', Utils::get_file_url( 'assets/vendors/js/modal.min.js' ), [], Plugin::instance()->get_version(), true );

		// Video
		wp_register_script( 'zb-video', Utils::get_file_url( 'assets/vendors/js/ZBVideo.js' ), [], Plugin::instance()->get_version(), true );
		wp_register_script( 'zb-video-bg', Utils::get_file_url( 'assets/vendors/js/ZBVideoBg.js' ), [ 'zb-video' ], Plugin::instance()->get_version(), true );

		// Swiper slider
		wp_register_script( 'swiper', Utils::get_file_url( 'assets/vendors/swiper/swiper.min.js' ), [], Plugin::instance()->get_version(), true );

		// Animate JS
		wp_register_script( 'zionbuilder-animatejs', Utils::get_file_url( 'dist/js/animateJS.js' ), [], Plugin::instance()->get_version(), true );
		wp_add_inline_script( 'zionbuilder-animatejs', 'animateJS()' );
	}

	/**
	 * Enqueue dynamic css file
	 *
	 * @return void
	 */
	public function enqueue_dynamic_css() {
		$cache_directory        = $this->get_cache_directory();
		$dynamic_cache_file     = $cache_directory['path'] . self::DYNAMIC_CSS_FILENAME;
		$dynamic_cache_file_url = $cache_directory['url'] . self::DYNAMIC_CSS_FILENAME;

		// Create the file if it doesn't exists
		if ( ! is_file( $dynamic_cache_file ) || Environment::is_debug() ) {
			$this->compile_dynamic_css();
		}

		$version = (string) filemtime( $dynamic_cache_file );
		wp_enqueue_style( 'znpb-dynamic-css', $dynamic_cache_file_url, [], $version );
	}

	/**
	 * Get Cache Directory
	 *
	 * Returns the cache directory config
	 *
	 * @return array{path: string, url: string} An array containing cache directory path and url
	 */
	private function get_cache_directory() {
		if ( null === $this->cache_directory_config ) {
			$relative_cache_path       = trailingslashit( self::CACHE_FOLDER_NAME );
			$zionbuilder_upload_folder = FileSystem::get_zionbuilder_upload_dir();

			$this->cache_directory_config = [
				'path' => $zionbuilder_upload_folder['basedir'] . $relative_cache_path,
				'url'  => esc_url( set_url_scheme( $zionbuilder_upload_folder['baseurl'] . $relative_cache_path ) ),
			];

			// Create the cache folder
			wp_mkdir_p( $this->cache_directory_config['path'] );
		}

		return $this->cache_directory_config;
	}

	/**
	 * Get Cache File Config
	 *
	 * Returns the cache folder config for a given post id
	 *
	 * @param int   $post_id
	 * @param mixed $type
	 *
	 * @return array{file_name: string, handle: string, path: string, url: string } The cache file paths
	 */
	public function get_cache_file_config( $post_id, $filename_append = false ) {
		$post_id         = absint( $post_id );
		$append_text     = $filename_append ? '-' . $filename_append : '';
		$file_name       = sprintf( '%s-layout%s', $post_id, $append_text );
		$cache_directory = $this->get_cache_directory();

		return [
			'file_name' => $file_name,
			'handle'    => $file_name,
			'path'      => $cache_directory['path'] . $file_name,
			'url'       => esc_url( $cache_directory['url'] . $file_name ),
		];
	}


	public function get_css_for_element( $element_instance ) {
		return;
		$css = '';

		$element_type = $element_instance->get_type();

		if ( ! isset( self::$loaded_assets[$element_type] ) ) {
			// $element_instance->do_enqueue_styles();
			$element_styles = $element_instance->get_element_styles();

			foreach ( $element_styles as $style_url ) {
				$style_path = Utils::get_file_path_from_url( $style_url );
				$css       .= FileSystem::get_file_system()->get_contents( $style_path );
			}

			self::$loaded_assets[$element_type] = true;
		}

		// Check for children
		$children = $element_instance->get_children();

		if ( is_array( $children ) ) {
			foreach ( $children as $element ) {
				$child_element_instance = Plugin::$instance->renderer->get_element_instance( $element['uid'] );
				if ( $child_element_instance ) {
					$css .= $this->get_css_for_element( $child_element_instance );
				}
			}
		}

		return $css;
	}

	private function get_javascript_for_element( $element_instance ) {
		$js = '';

		// Get element scripts
		$element_scripts = $element_instance->get_element_scripts();
		$element_type    = $element_instance->get_type();

		if ( ! isset( self::$loaded_javascript_assets[$element_type] ) ) {
			foreach ( $element_scripts as $script_url ) {
				$script_path = Utils::get_file_path_from_url( $script_url );
				$js         .= FileSystem::get_file_system()->get_contents( $script_path );
				self::$loaded_javascript_assets[$element_type] = true;
			}
		}

		// Check for children
		$childs = $element_instance->get_children();

		if ( is_array( $childs ) ) {
			foreach ( $childs as $element ) {
				$child_element_instance = Plugin::$instance->renderer->get_element_instance( $element['uid'] );
				if ( $child_element_instance ) {
					$js .= $this->get_javascript_for_element( $child_element_instance );
				}
			}
		}

		return $js;
	}

	/**
	 * Will compile dynamic css
	 *
	 * @return string The compiled css
	 */
	private function compile_dynamic_css() {
		$cache_directory    = $this->get_cache_directory();
		$dynamic_cache_file = $cache_directory['path'] . self::DYNAMIC_CSS_FILENAME;

		$dynamic_css = '';

		// Add css classes css
		$dynamic_css .= CSSClasses::get_css();

		$dynamic_css = apply_filters( 'zionbuilder/cache/dynamic_css', $dynamic_css );

		return FileSystem::get_file_system()->put_contents( $dynamic_cache_file, $this->minify( $dynamic_css ), 0644 );
	}

	/**
	 * Minify
	 *
	 * Will minify css code by removing comments and whitespaces
	 *
	 * @param string $css
	 *
	 * @return string The minified css
	 */
	public function minify( $css ) {
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( [ "\r\n", "\r", "\n", "\t" ], '', $css );

		return $css;
	}

	/**
	 * Delete Post Cache
	 *
	 * Deletes the cache file for a given post
	 *
	 * @param mixed $post_id
	 *
	 * @return void
	 */
	public function delete_post_cache( $post_id ) {
		$post_id      = absint( $post_id );
		$cached_files = $this->get_cache_files_for_post( $post_id );

		foreach ( $cached_files as $file_path ) {
			FileSystem::get_file_system()->delete( $file_path );
		}
	}


	/**
	 * Will return all the cache files that mathches a post id
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_cache_files_for_post( $post_id ) {
		$cache_files_found = [];
		$cache_folder      = $this->get_cache_directory();
		$glob_pattern      = sprintf( '%s*.{css,js}', $cache_folder['path'] );
		$cached_files      = glob( $glob_pattern, GLOB_BRACE );

		foreach ( $cached_files as $file ) {
			$name = pathinfo( $file, PATHINFO_FILENAME );

			if ( false !== strpos( $name, (string) $post_id ) ) {
				$cache_files_found[] = $file;
			}
		}

		return $cache_files_found;
	}


	/**
	 * Purges the dynamic css cached file
	 *
	 * @return void
	 */
	public function delete_dynamic_css_cache() {
		$cache_directory    = $this->get_cache_directory();
		$dynamic_cache_file = $cache_directory['path'] . self::DYNAMIC_CSS_FILENAME;

		FileSystem::get_file_system()->delete( $dynamic_cache_file );
	}


	/**
	 * Delete all cache
	 *
	 * Deletes the cache directory
	 *
	 * @return boolean
	 */
	public function delete_all_cache() {
		$cache_directory = $this->get_cache_directory();
		return FileSystem::get_file_system()->delete( $cache_directory['path'], true );
	}
}
