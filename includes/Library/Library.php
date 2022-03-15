<?php

namespace ZionBuilder\Library;

use ZionBuilder\Library\Sources\LocalSource;
use ZionBuilder\Library\Sources\ZionSource;
use ZionBuilder\Library\Sources\BaseSource;


use WP_Error;

class Library {
	/**
	 * Holds a refference to all template library sources
	 *
	 * @var array
	 */
	private $sources = null;

	public function __construct() {
	}


	/**
	 * Will register the sources if they are not registered yet
	 *
	 * @return void
	 */
	public function register_sources() {
		if ( null === $this->sources ) {
			$this->register_default_sources();
			do_action( 'zionbulder/library/register_sources', $this );
		}
	}


	/**
	 * Registers default template library sources
	 *
	 * @return void
	 */
	public function register_default_sources() {
		$this->register_source(
			new LocalSource(
				[
					'id'   => 'local_library',
					'name' => esc_html__( 'Local library', 'zionbuilder' ),
				]
			)
		);

		$this->register_source(
			new ZionSource(
				[
					'id'   => 'zion_builder',
					'name' => esc_html__( 'Zion Builder library', 'zionbuilder' ),
				]
			)
		);
	}


	/**
	 * Registers a single template library source
	 *
	 * @param BaseSource $source_instance
	 *
	 * @return WP_Error|boolean
	 */
	public function register_source( $source_instance ) {
		if ( ! $source_instance instanceof BaseSource ) {
			return new WP_Error( 'invalid_data', 'The source must be an instance of BaseSource' );
		}

		$this->sources[$source_instance->get_id()] = $source_instance;

		return true;
	}


	/**
	 * Unregisters a template source
	 *
	 * @param string $source_id
	 *
	 * @return void
	 */
	public function unregister_source( $source_id ) {
		$this->register_sources();
		unset( $this->sources[$source_id] );
	}


	public function get_sources() {
		$this->register_sources();

		return $this->sources;
	}

	/**
	 * Returns a source by id
	 *
	 * @param string $source_id
	 *
	 * @return BaseSource|false
	 */
	public function get_source( $source_id ) {
		$this->register_sources();
		return $this->sources[$source_id] ? $this->sources[$source_id] : false;
	}


	/**
	 * Returns the list of sources as array so it can be used in javascript
	 *
	 * @return array
	 */
	public function get_sources_for_api() {
		$this->register_sources();
	}

}
