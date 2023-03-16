<?php

namespace ZionBuilder;

use ZionBuilder\Whitelabel;
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}


/**
 * Class AdminBar
 * Manages admin bar functionality
 * - removes admin bar if it is not needed
 * - adds edit buttons to pages built with the builder
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

	/**
	 * Removes the body css classes
	 *
	 * @return array<int, string>
	 */
	public function remove_body_classes() {
		return [];
	}

	/**
	 * Adds "Edit with Zion Builder" menu item for zion builder enabled pages
	 *
	 * @param \WP_Admin_Bar $admin_bar
	 *
	 * @return void
	 */
	public function add_toolbar_items( $admin_bar ) {
		// Add edit with zion link
		global $post;

		if ( ! $post ) {
			return;
		}

		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );

		if ( ! $post_instance ) {
			return;
		}

		// If this is a single page, show edit with zion builder button
		if ( is_singular() ) {
			$admin_bar->add_menu(
				[
					'id'    => 'edit-with-zion',
					/* translators: %s: ZionBuilder white label name */
					'title' => sprintf( __( 'Edit with %s', 'zionbuilder' ), Whitelabel::get_title() ),
					'href'  => $post_instance->get_edit_url(),
	
				]
			);
		} else {
			// If this is a post archive, show edit with zion builder button
			$admin_bar->add_menu(
				[
					'id'    => 'edit-with-zion',
					/* translators: %s: ZionBuilder white label name */
					'title' => Whitelabel::get_title(),
					'href'  => '#',
				]
			);
		}



		do_action( 'zionbuilder/admin-bar/register_menu_items', $admin_bar );
		
	}
}
