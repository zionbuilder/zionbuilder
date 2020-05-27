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
	 * @var array
	 */
	private $cache_directory_config = null;

	/**
	 * Holds a reference to all post ids that we need to enqueue styles
	 *
	 * @var array
	 */
	private $registered_post_ids = [];

	/**
	 * @var array
	 */
	private $wp_upload_dir;

	/**
	 * Main class constructor
	 */
	public function __construct() {
		/*
		 * Cache uploads dir
		 */
		$this->wp_upload_dir = wp_upload_dir();

		// Delete cache for posts actions
		add_action( 'delete_post', [ $this, 'delete_post_cache' ] );
		add_action( 'save_post', [ $this, 'delete_post_cache' ] );

		// Delete all cache
		add_action( 'after_switch_theme', [ $this, 'delete_all_cache' ] );
		add_action( 'activated_plugin', [ $this, 'delete_all_cache' ] );

		// Enqueue styles
		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ], 99 );
		} else {
			// Register default scripts so we can use them in edit mode
			add_action( 'zionbuilder/editor/before_scripts', [ $this, 'register_default_scripts' ], 99 );
		}
	}

	public function on_enqueue_scripts() {
		$this->register_default_scripts();
		$this->enqueue_post_styles();
		$this->enqueue_post_scripts();
		$this->enqueue_dynamic_css();
	}

	public function register_default_scripts() {
		// register styles
		wp_register_style( 'swiper', Utils::get_file_url( 'assets/vendors/swiper/swiper.min.css' ), [], Plugin::instance()->get_version() );

		// Register scripts
		// Video
		wp_register_script( 'zb-video', Utils::get_file_url( 'assets/vendors/js/ZBVideo.js' ), [], Plugin::instance()->get_version(), true );
		wp_register_script( 'zb-video-bg', Utils::get_file_url( 'assets/vendors/js/ZBVideoBg.js' ), [ 'zb-video' ], Plugin::instance()->get_version(), true );

		// Swiper slider
		wp_register_script( 'swiper', Utils::get_file_url( 'assets/vendors/swiper/swiper.min.js' ), [], Plugin::instance()->get_version(), true );

		// Animate JS
		wp_register_script( 'zionbuilder-animatejs', Utils::get_file_url( 'assets/vendors/js/animateJs.js' ), [], Plugin::instance()->get_version(), true );
		wp_add_inline_script( 'zionbuilder-animatejs', 'animateJs()' );
	}

	/**
	 * Register Post Id
	 *
	 * Will register post ids for which we need to enqueue styles
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function register_post_id( $post_id ) {
		$this->registered_post_ids[] = absint( $post_id );
	}

	/**
	 * Unregister Post Id
	 *
	 * Will remove a post id from the list of post ids for which we need to enqueue styles
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function unregister_post_id( $post_id ) {
		$post_id = absint( $post_id );
		unset( $this->registered_post_ids[$post_id] );
	}

	public function enqueue_dynamic_css() {
		$cache_directory        = $this->get_cache_directory();
		$dynamic_cache_file     = $cache_directory['path'] . self::DYNAMIC_CSS_FILENAME;
		$dynamic_cache_file_url = $cache_directory['url'] . self::DYNAMIC_CSS_FILENAME;

		// Create the file if it doesn't exists
		if ( ! is_file( $dynamic_cache_file ) || Environment::is_debug() ) {
			$this->compile_dynamic_css();
		}

		wp_enqueue_style( 'znpb-dynamic-css', $dynamic_cache_file_url, [], filemtime( $dynamic_cache_file ) );
	}

	/**
	 * Enqueue Post Styles
	 *
	 * Will enqueue the cached styles for all registered post ids
	 *
	 * @return void
	 */
	public function enqueue_post_styles() {
		if ( Plugin::$instance->editor->preview->is_preview_mode() ) {
			return;
		}

		// Enqueue element styles
		$this->enqueue_elements_styles();

		foreach ( $this->registered_post_ids as $post_id ) {
			$file_config = $this->get_cache_file_config( $post_id );
			$style_id    = 'zionbuilder-' . $post_id;

			// Create the file if it doesn't exists
			if ( ! is_file( $file_config['path'] ) || Environment::is_debug() ) {
				$this->compile_css_cache_file_for_post( $post_id );
			}

			wp_enqueue_style( $style_id, $file_config['url'], [], $this->get_cache_version( $post_id ) );
		}
	}

	public function enqueue_post_scripts() {
		if ( Plugin::$instance->editor->preview->is_preview_mode() ) {
			return;
		}

		// Enqueue element scripts
		$this->enqueue_elements_scripts();

		foreach ( $this->registered_post_ids as $post_id ) {
			$file_config = $this->get_cache_file_config( $post_id, 'js' );
			$script_id   = 'zionbuilder-' . $post_id;

			// Create the file if it doesn't exists
			if ( ! is_file( $file_config['path'] ) || Environment::is_debug() ) {
				$this->compile_js_cache_file_for_post( $post_id );
			}

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( $script_id, $file_config['url'], [], $this->get_cache_version( $post_id ), true );
		}
	}

	public function enqueue_elements_styles() {
		$elements_instances = Plugin::$instance->frontend->get_elements_instances();
		$loaded_assets      = [];

		foreach ( $elements_instances as $element_instance ) {
			$element_type = $element_instance->get_type();

			if ( ! isset( $loaded_assets[$element_type] ) ) {
				// Add the style.css file if present
				$element_instance->enqueue_styles();
				$loaded_assets[$element_type] = true;
			}
		}
	}

	public function enqueue_elements_scripts() {
		$elements_instances = Plugin::$instance->frontend->get_elements_instances();
		$loaded_assets      = [];

		foreach ( $elements_instances as $element_instance ) {
			$element_type = $element_instance->get_type();

			if ( ! isset( $loaded_assets[$element_type] ) ) {
				// Add the style.css file if present
				$element_instance->enqueue_scripts();
				$loaded_assets[$element_type] = true;
			}
		}
	}

	/**
	 * Get Cache Directory
	 *
	 * Returns the cache directory config
	 *
	 * @return array An array containing cache directory path and url
	 */
	private function get_cache_directory() {
		if ( null === $this->cache_directory_config ) {
			$relative_cache_path       = trailingslashit( self::CACHE_FOLDER_NAME );
			$zionbuilder_upload_folder = FileSystem::get_zionbuilder_upload_dir();

			$this->cache_directory_config = [
				'path' => $zionbuilder_upload_folder['basedir'] . $relative_cache_path,
				'url'  => esc_url( $zionbuilder_upload_folder['baseurl'] . $relative_cache_path ),
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
	 * @return array
	 */
	public function get_cache_file_config( $post_id, $type = 'css' ) {
		$post_id         = absint( $post_id );
		$file_name       = $post_id . '-layout.' . $type;
		$cache_directory = $this->get_cache_directory();

		return [
			'file_name' => $file_name,
			'path'      => $cache_directory['path'] . $file_name,
			'url'       => esc_url( $cache_directory['url'] . $file_name ),
		];
	}

	/**
	 * Compile cache file for post
	 *
	 * Will create the cached css file containing the css code that is needed for a specific post id
	 *
	 * @param int $post_id
	 *
	 * @return boolean
	 */
	private function compile_css_cache_file_for_post( $post_id ) {
		$elements_instances = Plugin::$instance->frontend->get_elements_instances();
		$loaded_assets      = [];
		$css                = '';

		foreach ( $elements_instances as $element_instance ) {
			$element_type = $element_instance->get_type();

			if ( ! isset( $loaded_assets[$element_type] ) ) {
				$element_styles = $element_instance->get_element_styles();

				foreach ( $element_styles as $style_url ) {
					$style_path = Utils::get_file_path_from_url( $style_url );
					$css       .= FileSystem::get_file_system()->get_contents( $style_path );
				}

				$loaded_assets[$element_type] = true;
			}

			// Add dynamic css
			$css .= $element_instance->get_element_extra_css();
		}

		$css = apply_filters( 'zionbuilder/cache/page_css', $css, $post_id );

		// Minify the css
		$css               = $this->minify( $css );
		$cache_file_config = $this->get_cache_file_config( $post_id );

		return FileSystem::get_file_system()->put_contents( $cache_file_config['path'], $css, 0644 );
	}

	/**
	 * Compile cache file for post
	 *
	 * Will create the cached css file containing the css code that is needed for a specific post id
	 *
	 * @param int $post_id
	 *
	 * @return boolean
	 */
	private function compile_js_cache_file_for_post( $post_id ) {
		$elements_instances = Plugin::$instance->frontend->get_elements_instances();
		$loaded_assets      = [];
		$js                 = '';

		foreach ( $elements_instances as $element_instance ) {
			$element_type = $element_instance->get_type();

			if ( ! isset( $loaded_assets[$element_type] ) ) {
				// Add the style.css file if present
				$element_scripts = $element_instance->get_element_scripts();

				foreach ( $element_scripts as $script_url ) {
					$script_path = Utils::get_file_path_from_url( $script_url );
					$js         .= FileSystem::get_file_system()->get_contents( $script_path );
				}

				// Add the style.css file if present
				$js_file_path = $element_instance->get_path( 'script.js' );
				if ( FileSystem::get_file_system()->is_file( $js_file_path ) ) {
					$js .= FileSystem::get_file_system()->get_contents( $js_file_path );
				}

				$loaded_assets[$element_type] = true;
			}
		}

		$js                = apply_filters( 'zionbuilder/cache/page_js', $js, $post_id );
		$cache_file_config = $this->get_cache_file_config( $post_id, 'js' );

		$final_script = sprintf(
			'
		(function($) {
			window.ZionBuilderFrontend = {
				scripts: {},
				registerScript: function (scriptId, scriptCallback) {
					this.scripts[scriptId] = scriptCallback;
				},
				getScript(scriptId) {
					return this.scripts[scriptId]
				},
				unregisterScript: function(scriptId) {
					delete this.scripts[scriptId];
				},
				run: function() {
					var that = this;
					var $scope = $(document)
					Object.keys(this.scripts).forEach(function(scriptId) {
						var scriptObject = that.scripts[scriptId];
						scriptObject.run( $scope );
					})
				}
			};

			%s

			window.ZionBuilderFrontend.run();

		})(jQuery);
		',
			$js
		);

		// Minify the js
		return FileSystem::get_file_system()->put_contents( $cache_file_config['path'], $final_script, 0644 );
	}

	/**
	 * Will compile dynamic css
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
		$post_id           = absint( $post_id );
		$cache_file_config = $this->get_cache_file_config( $post_id );

		FileSystem::get_file_system()->delete( $cache_file_config['path'] );
	}

	/**
	 * Delete all cache
	 *
	 * Deletes the cache directory
	 */
	public function delete_all_cache() {
		$cache_directory = $this->get_cache_directory();
		return FileSystem::get_file_system()->delete( $cache_directory['path'], true );
	}

	/**
	 * Get Cache Version
	 *
	 * Returns a string based on post modified date that will be used as file version
	 *
	 * @param integer $post_id
	 *
	 * @return string
	 */
	private function get_cache_version( $post_id = 0 ) {
		$post_id = $post_id ? absint( $post_id ) : get_the_ID();

		return md5( get_post_modified_time( 'U', false, $post_id ) );
	}
}
