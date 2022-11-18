<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Utils;
use ZionBuilder\Elements\Style;
use ZionBuilder\Plugin;
use ZionBuilder\Options\Options;
use ZionBuilder\Icons;
use ZionBuilder\RenderAttributes;
use ZionBuilder\CustomCSS;
use ZionBuilder\PageAssets;

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
	 * Holds a reference to all provided properties
	 *
	 * @var array
	 */
	private static $provides = array();

	/**
	 * Element content
	 *
	 * @var array<int, array<string, mixed>>
	 */
	public $content = array();

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
	 * Holds a reference to the element wrapper tag
	 *
	 * @var string The element wrapper tag
	 */
	protected $wrapper_tag = 'div';

	/**
	 * Holds a reference to the element style options tags
	 *
	 * @var array<string, array<string, mixed>> The element custom style options
	 */
	protected $registered_style_options = array();

	/**
	 * Adds extra data to element on render
	 *
	 * @var array<string, mixed>
	 */
	public $extra_render_data = array();

	/**
	 * Holds the render attributes class object
	 *
	 * @var RenderAttributes
	 */
	public $render_attributes = null;

	/**
	 * Holds the custom css class object
	 *
	 * @var CustomCSS
	 */
	public $custom_css = null;

	/**
	 * Holds a list of editor scripts URLs
	 *
	 * @var array<int, string>
	 */
	protected $editor_scripts = array();

	/**
	 * Holds a list of element scripts URLs
	 *
	 * @var array<int, string>
	 */
	protected $element_scripts = array();

	/**
	 * Holds a list of editor styles URLs
	 *
	 * @var array<int, string>
	 */
	protected $editor_styles = array();

	/**
	 * Holds a list of element styles URLs
	 *
	 * @var array<int, string>
	 */
	protected $element_styles = array();

	/**
	 * Holds a reference to the Element HTML id
	 *
	 * @var string
	 */
	protected $element_html_id = '';

	/**
	 * Holds a reference to all registered hooks
	 *
	 * @var array
	 */
	protected $hooks = array();

	public $data = array();

	// repeater
	public $element_css_selector = null;

	private $current_provides = array();

	/**
	 * Helper flag that is added when the element is rendered in editor mode from the server
	 *
	 * @var boolean
	 */
	public $is_server_render = false;

	/**
	 * Holds the body classes needed for rendering inside the builder
	 *
	 * @var array
	 */
	private $render_body_classes = [];

	/**
	 * Main class constructor
	 *
	 * @param array<string, mixed> $data The saved values for the current element
	 */
	final public function __construct( $data = array() ) {
		// Allow elements creators to hook here without rewriting construct
		$this->on_before_init( $data );

		// Set the element data if provided
		if ( ! empty( $data ) ) {
			$this->data = $data;
			$this->init_options();

			if ( isset( $data['uid'] ) ) {
				$this->uid = $data['uid'];
			}

			if ( isset( $data['content'] ) ) {
				$this->content = $data['content'];
			}

			// Setup helpers
			$model                   = isset( $data['options'] ) ? $data['options'] : array();
			$this->render_attributes = new RenderAttributes();
			$this->custom_css        = new CustomCSS( $this->get_css_selector() );
			$this->options->set_data( $model, $this->render_attributes, $this->custom_css );
			$this->custom_css->set_css_selector( $this->get_css_selector() );
		}

		// Allow elements creators to hook here without rewriting construct
		$this->on_after_init( $data );
	}

	public function init_options() {
		$element_type         = $this->get_type();
		$options_schema_id    = sprintf( 'zionbuilder\element\%s\options', $element_type );
		$is_schema_registered = Options::is_schema_registered( $options_schema_id );
		$this->options        = new Options( $options_schema_id );

		if ( ! $is_schema_registered ) {
			// Register element options. We only need them on class init with data
			$this->options( $this->options );

			// Trigger internal action
			$this->trigger( 'options/schema/set' );
		}
	}

	/**
	 * Adds render classes to body
	 *
	 * @internal Do not use
	 * @since 3.5.0
	 *
	 * @param string $css_class
	 * @return void
	 */
	public function add_render_body_class( $css_class ) {
		$this->render_body_classes[] = $css_class;
	}

	/**
	 * Returns the list of body classes needed for rendering
	 *
	 * @internal
	 * @since 3.5.0
	 *
	 * @return string[]
	 */
	public function get_render_body_classes() {
		return $this->render_body_classes;
	}

	/**
	 * Register an action
	 *
	 * @param string                         $action_name The action name
	 * @param array<callable, string>|string $callback    The callback to call when the action triggers
	 *
	 * @return void
	 */
	public function on( $action_name, $callback ) {
		if ( ! isset( $this->hooks[$action_name] ) ) {
			$this->hooks[$action_name] = array();
		}

		$this->hooks[$action_name][] = $callback;
	}

	public function get_clone( $data ) {
		$element_data = array_merge( $this->data, $data );
		return Plugin::instance()->elements_manager->get_element_instance_with_data( $element_data );
	}

	/**
	 * Calls all regisred callbacks for the given action name
	 *
	 * @param string $action_name
	 *
	 * @return void
	 */
	public function trigger( $action_name ) {
		if ( isset( $this->hooks[$action_name] ) ) {
			foreach ( $this->hooks[$action_name] as $callback ) {
				call_user_func( $callback );
			}
		}
	}

	/**
	 * Attaches all custom css classes to render attributes
	 *
	 * @return void
	 */
	public function apply_custom_classes_to_render_tags() {
		// Set the custom css classes for element
		$element_saved_styles = $this->options->get_value( '_styles', array() );
		$styles_config        = $this->get_style_elements_for_editor();

		foreach ( $element_saved_styles as $style_config_id => $style_value ) {
			if ( isset( $styles_config[$style_config_id] ) ) {
				$style_config = $styles_config[$style_config_id];
				$render_tag   = isset( $style_config['render_tag'] ) ? $style_config['render_tag'] : $style_config_id;

				if ( isset( $style_value['classes'] ) && is_array( $style_value['classes'] ) ) {
					foreach ( $style_value['classes'] as $css_class ) {
						$this->render_attributes->add( $render_tag, 'class', $css_class );
					}
				}
			}
		}
	}

	/**
	 * Attaches all custom render attributes
	 *
	 * @return void
	 */
	public function apply_custom_attributes_to_render_tags() {
		$styles_attrs = $this->options->get_value( '_styles', array() );

		foreach ( $styles_attrs as $id => $style_values ) {
			if ( isset( $style_values['attributes'] ) && is_array( $style_values['attributes'] ) ) {
				foreach ( $style_values['attributes'] as $attributes ) {
					if ( ! empty( $attributes['attribute_name'] ) ) {
						$attribute_value = isset( $attributes['attribute_value'] ) ? $attributes['attribute_value'] : '';
						$this->render_attributes->add( $id, $attributes['attribute_name'], esc_attr( $attribute_value ) );
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
		return ! empty( $this->element_css_selector ) ? $this->element_css_selector : sprintf( '#%s', $this->get_element_css_id() );
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
		return array();
	}

	/**
	 * Get Element Image
	 *
	 * Will return the element Image
	 *
	 * @return string
	 */
	public function get_element_image() {
		$image = '';
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
	 * @param array<string, mixed> $data The data for the element instance
	 *
	 * @return void
	 */
	public function on_after_init( $data = array() ) {
	}

	/**
	 * On before init
	 *
	 * Allow the users to add their own initialization process without extending __construct
	 *
	 * @param array<string, mixed> $data The data for the element instance
	 *
	 * @return void
	 */
	public function on_before_init( $data = array() ) {
	}


	/**
	 * Checks if the element can render. It must be implemented in child class
	 *
	 * @return boolean
	 */
	protected function can_render() {
		return true;
	}

	/**
	 * @return string
	 */
	public function css() {
		return '';
	}

	/**
	 * If it returns true, the element will not show in the add elements panel however it will be used in page if it was already present
	 *
	 * @return boolean
	 */
	public function is_deprecated() {
		return false;
	}


	/**
	 * Returns the element config that will be used by editor
	 *
	 * @return array<string, mixed> The element config
	 */
	public function internal_get_config_for_editor() {
		$show_in_ui = $this->is_child() ? false : $this->show_in_ui();

		// Init options
		$this->init_options();

		$config = array(
			'element_type'        => $this->get_type(),
			'name'                => $this->get_name(),
			'category'            => $this->get_category(),
			'deprecated'          => $this->is_deprecated(),
			'keywords'            => $this->get_keywords(),
			'options'             => $this->options->get_schema(),
			'wrapper'             => $this->is_wrapper(),
			'content_composition' => $this->content_composition(),
			'style_elements'      => $this->get_style_elements_for_editor(),
			'label'               => $this->get_label(),
			'show_in_ui'          => $show_in_ui,
			'is_child'            => $this->is_child(),
			'scripts'             => $this->get_element_scripts_for_editor(),
			'styles'              => $this->get_element_styles_for_editor(),
			'content_orientation' => $this->get_sortable_content_orientation(),
		);

		// Add extra data
		$extra_data = $this->extra_data();
		if ( ! empty( $extra_data ) ) {
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
	 *
	 * @return array<string, array<string, mixed>> The style option config
	 */
	public function get_style_elements_for_editor() {
		$this->register_wrapper_style_options();

		// Register element style options
		$this->on_register_styles();

		return $this->registered_style_options;
	}

	/**
	 * Will register the current element without showing it in editor
	 *
	 * This type of elements cannot be added from add elements popup
	 * and don't have any toolbox or events attached to them
	 * They are mainly used as children for other elements
	 *
	 * @return boolean True if the element will appear in elements list in editor
	 */
	public function show_in_ui() {
		return true;
	}

	/**
	 * Will register the current element as a child of another
	 *
	 * Child elements are not visible in add elements popup and cannot be
	 * interacted with them directly
	 *
	 * @return boolean True in case this element is a child of another element
	 */
	public function is_child() {
		return false;
	}

	/**
	 * Will register the style options for the wrapper
	 *
	 * @return void
	 */
	private function register_wrapper_style_options() {
		$this->register_style_options_element(
			'wrapper',
			array(
				'title'      => esc_html__( 'Wrapper', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}}',
				'config'     => array(
					'show_background_video' => true,
				),
				'render_tag' => 'wrapper',
			)
		);
	}


	/**
	 * Returns the label configuration that will appear in element list
	 *
	 * @return string
	 */
	public function get_label() {
		return '';
	}


	/**
	 * Get style elements
	 *
	 * Returns a list of elements/tags that for which you
	 * want to show style options
	 *
	 * @return void
	 */
	public function on_register_styles() {
	}


	/**
	 * Register style options
	 *
	 * @param string               $id
	 * @param array<string, mixed> $config
	 *
	 * @return void
	 */
	protected function register_style_options_element( $id, $config ) {
		$this->registered_style_options[$id] = $config;
	}

	/**
	 * Get config for editor
	 *
	 * Allow elements to add extra data to the page builder elements
	 * in edit mode
	 *
	 * @return array<string, mixed> Extra element configuration that will be added to the element in edit mode
	 */
	public function get_config_for_editor() {
		return array();
	}

	/**
	 * Using this method you can add extra data to your elements
	 * It is mainly used for WP widgets, however, it can be used by
	 * other elements as well.
	 *
	 * @return array<string, mixed>
	 */
	public function extra_data() {
		return array();
	}

	/**
	 * Allows to create elements that contains other elements
	 *
	 * @return array<string, mixed>
	 */
	public function content_composition() {
		return array();
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
			$filename = $reflection_class->getFileName();
			if ( $filename ) {
				$this->element_base_path = trailingslashit( dirname( $filename ) );
			}
		}
		return $this->element_base_path . wp_normalize_path( $path );
	}

	/**
	 * Main render function that will be overridden by elements classes
	 *
	 * @param Options $options The element options instance
	 *
	 * @return void The method should print the element
	 */
	public function render( $options ) {
		// Show an error in development mode
		throw new \Exception( 'The Element should implement the render() method and it should return the element unique id.' );
	}

	/**
	 * Will render the element without the wrappers
	 *
	 * @return void
	 */
	public function server_render( $request ) {
		$this->options->parse_data();
		$this->render( $this->options );
	}

	/**
	 * Allows for functionality injection before rendering
	 *
	 * @param Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
	}

	/**
	 * Allows for functionality injection after rendering
	 *
	 * @param Options $options
	 *
	 * @return void
	 */
	public function after_render( $options ) {
	}

	/**
	 * Private render function that will be used by us to render the element
	 *
	 * @param array<string, mixed> $extra_render_data Extra data that will be used for element render
	 *
	 * @return void
	 */
	final public function render_element( $extra_render_data ) {
		do_action( 'zionbuilder/element/before_custom_render', $this );

		/**
		 * Allows you to create a different renderer
		 */
		$custom_renderer = apply_filters( 'zionbuilder/renderer/custom_renderer', null, $this );
		if ( $custom_renderer ) {
			$custom_renderer->render_element( $extra_render_data, $this );

		} else {
			$this->do_element_render( $extra_render_data );
		}

		do_action( 'zionbuilder/element/after_custom_render', $this );
	}

	public function get_custom_css() {
		$custom_css = $this->options->get_value( '_advanced_options._custom_css' );

		if ( ! empty( $custom_css ) ) {
			return str_replace( '[ELEMENT]', '#' . $this->get_element_css_id(), $custom_css );
		}

		return '';

	}

	final public function do_element_render( $extra_render_data ) {
		// We need to parse data only on actual render
		$this->options->parse_data();

		if ( ! $this->element_is_allowed_render() ) {
			return;
		}

		// Setup render tags custom css classes
		$this->apply_custom_classes_to_render_tags();

		// Setup render tags custom attributes
		$this->apply_custom_attributes_to_render_tags();

		$this->extra_render_data = $extra_render_data;

		$this->before_render( $this->options );
		do_action( 'zionbuilder/element/before_render', $this, $extra_render_data );

		$element_type_css_class = Utils::camel_case( $this->get_type() );

		// Add default class for element
		$this->render_attributes->add( 'wrapper', 'class', sprintf( 'zb-element zb-el-%s', $element_type_css_class ) );

		// Add animation attributes
		$appear_animation = $this->options->get_value( '_advanced_options._appear_animation', false );

		if ( ! empty( $appear_animation ) ) {
			$this->render_attributes->add( 'wrapper', 'class', 'ajs__element' );
			$this->render_attributes->add( 'wrapper', 'data-ajs-animation', $appear_animation );
		}

		// Add video BG
		$background_video_options = $this->options->get_value( '_styles.wrapper.styles.default.default.background-video' );

		if ( ! empty( self::has_video_background( $background_video_options ) ) ) {
			wp_enqueue_script( 'zb-video-bg' );
		}

		$wrapper_tag = $this->get_wrapper_tag( $this->options );

		if ( $this->render_attributes->has_attribute( 'wrapper', 'id' ) ) {
			$html_id    = $this->render_attributes->get_attributes( 'wrapper', 'id' );
			$wrapper_id = end( $html_id );
		} else {
			$wrapper_id = $this->get_element_css_id();
		}

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

		// Reset provides
		$this->reset_provides();
		do_action( 'zionbuilder/element/after_render', $this, $extra_render_data );
	}

	/**
	 * Will render the video wrapper
	 *
	 * @param array<string, mixed> $options The element options instance
	 *
	 * @return void
	 */
	public static function render_video_background( $options ) {
		if ( self::has_video_background( $options ) ) {
			printf( '<div class="zb__videoBackground-wrapper zbjs_video_background" data-zion-video-background=\'%s\'></div>', wp_json_encode( $options ) );
		}
	}

	/**
	 * Checks to see if the element has video background
	 *
	 * @param array<string, mixed> $options The element options instance
	 *
	 * @return boolean
	 */
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
	 * @param string                    $html_tag_type HTML tag type (f.e. div, span )
	 * @param string|array              $tag_id        The tag is for which we have registered attributes
	 * @param string|array<int, string> $content       HTML tag content
	 * @param array<string, mixed>      $attributes
	 *
	 * @return void
	 */
	public function render_tag( $html_tag_type, $tag_id, $content = '', $attributes = array() ) {
		// Attributes are already escaped, the content must be escaped by the element creator
		echo $this->get_render_tag( $html_tag_type, $tag_id, $content, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput
	}


	/**
	 * Will return a html tag based on options and render attributes previously registered
	 *
	 * @param string                    $html_tag_type HTML tag type (f.e. div, span )
	 * @param string                    $tag_id        The tag is for which we have registered attributes
	 * @param string|array<int, string> $content       HTML tag content
	 * @param array<string, string>     $attributes    A list of extra attributes to add to the returned tag
	 *
	 * @return string The HTML tag
	 */
	public function get_render_tag( $html_tag_type, $tag_id, $content = '', $attributes = array() ) {
		$attributes = $this->render_attributes->get_attributes_as_string( $tag_id, $attributes );
		$content    = is_array( $content ) ? implode( '', $content ) : $content;
		return sprintf( '<%s %s>%s</%1$s>', esc_attr( $html_tag_type ), $attributes, $content );
	}

	/**
	 * Render Tag List
	 *
	 * Will render a list of html tags based on options and callback
	 *
	 * @param string               $html_tag_type HTML tag type (f.e. div, span )
	 * @param string               $option_id
	 * @param string               $tag_id        The tag is for which we have registered attributes
	 * @param array<string, mixed> $tag_config
	 *
	 * @return void
	 */
	final public function render_tag_group( $html_tag_type, $option_id, $tag_id, $tag_config ) {
		$tag_list = $this->options->get_value( $option_id, array() );

		foreach ( $tag_list as $index => $value ) {
			// Check if the render has extra tags
			if ( is_array( $tag_config['attributes'] ) ) {
				foreach ( $tag_config['attributes'] as $attribute_type => $attribute_value ) {
					$compiled_value          = str_replace( '{{INDEX}}', $index, $attribute_value );
					$compiled_attribute_type = str_replace( '{{INDEX}}', $index, $attribute_type );
					$this->render_attributes->add( $tag_id . $index, $compiled_attribute_type, $compiled_value );
				}
			}

			$content = isset( $tag_config['render_callback'] ) ? call_user_func( $tag_config['render_callback'], $value, $index ) : '';
			$this->render_tag( $html_tag_type, array( $tag_id, $tag_id . $index ), $content );
		}
	}

	/**
	 * Returns the Icon HTML markup from an icon option
	 *
	 * @param [] $icon
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public static function get_icon_markup( $icon, $content = '', $attributes = [] ) {
		$icon_attributes = Icons::get_icon_attributes( $icon );
		$attributes      = array_merge( $attributes, $icon_attributes );

		$attribute_list = [];
		foreach ( $attributes as $attribute_name => $attribute_value ) {

			$attribute_value  = is_array( $attribute_value ) ? implode( ' ', $attribute_value ) : $attribute_value;
			$attribute_list[] = "{$attribute_name}='{$attribute_value}'";
		}

		$attributes_as_string = join( ' ', $attribute_list );
		return "<span {$attributes_as_string}>{$content}</span>";
	}

	/**
	 * Attach icon attributes
	 *
	 * Will add the icon attributes to a registered tag
	 *
	 * @param string               $tag_id The tag id to which we will register the icon attributes
	 * @param array<string, mixed> $icon   The icon config
	 *
	 * @return void
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
	 * Will add the link attributes to a registered tag
	 *
	 * @param string                $tag_id The tag id to which we will register the link attributes
	 * @param array<string, mixed> $link   The link config
	 *
	 * @return void
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

		if ( isset( $link['attributes'] ) && is_array( $link['attributes'] ) ) {
			foreach ( $link['attributes'] as $attribute_config ) {
				if ( ! empty( $attribute_config['key'] ) ) {
					$attribute_value = isset( $attribute_config['value'] ) ? $attribute_config['value'] : null;
					$this->render_attributes->add( $tag_id, $attribute_config['key'], $attribute_value );
				}
			}
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

	/**
	 * Undocumented function
	 *
	 * @param string $tag The HTML tag type
	 *
	 * @return void
	 */
	public function set_wrapper_tag( $tag ) {
		$this->wrapper_tag = $tag;
	}

	/**
	 * @return bool
	 */
	private function element_is_allowed_render() {
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

		return apply_filters( 'zionbuilder/element/can_render', true, $this );
	}

	/**
	 * Undocumented function
	 *
	 * @param string             $style_id
	 * @param array<int, string> $extra_classes
	 *
	 * @return string
	 */
	final public function get_style_classes_as_string( $style_id, $extra_classes = array() ) {
		$option_path = sprintf( '_styles.%s.classes', $style_id );
		$css_classes = $this->options->get_value( $option_path, array() );
		$css_classes = array_merge( $css_classes, $extra_classes );

		return implode( ' ', $css_classes );
	}

	/**
	 * Returns the element css id
	 *
	 * @return string
	 */
	public function get_element_css_id() {
		$options = $this->options->get_model();
		return isset( $options['_advanced_options']['_element_id'] ) ? $options['_advanced_options']['_element_id'] : $this->get_uid();
	}

	/**
	 * Returns the element unique id
	 *
	 * @return string
	 */
	public function get_uid() {
		return $this->uid;
	}

	/**
	 * Will enqueue all scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_all_extra_scripts() {
		$this->do_enqueue_scripts();
		$this->do_enqueue_styles();
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

	public function do_enqueue_scripts() {
		// Check for animation
		$options = $this->options->get_model();
		if ( isset( $options['_advanced_options']['_appear_animation'] ) && ! empty( $options['_advanced_options']['_appear_animation'] ) ) {
			wp_enqueue_script( 'zionbuilder-animatejs' );
		}

		$this->enqueue_scripts();
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

	public function do_enqueue_styles() {
		// Check for animation
		$options = $this->options->get_model();
		if ( isset( $options['_advanced_options']['_appear_animation'] ) && ! empty( $options['_advanced_options']['_appear_animation'] ) ) {
			wp_enqueue_style( 'zion-frontend-animations' );
		}

		$this->enqueue_styles();
	}

	/**
	 * Adds a script to the list of scripts that will be loaded in edit mode
	 *
	 * @param string $script_url The URL of the scripts. This must be a local url
	 *
	 * @return void
	 */
	public function enqueue_editor_script( $script_url ) {
		if ( ! in_array( $script_url, $this->editor_scripts, true ) ) {
			$this->editor_scripts[] = $script_url;
		}
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
		if ( ! in_array( $script_url, $this->element_scripts, true ) ) {
			$this->element_scripts[] = $script_url;
		}
	}


	/**
	 * Adds a style to the list of scripts that will be loaded in edit mode.
	 *
	 * @param string $style_url The URL of the scripts. This must be a local url
	 *
	 * @return void
	 */
	public function enqueue_editor_style( $style_url ) {
		if ( ! in_array( $style_url, $this->editor_styles, true ) ) {
			$this->editor_styles[] = $style_url;
		}
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
		if ( ! in_array( $style_url, $this->element_styles, true ) ) {
			$this->element_styles[] = $style_url;
		}
	}

	/**
	 * Returns the formatter script url
	 *
	 * @param string $handle      The script id/handle
	 * @param string $src         The script relative URL
	 * @param string $base_url    The script base URL
	 * @param string $content_url The script base URL
	 * @param string $ver         The script version
	 *
	 * @return string
	 */
	private function compile_script_url( $handle, $src, $base_url, $content_url, $ver ) {
		if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $content_url && 0 === strpos( $src, $content_url ) ) ) {
			$src = $base_url . $src;
		}

		if ( ! empty( $ver ) ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		/* This filter is documented in wp-includes/class.wp-scripts.php */
		return esc_url( apply_filters( 'script_loader_src', $src, $handle ) );
	}

	/**
	 * Returns the script dependency configuration
	 *
	 * @param \WP_Scripts $manager
	 *
	 * @return array<string, mixed>
	 */
	private function get_dependency_config( $manager ) {
		$scripts = array();

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

	/**
	 * Returns the element style urls
	 *
	 * @return array<string, array<string, string>>
	 */
	private function get_element_styles_for_editor() {
		global $wp_styles;

		// Preserve a copy of styles queue
		$old_queue  = $wp_styles->queue;
		$old_todo   = $wp_styles->to_do;
		$old_done   = $wp_styles->done;
		$old_groups = $wp_styles->groups;

		$wp_styles->queue  = array();
		$wp_styles->to_do  = array();
		$wp_styles->done   = array();
		$wp_styles->groups = array();

		$this->enqueue_styles();

		$scripts = $this->get_dependency_config( $wp_styles );

		$wp_styles->queue  = $old_queue;
		$wp_styles->to_do  = $old_todo;
		$wp_styles->done   = $old_done;
		$wp_styles->groups = $old_groups;

		// Add editor script
		foreach ( $this->editor_styles as $key => $element_style_url ) {
			$scripts[$this->get_type() . '-editor-style-' . $key] = array(
				'src' => $element_style_url,
			);
		}

		foreach ( $this->element_styles as $key => $element_style_url ) {
			$scripts[$this->get_type() . '-element-style-' . $key] = array(
				'src' => $element_style_url,
			);
		}

		return $scripts;
	}


	/**
	 * Will return the default orientation for the element orientation
	 *
	 * @return string
	 */
	public function get_sortable_content_orientation() {
		return 'horizontal';
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
		$wp_scripts->queue  = array();
		$wp_scripts->to_do  = array();
		$wp_scripts->done   = array();
		$wp_scripts->groups = array();
		$this->enqueue_scripts();
		$scripts = $this->get_dependency_config( $wp_scripts );

		// Put back script info
		$wp_scripts->queue  = $old_queue;
		$wp_scripts->to_do  = $old_todo;
		$wp_scripts->done   = $old_done;
		$wp_scripts->groups = $old_groups;

		// Add editor script
		foreach ( $this->editor_scripts as $key => $editor_script_url ) {
			$scripts[$this->get_type() . '-editor-script-' . $key] = array(
				'src' => $editor_script_url,
			);
		}

		foreach ( $this->element_scripts as $key => $element_script_url ) {
			$scripts[$this->get_type() . '-element-script-' . $key] = array(
				'src' => $element_script_url,
			);
		}

		return $scripts;
	}

	/**
	 * Returns the list of element scripts that are used in both editor and frontend
	 *
	 * @return array<int, string> The element scripts
	 */
	public function get_element_scripts() {
		return $this->element_scripts;
	}

	/**
	 * Returns the list of element styles that are used in both editor and frontend
	 *
	 * @return array<int, string> The element scripts
	 */
	public function get_element_styles() {
		return $this->element_styles;
	}

	/**
	 * Returns the scripts data
	 *
	 * @param string          $handle  The script id
	 * @param \_WP_Dependency $obj
	 * @param \WP_Scripts     $manager
	 *
	 * @return array<string, mixed>
	 */
	private function prepare_script_data( $handle, $obj, $manager ) {
		if ( null === $obj->ver ) {
			$ver = '';
		} else {
			$ver = $obj->ver ? $obj->ver : get_bloginfo( 'version' );
		}

		if ( isset( $manager->args[$handle] ) ) {
			$ver = $ver ? $ver . '&amp;' . $manager->args[$handle] : $manager->args[$handle];
		}

		return array(
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
		);
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
	 *
	 * @return void
	 */
	public function render_children() {
		Plugin::$instance->renderer->render_children( $this->get_children() );
	}

	/**
	 * Returns the children HTML content
	 *
	 * @return string|false
	 */
	public function get_children_for_render() {
		ob_start();
		Plugin::$instance->renderer->render_children( $this->get_children() );
		return ob_get_clean();
	}

	/**
	 * Allows elements to pass data to nested children
	 *
	 * @param string $key The key used in provide/inject
	 * @param mixed $value The value provided
	 * @return void
	 */
	public function provide( $key, $value ) {
		if ( ! isset( self::$provides[$key] ) || ! is_array( self::$provides[$key] ) ) {
			self::$provides[$key] = array();
		}

		$this->current_provides[] = $key;
		self::$provides[$key][]   = $value;
	}


	/**
	 * Will return an injected value if it is set
	 *
	 * @param string $key
	 * @return null|mixed
	 */
	public function inject( $key ) {
		if ( isset( self::$provides[$key] ) && is_array( self::$provides[$key] ) ) {
			return end( self::$provides[$key] );
		}
	}

	public function reset_provides() {
		foreach ( $this->current_provides as $provide_key ) {
			array_pop( self::$provides[$provide_key] );
		}
	}

	/**
	 * Get Children
	 *
	 * Returns an array containing all children of this element.
	 * If the element can have multiple content areas ( for example tabs or accordions ) it will loop trough all areas
	 * and returns all it's children
	 *
	 * @return array<int, mixed>
	 */
	public function get_children() {
		$child_elements_data = array();

		if ( ! empty( $this->content ) && is_array( $this->content ) ) {
			foreach ( $this->content as $child_content_data ) {
				$child_elements_data[] = $child_content_data;
			}
		}

		return $child_elements_data;
	}

	public function render_placeholder_info( $config ) {
		// Only render the placeholder in server render
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		$config = wp_parse_args(
			$config,
			array(
				'title'       => esc_html__( 'Heads up!', 'zionbuilder' ),
				'description' => '',
			)
		);

		?>
			<div class="znpb-el-notice">
				<span class="znpb-editor-icon-wrapper">
					<svg class="zion-svg-inline znpb-editor-icon zion-icon zion-desktop" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 50 50" preserveAspectRatio=""><path d="M25.0001 0C31.6306 0 37.9892 2.63403 42.6777 7.32234C47.3664 12.0106 50 18.37 50 24.9999V50H25C18.3698 50 12.0108 47.366 7.32234 42.6777C2.63404 37.9894 1.50167e-06 31.63 1.50167e-06 25C1.50167e-06 18.3701 2.63404 12.0109 7.32234 7.32243C12.011 2.63413 18.37 9.9152e-05 25 9.9152e-05L25.0001 0ZM22.6474 39.1479C22.6474 39.2959 22.7063 39.4382 22.8109 39.5428C22.9155 39.6474 23.0578 39.7063 23.2058 39.7063H26.7951C27.1036 39.7063 27.3535 39.4564 27.3535 39.1479V21.4336C27.3535 21.1255 27.1036 20.8755 26.7951 20.8755H23.2058C23.0578 20.8755 22.9155 20.9344 22.8109 21.0391C22.7063 21.1437 22.6474 21.2855 22.6474 21.4336V39.1479ZM22.6474 15.3344C22.6474 15.4824 22.7063 15.6242 22.8109 15.7292C22.9155 15.8339 23.0578 15.8924 23.2058 15.8924H26.7951C27.1036 15.8924 27.3535 15.6428 27.3535 15.3344V10.8518C27.3535 10.5437 27.1036 10.2938 26.7951 10.2938H23.2058C23.0578 10.2938 22.9155 10.3523 22.8109 10.4573C22.7063 10.5619 22.6474 10.7038 22.6474 10.8518V15.3344Z"/></svg>
				</span>
				<h3><?php echo $config['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
				<p>
				<?php echo $config['description']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>
			</div>
		<?php
	}

	public function render_admin_info_text( $config ) {
		// Only render the placeholder in server render
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		$config = wp_parse_args(
			$config,
			array(
				'title'       => esc_html__( 'Heads up!', 'zionbuilder' ),
				'description' => '',
				'type'        => 'info',
			)
		);

		?>
			<div class="znpb-el-notice znpb-el-notice--<?php echo $config['type']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
				<span class="znpb-editor-icon-wrapper">
					<svg class="zion-svg-inline znpb-editor-icon zion-icon zion-desktop" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 50 50" preserveAspectRatio=""><path d="M25.0001 0C31.6306 0 37.9892 2.63403 42.6777 7.32234C47.3664 12.0106 50 18.37 50 24.9999V50H25C18.3698 50 12.0108 47.366 7.32234 42.6777C2.63404 37.9894 1.50167e-06 31.63 1.50167e-06 25C1.50167e-06 18.3701 2.63404 12.0109 7.32234 7.32243C12.011 2.63413 18.37 9.9152e-05 25 9.9152e-05L25.0001 0ZM22.6474 39.1479C22.6474 39.2959 22.7063 39.4382 22.8109 39.5428C22.9155 39.6474 23.0578 39.7063 23.2058 39.7063H26.7951C27.1036 39.7063 27.3535 39.4564 27.3535 39.1479V21.4336C27.3535 21.1255 27.1036 20.8755 26.7951 20.8755H23.2058C23.0578 20.8755 22.9155 20.9344 22.8109 21.0391C22.7063 21.1437 22.6474 21.2855 22.6474 21.4336V39.1479ZM22.6474 15.3344C22.6474 15.4824 22.7063 15.6242 22.8109 15.7292C22.9155 15.8339 23.0578 15.8924 23.2058 15.8924H26.7951C27.1036 15.8924 27.3535 15.6428 27.3535 15.3344V10.8518C27.3535 10.5437 27.1036 10.2938 26.7951 10.2938H23.2058C23.0578 10.2938 22.9155 10.3523 22.8109 10.4573C22.7063 10.5619 22.6474 10.7038 22.6474 10.8518V15.3344Z"/></svg>
				</span>
				<h3><?php echo $config['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
				<p>
				<?php echo $config['description']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>
			</div>

			<style>

				.znpb-el-notice {
					color: #fff;
					font-size: 13px;
					position: relative;
					background-color: rgba(40, 40, 44, 0.6);
					border-radius: 4px;
					padding: 20px 20px 20px 56px;
					width: 100%;
					margin: 20px;
				}

				.znpb-el-notice--error {
					background-color: #e84655;
				}

				.znpb-el-notice h3 {
					font-size: 15px !important;
					margin: 0 0 5px !important;
				}

				.znpb-el-notice a {
					font-weight: 700;
				}

				.znpb-el-notice .znpb-editor-icon-wrapper {
					color: rgba(255, 255, 255, 0.4);
					position: absolute;
					font-size: 26px;
					margin-left: -36px;
				}

				.znpb-el-notice .znpb-editor-icon-wrapper svg {
					fill: currentColor;
					width: 1em;
					height: 1em;
					display: block;
				}

			</style>
		<?php
	}
}
