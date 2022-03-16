<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Utill
 *
 * Will handle several util methods
 *
 * @since 1.0.0
 */
class Utils {
	/**
	 * Check if pro version is active
	 *
	 * @return boolean if zion builder pro path is defined
	 */
	public static function is_pro_active() {
		return apply_filters( 'zionbuilder/utils/pro_active', class_exists( 'ZionBuilderPro\Plugin' ) );
	}

		/**
	 * Check if pro version is active
	 *
	 * @return boolean if zion builder pro path is defined
	 */
	public static function is_pro_installed() {
		return class_exists( 'ZionBuilderPro\Plugin' );
	}

	/**
	 * Returns true if the license is valid
	 *
	 * @return boolean
	 */
	public static function has_valid_license() {
		return self::is_pro_active() && class_exists( '\ZionBuilderPro\License' ) && \ZionBuilderPro\License::has_valid_license();
	}

	/**
	 * Get Logo URL
	 *
	 * Returns the builder logo icon
	 *
	 * @return string The URL to the Page Builder logo
	 */
	public static function get_logo_url() {
		return apply_filters( 'zionbuilder/utils/logo_url', self::get_file_url( 'assets/img/zion-icon-dark.svg' ) );
	}


	/**
	 * Will generate an unique id
	 *
	 * @param string $prepend
	 * @param integer $length
	 *
	 * @return string
	 */
	public static function generate_uid( $prepend = 'uid', $length = 12 ) {
		return $prepend . substr( str_shuffle( MD5( microtime() ) ), 0, $length );
	}

	/**
	 * Returns the zion builder PRO logo URL
	 *
	 * @return string
	 */
	public static function get_pro_png_url() {
		return apply_filters( 'zionbuilder/utils/pro_png_url', self::get_file_url( 'assets/img/get-pro-illustration.png' ) );
	}

	/**
	 * Returns the URL to the image loader used in various plugin locations
	 *
	 * @return string
	 */
	public static function get_loader_url() {
		return apply_filters( 'zionbuilder/utils/loader_url', self::get_file_url( 'assets/img/zion-loader-slim--small.gif' ) );
	}

	/**
	 * Get File Path
	 *
	 * Will return the file path starting with the plugin directory for the given path
	 *
	 * @param string $path The path that will be appended to the plugin path
	 *
	 * @return string
	 */
	public static function get_file_path( $path = '' ) {
		return Plugin::instance()->get_root_path() . $path;
	}

	/**
	 * Get File URL
	 *
	 * Will return the file url starting with the plugin directory for the given path
	 *
	 * @param string $path The path that will be appended to the plugin URL
	 * @param string|null $scheme Optional. Scheme to give $url. Currently 'http', 'https', 'login',
	 *                      'login_post', 'admin', 'relative', 'rest', 'rpc', or null. Default null.
	 *
	 * @return string
	 */
	public static function get_file_url( $path = '', $scheme = null ) {
		return set_url_scheme( Plugin::instance()->get_root_url() . $path, $scheme );
	}

	/**
	 * Get Directory Info
	 *
	 * Returns the directory url and path for a given file/path
	 *
	 * @param mixed $path
	 *
	 * @return string
	 */
	public static function get_file_url_from_path( $path ) {
		// Set base URI
		$theme_base = get_template_directory();

		// Normalize paths
		$theme_base = wp_normalize_path( $theme_base );
		$path       = wp_normalize_path( $path );

		$is_theme       = preg_match( '#' . $theme_base . '#', $path );
		$directory_uri  = ( $is_theme ) ? get_template_directory_uri() : plugins_url();
		$directory_path = ( $is_theme ) ? $theme_base : \WP_PLUGIN_DIR;
		$fw_basename    = str_replace( wp_normalize_path( $directory_path ), '', $path );

		return $directory_uri . $fw_basename;
	}

	/**
	 * Get Directory Info
	 *
	 * Returns the directory url and path for a given file/path
	 *
	 * @param mixed $path
	 *
	 * @return string
	 */
	public static function get_file_path_from_url( $path ) {
		// Set base URI
		$theme_base = get_template_directory_uri();
		$path       = wp_normalize_path( $path );

		$is_theme       = preg_match( '#' . $theme_base . '#', $path );
		$directory_path = ( $is_theme ) ? get_template_directory() : \WP_PLUGIN_DIR;
		$directory_uri  = ( $is_theme ) ? $theme_base : plugins_url();
		$fw_basename    = str_replace( wp_normalize_path( $directory_uri ), '', $path );

		return $directory_path . $fw_basename;
	}


	/**
	 * Convert a string to camelcase
	 *
	 * @param string  $string
	 * @param boolean $capitalize_first_letter
	 *
	 * @return string The string in camelcase format
	 */
	public static function camel_case( $string, $capitalize_first_letter = false ) {
		$str = str_replace( ' ', '', ucwords( str_replace( [ '-', '_' ], ' ', $string ) ) );

		if ( ! $capitalize_first_letter ) {
			$str[0] = strtolower( $str[0] );
		}

		return $str;
	}
}
