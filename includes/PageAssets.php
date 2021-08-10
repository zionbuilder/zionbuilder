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
	 * Holds a refference to the active post ids
	 * During collection phase, this is used to add the extra element css to the right post id
	 * During generation phase, this is used to put the proper css into the generated page asset
	 *
	 * @var array
	 */
	private static $active_areas = [];

	/**
	 * Holds the extra css for each registered area in the page
	 *
	 * @var array
	 */
	private static $areas_extra_css = [];

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
	private $loaded_elements = [];

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
	 * @var array
	 */
	public $post_ids = null;

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


	public function __construct( $post_ids ) {
		$this->post_ids    = $post_ids;
		$this->file_handle = sprintf( '%s-layout', self::generate_key( $post_ids ) );
	}


	/**
	 * Will add elements css to the active area
	 *
	 * @param string $css
	 *
	 * @return void
	 */
	public static function add_active_area_raw_css( $css ) {
		$active_area = self::get_active_area();
		if ( ! isset( self::$areas_extra_css[$active_area] ) ) {
			self::$areas_extra_css[$active_area] = '';
		}

		self::$areas_extra_css[$active_area] .= $css;
	}


	/**
	 * Will set the active area during collection and
	 *
	 * @param integer $post_id
	 *
	 * @return void
	 */
	public static function set_active_post_id( $post_id = 0 ) {
		self::$active_areas[] = $post_id;
	}

	/**
	 * Will reset the active area to the previous one
	 *
	 * @param integer $post_id
	 *
	 * @return void
	 */
	public static function reset_active_post_id( $post_id = 0 ) {
		array_pop( self::$active_areas );
	}

	/**
	 * Returns the active area
	 *
	 * @return integer
	 */
	public static function get_active_area() {
		$last_area = end( self::$active_areas );

		return $last_area ? $last_area : 0;
	}


	/**
	 * Returns the extra area css
	 *
	 * @return void
	 */
	public static function get_css_for_area( $post_id ) {
		if ( isset( self::$areas_extra_css[$post_id] ) ) {
			return self::$areas_extra_css[$post_id];
		}

		return '';
	}

	/**
	 * Will generate a key based on provided post ids
	 *
	 * @param array $post_ids
	 *
	 * @return string
	 */
	public static function generate_key( $post_ids ) {
		return implode( '-', $post_ids );
	}

	/**
	 * Returns a single instance of the PageAssets class
	 *
	 * @param integer $post_id
	 *
	 * @return PageAssets
	 */
	public static function get_instance( $post_ids ) {
		$post_ids = is_array( $post_ids ) ? $post_ids : [ $post_ids ];
		$key      = self::generate_key( $post_ids );

		if ( ! isset( self::$instances[$key] ) ) {
			self::$instances[$key] = new self( $post_ids );
		}

		return self::$instances[$key];
	}

	/**
	 * Will enqueue the element scripts if they are not already enqueued
	 *
	 * @return void
	 */
	public function enqueue_element_scripts() {
		if ( ! $this->enqueued_element_scripts ) {

			foreach ( $this->post_ids as $post_id ) {
				$post_content = Plugin::$instance->renderer->get_content_for_area( $post_id );
				foreach ( $post_content as $element ) {
					$element_instance = Plugin::$instance->renderer->get_element_instance( $element['uid'] );
					$this->enqueue_element_assets( $element_instance );
				}
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

		$element->enqueue_all_extra_scripts();

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
		$this->loaded_elements = [];

		foreach ( $this->post_ids as $post_id ) {
			$post_content = Plugin::$instance->renderer->get_content_for_area( $post_id );

			// Load elements css/js from scripts files
			foreach ( $post_content as $element ) {
				$element_instance = Plugin::$instance->renderer->get_element_instance( $element['uid'] );

				if ( $element_instance ) {
					$this->extract_element_assets( $element_instance );
				}
			}

			// Add the css that was registered by the elements
			$extra_area_css = self::get_css_for_area( $post_id );
			$this->add_raw_css( $extra_area_css );
		}

		return $this;
	}

	public function extract_element_assets( $element_instance ) {
		$element_type = $element_instance->get_type();

		if ( ! isset( $this->loaded_elements[$element_type] ) ) {
			$element_styles  = $element_instance->get_element_styles();
			$element_scripts = $element_instance->get_element_scripts();

			foreach ( $element_styles as $style_url ) {
				$style_path = Utils::get_file_path_from_url( $style_url );
				$this->add_raw_css( FileSystem::get_file_system()->get_contents( $style_path ) );
			}

			foreach ( $element_scripts as $script_url ) {
				$script_path = Utils::get_file_path_from_url( $script_url );
				$this->js   .= FileSystem::get_file_system()->get_contents( $script_path );
			}

			$this->loaded_elements[$element_type] = true;
		}

		// Check for children
		$children = $element_instance->get_children();
		if ( is_array( $children ) ) {
			foreach ( $children as $element ) {
				$child_element = Plugin::$instance->renderer->get_element_instance( $element['uid'] );
				$this->extract_element_assets( $child_element );
			}
		}

	}

	/**
	 * Will save the files to server
	 *
	 * @return PageAssets
	 */
	public function save() {
		FileSystem::get_file_system()->put_contents( $this->get_path_for_asset(), $this->minify( $this->css ), 0644 );

		$js = $this->js;
		if ( ! empty( $js ) ) {
			$js = $this->wrap_javascript( $js );
		}

		FileSystem::get_file_system()->put_contents( $this->get_path_for_asset( 'js' ), $js, 0644 );

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
				wp_enqueue_style( $this->file_handle, $css_url, [], $this->get_cache_version( $css_path ) );
			}

			if ( 0 !== FileSystem::get_file_system()->size( $js_path ) ) {
				wp_enqueue_script( $this->file_handle, $js_url, [], $this->get_cache_version( $js_path ), true );
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
		return $cache_directory['url'] . $this->file_handle . '.' . $type;
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

	public function wrap_javascript( $js ) {
		if ( ! empty( $js ) ) {
			$js = sprintf(
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
		}

		return $js;
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
	private function get_cache_version( $file_path ) {
		return (string) filemtime( $file_path );
	}
}
