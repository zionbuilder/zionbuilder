<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Whitelabel
 * @package ZionBuilder
 */
class Whitelabel {

	/**
	 * Holds a reference to the plugin logo URL
	 *
	 * @var string
	 */
	public $logo_url = '';

	/**
	 * Holds a reference to the uploads directory to use for the plugin
	 *
	 * @var string
	 */
	public $uploads_folder = null;

	/**
	 * Whitelabel constructor.
	 */
	public function __construct() {
		$this->logo_url       = apply_filters( 'zionbuilder/whitelabel/logo', 'TODO_LOGO_PATH' );
		$this->uploads_folder = apply_filters( 'zionbuilder/whitelabel/logo', 'zionbuilder' );
	}

	/**
	 * @return string|null
	 */
	public function get_uploads_folder() {
		return $this->uploads_folder;
	}

	/**
	 * @return string
	 */
	public function get_logo_url() {
		return $this->logo_url;
	}
}
