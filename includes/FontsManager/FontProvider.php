<?php

namespace ZionBuilder\FontsManager;

class FontProvider {
	public function __construct() {
	}

	/**
	 * Returns the unique id for the font provider
	 *
	 * @throws \Exception
	 *
	 * @return string The unique id for the font provider
	 */
	public static function get_id() {
		throw new \Exception( esc_html__( 'get_id must be implemented by font provider.', 'ziobuilder' ) );
	}

	/**
	 * Returns the data that will be passed to the editor JavaScript files
	 *
	 * @return mixed
	 */
	public function get_data_set() {
		return false;
	}
}
