<?php

namespace ZionBuilder\Options\Schemas;

use ZionBuilder\Options\Options;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Advanced
 *
 * @package ZionBuilder\Options\Schemas
 */
class Advanced extends BaseSchema {
	/**
	 * @return array
	 */
	public static function get_schema() {
		$advanced_options = new Options( 'zionbuilder/schema/advanced_options' );

		$general_group = $advanced_options->add_group(
			'general-group',
			[
				'type'  => 'panel_accordion',
				'title' => __( 'General options', 'zionbuilder' ),
			]
		);

		$general_group->add_option(
			'_element_name',
			[
				'type'        => 'text',
				'description' => __( 'Set the desired name for this element. Will only appear in edit mode.' ),
				'title'       => __( 'Element name', 'zionbuilder' ),
				'placeholder' => '%%ELEMENT_TYPE%%',
				'save_path'   => 'name',
			]
		);

		$general_group->add_option(
			'_element_id',
			[
				'type'        => 'text',
				'description' => __( 'Set the desired element id. Please note that the id must be unique accross your page' ),
				'title'       => __( 'Element unique id', 'zionbuilder' ),
				'placeholder' => '%%ELEMENT_UID%%',
				'save_path'   => 'uid',
			]
		);

		$general_group->add_option(
			'_element_visibility',
			[
				'type'        => 'custom_selector',
				'description' => __( 'Set the visibility for this element.' ),
				'title'       => __( 'Element Visibility', 'zionbuilder' ),
				'default'     => 'all',
				'options'     => [
					[
						'name' => __( 'All', 'zionbuilder' ),
						'id'   => 'all',
					],
					[
						'name' => __( 'Logged in users', 'zionbuilder' ),
						'id'   => 'logged_in',
					],
					[
						'name' => __( 'Logged out users', 'zionbuilder' ),
						'id'   => 'logged_out',
					],
				],
			]
		);

		$animation_group = $advanced_options->add_group(
			'animation-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Animations options', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		$animation_group->add_option(
			'_appear_animation',
			[
				'type'        => 'radio_image',
				'description' => __( 'Set the desired appear animation when the element becomes visible in the viewport.' ),
				'title'       => __( 'Appear animation', 'zionbuilder' ),
				'default'     => '',
				'columns'     => 3,
				'options'     => [
					[
						'name'  => esc_html__( 'none', 'zionbuilder' ),
						'value' => '',
						'class' => 'znpb-no-animation-placeholder',
					],
					[
						'name'  => esc_html__( 'bounce', 'zionbuilder' ),
						'value' => 'bounce',
						'class' => 'bounce',
					],
					[
						'name'  => esc_html__( 'fade In', 'zionbuilder' ),
						'value' => 'fadeIn',
						'class' => 'fadeIn',
					],
					[
						'name'  => esc_html__( 'Slide In Left', 'zionbuilder' ),
						'value' => 'slideInLeft',
						'class' => 'slideInLeft',
					],
					[
						'name'  => esc_html__( 'flash', 'zionbuilder' ),
						'value' => 'flash',
						'class' => 'flash',
					],
					[
						'name'  => esc_html__( 'wobble', 'zionbuilder' ),
						'value' => 'wobble',
						'class' => 'wobble',
					],
					[
						'name'  => esc_html__( 'tada', 'zionbuilder' ),
						'value' => 'tada',
						'class' => 'tada',
					],
					[
						'name'  => esc_html__( 'rubber Band', 'zionbuilder' ),
						'value' => 'rubberBand',
						'class' => 'rubberBand',
					],
					[
						'name'  => esc_html__( 'shake', 'zionbuilder' ),
						'value' => 'shake',
						'class' => 'shake',
					],
					[
						'name'  => esc_html__( 'jello', 'zionbuilder' ),
						'value' => 'jello',
						'class' => 'jello',
					],
					[
						'name'  => esc_html__( 'heart Beat', 'zionbuilder' ),
						'value' => 'heartBeat',
						'class' => 'heartBeat',
					],
					[
						'name'  => esc_html__( 'bounce In', 'zionbuilder' ),
						'value' => 'bounceIn',
						'class' => 'bounceIn',
					],
					[
						'name'  => esc_html__( 'rotate Out Up Left', 'zionbuilder' ),
						'value' => 'rotateOutUpLeft',
						'class' => 'rotateOutUpLeft',
					],
					[
						'name'  => esc_html__( 'rotate Out Up Right', 'zionbuilder' ),
						'value' => 'rotateOutUpRight',
						'class' => 'rotateOutUpRight',
					],
					[
						'name'  => esc_html__( 'slide In Down', 'zionbuilder' ),
						'value' => 'slideInDown',
						'class' => 'slideInDown',
					],
					[
						'name'  => esc_html__( 'slide In Up', 'zionbuilder' ),
						'value' => 'slideInUp',
						'class' => 'slideInUp',
					],
					[
						'name'  => esc_html__( 'slide In Right', 'zionbuilder' ),
						'value' => 'slideInRight',
						'class' => 'slideInRight',
					],
					[
						'name'  => esc_html__( 'bounce In Right', 'zionbuilder' ),
						'value' => 'bounceInRight',
						'class' => 'bounceInRight',
					],
					[
						'name'  => esc_html__( 'fade In Down Big', 'zionbuilder' ),
						'value' => 'fadeInDownBig',
						'class' => 'fadeInDownBig',
					],
					[
						'name'  => esc_html__( 'zoom Out Up', 'zionbuilder' ),
						'value' => 'zoomOutUp',
						'class' => 'zoomOutUp',
					],
					[
						'name'  => esc_html__( 'rotate Out', 'zionbuilder' ),
						'value' => 'rotateOut',
						'class' => 'rotateOut',
					],
					[
						'name'  => esc_html__( 'slideOutUp', 'zionbuilder' ),
						'value' => 'slideOutUp',
						'class' => 'slideOutUp',
					],
					[
						'name'  => esc_html__( 'fade In Left Big', 'zionbuilder' ),
						'value' => 'fadeInLeftBig',
						'class' => 'fadeInLeftBig',
					],
					[
						'name'  => esc_html__( 'bounce In Up', 'zionbuilder' ),
						'value' => 'bounceInUp',
						'class' => 'bounceInUp',
					],
				],
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'animated {{VALUE}}',
					],
				],
			]
		);

