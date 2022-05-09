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

	/**
	 * Holds the flag that we check in order to collect the element css
	 *
	 * @var boolean
	 */
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

			// This is needed so we can load scripts that are not available on WP action ( for example, shortcode )
			add_action( 'wp_footer', [ $this, 'enqueue_post_assets' ] );

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

	/**
	 * Generates and enqueue the post assets
	 * If the post assets are not available in head, they will be generated and enqueued in footer
	 *
	 * @return void
	 */
	public function enqueue_post_assets() {
		$in_footer            = did_action( 'wp_enqueue_scripts' ) && ! doing_filter( 'wp_enqueue_scripts' );
		$registered_posts     = Plugin::$instance->renderer->get_registered_areas();
		$registered_areas_ids = array_keys( $registered_posts );

		// If we have registered areas, just generate the assets based on the areas
		if ( count( $registered_areas_ids ) > 0 ) {
			$page_dynamic_assets = PageAssets::get_instance( $registered_areas_ids );

			// Enqueue element external scripts
			$page_dynamic_assets->enqueue_external_files();

			// Check to see if the file was already generated
			if ( $page_dynamic_assets->is_generated() ) {
				$page_dynamic_assets->enqueue();
			} elseif ( $in_footer ) {
				$page_dynamic_assets->generate_dynamic_assets()->save()->enqueue();
			} else {
				// Set a flag so we can collect the css and js from elements
				$this->generate_element_css = true;
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

	/**
	 * Sets the flag for css collection
	 *
	 * @param boolean $status
	 *
	 * @return void
	 */
	public function set_assets_collection( $status ) {
		$this->generate_element_css = $status;
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
		wp_register_script( 'zb-modal', Utils::get_file_url( 'dist/js/modalJS.js' ), [], Plugin::instance()->get_version(), true );

		// Video
		wp_register_script( 'zb-video', Utils::get_file_url( 'dist/js/ZBVideo.js' ), [ 'jquery' ], Plugin::instance()->get_version(), true );
		wp_register_script( 'zb-video-bg', Utils::get_file_url( 'dist/js/ZBVideoBg.js' ), [ 'zb-video' ], Plugin::instance()->get_version(), true );

		// Swiper slider
		wp_register_script( 'swiper', Utils::get_file_url( 'assets/vendors/swiper/swiper.min.js' ), [], Plugin::instance()->get_version(), true );
		wp_register_script( 'zion-builder-slider', Utils::get_file_url( 'dist/js/elements/ImageSlider/frontend.js' ), [ 'swiper' ], Plugin::instance()->get_version(), true );

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
	 * @param mixed $filename_append
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


	/**
	 * Will compile dynamic css
	 *
	 * @return string The compiled css
	 */
	private function compile_dynamic_css() {
		$cache_directory    = $this->get_cache_directory();
		$dynamic_cache_file = $cache_directory['path'] . self::DYNAMIC_CSS_FILENAME;

		$dynamic_css = '';

		// Add normalize if necessary
		if ( Settings::get_value( 'performance.disable_normalize_css', false ) === false ) {
			$normalize_css = FileSystem::get_file_system()->get_contents( Utils::get_file_path( 'assets/vendors/css/normalize.css' ) );
			if ( $normalize_css ) {
				$dynamic_css .= $normalize_css;
			}
		}

		// Add frontent.css
		$frontend_css = FileSystem::get_file_system()->get_contents( Utils::get_file_path( 'dist/css/frontend.css' ) );

		if ( $frontend_css ) {
			$dynamic_css .= Responsive::replace_devices_in_css( $frontend_css );
		}

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

	/**
	 * @deprecated deprecated since version 2.7.3
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function register_post_id( $post_id ) {
		_deprecated_function( __METHOD__, '2.7.2' );
	}
}
