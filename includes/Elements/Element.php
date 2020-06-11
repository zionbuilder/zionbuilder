<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Utils;
use ZionBuilder\Elements\Style;
use ZionBuilder\Plugin;
use ZionBuilder\Options\Options;
use ZionBuilder\Icons;
use ZionBuilder\RenderAttributes;
use ZionBuilder\CustomCSS;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Element
 *
 * This is the base for each page builder element. All elements should extend this base class
 */
class Element {

	/**
	 * Element content
	 *
	 * @var array
	 */
	public $content = [];

	/**
	 * Element model
	 *
	 * @var Options
	 */
	public $options = null;

	/**
	 * Element uid
	 *
	 * @var string
	 */
	public $uid = null;

	/**
	 * If the elements holds multiple Elements inside different containers ( for example, accordion or tabs )
	 *
	 * @var boolean
	 */
	public $has_multiple = false;

	/**
	 * The element URL
	 *
	 * @var string
	 */
	protected $element_base_url = null;

	/**
	 * The element PATH
	 *
	 * @var string
	 */
	protected $element_base_path = null;

	/**
	 * Holds a refference to the element wrapper tag
	 *
	 * @var string
	 */
	protected $wrapper_tag = 'div';

	/**
	 * Holds a refference to the element style options tags
	 */
	protected $registered_style_options = [];

	// Adds extra data to element on render
	protected $extra_render_data = [];

	/**
	 * Holds the render attributes class object
	 *
	 * @var RenderAttributes
	 */
	protected $render_attributes = null;

	/**
	 * Holds the custom css class object
	 *
	 * @var CustomCSS
	 */
	protected $custom_css = null;

	/**
	 * Holds a list of editor scripts URLs
	 *
	 * @var array
	 */
	protected $editor_scripts = [];

	/**
	 * Holds a list of element scripts URLs
	 *
	 * @var array
	 */
	protected $element_scripts = [];

	/**
	 * Holds a list of editor styles URLs
	 *
	 * @var array
	 */
	protected $editor_styles = [];

	/**
	 * Holds a list of element styles URLs
	 *
	 * @var array
	 */
	protected $element_styles = [];

	/**
	 * Main class constructor
	 *
	 * @var array $data The saved values for the current element
	 */
	final public function __construct( $data = [] ) {
		// Allow elements creators to hook here without rewriting contruct
		$this->on_before_init( $data );

		$element_type  = $this->get_type();
		$this->options = new Options( sprintf( 'zionbuilder\element\%s\options', $element_type ) );

		// Register element options
		$this->options( $this->options );

		// Set the element data if provided
		if ( ! empty( $data ) ) {
			if ( isset( $data['uid'] ) ) {
				$this->uid = $data['uid'];
			}

			if ( isset( $data['content'] ) ) {
				$this->content = $data['content'];
			}

			if ( isset( $data['options'] ) ) {
				$this->options->set_model( $data['options'] );
			}

			// Setup helpers
			$this->render_attributes = new RenderAttributes();
			$this->custom_css        = new CustomCSS( $this->get_css_selector() );

			// loops through the options model and schema to set the proper model
			$this->options->parse_data( $this->render_attributes, $this->custom_css );

			// Setup render tags custom css classes
			$this->apply_custom_classes_to_render_tags();

		}

		// Allow elements creators to hook here without rewriting contruct
		$this->on_after_init( $data );
	}

	/**
	 * Attaches all custom css classes to render attributes
	 *
	 * @return void
	 */
	public function apply_custom_classes_to_render_tags() {
		// Set the custom css classes for element
		$element_saved_styles = $this->options->get_value( '_styles', [] );
		$styles_config        = $this->get_style_elements_for_editor();

		foreach ( $element_saved_styles as $style_config_id => $style_value ) {
			if ( isset( $styles_config[$style_config_id] ) ) {
				$style_config = $styles_config[$style_config_id];

				if ( isset( $style_config['render_tag'] ) && isset( $style_value['classes'] ) && is_array( $style_value['classes'] ) ) {

					foreach ( $style_value['classes'] as $css_class ) {
						$this->render_attributes->add( $style_config['render_tag'], 'class', $css_class );
					}
				}
			}
		}
	}

