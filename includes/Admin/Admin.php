<?php

namespace ZionBuilder\Admin;

use ZionBuilder\Utils;
use ZionBuilder\Plugin;
use ZionBuilder\Permissions;
use ZionBuilder\Settings;
use ZionBuilder\Whitelabel;
use ZionBuilder\WPMedia;
use ZionBuilder\Templates;
use ZionBuilder\Options\Schemas\Performance;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}


/**
 * Class Admin
 *
 * Admin class
 *
 * Will handle all interactions with the WordPress admin area and the page builder
 *
 * @package ZionBuilder\Admin
 */
class Admin {
	/**
	 * Admin constructor.
	 */
	public function __construct() {
		new WPMedia();

		// Add edit with page builder links to post/page row actions
		add_filter( 'page_row_actions', [ $this, 'add_edit_links_to_rows' ], 10, 2 );
		add_filter( 'post_row_actions', [ $this, 'add_edit_links_to_rows' ], 10, 2 );
		add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );

		// Set editor status on body class
		add_filter( 'admin_body_class', [ $this, 'set_admin_body_status_classes' ] );

		// Add the edit with page builder button on post/pages
		add_action( 'edit_form_after_title', [ $this, 'add_editor_button_to_page' ] );
		add_action( 'edit_form_after_title', [ $this, 'add_editor_block' ], 11 );

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 11 ); // use 11 priority so we can fix bad written plugins loading their scripts on our pages

		// Disable editor
		add_filter( 'heartbeat_received', [ $this, 'on_heartbeat_received' ], 10, 2 );

		// Add admin page
		add_action( 'admin_menu', [ $this, 'add_admin_page' ] );

		// Set admin body class
		add_filter( 'admin_body_class', [ $this, 'set_builder_theme' ] );
	}

	/**
	 * Set editor status on ajax request
	 *
	 * @param array $response
	 * @param array $data     The event data received
	 *
	 * @return array
	 */
	public function on_heartbeat_received( $response, $data ) {
		if ( ! isset( $data['zion_builder_status'] ) || ! isset( $data['zion_builder_post_id'] ) ) {
			return $response;
		}

		$status        = ( 'true' === $data['zion_builder_status'] ) ? true : false;
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $data['zion_builder_post_id'] );

		if ( $post_instance ) {
			$post_instance->set_builder_status( $status );
			$response['zion_builder_status'] = $post_instance->is_built_with_zion();
		}

		return $response;
	}

	public function set_admin_body_status_classes( $classes ) {
		global $pagenow;

		$post_type = get_post_type();
		// Check if post type can be edited and user permissions
		if ( ! Permissions::allowed_post_type( $post_type ) ) {
			return $classes;
		}

		$edit_pages = [
			'post.php',
			'post-new.php',
		];

		if ( in_array( $pagenow, $edit_pages, true ) ) {
			$post          = get_post();
			$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );
			if ( $post_instance->is_built_with_zion() ) {
				$classes .= ' znpb-admin-post-editor--active';
			}
		}

		return $classes;
	}

	public function display_post_states( $post_states, $post ) {
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );

		if ( $post_instance->is_built_with_zion() ) {
			$post_states['zionbuilder'] = Whitelabel::get_title();
		}

		return $post_states;
	}

	/**
	 * Load scripts
	 *
	 * @param string $hook
	 */
	public function enqueue_scripts( $hook = '' ) {
		wp_add_inline_style(
			'admin-menu',
			sprintf(
				'#adminmenu .toplevel_page_%s img {
					max-width: 100%%;
					height: auto;
					padding: 8px;
					box-sizing: border-box;
				}',
				Whitelabel::get_id()
			)
		);

		// Invert the color for admin icon
		if ( Utils::is_pro_active() && ! empty( Whitelabel::get_logo_url() ) ) {
			wp_add_inline_style(
				'admin-menu',
				'#adminmenu .toplevel_page_zionbuilder img { filter: invert(1); }'
			);
		}

		// Load scripts on edit page
		if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
			global $post;

			// Don't proceed if the current user cannot edit this page
			if ( ! Permissions::can_edit_post( $post->ID ) ) {
				return;
			}

			$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );

			Plugin::instance()->scripts->enqueue_style(
				'znpb-admin-post-styles',
				'css/edit-page.css',
				[],
				Plugin::instance()->get_version()
			);

			Plugin::instance()->scripts->enqueue_script(
				'znpb-admin-post-script',
				'js/edit-page.js',
				[ 'heartbeat' ],
				Plugin::instance()->get_version(),
				true
			);

			wp_localize_script(
				'znpb-admin-post-script',
				'ZnPbEditPostData',
				[
					// Set multi dimension to prevent WP casting to strings
					'data' => [
						'post_id'           => $post->ID,
						'is_editor_enabled' => $post_instance && $post_instance->is_built_with_zion(),
						'l10n'              => [
							'wp_heartbeat_disabled' => esc_html__( 'WordPress Heartbeat is disabled. Zion builder requires it in order to function properly', 'zionbuilder' ),
						],
					],
				]
			);
		}

		// Admin settings page
		$admin_hook = sprintf( 'toplevel_page_%s', Whitelabel::get_id() );
		if ( $admin_hook === $hook ) {
			do_action( 'zionbuilder/admin/before_admin_scripts' );

			wp_enqueue_style(
				'znpb-roboto-font',
				'https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i&display=swap&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese',
				[],
				Plugin::instance()->get_version()
			);

			// Load styles
			Plugin::instance()->scripts->enqueue_style(
				'znpb-admin-settings-page-styles',
				'css/admin.css',
				[ 'zb-components' ],
				Plugin::instance()->get_version()
			);

			if ( is_rtl() ) {
				Plugin::instance()->scripts->enqueue_style(
					'znpb-admin-rtl-styles',
					'css/rtl.css',
					[],
					Plugin::instance()->get_version()
				);
			};

			Plugin::instance()->scripts->enqueue_script(
				'zb-admin',
				'js/admin.js',
				[
					'zb-components',
				],
				Plugin::$instance->get_version(),
				true
			);

			wp_enqueue_media();

			// This is needed because wp_editor somehow unloads dashicons
			wp_print_styles( 'media-views' );

			wp_localize_script(
				'zb-admin',
				'ZnPbAdminPageData',
				apply_filters(
					'zionbuilder/admin/initial_data',
					[
						'is_pro_active'    => Utils::is_pro_active(),
						'template_types'   => Plugin::$instance->templates->get_template_types(),
						'template_sources' => Plugin::$instance->library->get_sources(),
						'plugin_version'   => Plugin::$instance->get_version(),
						'schemas'          => [
							'performance' => Performance::get_schema(),
						],
						'appearance'       => [
							'schema' => [
								'builder_theme' => [
									'type'      => 'custom_selector',
									'title'     => esc_html__( 'Builder theme.', 'zionbuilder' ),
									'default'   => 'light',
									'on_change' => 'znpb_set_editor_theme',
									'options'   => [
										[
											'name' => __( 'light', 'zionbuilder' ),
											'id'   => 'light',
										],
										[
											'name' => __( 'dark', 'zionbuilder' ),
											'id'   => 'dark',
										],
									],
								],
							],
						],
						'custom_code'      => [
							'schema' => [
								'custom_css'     => [
									'type'        => 'code',
									'description' => esc_html__( 'Add css that will be applied to all pages.', 'zionbuilder' ),
									'title'       => esc_html__( 'Custom css', 'zionbuilder' ),
									'mode'        => 'css',
								],
								'header_scripts' => [
									'type'        => 'code',
									'description' => esc_html__( 'Add scripts that will be placed just before the closing </head> tag.', 'zionbuilder' ),
									'title'       => esc_html__( 'Header scripts', 'zionbuilder' ),
									'mode'        => 'html',
								],
								'body_scripts'   => [
									'type'        => 'code',
									'description' => esc_html__( 'Add scripts that will be placed just after the <body> opening tag.', 'zionbuilder' ),
									'title'       => esc_html__( 'Body scripts', 'zionbuilder' ),
									'mode'        => 'html',
								],
								'footer_scripts' => [
									'type'        => 'code',
									'description' => esc_html__( 'Add scripts that will be placed just before the closing </body> tag.', 'zionbuilder' ),
									'title'       => esc_html__( 'Footer scripts', 'zionbuilder' ),
									'mode'        => 'html',
								],
							],
						],
						'urls'             => [
							'logo'     => Whitelabel::get_logo_url(),
							'pro_logo' => Utils::get_pro_png_url(),
						],
					]
				)
			);

			do_action( 'zionbuilder/admin/after_admin_scripts' );
		}
	}


	/**
	 * Adds the render with page builder action to the inline actions
	 *
	 * @access public
	 *
	 * @param array    $actions
	 * @param \WP_Post $post
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function add_edit_links_to_rows( $actions, \WP_Post $post ) {
		// Check if post type can be edited and user permissions
		if ( ! Permissions::can_edit_post( $post->ID ) ) {
			return $actions;
		}

		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );
		if ( $post_instance->is_built_with_zion() ) {
			$whitelabel_title = sprintf( 'Edit with %s', Whitelabel::get_title() );

			$actions['zionbuilder_edit_link'] = '<a href="' . $post_instance->get_edit_url() . '">' . $whitelabel_title . '</a>';
		}

		return $actions;
	}

	/**
	 * Add edit with Zion Builder button to pages edit screen
	 *
	 * @uses $post
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post
	 *
	 * @return void The HTML output for the editor buttons
	 */
	public function add_editor_button_to_page( \WP_Post $post ) {
		// Don't proceed if the current user cannot edit this page
		if ( ! Permissions::can_edit_post( $post->ID ) ) {
			return;
		}

		// Get the post or auto-save status for editor
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );
		$editor_status = $post_instance->is_built_with_zion() ? 'active' : 'inactive';

		?>
		<div class="znpb-admin-post__edit znpb-admin-post__edit-status--<?php echo esc_attr( $editor_status ); ?>" data-toolbar-item="true">

			<a data-toolbar-item="true" href="#disable_editor" class="znpb-admin-post__edit-button znpb-admin-post__edit-button--deactivate">
				<span class="znpb-admin-post__edit-button-icon dashicons dashicons-wordpress-alt"></span>
				<span class="">
				<?php
				echo esc_html_e( 'Disable ', 'zionbuilder' ) . esc_html( Whitelabel::get_title() );
				?>
				</span>
			</a>

			<a data-toolbar-item="true" href="<?php echo esc_html( $post_instance->get_edit_url() ); ?>" class="znpb-admin-post__edit-button znpb-admin-post__edit-button--activate">
				<span class="znpb-admin-post__edit-button-icon znpb-admin-post--builder-mode znpb-editor-icon-wrapper">
					<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" class="zion-svg-inline zion-icon znpb-editor-icon">
						<path d="M4 4v42h42V4H4zm5 37V24.5h13.5V41H9zm32 0H27.5V24.5H41V41zm0-21.5H9V9h32v10.5z"/>
					</svg>
				</span>
				<span class=""><?php echo esc_html_e( 'Edit with ', 'zionbuilder' ) . esc_html( Whitelabel::get_title() ); ?></span>
			</a>

		</div>
		<?php
	}

	public function add_editor_block( $post ) {
		// Don't proceed if the current user cannot edit this page
		if ( ! Permissions::can_edit_post( $post->ID ) ) {
			return;
		}

		// Get the post or auto-save status for editor
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $post->ID );
		?>
			<div class="znpb-admin-post__edit-block">
				<a href="<?php echo esc_html( $post_instance->get_edit_url() ); ?>" class="znpb-admin-post__edit-button znpb-admin-post__edit-button--activate">
					<span class="znpb-admin-post__edit-button-icon znpb-admin-post--builder-mode znpb-editor-icon-wrapper">
						<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" class="zion-svg-inline zion-icon znpb-editor-icon">
							<path d="M4 4v42h42V4H4zm5 37V24.5h13.5V41H9zm32 0H27.5V24.5H41V41zm0-21.5H9V9h32v10.5z"/>
						</svg>
					</span>
					<span class=""><?php echo esc_html_e( 'Edit with ', 'zionbuilder' ) . esc_html( Whitelabel::get_title() ); ?></span>
				</a>
			</div>
		<?php
	}


	/**
	 * Add Admin Page
	 *
	 * Will add the editor admin page to WordPress menu
	 *
	 * @return void
	 */
	public function add_admin_page() {
		$admin_logo = ( Utils::is_pro_active() && ! empty( Whitelabel::get_logo_url() ) ) ? Whitelabel::get_logo_url() : Utils::get_file_url( 'assets/img/dash-icon.svg' );
		add_menu_page(
			Whitelabel::get_title(),
			Whitelabel::get_title(),
			'manage_options',
			Whitelabel::get_id(),
			[ $this, 'render_options_page' ],
			$admin_logo
		);
	}

	/**
	 * Render Options Page
	 *
	 * Will render the admin options page
	 */
	public function render_options_page() {
		echo '<div id="znpb-admin"></div>';
	}

	public function set_builder_theme( $classes ) {
		$builder_theme = Settings::get_value_from_path( 'appearance.builder_theme', 'light' );

		$classes = explode( ' ', $classes );

		$classes[] = sprintf( 'znpb-theme-%s', $builder_theme );

		return implode( ' ', $classes );
	}
}
