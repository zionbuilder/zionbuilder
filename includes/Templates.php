<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Templates
 *
 * @package ZionBuilder
 */
class Templates {
	const TEMPLATE_POST_TYPE         = 'zion_template';
	const TEMPLATE_CATEGORY_TAXONOMY = 'zion_template_category';
	const TEMPLATE_TYPE_META         = 'zionbuilder_template_type';

	/**
	 * @var array
	 */
	private $template_types = [];

	/**
	 * Templates constructor.
	 */
	public function __construct() {
		// Enable editor for templates
		add_post_type_support( self::TEMPLATE_POST_TYPE, Permissions::POST_TYPE_EDIT_PERMISSION );

		add_filter( 'zionbuilder/permissions/get_allowed_post_types', [ $this, 'add_post_type_for_builder' ] );
		add_filter( 'zionbuilder/data_sets/post_types', [ $this, 'remove_post_type_from_data_sets' ] );

		add_action( 'init', [ $this, 'init' ] );
	}

	public function remove_post_type_from_data_sets( $post_types ) {
		$post_type_index = null;
		foreach ( $post_types as $key => $post_type ) {
			if ( $post_type['id'] === self::TEMPLATE_POST_TYPE ) {
				$post_type_index = $key;
				break;
			}
		}

		if ( null !== $post_type_index ) {
			array_splice( $post_types, $post_type_index, 1 );
		}

		return $post_types;
	}

	/**
	 * Add post type for builder
	 *
	 * Enables the templates to use the pagebuilder
	 *
	 * @param array $post_types The post types that are already registered
	 *
	 * @return array
	 */
	public function add_post_type_for_builder( $post_types ) {
		$post_types[] = self::TEMPLATE_POST_TYPE;

		return $post_types;
	}

	/**
	 * @throws \Exception
	 */
	public function init() {
		// Register default template types
		$this->register_default_template_types();

		// Allow others to register their own template types
		do_action( 'zionbuilder/templates/before_init' );

		$this->register_templates_post_types_and_taxonomy();
	}

	/**
	 * Registers Plugin default template types
	 */
	private function register_default_template_types() {
		$template_types = [
			[
				'name'          => __( 'Templates', 'zionbuilder' ),
				'singular_name' => __( 'Template', 'zionbuilder' ),
				'id'            => 'template',
			],
			[
				'name'          => __( 'Blocks', 'zionbuilder' ),
				'singular_name' => __( 'Block', 'zionbuilder' ),
				'id'            => 'block',
			],
		];

		foreach ( $template_types as $template_config ) {
			$this->register_template_type( $template_config );
		}
	}


	/**
	 * Get template types
	 *
	 * Returns a list of registered template types
	 *
	 * @return array
	 */
	public function get_template_types() {
		return $this->template_types;
	}


	/**
	 * Returns the template categories as list
	 *
	 * @return int|\WP_Error|\WP_Term[]
	 */
	public function get_template_categories() {
		return get_terms(
			[
				'taxonomy'   => self::TEMPLATE_CATEGORY_TAXONOMY,
				'hide_empty' => true,
			]
		);
	}

	/**
	 * Register template type
	 *
	 * Will register the template type
	 *
	 * @param array $template_type_config
	 *
	 * @throws \Exception
	 *
	 * @return \WP_Error|array
	 */
	public function register_template_type( $template_type_config ) {
		if ( ! is_array( $template_type_config ) ) {
			throw new \Exception( 'The template type must be an array containing name and id' );
		}

		if ( empty( $template_type_config['name'] ) ) {
			throw new \Exception( 'The template name must be provided' );
		}

		if ( empty( $template_type_config['id'] ) ) {
			throw new \Exception( 'The template id must be provided' );
		}

		// Check to see if the template type already exists
		if ( $this->is_template_type_registered( $template_type_config['id'] ) ) {
			return new \WP_Error( 'template_type_exists', esc_html__( 'The template type already exists.', 'zionbuilder' ) );
		}

		array_push( $this->template_types, $template_type_config );

		return $template_type_config;
	}


	/**
	 * Checks to see if a specific template type is already registered
	 *
	 * @param int $template_id
	 *
	 * @return boolean
	 */
	private function is_template_type_registered( $template_id ) {
		$exists = false;

		foreach ( $this->template_types as $template_config ) {
			if ( $template_config['id'] === $template_id ) {
				$exists = true;
				break;
			}
		}

		return $exists;
	}

	/**
	 * Registers the plugin post type and taxonomies used for templates
	 *
	 * @return void
	 */
	public function register_templates_post_types_and_taxonomy() {
		$this->register_post_type();
		$this->register_template_category_taxonomy();
	}

