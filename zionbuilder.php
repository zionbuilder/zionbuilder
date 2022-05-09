<?php
/*
Plugin Name: Zion Builder
Plugin URI: https://zionbuilder.io/?utm_campaign=plugin-uri&utm_medium=wp-dashboard-plugins
Description: The page builder you always wanted. Create any design you want using live editor.
Version: 3.2.0
Author: zionbuilder.io
Author URI: https://zionbuilder.io/?utm_campaign=plugin-uri&utm_medium=wp-dashboard-plugins
Text Domain: zionbuilder
Domain Path: /languages
License: GPL3

Zion builder is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Zion builder is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Zion builder. If not, see {License URI}.
*/

namespace ZionBuilder;

use ZionBuilder\Plugin;
use ZionBuilder\Install;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// Get the info for this plugin
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$plugin_data = get_plugin_data( __FILE__ );

/**
 * Load text domain.
 *
 * Will load plugin text domain
 *
 * @since 1.0.0
 *
 * @return void
 */
function zionbuilder_load_textdomain() {
	load_plugin_textdomain( 'zionbuilder' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\zionbuilder_load_textdomain' );

// Set the minimum PRO compatible version
define( 'MINIMUM_ZION_PRO_VERSION', '3.0.0' );

/*
 * Check to see if the minimum requirements are meet
 */
if ( \version_compare( PHP_VERSION, '5.6.20', '<' ) ) {
	define( 'ZIONBUILDER_REQUIREMENTS_FAILED', true );
	add_action( 'admin_notices', __NAMESPACE__ . '\\zionbuilder_fail_php_version' );
} elseif ( version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
	define( 'ZIONBUILDER_REQUIREMENTS_FAILED', true );
	add_action( 'admin_notices', __NAMESPACE__ . '\\zionbuilder_fail_wp_version' );
} else {
	// Load autoloader
	require __DIR__ . '/vendor/autoload.php';

	\register_activation_hook( __FILE__, [ Install::class, 'activate' ] );

	// Load main plugin
	require dirname( __FILE__ ) . '/includes/Plugin.php';

	// Fire up the plugin
	$manager = new Plugin( trailingslashit( dirname( __FILE__ ) ), plugin_dir_url( __FILE__ ), $plugin_data['Version'] );
}

/**
 * WordPress admin notice for minimum PHP version.
 *
 * Displays an admin notice informing the user that the minimum PHP version is not installed on his server
 *
 * @since 1.0.0
 *
 * @return void
 */
function zionbuilder_fail_php_version() {
	/* translators: %s: Minimum PHP version */
	$message = sprintf( esc_html__( 'Zion Builder requires at least PHP version %s. Please contact your hosting provider and ask them to update the PHP version used on your server. Zion builder will not be activated until the minimum PHP version is installed.', 'zionbuilder' ), '5.4' );
	printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
}


/**
 * WordPress admin notice for minimum WordPress version.
 *
 * Displays an admin notice informing the user the the minimum WordPress version is not installed and he needs to update
 *
 * @since 1.0.0
 *
 * @return void
 */
function zionbuilder_fail_wp_version() {
	$button = '';
	$screen = get_current_screen();

	// Don't show the notice on WordPress core update page
	if ( isset( $screen->id ) && 'update-core' === $screen->id ) {
		return;
	}

	$update_url = wp_nonce_url( self_admin_url( 'update-core.php' ) );

	// Show update button if the current user can update WordPress
	if ( current_user_can( 'update_core' ) ) {
		$button = sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $update_url, __( 'Update WordPress', 'zionbuilder' ) );
	}

	/* translators: %s: Minimum WordPress version */
	$message = sprintf( esc_html__( 'Zion Builder requires at least WordPress version %s. Please update WordPress to the latest version. Zion Builder will not be activated until the minimum WordPress version is installed.', 'zionbuilder' ), '4.9' );
	echo wp_kses_post( sprintf( '<div class="notice notice-error is-dismissible"><p>%s</p>%s</div>', $message, $button ) );
}
