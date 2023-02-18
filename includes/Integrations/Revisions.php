<?php

namespace ZionBuilder\Integrations;

use ZionBuilder\Plugin;
use ZionBuilder\Post\BasePostType;
use ZionBuilder\Permissions;
use ZionBuilder\Whitelabel;
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Revisions
 *
 * @package ZionBuilder\Integrations
 */
class Revisions implements IBaseIntegration {
	/**
	 * retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'revisions';
	}

	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return true;
	}


	/**
	 * Main class constructor
	 */
	public function __construct() {
		add_action( '_wp_put_post_revision', [ $this, 'add_post_revision_data' ] );
		add_filter( 'wp_creating_autosave', [ $this, 'update_autosave' ] );
		add_filter( '_wp_post_revision_fields', [ $this, 'add_pb_data_to_revision_page' ], 10, 2 );
		add_action( 'wp_restore_post_revision', [ $this, 'restore_post_revision' ], 10, 2 );
	}

	public function update_autosave( $autosave_id ) {
		$this->add_post_revision_data( $autosave_id );
	}

	public function add_post_revision_data( $revision_id ) {
		$parent_revision_id = wp_is_post_revision( $revision_id );
		$revision_instance  = Plugin::$instance->post_manager->get_post_instance( $parent_revision_id );

		if ( $parent_revision_id && $revision_instance ) {
			$revision_instance->copy_meta_fields( $revision_id );
		}
	}

	/**
	 * Restore the PB data as a post revision
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 * @param int $revision_id
	 *
	 * @return void
	 */
	public function restore_post_revision( $post_id, $revision_id ) {
		$revision_post_instance = Plugin::$instance->post_manager->get_post_instance( $revision_id );
		$parent_post_instance   = Plugin::$instance->post_manager->get_post_instance( $post_id );

		if ( ! $revision_post_instance || ! $parent_post_instance ) {
			return;
		}

		if ( ! post_type_supports( $parent_post_instance->get_post_value( 'post_type' ), Permissions::POST_TYPE_EDIT_PERMISSION ) ) {
			return;
		}

		$pb_data = $revision_post_instance->get_template_data();
		$parent_post_instance->save_template_data( $pb_data );
	}

	public function add_pb_data_to_revision_page( $fields, $post ) {
		if ( post_type_supports( $post['post_type'], Permissions::POST_TYPE_EDIT_PERMISSION ) ) {
			$fields[BasePostType::PAGE_TEMPLATE_META_KEY] = sprintf( '%s Data', Whitelabel::get_title() );
		}

		return $fields;
	}
}
