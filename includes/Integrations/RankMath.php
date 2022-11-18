<?php

namespace ZionBuilder\Integrations;

use ZionBuilder\Plugin;
use ZionBuilder\CommonJS;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Gutenberg
 *
 * @package ZionBuilder\Integrations
 */
class RankMath implements IBaseIntegration {
	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'rank_math';
	}


	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return class_exists( 'RankMath' );
	}


	/**
	 * Main class constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'on_enqueue_scripts' ] );
	}


	/**
	 * This method will fire when the scripts are enqueued in the admin area and
	 *      will add the Rank Math integration scripts on edit page screen
	 *
	 * @param string $hook
	 *
	 * @return void
	 */
	public function on_enqueue_scripts( $hook ) {
		if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
			// Load the scripts
			CommonJS::register_scripts();

			Plugin::instance()->scripts->enqueue_script(
				'zb-rankmath',
				'js/integrations/rankmath.js',
				[
					'wp-hooks',
					'rank-math-analyzer',
				],
				Plugin::instance()->get_version(),
				true
			);
		}
	}
}
