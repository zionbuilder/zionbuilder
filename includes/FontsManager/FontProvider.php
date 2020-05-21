<?php

namespace ZionBuilder\FontsManager;

class FontProvider {
	public function __construct() {
	}

    /**
     * @throws \Exception
     */
	public static function get_id() {
		throw new \Exception( esc_html__( 'get_id must be implemented by font provider.', 'ziobuilder' ) );
	}

	public function get_data_set() {
		return false;
	}
}
