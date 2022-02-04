<?php

namespace ZionBuilder\Options\Schemas;

use ZionBuilder\Options\Options;
use ZionBuilder\Options\Option;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class StyleOptions
 *
 * @package ZionBuilder\Editor
 */
class StyleOptions extends BaseSchema {
	/**
	 * Attach background options
	 *
	 * Will attach the background options to the options instance
	 *
	 * @param Option $options
	 *
	 * @return void
	 */
	private static function attach_background_options( $options ) {
		// Background option
		$options->add_group(
			'background-config',
			[
				'type'        => 'background',
				'search_tags' => [
					'background',
					'video',
					'image',
					'color',
					'gradient',
				],
			]
		);
	}

	/**
	 * Adds the typography options to a group of existing options
	 *
	 * @param Option $options
	 *
	 * @return void
	 */
	public static function attach_typography_options( $options ) {
		$typography_group = $options->add_group(
			'typography',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Font', 'zionbuilder' ),
			]
		);

		// Background option
		$typography_group->add_group(
			'typography',
			[
				'type'        => 'typography',
				'title'       => esc_html__( 'Element font', 'zionbuilder' ),
				'description' => esc_html__( 'Select the desired font to use for this element.', 'zionbuilder' ),
				'search_tags' => [
					'typography',
					'text',
					'color',
				],
			]
		);

