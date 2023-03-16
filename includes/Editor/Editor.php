<?php

namespace ZionBuilder\Editor;

use ZionBuilder\Utils;
use ZionBuilder\Settings;
use ZionBuilder\Plugin;
use ZionBuilder\Permissions;
use ZionBuilder\CSSClasses;
use ZionBuilder\Elements\Masks;
use ZionBuilder\Whitelabel;
use ZionBuilder\User;
use ZionBuilder\Nonces;
use ZionBuilder\CommonJS;
use ZionBuilder\Scripts;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Editor
 *
 * Will handle the editor part of the page builder. Will hook into 'admin_action_zion_builder_active' action
 */
class Editor {
	/**
	 * Holds a reference to the current post id
	 *
	 * @var integer
	 */
	private $post_id = 0;

	/**
	 * @var Preview
	 */
	public $preview = null;

	/**
	 * Main class constructor
	 *
	 * Will hook into 'admin_action_zion_builder_active' action to show the PB editor
	 */
	public function __construct() {
		// load preview
		$this->preview = new Preview();
		add_action( 'admin_action_zion_builder_active', [ $this, 'init' ] );
	}

	public function set_builder_theme( $classes ) {
		$builder_theme = Settings::get_value_from_path( 'appearance.builder_theme', 'light' );

		$classes = explode( ' ', $classes );

		$classes[] = sprintf( 'znpb znpb-theme-%s', $builder_theme );

		return implode( ' ', $classes );
	}

	/**
	 * Init
	 *
	 * Will show the editor and load all scripts and styles needed for the editor
	 *
	 * @return void
	 */
	public function init() {
		// Get the post id
		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification

		// Don't proceed if the post id is missing
		if ( ! $post_id ) {
			return;
		}

		// Check permissions
		if ( ! Permissions::can_edit_post( $post_id ) ) {
			return;
		}



		// Set active post instance and global post data
		Plugin::$instance->post_manager->switch_to_post( $post_id );
		$this->post_id = $post_id;

		// Activate the builder for the post
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post_id );

		if ($post_instance) {
			$post_instance->set_builder_status( true );
		}

		// Set post lock if not locked already
		if ( ! $this->get_locked_user( $this->post_id ) ) {
			wp_set_post_lock( $this->post_id );
		}

		add_filter( 'heartbeat_settings', [ $this, 'set_heartbeat_settings' ] );

		// Remove admin bar
		add_filter( 'show_admin_bar', '__return_false' );
		add_filter( 'zionbulder/editor/body_class', [ $this, 'set_builder_theme' ] );

		// Remove title tag as it is manually added by the template
		remove_theme_support( 'title-tag' );

		// remove all actions
		$this->remove_wp_actions();

		// Add head actions again
		add_action( 'wp_head', '_wp_render_title_tag', 1 );
		add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		// @phpstan-ignore-next-line
		add_action( 'wp_head', 'wp_print_styles', 8 );
		// @phpstan-ignore-next-line
		add_action( 'wp_head', 'wp_print_head_scripts', 9 );
		add_action( 'wp_head', 'wp_site_icon', 99 );

		// Add our scripts last so we can get a list of other scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// Add footer actions
		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

		$this->print_editor_template();

