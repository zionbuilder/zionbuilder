<?php

namespace ZionBuilder\Integrations;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Gutenberg
 *
 * @package ZionBuilder\Integrations
 */
class HappyFiles implements IBaseIntegration {
	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'happyFiles';
	}


	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return class_exists( 'HappyFiles\Init' );
	}


	/**
	 * Main class constructor
	 */
	public function __construct() {
		/** @phpstan-ignore-next-line -- The class is provided by a 3rd party plugin */
		$instance = \HappyFiles\Init::run();
		add_action( 'zionbuilder/editor/after_scripts', [ $instance->setup, 'enqueue_scripts' ] );
	}
}
