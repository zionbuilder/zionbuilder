<?php

namespace ZionBuilder\Editor;

use ZionBuilder\Scripts;
use ZionBuilder\Permissions;
use ZionBuilder\Plugin;
use ZionBuilder\Nonces;
use ZionBuilder\Settings;
use ZionBuilder\Elements\Masks;
use ZionBuilder\Utils;
use ZionBuilder\Whitelabel;
use ZionBuilder\User;

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
	 * Preview constructor.
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'disable_admin_bar' ] );
		add_action( 'zionbuilder/frontend/init', [ $this, 'init' ] );
	}

	/**
	 * Disable Admin Bar
	 *
	 * This runs on WP action since the admin bar is initialized on template_redirect action with 0 priority
	 *
	 * @return void
	 */
	public function disable_admin_bar() {
		if ( $this->is_preview_mode() ) {
			add_theme_support( 'admin-bar', [ 'callback' => '__return_false' ] );
			add_filter( 'show_admin_bar', '__return_false' );
			add_filter( 'style_loader_tag', [ __CLASS__, 'add_cross_origin_to_google_fonts' ] );
		}
	}

	/**
	 * Adds crossorigin attribute to google fonts. This is needed for html-to-image to work
	 *
	 * @return string
	 */
	public static function add_cross_origin_to_google_fonts( $html ) {
		if ( strpos( $html, 'fonts.googleapis.com' ) !== false ) {
			return str_replace( "media='all'", "media='all' crossorigin='anonymous'", $html );
		}

		return $html;
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
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 9 );
		add_action( 'wp_footer', [ $this, 'add_data' ] );
	}

	public function add_body_class( $classes ) {
		$builder_theme = Settings::get_value_from_path( 'appearance.builder_theme', 'light' );
		$classes[]     = sprintf( 'znpb-theme-%s', $builder_theme );
		$classes[]     = 'znpb-editor-preview';
		return $classes;
	}

	public function add_data() {
		?>
			<script type="text/javascript">
				var ZnPbInitialData = <?php echo wp_json_encode( $this->get_preview_initial_data() ); ?>;

				/**
				 * Adds backwards compatibility with plugins that use this variable
				 * 
				 * @deprecated 3.6.0
				 */
				var zionBuilderPaths = {
					appName: 'zionbuilder'
				}
			</script>
		<?php
	}

	public function enqueue_scripts() {
		// Trigger action before load scripts
		do_action( 'zionbuilder/preview/before_load_scripts', $this );
		do_action( 'zionbuilder/preview/before_load_styles', $this );

		// Enqueue common js and css. This is needed as we need to style the element toolboxes
		Scripts::enqueue_common();

		wp_enqueue_style( 'zion-frontend-animations' );
		wp_enqueue_script( 'zionbuilder-animatejs' );
		wp_enqueue_script( 'zb-video' );

		Plugin::instance()->scripts->enqueue_style(
			'znpb-preview-frame-styles',
			'editor',
			[
				'zb-common',
			],
			Plugin::instance()->get_version()
		);

		do_action( 'zionbuilder/preview/after_load_scripts', $this );
		do_action( 'zionbuilder/preview/after_load_styles', $this );
	}

	public function get_preview_initial_data() {
		$post_instance = Plugin::$instance->post_manager->get_active_post_instance();

		$plugin_updates = get_site_transient( 'update_plugins' );

		return [
			'nonce'                   => Nonces::generate_nonce( 'preview-frame' ),
			'page_content'            => Plugin::$instance->renderer->get_registered_areas(),
			'template_types'          => Plugin::$instance->templates->get_template_types(),

			// Elements data - This needs to be loaded from preview so we can load all their scripts and styles
			'elements_categories'     => Plugin::$instance->elements_manager->get_elements_categories(),
			'elements_data'           => Plugin::$instance->elements_manager->get_elements_config_for_editor(),

			'preview_app_css_classes' => apply_filters( 'zionbuilder/preview/app/css_classes', [] ),
			'post'                    => get_post(),
			'masks'                   => Masks::get_shapes(),
			'plugin_info'             => [
				'is_pro_active'    => Utils::is_pro_active(),
				'is_pro_installed' => Utils::is_pro_installed(),
			],
			'urls'                    => [
				'assets_url'        => Utils::get_file_url( 'assets' ),
				'logo'              => Whitelabel::get_logo_url(),
				'loader'            => Whitelabel::get_loader_url(),
				'edit_page'         => get_edit_post_link( $post_instance->get_post_id(), '' ),
				'zion_admin'        => admin_url( sprintf( 'admin.php?page=%s', Whitelabel::get_id() ) ),
				'preview_frame_url' => $post_instance->get_preview_frame_url(),
				'preview_url'       => $post_instance->get_preview_url(),
				'all_pages_url'     => $post_instance->get_all_pages_url(),
				'purchase_url'      => 'https://zionbuilder.io/pricing/',
				'documentation_url' => 'https://zionbuilder.io/help-center/',
				'free_changelog'    => 'https://zionbuilder.io/changelog-free-version/',
				'pro_changelog'     => 'https://zionbuilder.io/changelog-pro-version/',
				'ajax_url'          => admin_url( 'admin-ajax.php', 'relative' ),
			],
			'user_data'               => User::get_user_data(),
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
