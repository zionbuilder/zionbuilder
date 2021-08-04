<?php

namespace ZionBuilder;

use ZionBuilder\Elements\Element;
use ZionBuilder\Plugin;
use ZionBuilder\Environment;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class PageAssets {
	/**
	 * Holds the name of the directory to use by default for assets config
	 */
	const CACHE_FOLDER_NAME = 'cache';

	/**
	 * Holds a refference to all the instances of PageAssets
	 *
	 * @var array
	 */
	private static $instances = [];

	/**
	 * Holds a refference to the cache directory
	 *
	 * @var string
	 */
	private static $cache_directory_config = null;

	/**
	 * Holds a refference to the scripts loaded by elements
	 *
	 * @var array
	 */
	private static $loaded_elements = [];

	/**
	 * Holds the CSS generated for this post id
	 *
	 * @var string
	 */
	public $css = '';

	/**
	 * Holds the JS generated for this post id
	 *
	 * @var string
	 */
	public $js = '';


	/**
	 * Holds a refference to the post id for which we need to generated the assets
	 *
	 * @var int
	 */
	public $post_id = null;

	/**
	 * Flag to check if the assets were already processed
	 *
	 * @var boolean
	 */
	private $enqueued_element_scripts = false;

	/**
	 * Flag to check if the dynamic assets were already enqueued
	 *
	 * @var boolean
	 */
	private $enqueued_dynamic_assets = false;

	public function __construct( $post_id, $is_partial = true ) {
		$this->post_id     = $post_id;
		$append_text       = $is_partial ? '-partial' : '';
		$this->file_handle = sprintf( '%s-layout%s', $post_id, $append_text );
	}

	/**
	 * Returns a single instance of the PageAssets class
	 *
	 * @param integer $post_id
	 *
	 * @return PageAssets
	 */
	public static function get_instance( $post_id, $is_partial = true ) {
		if ( ! isset( self::$instances[$post_id] ) ) {
			self::$instances[$post_id] = new self( $post_id );
		}

		return self::$instances[$post_id];
	}

	/**
	 * Will enqueue the element scripts if they are not already enqueued
	 *
	 * @return void
	 */
	public function enqueue_element_scripts() {
		if ( ! $this->enqueued_element_scripts ) {
			$post_content = Plugin::$instance->renderer->get_content_for_area( $this->post_id );
			foreach ( $post_content as $element ) {
				$element_instance = Plugin::$instance->renderer->get_element_instance( $element['uid'] );
				$this->enqueue_element_assets( $element_instance );
			}

			$this->enqueued_element_scripts = true;
		}
	}

	/**
	 * Will enqueue the elements scripts
	 *
	 * @param Element $element
	 *
	 * @return void
	 */
	public function enqueue_element_assets( $element ) {
		if ( $element === false ) {
			return;
		}

		$element->do_enqueue_scripts();

		// Check for children
		$children = $element->get_children();

		if ( is_array( $children ) ) {
			foreach ( $children as $element ) {
				$child_element = Plugin::$instance->renderer->get_element_instance( $element['uid'] );
				$this->enqueue_element_assets( $child_element );
			}
		}
	}


	/**
	 * Will generate the needed css and JS for the given post id
	 *
	 * @return PageAssets
	 */
	public function generate_dynamic_assets( $use_global_cache = false ) {
		// Check to see if we need to clear the global cache
		if ( ! $use_global_cache ) {
			self::$loaded_elements = [];
		}

		$post_content = Plugin::$instance->renderer->get_content_for_area( $this->post_id );

		foreach ( $post_content as $element ) {
			$element_instance = Plugin::$instance->renderer->get_element_instance( $element['uid'] );

			if ( $element_instance ) {
				$this->extract_element_assets( $element_instance );
			}
		}

		return $this;
	}

	public function extract_element_assets( $element_instance ) {
		$element_type = $element_instance->get_type();

		if ( ! isset( self::$loaded_elements[$element_type] ) ) {
			// $element_instance->do_enqueue_styles();
			$element_styles  = $element_instance->get_element_styles();
			$element_scripts = $element_instance->get_element_scripts();

			foreach ( $element_styles as $style_url ) {
				$style_path = Utils::get_file_path_from_url( $style_url );
				$this->css .= FileSystem::get_file_system()->get_contents( $style_path );
			}

			foreach ( $element_scripts as $script_url ) {
				$script_path = Utils::get_file_path_from_url( $script_url );
				$this->js   .= FileSystem::get_file_system()->get_contents( $script_path );
			}

			self::$loaded_elements[$element_type] = true;
		}

	}

	/**
	 * Will save the files to server
	 *
	 * @return PageAssets
	 */
	public function save() {
		FileSystem::get_file_system()->put_contents( $this->get_path_for_asset(), $this->css, 0644 );
		FileSystem::get_file_system()->put_contents( $this->get_path_for_asset( 'js' ), $this->js, 0644 );

		return $this;
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
	 * Will enqueue the scripts
	 *
	 * @return PageAssets
	 */
	public function enqueue() {
		if ( ! $this->enqueued_dynamic_assets ) {
			$css_path = $this->get_path_for_asset();
			$css_url  = $this->get_url_for_asset();
			$js_path  = $this->get_path_for_asset( 'js' );
			$js_url   = $this->get_url_for_asset( 'js' );

			if ( 0 !== FileSystem::get_file_system()->size( $css_path ) ) {
				wp_enqueue_style( $this->file_handle, $css_url, [], $this->get_cache_version() );
			}

			if ( 0 !== FileSystem::get_file_system()->size( $js_path ) ) {
				wp_enqueue_script( $this->file_handle, $js_url, [], $this->get_cache_version(), true );
			}

			$this->enqueued_dynamic_assets = true;
		}
	}

	/**
	 * Returns the path for a specific dynamic asset
	 *
	 * @param string $type The type of the asset ( css or js )
	 *
	 * @return string
	 */
	public function get_path_for_asset( $type = 'css' ) {
		$cache_directory = $this->get_cache_directory();
		return $cache_directory['path'] . $this->file_handle . '.' . $type;
	}

	/**
	 * Returns the URL for a specific dynamic asset
	 *
	 * @param string $type The type of the asset ( css or js )
	 *
	 * @return string
	 */
	public function get_url_for_asset( $type = 'css' ) {
		$cache_directory = $this->get_cache_directory();
		return $cache_directory['path'] . $this->file_handle . '.' . $type;
	}

	/**
	 * Checks to see if the files are generated
	 *
	 * @return boolean
	 */
	public function is_generated() {
		// Always regenerate in dev mode
		if ( Environment::is_debug() ) {
			return false;
		}

		// We only check the CSS file as they are both generated at the same time
		return is_file( $this->get_path_for_asset() );
	}

	public function add_raw_css( $css ) {
		$this->css .= $css;
	}

	public function get_css() {
		return $this->css;
	}



	public function add_raw_js( $js ) {
		$this->js .= $js;
	}

	public function get_js() {
		return $this->js;
	}

	/**
	 * Returns the path and URL to the dynamic assets cache directory
	 *
	 * @return void
	 */
	public function get_cache_directory() {
		if ( null === self::$cache_directory_config ) {
			$relative_cache_path       = trailingslashit( self::CACHE_FOLDER_NAME );
			$zionbuilder_upload_folder = FileSystem::get_zionbuilder_upload_dir();

			self::$cache_directory_config = [
				'path' => $zionbuilder_upload_folder['basedir'] . $relative_cache_path,
				'url'  => esc_url( set_url_scheme( $zionbuilder_upload_folder['baseurl'] . $relative_cache_path ) ),
			];

			// Create the cache folder
			wp_mkdir_p( self::$cache_directory_config['path'] );
		}

		return self::$cache_directory_config;
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
	private function get_cache_version() {
		return md5( get_post_modified_time( 'U', false, $this->post_id ) );
	}
}
