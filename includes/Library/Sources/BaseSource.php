<?php

namespace ZionBuilder\Library\Sources;

use WP_Error;

class BaseSource {
	const TYPE_LOCAL    = 'local';
	const TYPE_EXTERNAL = 'external';
	const TYPE_ZION     = 'zion_library';

	public $id              = '';
	public $use_cache       = false;
	public $name            = true;
	public $url             = null;
	public $request_headers = [];
	public $type            = '';

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
	 * @return void
	 */
	protected function on_init( $source_config ) {
	}


	/**
	 * Returns the source unique id
	 *
	 * @return void
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
		return new WP_Error( 'invalid_type', 'get_type() must be implemented in the child class' );
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
	 * @return WP_Error/array The inserted template data or WP_Error in case of failure
	 */
	public function add_item() {
		return new \WP_Error( 'invalid_action', 'Cannot import template' );
	}

	/**
	 * Duplicates a template and regenerates the element UIDs
	 *
	 * @return WP_Error/array The new template data
	 */
	public function duplicate_item() {
		return new \WP_Error( 'invalid_action', 'Cannot import template' );
	}

	/**
	 * Imports a template into DB
	 *
	 * @return WP_Error/array The inserted template data or WP_Error in case of failure
	 */
	public function insert_template() {
		return new \WP_Error( 'invalid_action', 'Cannot import template' );
	}

	/**
	 * Imports a template into DB
	 *
	 * @return WP_Error/array The inserted template data or WP_Error in case of failure
	 */
	public function update_template() {
		return new \WP_Error( 'invalid_action', 'Cannot import template' );
	}

	/**
	 * Imports a template into DB
	 *
	 * @return WP_Error/array The inserted template data or WP_Error in case of failure
	 */
	public function import_item() {
		return new \WP_Error( 'invalid_action', 'Cannot import template' );
	}


	/**
	 * Exports an item as zip file
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


	/**
	 * Will return the JSON structure of a template
	 *
	 * @return WP_Error/boolean true in case the template was deleted or WP_Error in case of failure
	 */
	public function get_item_builder_data( $item_id ) {
		return new \WP_Error( 'invalid_action', 'Cannot insert the template' );
	}

	public function get_item_data() {
		return [];
	}
}