	/**
	 * Returns the css selector for this element
	 *
	 * @return string
	 */
	public function get_css_selector() {
		return sprintf( '#%s', $this->get_element_css_id() );
	}

	/**
	 * Get Type
	 *
	 * Will return the element nice name
	 *
	 * @return string
	 */
	public function get_type() {
		$element_class = $this->get_class_name();
		throw new \Exception( 'The Element should implement the get_type() method and it should return the element unique id. Found in ' . $element_class );
	}

	/**
	 * Is wrapper
	 *
	 * Returns true if the element can contain other elements ( f.e. section, column )
	 *
	 * @return boolean The element icon
	 */
	public function is_wrapper() {
		return false;
	}

	/**
	 * Dynamic options form
	 *
	 * Using this method you can have dinamically created options.
	 * This must be used in conjunction with the wp_widget option type
	 *
	 * @return string The list of options to send to editor
	 */
	public function dynamic_options_form() {
		return 'The dynamic_options_form() method must be implemented in child class';
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'content';
	}

	/**
	 * Get Name
	 *
	 * Will return the element nice name
	 *
	 * @return string
	 */
	public function get_name() {
		$element_class = $this->get_class_name();
		throw new \Exception( 'The Element should implement the get_name() method and it should return the element name. Found in ' . $element_class );
	}

	/**
	 * Get icon
	 *
	 * Will return the element icon
	 *
	 * @return string
	 */
	public function get_element_icon() {
		return '';
	}

	/**
	 * Get Keywords
	 *
	 * Will return the list of keywords for the current element
	 *
	 * @return array<string> The keywords list
	 */
	public function get_keywords() {
		return [];
	}

	/**
	 * Get Element Image
	 *
	 * Will return the element Image
	 *
	 * @return string
	 */
	public function get_element_image() {
		$image = null;
		if ( is_file( $this->get_path( 'icon.png' ) ) ) {
			$image = $this->get_url( 'icon.png' );
		} elseif ( is_file( $this->get_path( 'icon.svg' ) ) ) {
			$image = $this->get_url( 'icon.svg' );
		}

		return $image;
	}

	/**
	 * Registers the element options
	 *
	 * @param Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
	}



	/**
	 * On init
	 *
	 * Allow the users to add their own initialization process without extending __construct
	 *
	 * @param array $data
	 */
	public function on_after_init( $data = [] ) {
		return null;
	}

	/**
	 * On before init
	 *
	 * Allow the users to add their own initialization process without extending __construct
	 *
	 * @param array $data
	 */
	public function on_before_init( $data = [] ) {
		return null;
	}


	/**
	 * Checks if the element can render. It must be implemented in child class
	 *
	 * @return boolean
	 */
	private function can_render() {
		return true;
	}

	/**
	 * @return string
	 */
	public function css() {
		//#! TODO @Stefan: Implement Element::css() method
		return '';
	}


	/**
	 * Is Legacy
	 *
	 * By returning true, this element won't appear in element list,
	 * however, it will be visible in page if it is used
	 */
	public function is_legacy() {
		return false;
	}

	/**
	 * Is Deprecated
	 *
	 * If it returns true, the element will not show in the add elements panel however it will be used in page if it was already present
	 *
	 * @return boolean
	 */
	public function is_deprecated() {
		return false;
	}


	public function internal_get_config_for_editor() {
		$show_in_ui = $this->is_child() ? false : $this->show_in_ui();

		$config = [
			'element_type'        => $this->get_type(),
			'name'                => $this->get_name(),
			'category'            => $this->get_category(),
			'legacy'              => $this->is_legacy(),
			'keywords'            => $this->get_keywords(),
			'has_multiple'        => $this->has_multiple,
			'options'             => $this->options->get_schema(),
			'wrapper'             => $this->is_wrapper(),
			'content_composition' => $this->content_composition(),
			'style_elements'      => $this->get_style_elements_for_editor(),
			'label'               => $this->get_label(),
			'show_in_ui'          => $show_in_ui,
			'is_child'            => $this->is_child(),
			'scripts'             => $this->get_element_scripts_for_editor(),
			'styles'              => $this->get_element_styles_for_editor(),
		];

		// Add extra data
		$extra_data = $this->extra_data();
		if ( $extra_data ) {
			$config['extra_data'] = $extra_data;
		}

		$icon = $this->get_element_icon();

		if ( $icon ) {
			$config['icon'] = $icon;
		} else {
			$config['thumb'] = $this->get_element_image();
		}

		return wp_parse_args( $this->get_config_for_editor(), $config );
	}


