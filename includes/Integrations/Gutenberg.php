<?php

namespace ZionBuilder\Integrations;

use WP_Post;
use ZionBuilder\Plugin;
use ZionBuilder\Permissions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Gutenberg
 *
 * @package ZionBuilder\Integrations
 */
class Gutenberg implements IBaseIntegration {
	/**
	 * Whether or not the Gutenberg editor is enabled
	 *
	 * @var bool
	 */
	private $is_gutenberg_active = false;

	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'gutenberg';
	}


	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return function_exists( 'register_block_type' );
	}


	/**
	 * Main class constructor
	 */
	public function __construct() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'load_scripts' ] );
		add_action( 'admin_footer', [ $this, 'print_admin_js_template' ] );
		add_action( 'rest_api_init', [ $this, 'create_api_posts_meta_field' ] );
	}


	/**
	 * Is gutenberg active
	 *
	 * Checks if gutenberg is active on the page
	 *
	 * @return boolean
	 */
	public function is_gutenberg_active() {
		return $this->is_gutenberg_active;
	}

	/**
	 * Load Scripts
	 *
	 * Load Gutenberg integration script
	 *
	 * @return void
	 */
	public function load_scripts() {
		$this->is_gutenberg_active = true;

		Plugin::instance()->scripts->enqueue_script(
			'znpb-admin-gutenberg-integration',
			'js/gutenberg.js',
			[
				'wp-plugins',
				'wp-edit-post',
				'wp-i18n',
				'znpb-admin-post-script',
			],
			Plugin::instance()->get_version(),
			true
		);

	}


	/**
	 * Print admin js template
	 *
	 * Will print the editor buttons to edit post footer if gutenberg is enabled on the page
	 *
	 * @return void
	 */
	public function print_admin_js_template() {
		if ( ! $this->is_gutenberg_active() ) {
			return;
		}

		global $post;
		if ( $post instanceof WP_Post ) {
			?>
			<script id="zionbuilder-gutenberg-buttons" type="text/html">
				<?php Plugin::$instance->admin->add_editor_button_to_page( $post ); ?>
			</script>
			<script id="zionbuilder-gutenberg-editor-block" type="text/html">
				<?php Plugin::$instance->admin->add_editor_block( $post ); ?>
			</script>
			<?php
		}
	}

	public function create_api_posts_meta_field() {
		$allowed_post_types = Permissions::get_allowed_post_types();

		register_rest_field(
			$allowed_post_types,
			'zion_builder_status',
			[
				'update_callback' => [ $this, 'set_editor_status' ],
				'get_callback'    => [ $this, 'get_editor_status' ],
			]
		);
	}

	public function set_editor_status( $value, $post ) {
		// Don't proceed if the current user cannot edit this page
		if ( ! Permissions::can_edit_post( $post->ID ) ) {
			return;
		}

		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );

		if ( $post_instance ) {
			$post_instance->set_builder_status( $value );
		}
	}

	public function get_editor_status( $post ) {
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post['id'] );

		if ( $post_instance ) {
			return $post_instance->is_built_with_zion();
		}

		return false;
	}
}
