<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\FileSystem;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class FileUpload
 *
 * @package ZionBuilder\Api\RestControllers
 */
class FileUpload extends RestApiController {

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
	protected $base = 'upload';

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
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_item' ],
					'permission_callback' => [ $this, 'create_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
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
	 * This function will create a new page template based on arguments
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function create_item( $request ) {
		FileSystem::get_file_system();

		$files = $request->get_file_params();

		if ( empty( $files['file'] ) ) {
			return new \WP_Error( 'Import failed!', __( 'Your file is empty or invalid', 'zionbuilder' ) );
		}

		// check if is a valid file
		switch ( $files['file']['error'] ) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				return new \WP_Error( 'no_file_sent', __( 'No file was uploaded', 'zionbuilder' ) );
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return new \WP_Error( 'maximum_size_exceeded', __( 'The uploaded file exceeds the MAX_FILE_SIZE', 'zionbuilder' ) );
			default:
				break;
		}

		$file_path              = $files['file']['tmp_name'];
		$upload_dir             = wp_upload_dir();
		$upload_dir_url         = $upload_dir['baseurl'];
		$upload_dir_path        = $upload_dir['basedir'];
		$upload_location        = $upload_dir_path . '/zionbuilder/files';
		$upload_location_url    = $upload_dir_url . '/zionbuilder/files';
		$newfilename            = wp_unique_filename( $upload_location, $files['file']['name'] );
		$uploaded_file_location = sprintf( '%s/%s', $upload_location, $newfilename );
		$uploaded_file_url      = sprintf( '%s/%s', $upload_location_url, $newfilename );

		// ALlowed mimes
		$allowed_mimes          = [];
		$allowed_mimes['otf']   = 'application/x-font-otf';
		$allowed_mimes['woff']  = 'application/x-font-woff';
		$allowed_mimes['ttf']   = 'application/x-font-ttf';
		$allowed_mimes['svg']   = 'image/svg+xml';
		$allowed_mimes['eot']   = 'application/vnd.ms-fontobject';
		$allowed_mimes['woff2'] = 'font/woff2';

		$wp_filetype = wp_check_filetype( $files['file']['name'], $allowed_mimes );

		// CHeck for file type
		if ( ! $wp_filetype['ext'] && ! current_user_can( 'unfiltered_upload' ) ) {
			return new \WP_Error( 'file_type_not_suported', __( 'The file type is not supported', 'zionbuilder' ) );
		}

		if ( ! file_exists( $upload_location ) ) {
			FileSystem::get_file_system()->mkdir( $upload_location, 0777, true );
		}

		move_uploaded_file( $file_path, $uploaded_file_location );

		return rest_ensure_response(
			[
				'file_url' => $uploaded_file_url,
			]
		);
	}
}
