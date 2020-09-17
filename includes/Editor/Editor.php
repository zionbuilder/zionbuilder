<?php

namespace ZionBuilder\Editor;

use ZionBuilder\Utils;
use ZionBuilder\Nonces;
use ZionBuilder\Plugin;
use ZionBuilder\Permissions;
use ZionBuilder\Localization;
use ZionBuilder\CSSClasses;
use ZionBuilder\Options\Schemas\StyleOptions;
use ZionBuilder\Options\Schemas\Typography;
use ZionBuilder\Options\Schemas\Advanced;
use ZionBuilder\Options\Schemas\Video;
use ZionBuilder\Options\Schemas\BackgroundImage;
use ZionBuilder\Options\Schemas\Shadow;
use ZionBuilder\Elements\Masks;
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
	 * Holds a refference to the current post id
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

	/**
	 * Init
	 *
	 * Will show the editor and load all scripts and styles needed for the editor
	 *
	 * @return void
	 */
	public function init() {

		// Verify nonce
		if ( ! Nonces::verify_nonce( Nonces::ACTIVE_EDITOR ) ) {
			return;
		}

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

		// Set post lock if not locked already
		if ( ! $this->get_locked_user( $this->post_id ) ) {
			wp_set_post_lock( $this->post_id );
		}

		add_filter( 'heartbeat_settings', [ $this, 'set_heartbeat_settings' ] );

		// Remove admin bar
		add_filter( 'show_admin_bar', '__return_false' );

		// Remove title tag as it is manually added by the template
		remove_theme_support( 'title-tag' );

		// remove all actions
		$this->remove_wp_actions();

		// Add head actions again
		add_action( 'wp_head', '_wp_render_title_tag', 1 );
		add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		add_action( 'wp_head', 'wp_print_styles', 8 );
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

		// Register our scripts
		Plugin::instance()->register_scripts();

		do_action( 'zionbuilder/editor/before_scripts' );

		// Custom HTML widgets
		$wp_scripts->add( 'custom-html-widgets', admin_url( 'js/widgets/custom-html-widgets.js' ), [ 'jquery', 'backbone', 'wp-util', 'jquery-ui-core', 'wp-a11y' ] );

		wp_enqueue_style( 'widgets' );
		wp_enqueue_style( 'media-views' );

		do_action( 'admin_print_scripts-widgets.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName
		do_action( 'admin_footer-widgets.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName

		wp_enqueue_media();

		// This is needed because wp_editor somehow unloads dashicons
		wp_print_styles( 'media-views' );

		Plugin::$instance->cache->enqueue_dynamic_css();

		// Load roboto font
		wp_enqueue_style( 'znpb-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i&display=swap&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese', [], Plugin::instance()->get_version() );

		// Load Scripts
		Plugin::instance()->scripts->enqueue_style(
			'zion-editor-style',
			'css/editor.css',
			[
				'wp-codemirror',
				'zb-common',
			],
			Plugin::instance()->get_version()
		);

		// Load styles
		wp_enqueue_style(
			'zion-editor-style',
			Utils::get_file_url( 'dist/css/editor.css' ),
			[
				'wp-codemirror',
			],
			Plugin::instance()->get_version()
		);

		// Load animations
		wp_enqueue_style( 'zion-frontend-animations', plugins_url( 'zionbuilder/assets/vendors/css/animate.css' ), [], Plugin::instance()->get_version() );

		// Load Scripts
		Plugin::instance()->scripts->enqueue_script(
			'zion-editor-script',
			'js/editor.js',
			[
				'wp-auth-check',
				'heartbeat',
				'wp-codemirror',
				'csslint',
				'htmlhint',
				'jshint',
				'jsonlint',
				'jquery-masonry',
				'zb-common',
			],
			Plugin::instance()->get_version(),
			true
		);

		wp_localize_script(
			'zion-editor-script',
			'ZnPbRestConfig',
			[
				'nonce'     => Nonces::generate_nonce( Nonces::REST_API ),
				'rest_root' => esc_url_raw( rest_url() ),
			]
		);

		wp_localize_script( 'zion-editor-script', 'ZnPbInitalData', $this->get_editor_initial_data() );

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

		$plugin_updates = get_site_transient( 'update_plugins' );

		$free_plugin_update = null;
		$pro_plugin_update  = null;
		if ( isset( $plugin_updates->response ) && is_array( $plugin_updates->response ) ) {
			if ( isset( $plugin_updates->response['zionbuilder/zionbuilder.php'] ) ) {
				$free_plugin_update = $plugin_updates->response['zionbuilder/zionbuilder.php'];
			}
		}

		if ( isset( $plugin_updates->response ) && is_array( $plugin_updates->response ) ) {
			if ( isset( $plugin_updates->response['zionbuilder-pro/zionbuilder-pro.php'] ) ) {
				$pro_plugin_update = $plugin_updates->response['zionbuilder-pro/zionbuilder-pro.php'];
			}
		}

		return apply_filters(
			'zionbuilder/editor/initial_data',
			[
				'l10n'                           => Localization::get_strings(),
				'page_settings'                  => [
					'schema' => $post_instance->get_page_settings_schema(),
					'values' => $post_instance->get_page_settings_values(),
				],
				'urls'                           => [
					'assets_url'        => Utils::get_file_url( 'assets' ),
					'logo'              => Utils::get_logo_url(),
					'loader'            => Utils::get_loader_url(),
					'edit_page'         => get_edit_post_link( $this->post_id, '' ),
					'zion_admin'        => admin_url( 'admin.php?page=zionbuilder' ),
					'preview_frame_url' => $post_instance->get_preview_frame_url(),
					'preview_url'       => $post_instance->get_preview_url(),
					'all_pages_url'     => $post_instance->get_all_pages_url(),
					'purchase_url'      => 'https://zionbuilder.io/pricing/',
					'ajax_url'          => admin_url( 'admin-ajax.php', 'relative' ),
				],
				'masks'                          => Masks::getshapes(),
				'builder_settings'               => [],
				'element_default_options_schema' => [
					'style_options'           => StyleOptions::get_schema(),
					'advanced_options'        => Advanced::get_schema(),
					'typography_schema'       => Typography::get_schema(),
					'video_schema'            => Video::get_schema(),
					'background_image_schema' => BackgroundImage::get_schema(),
					'shadow'                  => Shadow::get_schema(),
				],
				'page_id'                        => $this->post_id,
				'page_data'                      => get_post( $this->post_id ),
				'autosaveInterval'               => AUTOSAVE_INTERVAL,

				// Elements data
				'elements_data'                  => Plugin::$instance->elements_manager->get_elements_config_for_editor(),
				'elements_categories'            => Plugin::$instance->elements_manager->get_elements_categories(),

				// User data
				'post_lock_user'                 => $locked_user_name,
				'wp_editor'                      => $this->get_wp_editor(),

				// Css classes
				'css_classes'                    => CSSClasses::get_classes(),

				// Plugin info
				'plugin_info'                    => [
					'is_pro_active'      => Utils::is_pro_active(),
					'is_pro_connected'   => apply_filters( 'zionbuilder/pro/is_conected', false ),
					'free_version'       => Plugin::instance()->get_version(),
					'pro_version'        => ( Utils::is_pro_active() && defined( 'ZIONBUILDERPRO_VERSION' ) ? Plugin::instance()->get_version() : null ),
					'free_plugin_update' => $free_plugin_update,
					'pro_plugin_update'  => $pro_plugin_update,
				],

				// Templates
				'template_types'                 => Plugin::$instance->templates->get_template_types(),
				'template_categories'            => Plugin::$instance->templates->get_template_categories(),
			]
		);
	}


	/**
	 * Returns the HTML content for the WP editor
	 *
	 * @return string|false The editor HTML content or false on failure
	 */
	public function get_wp_editor() {
		// Remove all TinyMCE plugins.
		remove_all_filters( 'mce_buttons', 10 );
		remove_all_filters( 'mce_external_plugins', 10 );

		ob_start();
		wp_editor(
			'%%ZNPB_EDITOR_CONTENT%%',
			'znpbwpeditorid',
			[
				'editor_height' => '150',
			]
		);
		$wp_editor = ob_get_clean();

		return $wp_editor;
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
