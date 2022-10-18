<?php

namespace ZionBuilder\Api;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class RestApi
 *
 * @package ZionBuilder\Api
 */
class RestApi {

	/**
	 * Holds a reference to all rest controllers classes
	 *
	 * @var array
	 */
	private $controllers = [];


	/**
	 * RestApi constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'init_controllers' ] );
	}

	/**
	 * Initialize the registered controllers
	 *
	 * @hooked zionbuilder/rest_api/register_controllers
	 *
	 * @return void
	 */
	public function init_controllers() {
		$this->register_default_controllers();

		do_action( 'zionbuilder/rest_api/register_controllers', $this );

		foreach ( $this->get_controllers() as $key => $controller ) {
			$controller->init();
		}
	}

	/**
	 * Registers a new Rest API controller
	 *
	 * @param RestApiController $controller_instance
	 *
	 * @return void
	 */
	public function register_controller( $controller_instance ) {
		$this->controllers[$controller_instance->get_controller_id()] = $controller_instance;
	}

	/**
	 * Get Controllers
	 *
	 * Returns all registers Rest Api controllers
	 *
	 * @return array
	 */
	public function get_controllers() {
		return $this->controllers;
	}

	/**
	 * Register the plugin's default controllers
	 *
	 * @return void
	 */
	public function register_default_controllers() {
		$controllers = [
			'ZionBuilder\Api\RestControllers\Options',
			'ZionBuilder\Api\RestControllers\GoogleFonts',
			'ZionBuilder\Api\RestControllers\DataSets',
			'ZionBuilder\Api\RestControllers\SystemInfo',
			'ZionBuilder\Api\RestControllers\Templates',
			'ZionBuilder\Api\RestControllers\DownloadArchive',
			'ZionBuilder\Api\RestControllers\Assets',
			'ZionBuilder\Api\RestControllers\ReplaceUrl',
			'ZionBuilder\Api\RestControllers\SavePage',
			'ZionBuilder\Api\RestControllers\Pages',
			'ZionBuilder\Api\RestControllers\Elements',
			'ZionBuilder\Api\RestControllers\BulkActions',
			'ZionBuilder\Api\RestControllers\Media',
			'ZionBuilder\Api\RestControllers\FileUpload',
			'ZionBuilder\Api\RestControllers\UserController',
			'ZionBuilder\Api\RestControllers\Library',
			'ZionBuilder\Api\RestControllers\Breakpoints',
		];

		foreach ( $controllers as $controller ) {
			$this->register_controller( new $controller() );
		}
	}
}
