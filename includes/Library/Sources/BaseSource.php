<?php

namespace ZionBuilder\Library\Sources;

use WP_Error;

class BaseSource {
	const TYPE_LOCAL    = 'local';
	const TYPE_EXTERNAL = 'external';
	const TYPE_ZION     = 'zion_library';

	/**
	 * Holds the library source unique id
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Flag if the source used browser cache or not
	 *
	 * @var boolean
	 */
	public $use_cache = false;

	/**
	 * Local source name
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Source URL
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * Additional request headers used when making a request
	 *
	 * @var array
	 */
	public $request_headers = [];

	/**
	 * Source type
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Source main constructor
	 *
	 * @param array $source_config
	 */
	public function __construct( $source_config = [] ) {
		// Set defaults
		$this->id   = $source_config['id'];
		$this->name = $source_config['name'];

		$this->type = $this->get_type();
		$this->url  = \get_rest_url( null, sprintf( 'zionbuilder/v1/library/%s/items-and-categories', $this->get_id() ) );

		$this->on_init( $source_config );
	}

	/**
	 * Actions that will fire at the class __construct
	 *
	 * @param array $source_config
	 *
	 * @return void
	 */
	protected function on_init( $source_config ) {
	}


	/**
	 * Returns the source unique id
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Returns the type of the source
	 * F.e. 'local', 'external', etc
	 *
	 * @return string
	 */
	public function get_type() {
		return '';
	}


	/**
	 * Returns a list of template items and their categories
	 *
	 * @return array
	 */
	public function get_items_and_categories() {
		return [
			'items'      => $this->get_items(),
			'categories' => $this->get_categories(),
		];
	}


	/**
	 * Returns a list of template items
	 *
	 * @return array
	 */
	public function get_items() {
		return [];
	}

	/**
	 * Returns a single template item data for REST API
	 *
	 * @param integer $item_id The template id we want to return
	 *
	 * @return array
	 */
	public function get_item( $item_id ) {
		return [];
	}

	/**
	 * Returns a list of template categories
	 *
	 * @return array
	 */
	public function get_categories() {
		return [];
	}

	/**
	 * Adds a new template to DB
	 *
	 * @param array $item_data
	 *
	 * @return WP_Error | array The inserted template data or WP_Error in case of failure
	 */
	public function create_item( $item_data ) {
		return new \WP_Error( 'invalid_action', 'Cannot create template' );
	}

	/**
	 * Duplicates a template and regenerates the element UIDs
	 *
	 * @return WP_Error | array The new template data
	 */
	public function duplicate_item() {
		return new \WP_Error( 'invalid_action', 'Cannot duplicate template' );
	}

	/**
	 * Imports a template into DB
	 *
	 * @param integer $item_id
	 *
	 * @return WP_Error | array The inserted template data or WP_Error in case of failure
	 */
	public function insert_item( $item_id ) {
		return new \WP_Error( 'invalid_action', 'Cannot insert template' );
	}

	/**
	 * Imports a template into DB
	 *
	 * @return WP_Error | array The inserted template data or WP_Error in case of failure
	 */
	public function update_item() {
		return new \WP_Error( 'invalid_action', 'Cannot update template' );
	}

	/**
	 * Imports a template into DB
	 *
	 * @return WP_Error | array The inserted template data or WP_Error in case of failure
	 */
	public function import_item() {
		return new \WP_Error( 'invalid_action', 'Cannot import template' );
	}


	/**
	 * Exports an item as zip file
	 *
	 * @param integer $item_id
	 *
	 * @return WP_Error
	 */
	public function export_item( $item_id ) {
		return new \WP_Error( 'invalid_action', 'Cannot export template' );
	}


	/**
	 * Will delete the item from DB
	 *
	 * @return WP_Error/boolean true in case the template was deleted or WP_Error in case of failure
	 */
	public function delete_item() {
		return new \WP_Error( 'invalid_action', 'Cannot delete template' );
	}
}
