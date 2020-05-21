<?php

namespace ZionBuilder;

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
			$post_instance = Plugin::$instance->post_manager->get_active_post_instance_for_render();

			if ( ! $post_instance ) {
				return;
			}

			// Load post content data
			if ( ! $post_instance->is_password_protected() && $post_instance->is_built_with_zion() ) {
				$post_template_data = $post_instance->get_template_data();

				if ( ! empty( $post_template_data ) ) {
					// Register content area
					$this->register_area( 'content', $post_template_data );

					// Add content filters
					add_filter( 'the_content', [ $this, 'add_pagebuilder_content' ], self::CONTENT_FILTER_PRIORITY );

					// Register styles cache file for current page
					Plugin::$instance->cache->register_post_id( $post_instance->get_post_id() );
				}
			}
		}

		// Instantiate all elements that are present on the current page
		$this->prepare_areas_for_render();

		// Allow others to add their own areas
		do_action( 'zionbuilder/frontend/after_init', $this );

		// Load elements scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
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
		// Run only once on main content. This allows other the_content to render properly
		$this->remove_content_filter();
		$this->remove_content_filters();

		// Remove filters that affect content
		$content = $this->get_content();

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
		];

		foreach ( $filters_to_remove as $filter ) {
			remove_filter( 'the_content', $filter );
		}
	}

	public function restore_content_filters() {
		add_filter( 'the_content', 'wpautop' );
	}

	private function get_content() {
		ob_start();
		$this->render_area( 'content' );
		return ob_get_clean();
	}

	/**
	 * Get Elements Instances
	 *
	 * Returns an arary containing all element instances for the current page
	 *
	 * @return array
	 */
	public function get_elements_instances() {
		return $this->instantiated_elements;
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
		wp_enqueue_style( 'zion-frontend-animations', plugins_url( 'zionbuilder/assets/vendors/css/animate.css' ), [], ZIONBUILDER_VERSION );

		do_action( 'zionbuilder/frontend/after_load_styles', $this );
	}

	public function enqueue_scripts() {
		do_action( 'zionbuilder/frontend/before_load_scripts', $this );

		do_action( 'zionbuilder/frontend/after_load_scripts', $this );
	}

	/**
	 * Get Registered Areas
	 *
	 * Returns an array containing all registered areas and their data
	 *
	 * @return array
	 */
	public function get_registered_areas() {
		return $this->registered_areas;
	}


	/**
	 * Get Content For Area
	 *
	 * Returns the area content data for the given area id
	 *
	 * @param string $area_id
	 *
	 * @return []
	 */
	public function get_content_for_area( $area_id ) {
		if ( ! empty( $this->registered_areas[$area_id] ) && is_array( $this->registered_areas[$area_id] ) ) {
			return $this->registered_areas[$area_id];
		}

		return [];
	}

	/**
	 * Register Area
	 *
	 * Register a new area of elements
	 *
	 * @param string $area_name
	 * @param array  $area_template_data
	 *
	 * @return void
	 */
	public function register_area( $area_name, $area_template_data ) {
		$this->registered_areas[$area_name] = $area_template_data;
	}


	/**
	 * Register Element Instance
	 *
	 * Registers the element instances so we can use them
	 *
	 * @param array $element_data
	 *
	 * @return void
	 */
	private function register_element_instance( $element_data ) {
		$element_instance_with_data = Plugin::$instance->elements_manager->get_element_instance_with_data( $element_data );

		// Don't proceed if we do not have an element instance
		if ( false === $element_instance_with_data || ! isset( $element_data['uid'] ) ) {
			return;
		}

		$this->instantiated_elements[$element_data['uid']] = $element_instance_with_data;

		// Check if element has children
		$element_children = $element_instance_with_data->get_children();

		// Instantiate all children elements
		if ( ! empty( $element_children ) && is_array( $element_children ) ) {
			foreach ( $element_children as $child_element_data ) {
				$this->register_element_instance( $child_element_data );
			}
		}
	}


	/**
	 * Prepare content for render
	 *
	 * Instantiate all elements that should be rendered on the current page
	 *
	 * @param mixed $area_id
	 *
	 * @return void
	 */
	public function prepare_areas_for_render() {
		foreach ( $this->get_registered_areas() as $area_name => $area_template_data ) {
			if ( is_array( $area_template_data ) ) {
				foreach ( $area_template_data as $element_data ) {
					$this->register_element_instance( $element_data );
				}
			}
		}
	}

	public function render_area( $area_id ) {
		echo '<div class="zb zb-area-' . esc_attr( $area_id ) . '">';
		$this->render_children( $this->get_content_for_area( $area_id ) );
		echo '</div>';
	}

	/**
	 * Render Children
	 *
	 * Will loop trough all provided elements and will render them
	 *
	 * @param array $children Array containing all children elements
	 *
	 * @return void
	 */
	public function render_children( $children ) {
		foreach ( $children as $element_id => $element_data ) {
			$this->render_element( $element_data );
		}
	}

	/**
	 * Render a single element
	 *
	 * @param array $element_data
	 * @param array $extra_data
	 *
	 * @return void
	 */
	public function render_element( $element_data, $extra_data = [] ) {
		if ( isset( $element_data['uid'] ) && isset( $this->instantiated_elements[$element_data['uid']] ) ) {
			$this->instantiated_elements[$element_data['uid']]->render_element( $extra_data );
		}
	}
}