	/**
	 * Get style elements for editor
	 *
	 * Returns the full list of styled elements/tags
	 * for this element
	 */
	public function get_style_elements_for_editor() {
		$this->register_wrapper_style_options();

		// Register element style options
		$this->on_register_styles();

		return $this->registered_style_options;
	}

	/**
	 * Show in ui
	 *
	 * Will register the current element without showing it in editor
	 *
	 * This type of elements cannot be added from add elements popup
	 * and don't have any toolbox or events attached to them
	 * They are mainly used as childs for other elements
	 */
	public function show_in_ui() {
		return true;
	}

	/**
	 * Is child
	 *
	 * Will register the current element as a child of another
	 *
	 * Child elements are not visible in add elements popup and cannot be
	 * interacted with them directly
	 */
	public function is_child() {
		return false;
	}

	/**
	 * Register wrapper style options
	 *
	 * Will register the style options for the wrapper
	 *
	 * @uses register_style_options_element
	 */
	private function register_wrapper_style_options() {
		$this->register_style_options_element(
			'wrapper',
			[
				'title'      => esc_html__( 'Wrapper', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}}',
				'config'     => [
					'show_background_video' => true,
				],
				'render_tag' => 'wrapper',
			]
		);
	}


	/**
	 * Get label
	 *
	 * Returns the label configuration that will appear in element list
	 */
	public function get_label() {
		return false;
	}


	/**
	 * Get style elements
	 *
	 * Returns a list of elements/tags that for which you
	 * want to show style options
	 */
	public function on_register_styles() {
	}


	/**
	 * Register style options
	 *
	 * @param mixed $id
	 * @param mixed $config
	 */
	protected function register_style_options_element( $id, $config ) {
		$this->registered_style_options[$id] = $config;
	}

	/**
	 * Get config for editor
	 *
	 * Allow elements to add extra data to the page builder elements
	 * in edit mode
	 */
	public function get_config_for_editor() {
		return [];
	}

	/**
	 * Extra data
	 *
	 * Using this method you can add extra data to your elements
	 * It is mainly used for WP widgets, however, it can be used by
	 * other elements as well.
	 */
	public function extra_data() {
		return false;
	}

	/**
	 * @return array
	 */
	public function content_composition() {
		return [];
	}

	/**
	 * Get Element Url
	 *
	 * Returns the url for the current element and the provide path
	 *
	 * @param string $path The path that will be appended to the element URL
	 *
	 * @return string
	 */
	final public function get_url( $path = '' ) {
		if ( null === $this->element_base_url ) {
			// set the base url
			$this->element_base_url = trailingslashit( Utils::get_file_url_from_path( $this->get_path() ) );
		}

		return esc_url( $this->element_base_url . wp_normalize_path( $path ) );
	}

	/**
	 * Get Element Path
	 *
	 * Returns the path for the current element and the provide path
	 *
	 * @param string $path The path that will be appended to the element base path
	 *
	 * @return string
	 */
	final public function get_path( $path = '' ) {
		if ( null === $this->element_base_path ) {
			$reflection_class = new \ReflectionClass( get_class( $this ) );
			// Set the base path
			$this->element_base_path = trailingslashit( dirname( $reflection_class->getFileName() ) );
		}
		return $this->element_base_path . wp_normalize_path( $path );
	}

	/**
	 * Render
	 *
	 * Main render function that will be overridden by elements classes
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		// Show an error in development mode
		throw new \Exception( 'The Element should implement the render() method and it should return the element unique id.' );
	}

	/**
	 * Server render
	 *
	 * Will render the element without the wrappers
	 *
	 */
	final public function server_render() {
		$this->render( $this->options );
	}

	/**
	 * Allows for functionality injection before rendering
	 *
	 * @param Options $options
	 */
	public function before_render( $options ) {
	}

	/**
	 * Allows for functionality injection after rendering
	 *
	 * @param Options $options
	 */
	public function after_render( $options ) {
	}

	/**
	 * Render
	 *
	 * Private render function that will be used by us to render the element
	 */
	final public function render_element( $extra_data ) {
		$this->extra_render_data = $extra_data;

		if ( $this->element_is_allowed_render() ) {
			$this->before_render( $this->options );

			$element_type_css_class = Utils::camel_case( $this->get_type() );

			// Add default class for element
			$this->render_attributes->add( 'wrapper', 'class', sprintf( 'zb-element zb-el-%s', $element_type_css_class ) );

			// Add animation attributes
			$appear_animation = $this->options->get_value( '_advanced_options._appear_animation', false );
			if ( ! empty( $appear_animation ) ) {
				wp_enqueue_script( 'zionbuilder-animatejs' );
				$this->render_attributes->add( 'wrapper', 'class', 'ajs__element' );
				$this->render_attributes->add( 'wrapper', 'data-ajs-animation', $appear_animation );
			}

			// Add video BG
			$background_video_options = $this->options->get_value( '_styles.wrapper.styles.default.default.background-video' );
			if ( ! empty( self::has_video_background( $background_video_options ) ) ) {
				wp_enqueue_script( 'zb-video-bg' );
			}

			$wrapper_tag = $this->get_wrapper_tag( $this->options );
			$wrapper_id  = $this->get_element_css_id();

			// Add wrapper attributes
			$attributes = $this->render_attributes->get_attributes_as_string( 'wrapper' );

			// Render element
			// The attributes are already escaped in RenderAttributes::get_attributes_as_string()
			printf( '<%s id="%s" %s>', esc_html( $wrapper_tag ), esc_attr( $wrapper_id ), $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput

			// Render video background
			$video_config = $this->options->get_value( '_styles.wrapper.styles.default.default.background-video', false );
			self::render_video_background( $video_config );

			// Render element
			$this->render( $this->options );
			printf( '</%s>', esc_html( $wrapper_tag ) );

			$this->after_render( $this->options );
		}
	}

	public static function render_video_background( $options ) {
		if ( self::has_video_background( $options ) ) {
			printf( '<div class="zb__videoBackground-wrapper zbjs_video_background" data-zion-video-background=\'%s\'></div>', wp_json_encode( $options ) );
		}
	}

	public static function has_video_background( $options ) {
		$video_source = ! empty( $options['videoSource'] ) ? $options['videoSource'] : 'local';
		if ( $video_source === 'local' && ! empty( $options['mp4'] ) ) {
			return true;
		} else {
			if ( $video_source === 'youtube' && ! empty( $options['youtubeURL'] ) ) {
				return true;
			} else {
				if ( $video_source === 'vimeo' && ! empty( $options['vimeoURL'] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Will render a html tag based on options and render attributes previously registered
	 *
	 * @param string $html_tag_type HTML tag type (f.e. div, span )
	 * @param string $tag_id        The tag is for which we have registered attributes
	 * @param string|array $content       HTML tag content
	 * @param array  $attributes
	 *
	 * @return void
	 */
	public function render_tag( $html_tag_type, $tag_id, $content = '', $attributes = [] ) {
		// Attributes are already escaped, the content must be escaped by the element creator
		echo $this->get_render_tag( $html_tag_type, $tag_id, $content, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput
	}


	/**
	 * Will return a html tag based on options and render attributes previously registered
	 *
	 * @param string $html_tag_type HTML tag type (f.e. div, span )
	 * @param string $tag_id        The tag is for which we have registered attributes
	 * @param string|array $content HTML tag content
	 * @param array $attributes A list of extra attributes to add to the returned tag
	 *
	 * @return string The HTML tag
	 */
	public function get_render_tag( $html_tag_type, $tag_id, $content = '', $attributes = [] ) {
		$attributes = $this->render_attributes->get_attributes_as_string( $tag_id, $attributes );
		$content    = is_array( $content ) ? implode( '', $content ) : $content;
		return sprintf( '<%s %s>%s</%1$s>', esc_attr( $html_tag_type ), $attributes, $content );
	}

	/**
	 * Render Tag List
	 *
	 * Will render a list of html tags based on options and callback
	 *
	 * @param string $html_tag_type HTML tag type (f.e. div, span )
	 * @param string $option_id
	 * @param string $tag_id        The tag is for which we have registered attributes
	 * @param array  $tag_config
	 */
	final public function render_tag_group( $html_tag_type, $option_id, $tag_id, $tag_config ) {
		$tag_list = $this->options->get_value( $option_id, [] );

		foreach ( $tag_list as $index => $value ) {
			// Check if the render has extra tags
			if ( is_array( $tag_config['attributes'] ) ) {
				foreach ( $tag_config['attributes'] as $attribute_type => $attribute_value ) {
					$compiled_value          = str_replace( '{{INDEX}}', $index, $attribute_value );
					$compiled_attribute_type = str_replace( '{{INDEX}}', $index, $attribute_type );
					$this->render_attributes->add( $tag_id . $index, $compiled_attribute_type, $compiled_value );
				}
			}

			$content = isset( $tag_config['render_callback'] ) ? call_user_func( $tag_config['render_callback'], $value ) : '';
			$this->render_tag( $html_tag_type, $tag_id . $index, $content );
		}
	}


	/**
	 * Attach icon attributes
	 *
	 * Will add the icon attributes to a registerd tag
	 *
	 * @param string $tag_id The tag id to which we will register the icon attributes
	 * @param array  $icon   The icon config
	 */
	public function attach_icon_attributes( $tag_id, $icon ) {
		$icon_attributes = Icons::get_icon_attributes( $icon );

		// Attach icon attributes
		if ( $icon_attributes ) {
			foreach ( $icon_attributes as $attribute_key => $attribute_value ) {
				$this->render_attributes->add( $tag_id, $attribute_key, $attribute_value, true );
			}
		}
	}

	/**
	 * Attach link attributes
	 *
	 * Will add the link attributes to a registerd tag
	 *
	 * @param string $tag_id The tag id to which we will register the link attributes
	 * @param array  $link   The link config
	 */
	public function attach_link_attributes( $tag_id, $link ) {
		if ( isset( $link['link'] ) ) {
			$this->render_attributes->add( $tag_id, 'href', $link['link'] );
		}

		if ( isset( $link['target'] ) ) {
			$this->render_attributes->add( $tag_id, 'target', $link['target'] );
		}

		if ( isset( $link['title'] ) ) {
			$this->render_attributes->add( $tag_id, 'title', $link['title'] );
		}
	}

	/**
	 * @param mixed $options
	 *
	 * @return string
	 */
	public function get_wrapper_tag( $options ) {
		return $this->wrapper_tag;
	}

	public function set_wrapper_tag( $tag ) {
		$this->wrapper_tag = $tag;
	}

	/**
	 * @return bool
	 */
	final private function element_is_allowed_render() {
		// Check user generated render allowed
		if ( ! $this->can_render() ) {
			return false;
		}

		// Check element permissions settings
		$element_visibility = $this->options->get_value( '_advanced_options._element_visibility', 'all' );
		if ( 'logged_in' === $element_visibility && ! is_user_logged_in() ) {
			return false;
		} elseif ( 'logged_out' === $element_visibility && is_user_logged_in() ) {
			return false;
		}

		// Check to see if the element is hidden
		$is_element_hidden = $this->options->get_value( '_isVisible', true );
		if ( ! $is_element_hidden ) {
			return false;
		}

		return true;
	}

	final public function get_style_classes_as_string( $style_id, $extra_classes = [] ) {
		$option_path = sprintf( '_styles.%s.classes', $style_id );
		$css_classes = $this->options->get_value( $option_path, [] );
		$css_classes = array_merge( $css_classes, $extra_classes );

		return implode( ' ', $css_classes );
	}

	/**
	 * Get Element Styles
	 *
	 * Compiles all elements css styles
	 *
	 * @return string The compiled element styles
	 */
	public function get_element_extra_css() {
		$css = '';

		// Compile styling options
		$styles            = $this->options->get_value( '_styles', [] );
		$registered_styles = $this->get_style_elements_for_editor();

		if ( ! empty( $styles ) && is_array( $registered_styles ) ) {
			foreach ( $registered_styles as $id => $style_config ) {
				if ( ! empty( $styles[$id] ) && isset( $styles[$id]['styles'] ) ) {
					$selector = str_replace( '{{ELEMENT}}', '#' . $this->get_element_css_id(), $style_config['selector'] );
					$css     .= Style::get_styles( $selector, $styles[$id]['styles'] );
				}
			}
		}

		// Compile options css
		$css .= $this->custom_css->get_css();

		// Compile element options
		$css .= $this->css();

		// Compile custom css
		return $css;
	}

	/**
	 * Returns the element css id
	 *
	 * @return string
	 */
	public function get_element_css_id() {
		return $this->options->get_value( '_advanced_options._element_id', $this->get_uid() );
	}

	public function get_uid() {
		return $this->uid;
	}

	/**
	 * Enqueue Scripts
	 *
	 * Loads the scripts necessary for the current element
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
	}


	/**
	 * Enqueue Styles
	 *
	 * Loads the style files necessary for the current element
	 *
	 * @return void
	 */
	public function enqueue_styles() {
	}


	/**
	 * Adds a script to the list of scripts that will be loaded in edit mode
	 *
	 * @param string $script_url The URL of the scripts. This must be a local url
	 *
	 * @return void
	 */
	public function enqueue_editor_script( $script_url ) {
		$this->editor_scripts[] = $script_url;
	}


	/**
	 * Adds a script to the list of scripts that will be loaded in frontend.
	 * All scripts loaded this way will be subject of Plugin cache system.
	 * If you do not want this script to be cached by the plugin, use WP native
	 * enqueue functions
	 *
	 * @param string $script_url The URL of the scripts. This must be a local url
	 *
	 * @return void
	 */
	public function enqueue_element_script( $script_url ) {
		$this->element_scripts[] = $script_url;
	}


	/**
	 * Adds a style to the list of scripts that will be loaded in edit mode.
	 *
	 * @param string $style_url The URL of the scripts. This must be a local url
	 *
	 * @return void
	 */
	public function enqueue_editor_style( $style_url ) {
		$this->editor_styles[] = $style_url;
	}


	/**
	 * Adds a style URL to the list of styles that will be loaded in frontend.
	 * All styles loaded this way will be subject of Plugin cache system.
	 * If you do not want this style to be cached by the plugin, use WP native
	 * enqueue functions
	 *
	 * @param string $style_url The URL of the style. This must be a local url
	 *
	 * @return void
	 */
	public function enqueue_element_style( $style_url ) {
		$this->element_styles[] = $style_url;
	}

	private function compile_script_url( $handle, $src, $base_url, $content_url, $ver ) {
		if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $content_url && 0 === strpos( $src, $content_url ) ) ) {
			$src = $base_url . $src;
		}

		if ( ! empty( $ver ) ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		/** This filter is documented in wp-includes/class.wp-scripts.php */
		return esc_url( apply_filters( 'script_loader_src', $src, $handle ) );

	}

	private function get_dependency_config( $manager ) {
		$scripts = [];

		// Set all scripts
		$manager->all_deps( $manager->queue );
		foreach ( $manager->to_do as $handle ) {
			// Don't proceed if the script was not registered
			if ( ! isset( $manager->registered[$handle] ) ) {
				continue;
			}

			$obj = $manager->registered[$handle];
			$src = $obj->src;

			if ( $src ) {
				$scripts[$handle] = $this->prepare_script_data( $handle, $obj, $manager );
			}
		}

		return $scripts;
	}

	private function get_element_styles_for_editor() {
		global $wp_styles;

		// Preserve a copy of styles queue
		$old_queue  = $wp_styles->queue;
		$old_todo   = $wp_styles->to_do;
		$old_done   = $wp_styles->done;
		$old_groups = $wp_styles->groups;

		$wp_styles->queue  = [];
		$wp_styles->to_do  = [];
		$wp_styles->done   = [];
		$wp_styles->groups = [];

		$this->enqueue_styles();

		$scripts = $this->get_dependency_config( $wp_styles );

		$wp_styles->queue  = $old_queue;
		$wp_styles->to_do  = $old_todo;
		$wp_styles->done   = $old_done;
		$wp_styles->groups = $old_groups;

		// Add editor script
		foreach ( $this->editor_styles as $key => $element_style_url ) {
			$scripts[$this->get_type() . '-editor-style-' . $key] = [
				'src' => $element_style_url,
			];
		}

		foreach ( $this->element_styles as $key => $element_style_url ) {
			$scripts[$this->get_type() . '-element-style-' . $key] = [
				'src' => $element_style_url,
			];
		}

		return $scripts;
	}

	/**
	 * Get Editor Scripts
	 *
	 * Loads the scripts necessary for the current element in editor mode
	 *
	 * @return array<string, array<string, string>>
	 */
	public function get_element_scripts_for_editor() {
		global $wp_scripts;

		// Preserve a copy of wp scripts info
		$old_queue  = $wp_scripts->queue;
		$old_todo   = $wp_scripts->to_do;
		$old_done   = $wp_scripts->done;
		$old_groups = $wp_scripts->groups;

		// Reset scripts
		$wp_scripts->queue  = [];
		$wp_scripts->to_do  = [];
		$wp_scripts->done   = [];
		$wp_scripts->groups = [];

		$this->enqueue_scripts();
		$scripts = $this->get_dependency_config( $wp_scripts );

		// Put back script info
		$wp_scripts->queue  = $old_queue;
		$wp_scripts->to_do  = $old_todo;
		$wp_scripts->done   = $old_done;
		$wp_scripts->groups = $old_groups;

		// Add editor script
		foreach ( $this->editor_scripts as $key => $editor_script_url ) {
			$scripts[$this->get_type() . '-editor-script-' . $key] = [
				'src' => $editor_script_url,
			];
		}

		foreach ( $this->element_scripts as $key => $element_script_url ) {
			$scripts[$this->get_type() . '-element-script-' . $key] = [
				'src' => $element_script_url,
			];
		}

		return $scripts;
	}

	public function get_element_scripts() {
		return $this->element_scripts;
	}

	public function get_element_styles() {
		return $this->element_styles;
	}

	private function prepare_script_data( $handle, $obj, $manager ) {
		if ( null === $obj->ver ) {
			$ver = '';
		} else {
			$ver = $obj->ver ? $obj->ver : get_bloginfo( 'version' );
		}

		if ( isset( $manager->args[ $handle ] ) ) {
			$ver = $ver ? $ver . '&amp;' . $manager->args[ $handle ] : $manager->args[ $handle ];
		}

		return [
			'handle' => $handle,
			'src'    => $this->compile_script_url(
				$handle,
				$manager->registered[$handle]->src,
				$manager->base_url,
				$manager->content_url,
				$ver
			),

			'data'   => $manager->get_data( $handle, 'data' ),
			'before' => $manager->get_data( $handle, 'before' ),
			'after'  => $manager->get_data( $handle, 'after' ),
		];
	}

	/**
	 * Get class name.
	 *
	 * Return the name of the current class.
	 * Used to instantiate elements with data.
	 *
	 * @return string The current class name
	 */
	final public function get_class_name() {
		return get_called_class();
	}

	/**
	 * Render all children elements of the current element
	 */
	public function render_children() {
		Plugin::$instance->frontend->render_children( $this->get_children() );
	}

	/**
	 * Get children for render
	 *
	 * returns the children HTML content
	 */
	public function get_children_for_render() {
		ob_start();
		Plugin::$instance->frontend->render_children( $this->get_children() );
		return ob_get_clean();
	}

	/**
	 * Get Children
	 *
	 * Returns an array containing all children of this element.
	 * If the element can have multiple content areas ( for example tabs or accordions ) it will loop trough all areas
	 * and returns all it's children
	 *
	 * @return array
	 */
	public function get_children() {
		$child_elements_data = [];

		if ( ! empty( $this->content ) && is_array( $this->content ) ) {
			if ( $this->has_multiple ) {
				foreach ( $this->content as $multiple_content_data ) {
					if ( is_array( $multiple_content_data ) ) {
						foreach ( $multiple_content_data as $child_content_data ) {
							$child_elements_data[] = $child_content_data;
						}
					}
				}
			} else {
				foreach ( $this->content as $child_content_data ) {
					$child_elements_data[] = $child_content_data;
				}
			}
		}

		return $child_elements_data;
	}
}
