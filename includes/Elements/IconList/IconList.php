<?php

namespace ZionBuilder\Elements\IconList;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class IconList extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'icon_list';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Icon List', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'list', 'icons', 'symbol', 'font', 'svg', 'path', 'circle', 'stroke', 'icn' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'icon-list';
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'layout',
			[
				'type'             => 'custom_selector',
				'columns'          => 2,
				'default'          => '',
				'title'            => 'Layout',
				'options'          => [
					[
						'name' => __( 'column', 'zionbuilder' ),
						'id'   => 'column',
					],
					[
						'name' => __( 'row', 'zionbuilder' ),
						'id'   => 'row',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => '-layout--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'icon_position',
			[
				'type'             => 'custom_selector',
				'columns'          => 2,
				'default'          => 'left',
				'title'            => 'Icons position',
				'options'          => [
					[
						'name' => __( 'left', 'zionbuilder' ),
						'id'   => 'Left',
					],
					[
						'name' => __( 'right', 'zionbuilder' ),
						'id'   => 'Right',
					],
					[
						'name' => __( 'top', 'zionbuilder' ),
						'id'   => 'Top',
					],
					[
						'name' => __( 'bottom', 'zionbuilder' ),
						'id'   => 'Bottom',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb-el-iconList__item--icon{{VALUE}}',
					],
				],
			]
		);

		$icons = $options->add_option(
			'icons',
			[
				'type'               => 'repeater',
				'title'              => __( 'List items', 'zionbuilder' ),
				'add_button_text'    => __( 'Add new list item', 'zionbuilder' ),
				'item_title'         => 'text',
				'default_item_title' => 'item %s',
				'default'            => [
					[
						'text' => 'Icon list sample text #1',
						'icon' => [
							'family'  => 'Font Awesome 5 Free Regular',
							'name'    => 'user',
							'unicode' => 'uf007',
						],
					],
					[
						'text' => 'Icon list sample text #2',
						'icon' => [
							'family'  => 'Font Awesome 5 Free Regular',
							'name'    => 'star',
							'unicode' => 'uf005',
						],
					],
					[
						'text' => 'Icon list sample text #3',
						'icon' => [
							'family'  => 'Font Awesome 5 Free Regular',
							'name'    => 'heart',
							'unicode' => 'uf004',
						],
					],
				],
			]
		);

		$icons->add_option(
			'icon',
			[
				'type'        => 'icon_library',
				'title'       => esc_html__( 'Icon', 'zionbuilder' ),
				'description' => esc_html__( 'Choose an icon', 'zionbuilder' ),
			]
		);

		$icons->add_option(
			'text',
			[
				'type'        => 'text',
				'description' => esc_html__( 'Set the desired content for this element', 'zionbuilder' ),
				'title'       => esc_html__( 'Content', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Enter item text', 'zionbuilder' ),
			]
		);

		$icons->add_option(
			'link',
			[
				'type'        => 'link',
				'description' => 'This is the element content',
				'title'       => __( 'Link', 'zionbuilder' ),
			]
		);

		$icons->add_option(
			'icon_color',
			[
				'type'        => 'colorpicker',
				'title'       => __( 'Icon Color', 'zionbuilder' ),
				'description' => __( 'Select the color of the icon', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => '#006dd2',
				'css_style'   => [
					[
						'selector' => '{{ELEMENT}} .zb-el-iconList__item--{{INDEX}} .zb-el-iconList__itemIcon',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$icons->add_option(
			'text_color',
			[
				'type'        => 'colorpicker',
				'title'       => __( 'Text Color', 'zionbuilder' ),
				'description' => __( 'Select the color of the text', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => '#000',
				'css_style'   => [
					[
						'selector' => '{{ELEMENT}} .zb-el-iconList__item--{{INDEX}} .zb-el-iconList__itemText',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);
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
		$this->register_style_options_element(
			'item_styles',
			[
				'title'    => esc_html__( 'Item styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-iconList__item',

			]
		);
		$this->register_style_options_element(
			'text_styles',
			[
				'title'      => esc_html__( 'Text styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-iconList__itemText',
				'render_tag' => 'button_text',
			]
		);
		$this->register_style_options_element(
			'icon_styles',
			[
				'title'      => esc_html__( 'Icon styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-iconList__itemIcon',
				'render_tag' => 'icon',
			]
		);

	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/IconList/editor', 'js' ) );
	}

	/**
	 * Enqueue element styles for both frontend and editor
	 *
	 * If you want to use the ZionBuilder cache system you must use
	 * the enqueue_editor_style(), enqueue_element_style() functions
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		// Using helper methods will go through caching policy
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/IconList/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$icons = $options->get_value( 'icons', [] );
		$index = 0;
		foreach ( $icons as $config ) {
			$this->render_single_icon( $config, $options, $index );
			$index++;

		}
	}

	public function render_single_icon( $config, $options, $index ) {
		$icon_position    = $options->get_value( 'icon_position', 'left' );
		$icon_html        = '';
		$text_html        = '';
		$html_tag         = 'span';
		$link             = ! empty( $config['link'] ) ? $config['link'] : false;
		$item_index_class = sprintf( 'zb-el-iconList__item zb-el-iconList__item--%s zb-el-iconList__item--icon%s', $index, $icon_position );

		$combined_icon_attr = $this->render_attributes->get_combined_attributes( 'icon_styles', [ 'class' => 'zb-el-iconList__itemIcon' ] );
		$combined_text_attr = $this->render_attributes->get_combined_attributes( 'text_styles', [ 'class' => 'zb-el-iconList__itemText' ] );
		$combined_item_attr = $this->render_attributes->get_combined_attributes( 'item_styles', [ 'class' => $item_index_class ] );

		if ( ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'item' . $index, $link );
			$html_tag = 'a';
		}

		if ( ! empty( $config['icon'] ) ) {
			$this->attach_icon_attributes( 'icon', $config['icon'] );
			$icon_html = $this->get_render_tag(
				'span',
				'icon',
				'',
				$combined_icon_attr
			);
		}

		if ( ! empty( $config['text'] ) ) {
			$text_html = $this->get_render_tag(
				'span',
				'button_text',
				$config['text'],
				$combined_text_attr
			);
		}

		$this->render_tag(
			$html_tag,
			'item' . $index,
			[ $icon_html, $text_html ],
			$combined_item_attr
		);
	}
}