		$font_style_group = $options->add_group(
			'text-shadow',
			[
				'type'      => 'panel_accordion',
				'title'     => esc_html__( 'Text Shadow', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		// Text shadow
		$font_style_group->add_option(
			'text-shadow',
			[
				'type'        => 'shadow',
				'title'       => esc_html__( 'Text Shadow', 'zionbuilder' ),
				'description' => esc_html__( 'Set the desired text shadow', 'zionbuilder' ),
			]
		);
	}

	public static function attach_border_options( $options ) {
		// Border
		$border_group = $options->add_group(
			'border',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Border', 'zionbuilder' ),
			]
		);
		$border_group->add_option(
			'border',
			[
				'type'        => 'borders',
				'title'       => esc_html__( 'Border', 'zionbuilder' ),
				'description' => esc_html__( 'Choose the desired border style.', 'zionbuilder' ),
				'dimensions'  => [
					[
						'name' => 'right',
						'icon' => 'border-right',
						'id'   => 'border-right',
					],
					[
						'name' => 'top',
						'icon' => 'border-top',
						'id'   => 'border-top',
					],
					[
						'name' => 'left',
						'icon' => 'border-left',
						'id'   => 'border-left',
					],
					[
						'name' => 'bottom',
						'icon' => 'border-bottom',
						'id'   => 'border-bottom',
					],
				],
			]
		);

		// Border radius
		$border_radius_group = $options->add_group(
			'border-radius',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Border radius', 'zionbuilder' ),
			]
		);

		$border_radius_group->add_option(
			'border-radius',
			[
				'type'        => 'dimensions',
				'title'       => esc_html__( 'Border', 'zionbuilder' ),
				'description' => esc_html__( 'Choose the desired border radius style.', 'zionbuilder' ),
				'min'         => 0,
				'dimensions'  => [
					[
						'name' => 'top right',
						'icon' => 't-r-corner',
						'id'   => 'border-top-right-radius',
					],
					[
						'name' => 'top left',
						'icon' => 't-l-corner',
						'id'   => 'border-top-left-radius',
					],
					[
						'name' => 'bottom right',
						'icon' => 'b-r-corner',
						'id'   => 'border-bottom-right-radius',
					],
					[
						'name' => 'bottom left',
						'icon' => 'b-l-corner',
						'id'   => 'border-bottom-left-radius',
					],
				],
			]
		);

		$box_shadow_group = $options->add_group(
			'box-shadow-group',
			[
				'type'             => 'panel_accordion',
				'title'            => esc_html__( 'Box shadow options', 'zionbuilder' ),
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'icon'             => 'border',
				'label'            => [
					'type' => 'pro',
					'text' => esc_html__( 'PRO', 'zionbuilder' ),
				],
				'show_title'       => false,
			]
		);

		$box_shadow_group->add_option(
			'upgrade_message',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Meet Box Shadow', 'zionbuilder' ),
				'message_description' => esc_html__( 'With box shadow, you can create impressive 3d effects and designs', 'zionbuilder' ),
				'info_text'           => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
			]
		);
	}

	public static function attach_size_and_spacings_options( $options ) {
		$spacing_group = $options->add_group(
			'margin_padding',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Spacing', 'zionbuilder' ),
			]
		);

		$spacing_group->add_group(
			'margin-padding',
			[
				'type'        => 'spacing',
				'title'       => esc_html__( 'Margin & padding', 'zionbuilder' ),
				'description' => esc_html__( 'Choose the desired margin and padding for this element.', 'zionbuilder' ),
			]
		);

		$sizings_group = $options->add_group(
			'sizing',
			[
				'type'  => 'panel_accordion',
				'title' => esc_html__( 'Sizing', 'zionbuilder' ),
			]
		);

		$sizings_group->add_option(
			'width',
			[
				'type'  => 'number_unit',
				'title' => esc_html__( 'Width', 'zionbuilder' ),
				'width' => 33.3,
				'units' => [
					'px',
					'pt',
					'rem',
					'vh',
					'vw',
					'%',
					'auto',
					'initial',
					'unset',
				],
				'min'   => 0,
			]
		);

		$sizings_group->add_option(
			'min-width',
			[
				'type'  => 'number_unit',
				'title' => esc_html__( 'Min Width', 'zionbuilder' ),
				'width' => 33.3,
				'units' => [
					'px',
					'pt',
					'rem',
					'vh',
					'vw',
					'%',
					'auto',
					'initial',
					'unset',
				],
				'min'   => 0,
			]
		);

		$sizings_group->add_option(
			'max-width',
			[
				'type'  => 'number_unit',
				'title' => esc_html__( 'Max Width', 'zionbuilder' ),
				'width' => 33.3,
				'min'   => 0,
				'units' => [
					'px',
					'pt',
					'rem',
					'vh',
					'vw',
					'%',
					'auto',
					'initial',
					'unset',
				],
			]
		);

		$sizings_group->add_option(
			'height',
			[
				'type'  => 'number_unit',
				'title' => esc_html__( 'Height', 'zionbuilder' ),
				'width' => 33.3,
				'units' => self::get_units(),
				'min'   => 0,
			]
		);

		$sizings_group->add_option(
			'min-height',
			[
				'type'  => 'number_unit',
				'title' => esc_html__( 'Min Height', 'zionbuilder' ),
				'width' => 33.3,
				'units' => self::get_units(),
				'min'   => 0,
			]
		);

		$sizings_group->add_option(
			'max-height',
			[
				'type'  => 'number_unit',
				'title' => esc_html__( 'Max Height', 'zionbuilder' ),
				'width' => 33.3,
				'units' => self::get_units(),
				'min'   => 0,
			]
		);
	}

	/**
	 * Returns an instance of the Options
	 *
	 * @return Options
	 */
	public static function get_options_instance() {
		$options = new Options( 'zionbuilder/schema/style_options' );

		$responsive_option = $options->add_group(
			'_styles',
			[
				'type' => 'responsive_group',
			]
		);

		$pseudo_option = $responsive_option->add_group(
			'pseudo_selectors',
			[
				'type' => 'pseudo_group',
			]
		);

		// Background accordion
		$background_accordion = $pseudo_option->add_group(
			'background',
			[
				'type'             => 'accordion_menu',
				'title'            => esc_html__( 'Background', 'zionbuilder' ),
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'icon'             => 'background',
				'show_title'       => false,
			]
		);

		self::attach_background_options( $background_accordion );

		// Typography accordion
		$typography_accordion = $pseudo_option->add_group(
			'typography',
			[
				'type'             => 'accordion_menu',
				'title'            => esc_html__( 'Typography', 'zionbuilder' ),
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'icon'             => 'typography',
				'show_title'       => false,
			]
		);

		self::attach_typography_options( $typography_accordion );

		// Borders accordion
		$borders = $pseudo_option->add_group(
			'borders',
			[
				'type'             => 'accordion_menu',
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'title'            => esc_html__( 'Borders & Shadows', 'zionbuilder' ),
				'icon'             => 'borders',
				'show_title'       => false,
			]
		);

		self::attach_border_options( $borders );

		// Size and Spacings accordion
		$size_and_spacings = $pseudo_option->add_group(
			'size_and_spacings',
			[
				'type'             => 'accordion_menu',
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'title'            => esc_html__( 'Size & Spacing', 'zionbuilder' ),
				'icon'             => 'size-spacing',
				'show_title'       => false,
			]
		);

		self::attach_size_and_spacings_options( $size_and_spacings );

		// Display accordion
		$display = $pseudo_option->add_group(
			'display',
			[
				'type'             => 'accordion_menu',
				'title'            => esc_html__( 'Display', 'zionbuilder' ),
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'icon'             => 'display',
				'show_title'       => false,
			]
		);

		$zindex_group = $display->add_group(
			'z-index-group',
			[
				'type'  => 'panel_accordion',
				'title' => __( 'Z-index options', 'zionbuilder' ),
			]
		);

		$zindex_group->add_option(
			'z-index',
			[
				'type'           => 'number',
				'options-layout' => 'inline',
				'title'          => esc_html__( 'Z-index', 'zionbuilder' ),
				'description'    => esc_html__( 'Choose Z-index for element', 'zionbuilder' ),
			]
		);

		$display_group = $display->add_group(
			'display-group',
			[
				'type'  => 'panel_accordion',
				'title' => __( 'Display options', 'zionbuilder' ),
			]
		);

		$display_group->add_option(
			'direction',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Direction', 'zionbuilder' ),
				'description' => __( 'Set the text direction', 'zionbuilder' ),
				'columns'     => 2,
				'default'     => 'ltr',
				'options'     => [
					[
						'name' => __( 'ltr', 'zionbuilder' ),
						'id'   => 'ltr',
					],
					[
						'name' => __( 'rtl', 'zionbuilder' ),
						'id'   => 'rtl',
					],
				],
			]
		);

		$display_group->add_option(
			'cursor',
			[
				'type'        => 'select',
				'title'       => __( 'Cursor', 'zionbuilder' ),
				'description' => __( 'Set the cursor style', 'zionbuilder' ),
				'columns'     => 2,
				'default'     => 'auto',
				'filterable'  => true,
				'options'     => [
					[
						'name' => 'auto',
						'id'   => 'auto',
					],
					[
						'name' => 'default',
						'id'   => 'default',
					],
					[
						'name' => 'none',
						'id'   => 'none',
					],
					[
						'name' => 'context-menu',
						'id'   => 'context-menu',
					],
					[
						'name' => 'help',
						'id'   => 'help',
					],
					[
						'name' => 'pointer',
						'id'   => 'pointer',
					],
					[
						'name' => 'progress',
						'id'   => 'progress',
					],
					[
						'name' => 'wait',
						'id'   => 'wait',
					],
					[
						'name' => 'cell',
						'id'   => 'cell',
					],
					[
						'name' => 'crosshair',
						'id'   => 'crosshair',
					],

					[
						'name' => 'text',
						'id'   => 'text',
					],
					[
						'name' => 'vertical-text',
						'id'   => 'vertical-text',
					],
					[
						'name' => 'alias',
						'id'   => 'alias',
					],
					[
						'name' => 'copy',
						'id'   => 'copy',
					],
					[
						'name' => 'move',
						'id'   => 'move',
					],
					[
						'name' => 'no-drop',
						'id'   => 'no-drop',
					],
					[
						'name' => 'not-allowed',
						'id'   => 'not-allowed',
					],
					[
						'name' => 'grab',
						'id'   => 'grab',
					],
					[
						'name' => 'grabbing',
						'id'   => 'grabbing',
					],
					[
						'name' => 'all-scroll',
						'id'   => 'all-scroll',
					],
					[
						'name' => 'col-resize',
						'id'   => 'col-resize',
					],
					[
						'name' => 'row-resize',
						'id'   => 'row-resize',
					],
					[
						'name' => 'n-resize',
						'id'   => 'n-resize',
					],
					[
						'name' => 'e-resize',
						'id'   => 'e-resize',
					],
					[
						'name' => 's-resize',
						'id'   => 's-resize',
					],
					[
						'name' => 'w-resize',
						'id'   => 'w-resize',
					],
					[
						'name' => 'ne-resize',
						'id'   => 'ne-resize',
					],
					[
						'name' => 'nw-resize',
						'id'   => 'nw-resize',
					],
					[
						'name' => 'se-resize',
						'id'   => 'se-resize',
					],
					[
						'name' => 'sw-resize',
						'id'   => 'sw-resize',
					],
					[
						'name' => 'ew-resize',
						'id'   => 'ew-resize',
					],
					[
						'name' => 'ns-resize',
						'id'   => 'ns-resize',
					],
					[
						'name' => 'nesw-resize',
						'id'   => 'nesw-resize',
					],
					[
						'name' => 'nwse-resize',
						'id'   => 'nwse-resize',
					],
					[
						'name' => 'zoom-in',
						'id'   => 'zoom-in',
					],
					[
						'name' => 'zoom-out',
						'id'   => 'zoom-out',
					],
				],
			]
		);

		$display->add_option(
			'upgrade_message',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Want additional display options?', 'zionbuilder' ),
				'message_description' => esc_html__( 'With ZionBuilder PRO you have access to additional options like display, flex, position, overflow and many more.', 'zionbuilder' ),
				'info_text'           => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
			]
		);

		// transitions accordion
		$transitions = $pseudo_option->add_group(
			'transitions',
			[
				'type'             => 'accordion_menu',
				'title'            => esc_html__( 'Transitions', 'zionbuilder' ),
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'icon'             => 'transitions',
				'label'            => [
					'type' => 'pro',
					'text' => esc_html__( 'PRO', 'zionbuilder' ),
				],
				'show_title'       => false,
			]
		);

		$transitions->add_option(
			'upgrade_message',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Meet Transitions', 'zionbuilder' ),
				'message_description' => esc_html__( 'With transitions you can bring your elements to life by animating them to suit your needs.', 'zionbuilder' ),
				'info_text'           => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
			]
		);

		// transform accordion
		$transform = $pseudo_option->add_group(
			'transform',
			[
				'type'             => 'accordion_menu',
				'title'            => esc_html__( 'Transform', 'zionbuilder' ),
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'icon'             => 'transform',
				'label'            => [
					'type' => 'pro',
					'text' => esc_html__( 'PRO', 'zionbuilder' ),
				],
				'show_title'       => false,
			]
		);

		$transform->add_option(
			'upgrade_message',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Meet Transform', 'zionbuilder' ),
				'message_description' => esc_html__( 'With Zion Builder PRO you have access to transform options such as translate, scale, rotate, skew and perspective.', 'zionbuilder' ),
				'info_text'           => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
			]
		);

		// filters accordion
		$filters = $pseudo_option->add_group(
			'filters',
			[
				'type'             => 'accordion_menu',
				'title'            => esc_html__( 'Filters', 'zionbuilder' ),
				'home-button-text' => esc_html__( 'Styling', 'zionbuilder' ),
				'icon'             => 'filters',
				'label'            => [
					'type' => 'pro',
					'text' => esc_html__( 'PRO', 'zionbuilder' ),
				],
				'show_title'       => false,
			]
		);

		$filters->add_option(
			'upgrade_message',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Meet Filters', 'zionbuilder' ),
				'message_description' => esc_html__( 'With filter options you can chang the element hue, saturate, opacity, contrast and box shadow.', 'zionbuilder' ),
				'info_text'           => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
			]
		);

		return $options;
	}

	/**
	 * @return array
	 */
	public static function get_schema() {
		$options = self::get_options_instance();

		return $options->get_schema();
	}
}
