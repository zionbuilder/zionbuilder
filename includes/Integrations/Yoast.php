<?php

namespace ZionBuilder\Integrations;

use ZionBuilder\Plugin;
use ZionBuilder\CommonJS;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Yoast
 *
 * @package ZionBuilder\Integrations
 */
class Yoast implements IBaseIntegration {
	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'yoast';
	}


	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return defined( 'WPSEO_VERSION' );
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
		global $post;

		if ( ( 'post-new.php' === $hook || 'post.php' === $hook ) && ! is_null( $post ) ) {
			// Load the scripts
			wp_enqueue_script(
				'zb-yoast',
				Plugin::instance()->scripts->get_script_url( 'integrations/yoast', 'js' ),
				[ 'yoast-seo-edit-page' ],
				Plugin::instance()->get_version(),
				true
			);

			wp_localize_script(
				'zb-yoast',
				'zb_yoast_data',
				array(
					'page_content' => $this->get_page_content( $post->ID ),
				)
			);
		}
	}

	public function get_page_content( $post_id ) {
		$post_instance = Plugin::$instance->post_manager->get_post_or_autosave_instance( $post_id );
		if ( ! $post_instance ) {
			return;
		}

		$post_template_data = $post_instance->get_template_data();
		Plugin::$instance->renderer->register_area( $post_id, $post_template_data );
		ob_start();
		Plugin::$instance->renderer->render_area( $post_id );
		return ob_get_clean();
	}
}
