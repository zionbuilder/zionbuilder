<?php

namespace ZionBuilder;

use ZionBuilder\Post\BasePostType;

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
	const TEMPLATE_POST_TYPE = 'zion_template';
	const TEMPLATE_TYPE_META = 'zionbuilder_template_type';

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
		add_filter( 'zionbuilder/post/post_template', [ $this, 'set_post_template' ], 10, 2 );

		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Will set the template for a post
	 *
	 * @param string $template
	 * @param BasePostType $post_instance
	 *
	 * @return string
	 */
	public function set_post_template( $template, $post_instance ) {
		$post_id   = $post_instance->get_post_id();
		$post_type = get_post_type( $post_id );

		if ( $post_type === self::TEMPLATE_POST_TYPE ) {
			return 'zion_builder_blank';
		}

		return $template;
	}


	/**
	 * Removes the Zion builder template post type from the list of post types
	 * used throughout the builder
	 *
	 * @param array $post_types
	 *
	 * @return array
	 */
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
	 * Main class init method
	 *
	 * @return void
	 */
	public function init() {
		// Register default template types
		$this->register_default_template_types();

		// Allow others to register their own template types
		do_action( 'zionbuilder/templates/before_init', $this );

		$this->register_post_type();
	}

	/**
	 * Registers Plugin default template types
	 *
	 * @return void
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

		// Add additional data
		$template_type_config['slug']    = $template_type_config['id'];
		$template_type_config['term_id'] = $template_type_config['id'];

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
			'rewrite'             => false,
			'supports'            => [ 'title', 'editor', 'author', 'thumbnail' ],
		];

		register_post_type( self::TEMPLATE_POST_TYPE, $args );
	}


	/**
	 * This function will return the templates based on post type
	 *
	 * @param string $template_type
	 *
	 * @return array with the templates
	 */
	public function get_templates_by_type( $template_type ) {
		$the_posts = get_posts(
			[
				'post_type'      => self::TEMPLATE_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
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
		];

		if ( empty( $template_config['template_type'] ) ) {
			return new \WP_Error( 'zion_template_missing_info', esc_html__( 'Missing template type', 'zionbuilder' ) );
		}

		// Set the template type
		$template_args = (array) wp_slash( $template_args );

		$post_id = wp_insert_post( $template_args, true );

		// Check to see if the post was succesfully created
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// set template type
		update_post_meta( $post_id, self::TEMPLATE_TYPE_META, sanitize_text_field( $template_config['template_type'] ) );

		// Get an instance of the template
		$template_instance = Plugin::$instance->post_manager->get_post_instance( $post_id );

		// Set template data
		if ( ! empty( $template_config['template_data'] ) ) {
			$template_instance->save_template_data( $template_config['template_data'] );
		}

		$template_instance->set_builder_status( true );

		return $post_id;
	}

	/**
	 * Returns the available library sources
	 *
	 * @return array The library sources as array
	 */
	public static function get_library_sources() {
		$sources = [
			[
				'name'     => esc_html__( 'Local library', 'zionbuilder' ),
				'url'      => self::get_local_library_url(),
				'id'       => 'local_library',
				'useCache' => false,
				'type'     => 'local',
			],
			[
				'name' => esc_html__( 'Zion Builder library', 'zionbuilder' ),
				'url'  => 'https://library.zionbuilder.io/wp-json/zionbuilder-library/v1/items-and-categories',
				'id'   => 'zion_builder',
				'type' => 'zion_library',
			],
		];

		// Allow devs to add their own sources without interfering with the official sources
		$filter_sources = apply_filters( 'zionbuilder/templates/library_sources', [] );

		return array_merge( $sources, $filter_sources );
	}

	/**
	 * Returns the API url for the local library
	 *
	 * @return string
	 */
	public static function get_local_library_url() {
		return \get_rest_url( null, 'zionbuilder/v1/templates/items-and-categories' );
	}
}
