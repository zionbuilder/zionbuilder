<?php

namespace ZionBuilder\Editor;

use ZionBuilder\Permissions;
use ZionBuilder\Plugin;
use ZionBuilder\Nonces;
use ZionBuilder\Settings;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Preview
 *
 * @package ZionBuilder\Editor
 */
class Preview {
	const CONTENT_FILTER_PRIORITY = 999999;

	/**
	 * Holds a refference to all enqueued scripts
	 */
	private static $enqueued_scripts = [];

	/**
	 * Preview constructor.
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'disable_admin_bar' ] );
		add_action( 'zionbuilder/frontend/init', [ $this, 'init' ] );
	}

	/**
	 * Disable Admin Bar
	 *
	 * This runs on WP action since the admin bar is initialised on template_redirect action with 0 priority
	 *
	 * @return void
	 */
	public function disable_admin_bar() {
		if ( $this->is_preview_mode() ) {
			add_theme_support( 'admin-bar', [ 'callback' => '__return_false' ] );
			add_filter( 'show_admin_bar', '__return_false' );
		}
	}

	public function init() {
		// Don't run if the user doesn't have permissions or we are on the admin page
		if ( is_admin() || ! $this->is_preview_mode() ) {
			return;
		}

		$post_instance = Plugin::$instance->post_manager->get_post_or_autosave_instance( $this->get_current_post_id() );
		if ( ! $post_instance ) {
			return;
		}

		// Disable caching on this page
		nocache_headers();

		// Don't redirect to permalink.
		remove_action( 'template_redirect', 'redirect_canonical' );

		// Prepare content
		$post_template_data = $post_instance->get_template_data();
		Plugin::$instance->renderer->register_area( $this->get_current_post_id(), $post_template_data );

		// We are in preview mode now
		add_filter( 'the_content', [ $this, 'add_content_filter' ], self::CONTENT_FILTER_PRIORITY );

		// Set body class
		add_filter( 'body_class', [ $this, 'add_body_class' ] );

		// Load preview scripts. We use a high order so we can create a list of other loaded scripts
		// Load styles before theme styles
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 9 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_filter( 'script_loader_tag', [ $this, 'on_script_loading' ], 10, 3 );
	}

	public function add_body_class( $classes ) {
		$builder_theme = Settings::get_value_from_path( 'appearance.builder_theme', 'light' );
		$classes[]     = sprintf( 'znpb-theme-%s', $builder_theme );
		$classes[]     = 'znpb-editor-preview';
		return $classes;
	}

	public function on_script_loading( $tag, $handle, $src ) {
		// Save the src
		self::$enqueued_scripts[] = $src;

		if ( $handle === 'znpb-preview-frame-scripts' ) {
			$scripts_json_data = wp_json_encode( self::$enqueued_scripts );
			$script            = "
				var ZnPbLoadedScripts = {$scripts_json_data};
			";

			$before_handle = sprintf( "<script type='text/javascript'>\n%s\n</script>\n", $script );

			return $before_handle . $tag;
		}

		return $tag;
	}

	public function enqueue_scripts() {
		// Trigger action before load scripts
		do_action( 'zionbuilder/preview/before_load_scripts', $this );

		wp_enqueue_media();

		wp_enqueue_style( 'zion-frontend-animations' );
		wp_enqueue_script( 'zionbuilder-animatejs' );
		wp_enqueue_script( 'zb-video-bg' );

		Plugin::instance()->scripts->enqueue_script(
			'znpb-preview-frame-scripts',
			'js/preview.js',
			[
				'zb-components',
				'wp-tinymce',
			],
			Plugin::instance()->get_version(),
			true
		);

		wp_localize_script( 'znpb-preview-frame-scripts', 'ZnPbPreviewData', $this->get_preview_initial_data() );

		wp_add_inline_script(
			'znpb-preview-frame-scripts',
			'
                (function($) {
                    window.ZionBuilderFrontend = {
                        scripts: {},
                        registerScript: function (scriptId, scriptCallback) {
                            this.scripts[scriptId] = scriptCallback;
                        },
                        getScript(scriptId) {
                            return this.scripts[scriptId]
                        },
                        unregisterScript: function(scriptId) {
                            delete this.scripts[scriptId];
                        },
                        run: function() {
                            var that = this;
                            var $scope = $(document)
                            Object.keys(this.scripts).forEach(function(scriptId) {
                                var scriptObject = that.scripts[scriptId];
                                scriptObject.run( $scope );
                            })
                        }
                    };
                })(jQuery);
            ',
			'before'
		);

		do_action( 'zionbuilder/preview/after_load_scripts', $this );
	}

	public function enqueue_styles() {
		// Trigger action before load styles
		do_action( 'zionbuilder/preview/before_load_styles', $this );

		// Load roboto font
		wp_enqueue_style( 'znpb-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i&display=swap&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese', [], Plugin::instance()->get_version() );

		Plugin::instance()->scripts->register_style(
			'znpb-editor-styles',
			'css/editor.css',
			[],
			Plugin::instance()->get_version()
		);

		Plugin::instance()->scripts->enqueue_style(
			'znpb-preview-frame-styles',
			'css/preview.css',
			[
				'zb-components',
				'znpb-editor-styles',
			],
			Plugin::instance()->get_version()
		);

		// Load rtl
		if ( is_rtl() ) {
			Plugin::instance()->scripts->enqueue_style(
				'znpb-preview-rtl-styles',
				'css/rtl.css',
				[],
				Plugin::instance()->get_version()
			);
		};

		// This is needed because wp_editor somehow unloads dashicons
		wp_print_styles( 'media-views' );

		do_action( 'zionbuilder/preview/after_load_styles', $this );
	}

	public function get_preview_initial_data() {
		return [
			'nonce'                   => Nonces::generate_nonce( 'preview-frame' ),
			'page_content'            => Plugin::$instance->renderer->get_registered_areas(),
			'template_types'          => Plugin::$instance->templates->get_template_types(),
			'elements_data'           => Plugin::$instance->elements_manager->get_elements_config_for_editor(),
			'preview_app_css_classes' => apply_filters( 'zionbuilder/preview/app/css_classes', [] ),
		];
	}

	/**
	 * Add Content Filter
	 *
	 * Replaces post content with our pagebuilder area data
	 * In preview mode we are rendering the elements for dynamic templating. We need to add the
	 * filter only to the post that mathces the preview post id
	 *
	 * @since 1.0.0
	 *
	 * @return string The div containing content area id
	 */
	public function add_content_filter() {
		if ( get_the_ID() === $this->get_current_post_id() ) {
			return apply_filters( 'zionbuilder/preview/content', $this->render_area( $this->get_current_post_id() ) );
		}
		return '';
	}

	public function render_area( $area_id ) {
		return '<div id="znpb-preview-' . $area_id . '-area"></div>';
	}


	/**
	 * Is Preview Mode
	 *
	 * Checks if the current page is in edit mode
	 *
	 * @return boolean
	 */
	public function is_preview_mode() {

		// Verify nonce
		if ( ! Nonces::verify_nonce( Nonces::EDITOR_PREVIEW_FRAME ) ) {
			return false;
		}

		// Check post type and user permissions
		if ( ! Permissions::can_edit_post( $this->get_current_post_id() ) ) {
			return false;
		}

		return true;
	}

	private function get_current_post_id() {
		return isset( $_GET['zionbuilder-preview'] ) ? absint( $_GET['zionbuilder-preview'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification
	}
}
