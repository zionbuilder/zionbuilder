<?php

namespace ZionBuilder\FontsManager;

use ZionBuilder\FontsManager\Fonts\GoogleFonts;
use ZionBuilder\FontsManager\FontProvider;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class FontsManager
 *
 * @package ZionBuilder\FontsManager
 */
class FontsManager {
	private $font_providers = [];

	/**
	 * Main class constructor
	 */
	public function __construct() {
		$this->register_font_provider( new GoogleFonts() );
	}

	public function register_font_provider( FontProvider $provider ) {
		try {
			$provider_id = $provider::get_id();
			if ( ! empty( $provider_id ) ) {
				$this->font_providers[ $provider_id ] = $provider;
			}
		} catch ( \Exception $e ) {
			_doing_it_wrong( 'FontsManager', esc_html__( 'get_id must be implemented by the font provider', 'zionbuilder' ), '1.0.0' );
		}
	}

	public function get_provider( $provider_id ) {
		if ( isset( $this->font_providers[$provider_id] ) ) {
			return $this->font_providers[$provider_id];
		}

		return false;
	}

	public function get_data_sets() {
		$sets = [];
		foreach ( $this->font_providers as $provider ) {
			$data_set = $provider->get_data_set();

			if ( $data_set ) {
				$sets[$provider::get_id()] = $data_set;
			}
		}

		return $sets;
	}
}
