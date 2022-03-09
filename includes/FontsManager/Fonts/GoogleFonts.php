<?php

namespace ZionBuilder\FontsManager\Fonts;

use ZionBuilder\Plugin;
use ZionBuilder\Settings;
use ZionBuilder\FontsManager\FontProvider;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class GoogleFonts
 *
 * Admin class
 *
 * Will handle all interactions with the WordPress admin area and the page builder
 *
 * @package ZionBuilder\FontsManager\Fonts
 */
class GoogleFonts extends FontProvider {
	public static function get_id() {
		return 'google_fonts';
	}

	/**
	 * Main class constructor
	 *
	 * Will load the scripts into 'zionbuilder/frontend/before_load_styles'
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'zionbuilder/frontend/before_load_styles', [ $this, 'enqueue_fonts' ] );
	}

	/**
	 * This function will return the google fonts from options
	 *
	 * @return array google fonts
	 */
	public static function get_fonts() {
		return Settings::get_value( 'google_fonts', [] );
	}


	/**
	 * Returns the list of Google fonts that can be used in the builder
	 *
	 * @return array
	 */
	public function get_data_set() {
		$fonts          = $this->get_fonts();
		$returned_value = [];

		foreach ( $fonts as $font_config ) {
			$returned_value[] = [
				'name' => $font_config['font_family'],
				'id'   => $font_config['font_family'],
			];
		}

		return $returned_value;
	}

	/**
	 *  This function return the google fonts URL
	 *
	 *  Built based on google fonts options
	 *
	 * @return string|boolean google fonts url eg: //fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700|Ubuntu:400,700,400italic,700italic&subset=latin,latin-ext
	 */
	public static function get_enqueue_link() {
		$fonts = self::get_fonts();

		if ( empty( $fonts ) ) {
			return false;
		}

		// Check if we need to load the fonts locally
		if ( Settings::get_value( 'performance.local_google_fonts', false ) === true ) {
			$local_fonts_instance = new LocalGoogleFonts( $fonts );

			if ( ! $local_fonts_instance->stylesheet_exists() ) {
				$local_fonts_instance->process_fonts();
			}

			return $local_fonts_instance->get_stylesheet_url();
		}

		// Don't place this in 'else' so e can use it as a fallback
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

		//This will be used for both local fonts and Google fonts
		$combined_fonts_string = implode( '|', $font_links ) . '&subset=' . implode( ',', $font_subsets ) . '&display=' . Settings::get_value( 'performance.fonts_display', 'swap' );

		$fonts_url_base = '//fonts.googleapis.com/css?family=';
		return $fonts_url_base . $combined_fonts_string;
	}

	/**
	 * This function call wp_enqueue_style with the font url from get_enqueue_link
	 *
	 * @return void
	 */
	public static function enqueue_fonts() {
		$enque_link = self::get_enqueue_link();

		// check if we have fonts
		if ( $enque_link ) {
			wp_enqueue_style( 'zion-google-fonts', $enque_link, [], Plugin::instance()->get_version() );
		}
	}
}
