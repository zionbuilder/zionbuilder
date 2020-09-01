<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Frontend
 *
 * @package ZionBuilder
 */
class Frontend {

	/**
	 * Sets the content filter priority
	 */
	const CONTENT_FILTER_PRIORITY = 9;

	/**
	 * Holds a reference to the current post id
	 * Holds a reference to the elements registered on the current page instances
	 *
	 * @var int
	 * @var array
	 */
	private $post_id = null;

	/**
	 * Holds a reference to the registered post areas that needs to render on the current page
	 *
	 * @var array
	 */
	private $registered_areas = [];

	/**
	 * Holds a reference to the elements registered on the current page instances
	 *
	 * @var array
	 */
	private $instantiated_elements = [];

	/**
	 * Holds a refference if this is excerpt or not
	 *
	 * @var boolean
	 */
	private $is_excerpt = null;

	/**
	 * Main class constructor
	 *
	 * Runs init function on WP action. At this point, the global query is set
	 */
	public function __construct() {
		// No need for this in admin
		if ( is_admin() ) {
			return;
		}

		// Check to see if we need to show the pagebuilder content
		add_action( 'template_redirect', [ $this, 'init' ] );
	}

	public function init() {
		// Allow clients to register their own areas
		do_action( 'zionbuilder/frontend/init', $this );

		// Don't run on preview mode
		if ( ! Plugin::$instance->editor->preview->is_preview_mode() ) {
			$post_id = Plugin::$instance->post_manager->get_active_post_id();
			$this->prepare_content_for_post_id( $post_id );
		}

		// Allow others to add their own areas
		do_action( 'zionbuilder/frontend/after_init', $this );

		// Load elements scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function prepare_content_for_post_id( $post_id ) {
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post_id );

		if ( ! $post_instance ) {
			return;
		}

		// Load post content data
		if ( ! $post_instance->is_password_protected() && $post_instance->is_built_with_zion() ) {
			$post_template_data = $post_instance->get_template_data();

			if ( ! empty( $post_template_data ) ) {
				Plugin::$instance->renderer->register_area( 'content', $post_template_data );

				// Add content filters
				add_filter( 'get_the_excerpt', [ $this, 'add_excerpt_flag' ], 0 );
				add_filter( 'get_the_excerpt', [ $this, 'remove_excerpt_flag' ], 99 );
				add_filter( 'the_content', [ $this, 'add_pagebuilder_content' ], self::CONTENT_FILTER_PRIORITY );

				// Register styles cache file for current page
				Plugin::$instance->cache->register_post_id( $post_instance->get_post_id() );
			}
		}

		// Instantiate all elements that are present on the current page
		Plugin::$instance->renderer->prepare_areas_for_render();
	}

	/**
	 * Sets the excerpt flag to true
	 *
	 * This is needed to check that the 'the_content' filter runs on excerpt or not
	 *
	 * @param mixed $excerpt
	 *
	 * @return string Returns the excerpt string
	 */
	public function add_excerpt_flag( $excerpt ) {
		$this->is_excerpt = true;

		return $excerpt;
	}

	/**
	 * Sets the excerpt flag to false
	 *
	 * This is needed to check that the 'the_content' filter runs on excerpt or not
	 *
	 * @param mixed $excerpt
	 *
	 * @return string Returns the excerpt string
	 */
	public function remove_excerpt_flag( $excerpt ) {
		$this->is_excerpt = false;

		return $excerpt;
	}


	/**
	 * Returns the excerpt flag status
	 *
	 * @return boolean
	 */
	public function is_excertpt() {
		return $this->is_excerpt;
	}

	public function remove_content_filter( $content = '' ) {
		remove_filter( 'the_content', [ $this, 'add_pagebuilder_content' ], self::CONTENT_FILTER_PRIORITY );

		return $content;
	}

	/**
	 * Add Pagebuilder Content
	 *
	 * Renders the content for the current page inside the post content ( the_content filter )
	 *
	 * @param string $content The current page content
	 *
	 * @return string The generated HTML content for the current page
	 */
	public function add_pagebuilder_content( $content ) {
		$this->restore_content_filters();

		// Don't run on excerpt
		if ( $this->is_excertpt() ) {
			return $content;
		}

		$pb_content = Plugin::$instance->renderer->get_content();
		if ( ! empty( $pb_content ) ) {
			$content = $pb_content;
			// Remove filters that may affect content
			$this->remove_content_filters();
		}

		return $content;
	}


	/**
	 * Remove content filters
	 *
	 * Will remove filters that may affect pagebuilder content
	 */
	private function remove_content_filters() {
		$filters_to_remove = [
			'wpautop',
			'wptexturize',
		];

		foreach ( $filters_to_remove as $filter ) {
			remove_filter( 'the_content', $filter );
		}
	}

	public function restore_content_filters() {
		add_filter( 'the_content', 'wpautop' );
		add_filter( 'the_content', 'wptexturize' );
	}

	public function enqueue_styles() {
		// Trigger action before load styles
		do_action( 'zionbuilder/frontend/before_load_styles', $this );

		// Load styles
		Plugin::instance()->scripts->enqueue_style(
			'zion-frontend-style',
			'css/frontend.css',
			[],
			Plugin::instance()->get_version()
		);

		// Load animations
		wp_enqueue_style( 'zion-frontend-animations', plugins_url( 'zionbuilder/assets/vendors/css/animate.css' ), [], Plugin::instance()->get_version() );

		do_action( 'zionbuilder/frontend/after_load_styles', $this );
	}

	public function enqueue_scripts() {
		do_action( 'zionbuilder/frontend/before_load_scripts', $this );

		do_action( 'zionbuilder/frontend/after_load_scripts', $this );
	}
}
