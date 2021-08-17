<?php

namespace ZionBuilder;

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

	public function __construct() {
		$this->setup_environment();

		// Set dynamic public path
		add_action( 'wp_head', [ $this, 'print_public_path' ], -1000 );
		add_action( 'zionbuilder/editor/before_scripts', [ $this, 'print_public_path' ], -1000 );
		add_action( 'admin_print_scripts', [ $this, 'print_public_path' ], -1000 );
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

	/**
	 * Adds several JavaScript variables that will be used by Webpack generated files
	 *
	 * @return void
	 */
	public function print_public_path() {
		echo sprintf(
			'
				<script type="text/javascript">
					window.zionBuilderPaths = window.zionBuilderPaths || {};
					window.zionBuilderPaths["%s"] = "%s";
				</script>
			',
			esc_attr( Environment::get_value( 'appName' ) ),
			esc_url( $this->assets_root_url )
		);
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
		wp_register_script( $handle, $this->get_script_url( $src ), $deps, $ver, $in_footer );
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
		wp_enqueue_script( $handle, $this->get_script_url( $src ), $deps, $ver, $in_footer );
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
		wp_register_style( $handle, $this->get_script_url( $src ), $deps, $ver, $media );
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
		wp_enqueue_style( $handle, $this->get_script_url( $src ), $deps, $ver, $media );
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
	public function get_script_url( $path ) {
		$root_url = $this->assets_root_url;
		return $root_url . $path;
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