		$animation_group->add_option(
			'_appear_duration',
			[
				'type'        => 'dynamic_slider',
				'description' => esc_html__( 'Set the desired appear animation duration (in miliseconds).' ),
				'title'       => esc_html__( 'Appear duration', 'zionbuilder' ),
				'default'     => '1000ms',
				'content'     => 'ms',
				// 'dependency'  => [
				// 	[
				// 		'option' => '_appear_animation',
				// 		'type'   => 'not_in',
				// 		'value'  => [ '' ],
				// 	],
				// ],
				'options'     => [
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 5,
						'unit'       => 's',
					],
					[
						'min'        => 0,
						'max'        => 10000,
						'step'       => 10,
						'shift_step' => 100,
						'unit'       => 'ms',
					],
				],
				'sync' => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.animation-duration',
			]
		);

		$animation_group->add_option(
			'_appear_delay',
			[
				'type'        => 'dynamic_slider',
				'description' => esc_html__( 'Set the desired appear animation delay (in miliseconds).', 'zionbuilder' ),
				'title'       => esc_html__( 'Appear delay', 'zionbuilder' ),
				'default'     => '0ms',
				'dependency'  => [
					[
						'option' => '_appear_animation',
						'type'   => 'not_in',
						'value'  => [ '' ],
					],
				],
				'options'     => [
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 5,
						'unit'       => 's',
					],
					[
						'min'        => 0,
						'max'        => 10000,
						'step'       => 10,
						'shift_step' => 100,
						'unit'       => 'ms',
					],
				],
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.animation-delay',
			]
		);

		$custom_css_group = $advanced_options->add_group(
			'custom-css-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Custom CSS', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		$custom_css_group->add_option(
			'_custom_css',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Meet Custom CSS', 'zionbuilder' ),
				'message_description' => esc_html__( 'With custom CSS you can fine tune the styling of your elements.', 'zionbuilder' ),
				'message_link'        => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
			]
		);

		return $advanced_options->get_schema();
	}
}