	/**
	 * Registers the plugin post type used for templates
	 *
	 * @return void
	 */
	private function register_post_type() {
		$labels = [
			'name'               => _x( 'Zion Templates', 'Zion Template Library', 'zionbuilder' ),
			'singular_name'      => _x( 'Template', 'Zion Template Library', 'zionbuilder' ),
			'add_new'            => _x( 'Add New', 'Zion Template Library', 'zionbuilder' ),
			'add_new_item'       => _x( 'Add New Template', 'Zion Template Library', 'zionbuilder' ),
			'edit_item'          => _x( 'Edit Template', 'Zion Template Library', 'zionbuilder' ),
			'new_item'           => _x( 'New Template', 'Zion Template Library', 'zionbuilder' ),
			'all_items'          => _x( 'All Templates', 'Zion Template Library', 'zionbuilder' ),
			'view_item'          => _x( 'View Template', 'Zion Template Library', 'zionbuilder' ),
			'search_items'       => _x( 'Search Template', 'Zion Template Library', 'zionbuilder' ),
			'not_found'          => _x( 'No Templates found', 'Zion Template Library', 'zionbuilder' ),
			'not_found_in_trash' => _x( 'No Templates found in Trash', 'Zion Template Library', 'zionbuilder' ),
			'menu_name'          => _x( 'Zion Templates', 'Zion Template Library', 'zionbuilder' ),
		];

		$args = [
			'label'               => 'Zion Templates',
			'labels'              => $labels,
			'public'              => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'query_var'           => true,
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => [ 'title', 'editor', 'author', 'thumbnail' ],
		];

		register_post_type( self::TEMPLATE_POST_TYPE, $args );
	}



	/**
	 * Register template category taxonomy
	 *
	 * Will register the templates category taxonomy used for organizing the templates
	 *
	 * @return void
	 */
	private function register_template_category_taxonomy() {
		$labels = [
			'name'                       => _x( 'Zion Page Builder Template Category', 'Taxonomy General Name', 'zionbuilder' ),
			'singular_name'              => _x( 'Zion Page Builder Template Category', 'Taxonomy Singular Name', 'zionbuilder' ),
			'menu_name'                  => __( 'Template Category', 'zionbuilder' ),
			'all_items'                  => __( 'All Template Category', 'zionbuilder' ),
			'parent_item'                => __( 'Parent Template Category', 'zionbuilder' ),
			'parent_item_colon'          => __( 'Parent Template Category:', 'zionbuilder' ),
			'new_item_name'              => __( 'New Template Category', 'zionbuilder' ),
			'add_new_item'               => __( 'Add New Template Category', 'zionbuilder' ),
			'edit_item'                  => __( 'Edit Template Category', 'zionbuilder' ),
			'update_item'                => __( 'Update Template Category', 'zionbuilder' ),
			'view_item'                  => __( 'View Template Category', 'zionbuilder' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'zionbuilder' ),
			'add_or_remove_items'        => __( 'Add or remove Template Category', 'zionbuilder' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'zionbuilder' ),
			'popular_items'              => __( 'Popular Template Category', 'zionbuilder' ),
			'search_items'               => __( 'Search Template Category', 'zionbuilder' ),
			'not_found'                  => __( 'Not Found', 'zionbuilder' ),
			'no_terms'                   => __( 'No items', 'zionbuilder' ),
			'items_list'                 => __( 'Items list', 'zionbuilder' ),
			'items_list_navigation'      => __( 'Items list navigation', 'zionbuilder' ),
		];
		$args   = [
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		];

		register_taxonomy( self::TEMPLATE_CATEGORY_TAXONOMY, [ self::TEMPLATE_POST_TYPE ], $args );
	}

	/**
	 * This function will return the templates based on post type
	 *
	 * @param string  $template_type
	 *
	 * @return array with the templates
	 */
	public function get_templates_by_type( $template_type ) {
		$the_posts = get_posts(
			[
				'meta_query' => [
					[
						'key'     => self::TEMPLATE_TYPE_META,
						'value'   => $template_type,
						'compare' => '=',
					],
				],
			]
		);
		return $the_posts;
	}

	/**
	 * This function will create a new template
	 *
	 * @param string $template_title  - the template title
	 * @param mixed  $template_config
	 *
	 * @return int|\WP_Error template id
	 */
	public static function create_template( $template_title, $template_config ) {
		$template_name = sanitize_text_field( $template_title );
		$template_name = ! empty( $template_name ) ? $template_name : esc_html__( 'template', 'zionbuilder' );

		$template_args = [
			'post_title'  => $template_name,
			'post_type'   => self::TEMPLATE_POST_TYPE,
			'post_status' => 'publish',
			'meta_input'  => [],
		];

		if ( empty( $template_config['template_type'] ) ) {
			return new \WP_Error( 'zion_template_missing_info', esc_html__( 'Missing template type', 'zionbuilder' ) );
		}

		// Set the template type
		$template_args['meta_input'][self::TEMPLATE_TYPE_META] = sanitize_text_field( $template_config['template_type'] );

		$post_id = wp_insert_post( wp_slash( $template_args ), true );

		// Check to see if the post was succesfully created
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Set element category
		if ( ! empty( $template_config['category'] ) ) {
			$element_category = sanitize_text_field( $template_config['category'] );

			if ( ! term_exists( $element_category, self::TEMPLATE_CATEGORY_TAXONOMY ) ) {
				wp_insert_term( $element_category, self::TEMPLATE_CATEGORY_TAXONOMY );
			}
			wp_set_object_terms( $post_id, $element_category, self::TEMPLATE_CATEGORY_TAXONOMY, false );
		}

		// Get an instance of the template
		$template_instance = Plugin::$instance->post_manager->get_post_type_instance( $post_id );

		// Set template data
		if ( ! empty( $template_config['template_data'] ) ) {
			$template_instance->save_template_data( $template_config['template_data'] );
		}

		$template_instance->set_builder_status( true );

		return $post_id;
	}
}
