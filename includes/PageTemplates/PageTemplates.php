<?php

namespace ZionBuilder\PageTemplates;

use ZionBuilder\Plugin;
use ZionBuilder\Permissions;
use ZionBuilder\Whitelabel;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Page Templates
 *
 * Handles all page templates provided by the Zion Builder plugin
 */
class PageTemplates {
	/**
	 * PageTemplates constructor.
	 */
	public function __construct() {
		// Add our templates to template list dropdown
		add_filter( 'theme_templates', array( $this, 'register_page_templates_options' ), 10, 4 );
		// Check and load our template
		add_filter( 'template_include', array( $this, 'template_include' ), 12 );
	}

	/**
	 * Get Custom Templates
	 *
	 * Returns the list of custom page templates that can be used by Zion Builder
	 *
	 * @return array
	 */
	public function get_custom_templates() {
		return array(
			'zion_builder_full_width' => sprintf( '%s Full Width', Whitelabel::get_title() ),
			'zion_builder_blank'      => sprintf( '%s Blank Canvas', Whitelabel::get_title() ),
		);
	}

	/**
	 * Register Page Templates Options
	 *
	 * Adds the custom templates to templates selector dropdown
	 *
	 * @param array         $post_templates
	 * @param \WP_Theme     $wp_theme
	 * @param \WP_Post|null $post
	 * @param string        $post_type
	 *
	 * @return array
	 */
	public function register_page_templates_options( $post_templates, $wp_theme, $post, $post_type ) {
		$post_types = get_post_types_by_support( Permissions::POST_TYPE_EDIT_PERMISSION );

		// Only add the templates to post types supported by Zion Builder
		if ( is_array( $post_types ) && in_array( $post_type, $post_types, true ) ) {
			foreach ( $this->get_custom_templates() as $template_id => $template_name ) {
				$post_templates[$template_id] = $template_name;
			}
		}

		return $post_templates;
	}

	/**
	 * Modify Template Include
	 *
	 * @param string $template the path of the template to include
	 *
	 * @return string Path to the template that needs to be included
	 */
	public function template_include( $template ) {
		if ( ! is_singular() ) {
			return $template;
		}

		$post_instance = Plugin::$instance->post_manager->get_active_post_instance();

		if ( ! $post_instance ) {
			return $template;
		}

		// If we are on preview mode, check for autosave
		if ( Plugin::$instance->editor->preview->is_preview_mode() ) {
			// get_post_or_autosave_instance
			$post_instance = Plugin::$instance->post_manager->get_post_or_autosave_instance( $post_instance->get_post_id() );
		}

		$post_template = $post_instance->get_post_template();
		// Check to see if this is one of our templates
		if ( in_array( $post_template, array_keys( $this->get_custom_templates() ), true ) ) {
			// Fix for WP themes
			add_filter( 'body_class', array( $this, 'remove_body_classes' ) );
			$template = $this->get_template_file( $post_template );
		}

		/*
		 * Load barebone template if this is requested
		 * @see: admin templates page => preview
		 */
		if ( isset( $_GET['zionbuilder-barebone-preview'] ) && $_GET['zionbuilder-barebone-preview'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			$template = $this->get_template_file( 'zion_builder_blank' );
		}

		return $template;
	}

	/**
	 * Remove has-sidebar from body class as WP theme doesn't
	 * properly check if the seletor actually exists in page
	 * before using it.
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function remove_body_classes( $classes = array() ) {
		foreach ( $classes as $key => $css_class ) {
			if ( $css_class === 'has-sidebar' ) {
				unset( $classes[$key] );
			}
		}
		return $classes;
	}

	/**
	 * Get Template File
	 *
	 * Returns the template file path based on the specified template id
	 *
	 * @param string $template_id the template id for which we need to return the path
	 *
	 * @return string
	 */
	public function get_template_file( $template_id ) {
		return sprintf( '%s/templates/%s.php', __DIR__, $template_id );
	}

	/**
	 * Render Content
	 *
	 * Renders the page content
	 *
	 * @return void
	 */
	public function render_content() {
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
	}
}
