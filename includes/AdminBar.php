<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}


/**
 * Class AdminBar
 * Manages admin bar functionality
 * - removes admin bar if it is not needed
 * - adds edit buttons to pages built with zion builder
 *
 * @package ZionBuilder
 */
class AdminBar {
	/**
	 * AdminBar constructor.
	 */
	public function __construct() {
		if ( isset( $_GET['zionbuilder-barebone-preview'] ) && $_GET['zionbuilder-barebone-preview'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			show_admin_bar( false );
			add_filter( 'body_class', [ $this, 'remove_body_classes' ] );
		}

		add_action( 'admin_bar_menu', [ $this, 'add_toolbar_items' ], 100 );
	}

	public function remove_body_classes() {
		return array();
	}

	public function add_toolbar_items( $admin_bar ) {
		// Add edit with zion link
		global $post;

		if ( ! $post ) {
			return;
		}

		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );

		if ( $post_instance->is_built_with_zion() ) {
			$admin_bar->add_menu(
				[
					'id'    => 'edit-with-zion',
					'title' => esc_html__( 'Edit with Zion builder', 'zionbuilder' ),
					'href'  => $post_instance->get_edit_url(),
					'meta'  => [
						'title' => esc_html__( 'Edit with Zion builder', 'zionbuilder' ),
					],
				]
			);
		}
	}
}
