<?php
namespace ZionBuilder\FontsManager\Fonts;

use ZionBuilder\FileSystem;
use ZionBuilder\Settings;

class LocalGoogleFonts {
	/**
	 * Holds the name of the directory to use by default for assets config
	 */
	const CACHE_FOLDER_NAME = 'google_fonts';


	/**
	 * Url to the API that allows us to download the fonts
	 */
	const GOOGLE_DOWNLOAD_API_URL = 'https://gwfh.mranftl.com/api/fonts/';

	/**
	 * Holds a reference to the folder where we keep the fonts and stylesheets
	 *
	 * @var array
	 */
	private static $cache_directory_config = null;


	/**
	 * Holds a reference to the provided fonts config
	 *
	 * @var array
	 */
	private $fonts = [];

	/**
	 * Holds a reference to the font display
	 *
	 * @var string
	 */
	private $font_display = 'auto';


	/**
	 * Holds a reference to the stylesheet handle
	 *
	 * @var string
	 */
	private $stylesheet_handle = '';

	public function __construct( $fonts = [] ) {
		$this->fonts             = $fonts;
		$this->stylesheet_handle = $this->get_handle( $this->fonts );
		$this->font_display      = Settings::get_value( 'performance.fonts_display', 'auto' );
	}

	/**
	 * Returns a unique string for the configured fonts
	 *
	 * @return string
	 */
	public function get_handle( $fonts ) {

		$font_subsets = [];
		$font_links   = [];

		foreach ( $fonts as $font ) {
			// get font subsets
			foreach ( $font['font_subset'] as $subset ) {
				if ( ! in_array( $subset, $font_subsets, true ) ) {
					$font_subsets[] = $subset;
				}
			}

			$font_links[] = str_replace( ' ', '+', $font['font_family'] ) . ':' . implode( ',', $font['font_variants'] );
		}

		$string = implode( '|', $font_links ) . '&subset=' . implode( ',', $font_subsets );

		return md5( $string );
	}

	/**
	 * Get Cache Directory
	 *
	 * Returns the cache directory config
	 *
	 * @return array{path: string, url: string} An array containing cache directory path and url
	 */
	private static function get_cache_directory() {
		if ( null === self::$cache_directory_config ) {
			$relative_cache_path       = trailingslashit( self::CACHE_FOLDER_NAME );
			$zionbuilder_upload_folder = FileSystem::get_zionbuilder_upload_dir();

			wp_mkdir_p( $zionbuilder_upload_folder['basedir'] . $relative_cache_path );

			self::$cache_directory_config = [
				'path' => $zionbuilder_upload_folder['basedir'] . $relative_cache_path,
				'url'  => esc_url( set_url_scheme( $zionbuilder_upload_folder['baseurl'] . $relative_cache_path ) ),
			];

		}

		return self::$cache_directory_config;
	}

	public function get_file_path( $filename ) {
		$cache_directory = self::get_cache_directory();
		return trailingslashit( $cache_directory['path'] ) . $filename;
	}

	public function get_file_url( $filename ) {
		$cache_directory = self::get_cache_directory();
		return trailingslashit( $cache_directory['url'] ) . $filename;
	}


	public function get_stylesheet_url() {
		return $this->get_file_url( $this->stylesheet_handle . '.css' );
	}

	public function get_stylesheet_path() {
		return $this->get_file_path( $this->stylesheet_handle . '.css' );
	}


	/**
	 * Check if the stylesheet exists on server
	 *
	 * @return boolean
	 */
	public function stylesheet_exists() {
		return file_exists( $this->get_stylesheet_path() );
	}

	public function process_fonts() {
		$allowed_font_extension      = [ 'woff2', 'woff', 'eot', 'ttf', 'svg' ];
		$fonts_config_for_stylesheet = [];
		foreach ( $this->fonts as $font ) {

			$font_info = $this->get_font_info( $font );
			$files     = [];

			// If this is empty, something went wrong
			if ( empty( $font_info ) ) {
				continue;
			}

			// Download the font
			foreach ( $font_info->variants as $variant ) {
				// Check if the variants should be downloaded and processed
				if ( ! in_array( $variant->id, $font['font_variants'], true ) ) {
					continue;
				}

				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$filename = strtolower( $font_info->id . '-' . $variant->fontStyle . '-' . $variant->fontWeight );

				foreach ( $allowed_font_extension as $extension ) {
					if ( isset( $variant->$extension ) ) {
						// Check to see if we need to download
						if ( ! file_exists( $this->get_file_path( $filename . '.' . $extension ) ) ) {
							$url  = $variant->$extension;
							$path = $this->get_file_path( $filename . '.' . $extension );
							$this->download_font( $url, $path );
						}

						// Get the files
						$files[$extension] = $this->get_file_url( $filename . '.' . $extension );
					}
				}

				$fonts_config_for_stylesheet[] = [
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					'font_family' => $variant->fontFamily,
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					'font_style'  => $variant->fontStyle,
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					'font_weight' => $variant->fontWeight,
					'files'       => $files,
				];
			}
		}

		// Generate stylesheet
		$this->generate_stylesheet( $fonts_config_for_stylesheet );
	}

	public function download_font( $url, $path ) {
		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		$tmp_file = download_url( $url );

		if ( is_wp_error( $tmp_file ) ) {
			return '';
		}

		copy( $tmp_file, $path );
		// phpcs:ignore
		@unlink( $tmp_file );
	}

	public function get_font_info( $font ) {
		$font_family = strtolower( preg_replace( '/[\s\+]+/', '-', $font['font_family'] ) );
		$url         = self::GOOGLE_DOWNLOAD_API_URL . $font_family . '?subsets=' . implode( ',', $font['font_subset'] );
		$response    = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			// TODO: add a general error system in both frontend and admin
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions
			error_log( '[ZionBuilder local google fonts] ' . $response->get_error_message() );
			return [];
		}

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	public function generate_stylesheet( $fonts ) {
		$stylesheet = '';

		foreach ( $fonts as $font_config ) {
			$stylesheet .= "@font-face {\n";
			$stylesheet .= "	font-family: {$font_config['font_family']};\n";
			$stylesheet .= "	font-style: {$font_config['font_style']};\n";
			$stylesheet .= "	font-weight: {$font_config['font_weight']};\n";
			$stylesheet .= "	font-display: {$this->font_display};\n"; //swap

			$files = $font_config['files'];

			// EOT doesn't need the format
			if ( isset( $files['eot'] ) ) {
				$stylesheet .= "	src: url( '{$files['eot']}' );\n";
				unset( $files['eot'] );
			}

			$stylesheet .= "	src: {$this->generate_font_source($files)};\n"; //swap
			$stylesheet .= '}';
		}

		// Write the file
		FileSystem::get_file_system()->put_contents( $this->get_stylesheet_path(), $stylesheet, 0644 );
	}

	public function generate_font_source( $files ) {
		$count  = count( $files );
		$i      = 1;
		$return = '';

		foreach ( $files as $extension => $url ) {
			$delimiter = $i === $count ? ';' : ',';
			$type      = $extension === 'ttf' ? 'truetype' : $extension;
			$return   .= "	url( '{$url}' ) format( '{$type}' ){$delimiter}\n";

			$i++;
		}

		return $return;
	}

	public static function delete_cache() {
		$cache_directory = self::get_cache_directory();
		return FileSystem::get_file_system()->delete( $cache_directory['path'], true );
	}

}
