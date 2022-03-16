<?php

namespace ZionBuilder\Api\RestControllers;

use WP_REST_Request;
use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;
use ZionBuilder\Templates;
use ZionBuilder\Post\TemplatePostType;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class DataSets
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Library extends RestApiController {
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
	protected $base = 'library';


	/**
	 * Register routes
	 *
	 * @return void
	 */
	public function register_routes() {
		/**
		 * Returns all sources configuration
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_sources' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/export',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'export_template' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		/**
		 * Single source API
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_source' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/items',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/categories',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_categories' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/items-and-categories',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items_and_categories' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/import',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'import_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		/**
		 * Single source Item API
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/(?P<template_id>[\d]+)',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_single_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		// Delete
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/(?P<template_id>[\d]+)',
			[
				[
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_single_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		/**
		 * Exports an item as zip file
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/(?P<template_id>[\d]+)/export',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'export_single_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/(?P<template_id>[\d]+)/get-builder-config',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_single_item_builder_config' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<library_id>[\w-]+)/(?P<template_id>[\d]+)/save-thumbnail',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'save_item_thumbnail' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
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
	 * Checks to see if the response is not a \WP_Error
	 *
	 * @param array|\WP_Error $response
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function validate_and_send_response( $response ) {
		if ( is_wp_error( $response ) ) {
			$response->add_data( [ 'status' => 400 ] );
			return $response;
		}

		return rest_ensure_response( $response );
	}


	/**
	 * Returns the list of Library sources
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_sources() {
		return rest_ensure_response( Plugin::instance()->library->get_sources() );
	}


	/**
	 * Returns a single source
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_source( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return rest_ensure_response( $library );
	}


	/**
	 * Returns the source items
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return $this->validate_and_send_response( $library->get_items() );
	}

	/**
	 * Returns the source categories
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_categories( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return $this->validate_and_send_response( $library->get_categories() );
	}

	/**
	 * Returns the source items and categories
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items_and_categories( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return $this->validate_and_send_response( $library->get_items_and_categories() );
	}

	public function create_item( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		$template_config = apply_filters(
			'zionbuilder/rest/templates/add',
			[
				'title'         => $request->get_param( 'title' ),
				'template_type' => $request->get_param( 'template_type' ),
				'template_data' => $request->get_param( 'template_data' ),
			],
			$request
		);

		$template_data = $library->create_item( $template_config );

		if ( is_wp_error( $template_data ) ) {
			return $template_data->add_data( [ 'status', 500 ] );
		}

		// Fire an action so others can add extra data to templates
		do_action( 'zionbuilder/rest/templates/added', $template_data, $request );

		return $this->validate_and_send_response( $template_data );
	}

	/**
	 * This function will return a template data sets based on template id
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|mixed|\WP_Error
	 */
	public function get_single_item( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		$template_id = $request->get_param( 'template_id' );

		return $this->validate_and_send_response( $library->get_item( $template_id ) );
	}


	/**
	 * Returns a single template configuration
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function export_single_item( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		$template_id = $request->get_param( 'template_id' );

		// retrieves the template data
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $template_id );

		if ( ! $post_instance ) {
			return new \WP_Error( 'export_item', __( 'Could not return the post instance', 'zionbuilder' ) );
		}

		$template_name = get_the_title( $template_id );
		$template_data = $post_instance->get_template_data();
		$template_type = get_post_meta( $template_id, Templates::TEMPLATE_TYPE_META, true );

		// Allow empty template name
		if ( empty( $template_name ) ) {
			$template_name = 'export';
		}

		$template_name = sanitize_file_name( $template_name );

		$export = Plugin::instance()->import_export->export_template(
			$template_name,
			apply_filters(
				'zionbuilder/templates/export',
				[
					'template_name' => $template_name,
					'template_type' => $template_type,
					'template_data' => $template_data,
				],
				$request
			)
		);

		// throw error if there are problems during the export
		if ( is_wp_error( $export ) ) {
			$export->add_data( [ 'status' => 500 ] );
			return $export;
		}

		$download = Plugin::instance()->import_export->download_template( $template_name );

		// If we reach here we have an error
		$download->add_data( [ 'status' => 500 ] );
		return $download;
	}


	/**
	 * Deletes a template from source
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function delete_single_item( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		$template_id = $request->get_param( 'template_id' );
		$result      = wp_delete_post( $template_id, true );

		if ( ! $result ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'The template cannot be deleted.', 'zionbuilder' ), [ 'status' => 500 ] );
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Returns the template builder data
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_single_item_builder_config( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		$item_builder_data = $library->insert_item( $request->get_param( 'template_id' ) );
		if ( is_wp_error( $item_builder_data ) ) {
			$item_builder_data->add_data( [ 'status' => 400 ] );
			return $item_builder_data;
		}

		return rest_ensure_response(
			[
				'template_data' => $item_builder_data,
			]
		);
	}

	/**
	 * Saves the template thumbnail
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function save_item_thumbnail( $request ) {
		$template_id = $request->get_param( 'template_id' );
		$success     = $request->get_param( 'success' );
		$image_data  = $request->get_param( 'thumbnail' );

		if ( $template_id ) {
			$template_instance = Plugin::$instance->post_manager->get_post_instance( $template_id );

			// check if the id is valid
			if ( ! $template_instance || ! $template_instance instanceof TemplatePostType ) {
				return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
			}

			if ( $success ) {
				$template_instance->save_base64Image( $image_data );
			} else {
				$template_instance->set_failed_thumbnail_generation_status( true );
			}

			// TODO: show error message in case it failed
			return rest_ensure_response( $template_instance->get_data_for_api() );
		}

		return rest_ensure_response( [] );
	}

	/**
	 * Imports a template
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function import_item( $request ) {
		$library = Plugin::instance()->library->get_source( $request->get_param( 'library_id' ) );

		if ( ! $library ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No library found that matches your request.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

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
		$template_instance = Plugin::$instance->post_manager->get_post_instance( $template_id );

		// check if the id is valid
		if ( ! $template_instance ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		return $this->validate_and_send_response( $template_instance->get_data_for_api() );
	}

	/**
	 * Exports a template as zip file
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function export_template( $request ) {
		// retrieves the template data
		$template_name = sanitize_text_field( $request->get_param( 'title' ) );
		$template_data = $request->get_param( 'template_data' );
		$template_type = $request->get_param( 'template_type' );

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
					'template_data' => $template_data,
				],
				$request
			)
		);

		// throw error if there are problems during the export
		if ( is_wp_error( $export ) ) {
			$export->add_data( [ 'status' => 500 ] );
			return $export;
		}

		$import_export = Plugin::instance()->import_export;
		$download      = $import_export->download_template( $template_name );

		// If we reach here we have an error
		$download->add_data( [ 'status' => 500 ] );
		return $download;
	}
}