		die();
	}

	/**
	 * Checks if the current post is locked by another user
	 *
	 * @param mixed $post_id
	 *
	 * @return boolean|\WP_User
	 */
	public function get_locked_user( $post_id ) {
		$locked_user = wp_check_post_lock( $post_id );

		if ( ! $locked_user ) {
			return false;
		}

		return get_user_by( 'ID', $locked_user );
	}

	/**
	 * Modify Heartbeat settings to run at each 15 seconds
	 *
	 * @param array<string, mixed> $settings
	 *
	 * @return array<string, mixed> Modified settings for heartbeat
	 */
	public function set_heartbeat_settings( $settings = [] ) {
		$settings['interval'] = 15;
		return $settings;
	}

	/**
	 * Remove WP actions
	 *
	 * Will remove all default WP actions so we can only add our own scripts and styles
	 *
	 * @return void
	 */
	public function remove_wp_actions() {
		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_footer' );
		remove_all_actions( 'wp_enqueue_scripts' );
	}

	/**
	 * Enqueue scripts
	 *
	 * Will load the scripts required for the editor interface
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		global $wp_styles, $wp_scripts;
		// Reset global variables
		$wp_styles  = new \WP_Styles(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$wp_scripts = new \WP_Scripts(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride

		do_action( 'zionbuilder/editor/before_scripts' );

		// Custom HTML widgets
		$wp_scripts->add( 'custom-html-widgets', admin_url( 'js/widgets/custom-html-widgets.js' ), [ 'jquery', 'backbone', 'wp-util', 'jquery-ui-core', 'wp-a11y' ] );

		wp_enqueue_style( 'widgets' );
		wp_enqueue_style( 'media-views' );

		do_action( 'admin_print_scripts-widgets.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName
		do_action( 'admin_footer-widgets.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName

		// Load Scripts
		// Load animation css file
		wp_enqueue_style( 'zion-frontend-animations', plugins_url( 'zionbuilder/assets/vendors/css/animate.css' ), [], Plugin::instance()->get_version() );

		// Enqueue common js and css
		Scripts::enqueue_common();

		Plugin::instance()->scripts->enqueue_style(
			'zion-editor-style',
			'editor',
			[
				'zb-common',
			],
			Plugin::instance()->get_version()
		);

		wp_add_inline_style( 'zion-editor-style', Plugin::instance()->icons->get_icons_css() );

		Plugin::instance()->scripts->enqueue_script(
			'zb-editor',
			'editor',
			[
				'zb-common',
				'wp-auth-check',
				'heartbeat',
				'wp-i18n',
			],
			Plugin::instance()->get_version(),
			true
		);

		wp_set_script_translations( 'zb-editor', 'zionbuilder' );
		wp_localize_script( 'zb-editor', 'ZnPbInitialData', $this->get_editor_initial_data() );

		do_action( 'zionbuilder/editor/after_scripts' );
	}

	/**
	 * Get Editor Initial Data
	 *
	 * Returns the initial data required for the editor
	 *
	 * @return array<string, mixed> The initial data required by the editor interface
	 */
	private function get_editor_initial_data() {
		$locked_user_name = null;

		$post_instance = Plugin::$instance->post_manager->get_active_post_instance();

		if ( ! $post_instance ) {
			return [];
		}

		$locked_user_data = $this->get_locked_user( $this->post_id );
		if ( $locked_user_data ) {
			$locked_user_name = [
				/* translators: %s: The username who is locked from editing */
				'message' => sprintf( __( 'Post is locked by %s', 'zionbuilder' ), $locked_user_data->display_name ),
				'avatar'  => get_avatar_url( $locked_user_data->ID ),
			];
		}

		$autosave_instance = Plugin::$instance->post_manager->get_post_or_autosave_instance( $post_instance->get_post_id() );

		// Prepare content data
		Plugin::instance()->frontend->prepare_content_for_post_id( $this->post_id );

		return apply_filters(
			'zionbuilder/editor/initial_data',
			[
				'page_settings'      => [
					'schema' => $post_instance->get_page_settings_schema(),
					'values' => $autosave_instance->get_page_settings_values(),
				],
				'urls'               => [
					'assets_url'            => Utils::get_file_url( 'assets' ),
					'logo'                  => Whitelabel::get_logo_url(),
					'loader'                => Whitelabel::get_loader_url(),
					'getting_started_video' => Whitelabel::get_getting_started_video(),
					'edit_page'             => get_edit_post_link( $this->post_id, '' ),
					'zion_admin'            => admin_url( sprintf( 'admin.php?page=%s', Whitelabel::get_id() ) ),
					'preview_frame_url'     => $post_instance->get_preview_frame_url(),
					'preview_url'           => $post_instance->get_preview_url(),
					'all_pages_url'         => $post_instance->get_all_pages_url(),
					'purchase_url'          => 'https://zionbuilder.io/pricing/',
					'documentation_url'     => 'https://zionbuilder.io/help-center/',
					'free_changelog'        => 'https://zionbuilder.io/changelog-free-version/',
					'pro_changelog'         => 'https://zionbuilder.io/changelog-pro-version/',
					'ajax_url'              => admin_url( 'admin-ajax.php', 'relative' ),
					'plugin_root'           => Utils::get_file_url(),
				],
				'masks'              => Masks::get_shapes(),
				'builder_settings'   => [],
				'page_id'            => $this->post_id,
				'page_data'          => get_post( $this->post_id ),
				'autosaveInterval'   => AUTOSAVE_INTERVAL,

				// User data
				'post_lock_user'     => $locked_user_name,

				// Css classes
				'css_classes'        => CSSClasses::get_classes(),
				'css_static_classes' => CSSClasses::get_static_classes(),

				// Plugin info
				'plugin_info'        => [
					'is_pro_active'    => Utils::is_pro_active(),
					'is_pro_installed' => Utils::is_pro_installed(),
				],

				// Templates
				'template_types'     => Plugin::$instance->templates->get_template_types(),
				'template_sources'   => Plugin::$instance->library->get_sources(),

				// Misc
				'rtl'                => is_rtl(),

				// User data
				'user_data'          => User::get_user_data(),
				'user_permissions'   => Permissions::get_user_permissions(),
			]
		);
	}





	/**
	 * Print Editor Template
	 *
	 * Will include the editor template file
	 *
	 * @return void
	 */
	private function print_editor_template() {
		include Utils::get_file_path( 'includes/Editor/partials/editor-template.php' );
	}
}
