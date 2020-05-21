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
            $providerID = $provider::get_id();
            if( ! empty( $providerID ) ) {
                $this->font_providers[ $providerID ] = $provider;
            }
        }
        catch ( \Exception $e ) {
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
