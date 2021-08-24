<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;
use ZionBuilder\Templates as ZionBuilderTemplates;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Templates
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Templates extends RestApiController {

	/**
	 * Api endpoint namespace
	 *
	 * @var string
	 */
	protected $namespace = 'zionbuilder/v1';

	/**
	 * Api endpoint
	 *
	 * @var string
	 */
	protected $base = 'templates';

	/**
	 * Register routes
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'args'                => [
						'status'        => [
							'required' => false,
						],
						'template_type' => [
							'required' => false,
						],
					],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_item' ],
					'permission_callback' => [ $this, 'create_item_permissions_check' ],
					'args'                => [
						'title'         => [
							'required' => false,
						],
						'template_type' => [
							'required' => true,
						],
					],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/export',
			[
				'args'   => [
					'title'             => [
						'required' => false,
					],
					'template_type'     => [
						'required' => true,
					],
					'template_category' => [
						'required' => false,
					],
					'template_data'     => [
						'required' => true,
					],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'export_item' ],
					'permission_callback' => [ $this, 'export_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/duplicate',
			[
				'args'   => [
					'template_id' => [
						'required' => true,
					],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'duplicate_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/insert',
			[
				'args'   => [
					'template_file' => [
						'required' => false,
					],
					'template_id'   => [
						'required' => false,
					],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'insert_template' ],
					'permission_callback' => [ $this, 'export_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/import',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'import_template' ],
					'permission_callback' => [ $this, 'export_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<id>[\d]+)',
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the object.', 'zionbuilder' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_unique_item' ],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
					'args'                => [],
				],
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
					'args'                => [
						'title' => [
							'required' => false,
						],
					],
				],
				[
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_item' ],
					'permission_callback' => [ $this, 'delete_item_permissions_check' ],
					'args'                => [
						'force' => [
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( 'Whether to bypass trash and force deletion.', 'zionbuilder' ),
						],
					],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<id>[\d]+)/export',
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the object.', 'zionbuilder' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'export_item' ],
					'permission_callback' => [ $this, 'export_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}


	/**
	 * Checks if a given request has access to read a post.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return \WP_Error|bool true if the request has read access for the item, WP_Error object otherwise
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read a data set.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return \WP_Error|bool true if the request has read access for the item, WP_Error object otherwise
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'publish_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to edit this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete a post.
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return true|\WP_Error true if the request has access to delete the item, WP_Error object otherwise
	 */
	public function delete_item_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to delete this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update a post.
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return true|\WP_Error true if the request has access to delete the item, WP_Error object otherwise
	 */
	public function update_item_permissions_check( $request ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to export templates
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return \WP_Error|bool true if the request has read access for the item, WP_Error object otherwise
	 */
	public function export_item_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to export this template.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}


	/**
	 * This function will return the templates data sets
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response List of templates
	 */
	public function get_items( $request ) {

		// get default params
		$templates       = [];
		$template_type   = $request->get_param( 'template_type' ) ? $request->get_param( 'template_type' ) : false;
		$template_status = $request->get_param( 'status' ) ? $request->get_param( 'status' ) : 'any';

		$args = [
			'post_status'    => $template_status,
			'post_type'      => ZionBuilderTemplates::TEMPLATE_POST_TYPE,
			'posts_per_page' => -1,
		];

		if ( $template_type ) {
			$args['meta_query'] = [
				[
					'key'     => ZionBuilderTemplates::TEMPLATE_TYPE_META,
					'value'   => $template_type,
					'compare' => '=',
				],
			];
		}

		$templates_list = [];

		/** @var \WP_Post[] $templates */
		$templates = get_posts( $args );

		foreach ( $templates as $template ) {
			$templates_list[] = $this->attach_post_data( $template );
		}

		return rest_ensure_response( $templates_list );
	}

	/**
	 * This function will return a template data sets based on template id
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|mixed|\WP_Error
	 */
	public function get_unique_item( $request ) {
		$template_id = $request['id'];

		//return the post based on id
		$template = get_post( $template_id );

		// check if the id is valid
		if ( ! $template ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		return rest_ensure_response( $this->attach_post_data( $template ) );
	}

	/**
	 * Adds additional information to the WP_Post object
	 *
	 * @param \WP_Post $template
	 *
	 * @return array
	 */
	public function attach_post_data( $template ) {
		// Convert to array
		$template         = $template->to_array();
		$prefix_shortcode = 'zionbuilder';

		$shortcode         = '[' . $prefix_shortcode . ' id="' . $template['ID'] . '"]';
		$template_instance = Plugin::$instance->post_manager->get_post_type_instance( $template['ID'] );

		$template['edit_url']    = $template_instance->get_edit_url();
		$template['preview_url'] = $template_instance->get_preview_url_barebone();
		$template['shortcode']   = $shortcode;

		// Get author name
		$user_data = get_user_by( 'id', $template['post_author'] );
		if ( $user_data ) {
			$template['author_name'] = $user_data->display_name;
		}

		// Template type
		$template['template_type'] = get_post_meta( $template['ID'], ZionBuilderTemplates::TEMPLATE_TYPE_META, true );

		// Attach category
		$template_categories           = get_the_terms( $template['ID'], ZionBuilderTemplates::TEMPLATE_CATEGORY_TAXONOMY );
		$template['template_category'] = ! empty( $template_categories ) ? $template_categories : [];

		$template = apply_filters( 'zionbuilder/rest/templates/attach_data', $template );

		return $template;
	}

	/**
	 * This function will create a new page template based on arguments
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function create_item( $request ) {
		$template_config = apply_filters(
			'zionbuilder/rest/templates/add',
			[
				'template_type' => $request->get_param( 'template_type' ),
				'category'      => $request->get_param( 'template_category' ),
				'template_data' => $request->get_param( 'template_data' ),
			],
			$request
		);

		$post_id = ZionBuilderTemplates::create_template(
			$request->get_param( 'title' ),
			$template_config
		);

		// Check to see if the post was succesfully created
		if ( is_wp_error( $post_id ) ) {
			if ( 'db_insert_error' === $post_id->get_error_code() ) {
				$post_id->add_data( [ 'status' => 500 ] );
			} else {
				$post_id->add_data( [ 'status' => 400 ] );
			}

			return $post_id;
		}

		//return the post based on id
		$template = get_post( $post_id );

		// check if the id is valid
		if ( ! $template ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		// Fire an action so others can add extra data to templates
		do_action( 'zionbuilder/rest/templates/added', $template, $request );

		return rest_ensure_response( $this->attach_post_data( $template ) );
	}

	/**
	 * This function will delete a template based on template id
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 */
	public function delete_item( $request ) {
		$template_id = $request->get_param( 'id' );
		$result      = false;
		$post        = get_post( $template_id );

		// show error message if the post item does't exist
		if ( ! $post ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		$force = (bool) $request->get_param( 'force' );
		if ( $template_id ) {
			if ( $force ) {
				$result = wp_delete_post( $template_id, true );
			} else {
				$result = wp_trash_post( $template_id );
			}
		}

		if ( ! $result ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'The template cannot be deleted.', 'zionbuilder' ), [ 'status' => 500 ] );
		}

		return rest_ensure_response( $result );
	}

	/**
	 * This function will update a template with a specific info
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return int|\WP_Error|\WP_REST_Response
	 */
	public function update_item( $request ) {
		// show error message if the post item does't exist
		$request_id = $request['id'];
		if ( is_wp_error( $request_id ) ) {
			return $request_id;
		}

		// item information
		$update_args = [
			'ID' => $request_id,
		];

		if ( $request->get_param( 'post_title' ) ) {
			$update_args['post_title'] = $request->get_param( 'post_title' );
		}

		$post_id = wp_update_post( (array) wp_slash( $update_args ), true );

		// check for errors
		if ( is_wp_error( $post_id ) ) {
			if ( 'db_update_error' === $post_id->get_error_code() ) {
				$post_id->add_data( [ 'status' => 500 ] );
			} else {
				$post_id->add_data( [ 'status' => 400 ] );
			}
			return $post_id;
		}

		$template = get_post( $post_id );

		// check if the id is valid
		if ( ! $template ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		return rest_ensure_response( $this->attach_post_data( $template ) );
	}

	/**
	 * This function handles the template export
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return int|\WP_Error|\WP_REST_Response
	 */
	public function export_item( \WP_REST_Request $request ) {
		$template_id = $request->get_param( 'id' ) ? $request->get_param( 'id' ) : false;

		if ( $template_id ) {
			$category       = '';
			$template_terms = get_the_terms( $template_id, ZionBuilderTemplates::TEMPLATE_CATEGORY_TAXONOMY );
			if ( ! is_wp_error( $template_terms ) && ! empty( $template_terms ) ) {
				$first_term = array_pop( $template_terms );

				if ( $first_term !== null ) {
					$category = $first_term->name;
				}
			}

			// retrieves the template data
			$post_instance = Plugin::$instance->post_manager->get_post_instance( $template_id );

			if ( ! $post_instance ) {
				return new \WP_Error( 'export_item', __( 'Could not return the post instance', 'zionbuilder' ) );
			}

			$template_name     = get_the_title( $template_id );
			$template_data     = $post_instance->get_template_data();
			$template_category = $category;
			$template_type     = get_post_meta( $template_id, ZionBuilderTemplates::TEMPLATE_TYPE_META, true );
		} else {
			// retrieves the template data
			$template_name     = sanitize_text_field( $request->get_param( 'title' ) );
			$template_data     = $request->get_param( 'template_data' );
			$template_category = $request->get_param( 'template_category' );
			$template_type     = $request->get_param( 'template_type' );
		}

		// Allow empty template name
		if ( empty( $template_name ) ) {
			$template_name = 'export';
		}

		$template_name = sanitize_file_name( $template_name );

		$export = Plugin::instance()->import_export;
		$export = $export->export_template(
			$template_name,
			apply_filters(
				'zionbuilder/templates/export',
				[
					'template_name' => $template_name,
					'template_type' => $template_type,
					'category'      => $template_category,
					'template_data' => $template_data,
				],
				$request
			)
		);

		// throw error if there are problomes during the export
		if ( is_wp_error( $export ) ) {
			$export->add_data( [ 'status' => 500 ] );
			return $export;
		}

		$import_export = Plugin::instance()->import_export;
		$download      = $import_export->download_template( $template_name );
		$download->add_data( [ 'status' => 500 ] );
		return $download;
	}

	/**
	 * Will import a template and return the template data
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function import_template( \WP_REST_Request $request ) {
		$files = $request->get_file_params();

		if ( empty( $files['file'] ) ) {
			return new \WP_Error( 'Import failed!', __( 'Your file is empty or invalid', 'zionbuilder' ) );
		}

		$template_id = Plugin::instance()->import_export->import_template( $files );

		if ( is_wp_error( $template_id ) ) {
			$template_id->add_data( [ 'status' => 400 ] );
			return $template_id;
		}

		//return the post based on id
		$template = get_post( $template_id );

		// check if the id is valid
		if ( ! $template ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		return rest_ensure_response( $this->attach_post_data( $template ) );
	}

	/**
	 * Will insert a template and returns it's data
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function insert_template( \WP_REST_Request $request ) {
		$source = $request->get_param( 'source' );

		if ( $source === 'remote' ) {
			$template_file_url = $request->get_param( 'url' );

			// Check to see if this a PRO plugin or not
			$template_file_url = apply_filters( 'zionbuilder/template/insert/template_file', $template_file_url, $request );

			if ( is_wp_error( $template_file_url ) ) {
				$template_file_url->add_data( [ 'status' => 500 ] );
				return $template_file_url;
			}

			if ( empty( $template_file_url ) ) {
				return new \WP_Error( 'template_data_not_valid', __( 'Could not get the template zip file!', 'zionbuilder' ) );
			}

			$template_file_download = download_url( $template_file_url );

			if ( is_wp_error( $template_file_download ) ) {
				$template_file_download->add_data( [ 'status' => 500 ] );
				return $template_file_download;
			}

			$content_config = Plugin::instance()->import_export->insert_template_package( $template_file_download );

			// prepare the template data
			if ( is_wp_error( $content_config ) ) {
				$content_config->add_data( [ 'status' => 500 ] );
				return $content_config;
			}

			if ( empty( $content_config['template_data'] ) ) {
				return new \WP_Error( 'template_data_not_valid', __( 'The uploaded template is not valid!', 'zionbuilder' ) );
			}

			// Send back the response
			return rest_ensure_response( $content_config );
		} else {
			$template_id = $request->get_param( 'ID' );

			// Insert using id
			$post_instance = Plugin::$instance->post_manager->get_post_instance( $template_id );

			if ( ! $post_instance ) {
				return new \WP_Error( 'export_item', __( 'Could not return the post instance', 'zionbuilder' ) );
			}

			$template_data = $post_instance->get_template_data();

			if ( ! $template_data ) {
				return new \WP_Error( 'template_data_not_found', __( 'The template could not be inserted!', 'zionbuilder' ) );
			}

			return rest_ensure_response(
				[
					'template_data' => $template_data,
				]
			);
		}
	}


	/**
	 * Undocumented function
	 *
	 * @param \WP_REST_Request $request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function duplicate_item( $request ) {
		global $wpdb;

		$original_post_id = $request->get_param( 'template_id' );
		$duplicate        = get_post( $original_post_id );

		if ( ! $duplicate ) {
			return new \WP_Error( 'post_not_found', __( 'Post could not be cloned!', 'zionbuilder' ) );
		}

		$duplicate->post_title = $duplicate->post_title . ' (' . esc_html__( 'copy', 'zionbuilder' ) . ')';
		$duplicate->post_name  = sanitize_title( $duplicate->post_name . '-' . esc_html__( 'copy', 'zionbuilder' ) );

		// Remove values that needs to be regenerated
		unset( $duplicate->post_modified );
		unset( $duplicate->post_modified_gmt );
		unset( $duplicate->ID );
		unset( $duplicate->guid );
		unset( $duplicate->comment_count );

		$duplicate_post_id = wp_insert_post( (array) $duplicate );

		// check if the id is valid
		if ( is_wp_error( $duplicate_post_id ) ) {
			return new \WP_Error( 'post_not_inserted', $duplicate_post_id->get_error_message() );
		}

		// Duplicate all taxonomies
		/** @var string[] $taxonomies */
		$taxonomies = get_object_taxonomies( $duplicate->post_type );

		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_post_terms( $original_post_id, $taxonomy, array( 'fields' => 'names' ) );

			if ( is_wp_error( $terms ) ) {
				$terms->add_data( [ 'status' => 500 ] );
				return $terms;
			}

			wp_set_object_terms( $duplicate_post_id, $terms, $taxonomy );
		}

		// Duplicate all post meta
		$custom_meta_fields = get_post_meta( $original_post_id );

		foreach ( $custom_meta_fields as $key => $value ) {
			if ( is_array( $value ) && count( $value ) > 0 ) {
				foreach ( $value as $value ) {
					$wpdb->insert(
						$wpdb->prefix . 'postmeta',
						array(
							'post_id'    => $duplicate_post_id,
							'meta_key'   => $key,
							'meta_value' => $value,
						)
					);
				}
			}
		}

		$template = get_post( $duplicate_post_id );

		// check if the id is valid
		if ( ! $template ) {
			return new \WP_Error( 'post_not_found', __( 'Post could not be cloned!', 'zionbuilder' ) );
		}

		return rest_ensure_response( $this->attach_post_data( $template ) );

	}
}
