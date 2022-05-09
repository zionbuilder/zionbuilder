<?php

namespace ZionBuilder\Elements\ImageSlider;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\CommonJS;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Image
 *
 * @package ZionBuilder\Elements
 */
class ImageSlider extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'image_slider';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Image Slider', 'zionbuilder' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'media';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'image', 'media', 'carousell', 'slider', 'picture', 'transition', 'slides', 'gallery', 'portfolio', 'photo', 'sld' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-slider';
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
			'images',
			[
				'type'           => 'image_gallery',
				'preview_holder' => esc_html__( 'Select images.', 'zionbuilder' ),
				'button_title'   => esc_html__( 'Select images.', 'zionbuilder' ),
				'title'          => esc_html__( 'Gallery images.', 'zionbuilder' ),
				'default'        => [
					[
						'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
					],
					[
						'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
					],
					[
						'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
					],
				],
			]
		);

		$options->add_option(
			'arrows',
			[
				'type'    => 'checkbox_switch',
				'default' => true,
				'title'   => esc_html__( 'Show arrows', 'zionbuilder' ),
				'layout'  => 'inline',
			]
		);

		$options->add_option(
			'dots',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'title'   => esc_html__( 'Show dots', 'zionbuilder' ),
				'layout'  => 'inline',
			]
		);

		$options->add_option(
			'infinite',
			[
				'type'        => 'checkbox_switch',
				'default'     => true,
				'title'       => esc_html__( 'Infinite', 'zionbuilder' ),
				'description' => esc_html__( 'Set to yes to enable continuous loop mode. Please note that this is disabled in editor mode.', 'zionbuilder' ),

				'layout'      => 'inline',
			]
		);

		$options->add_option(
			'autoplay',
			[
				'type'    => 'checkbox_switch',
				'default' => true,
				'title'   => esc_html__( 'Autoplay', 'zionbuilder' ),
				'layout'  => 'inline',
			]
		);

		$options->add_option(
			'slides_to_show',
			[
				'type'               => 'number',
				'title'              => __( 'Slides to show', 'zionbuilder' ),
				'min'                => 1,
				'max'                => 15,
				'layout'             => 'inline',
				'responsive_options' => true,
			]
		);

		$options->add_option(
			'slides_to_scroll',
			[
				'type'               => 'number',
				'title'              => __( 'Slides to scroll', 'zionbuilder' ),
				'min'                => 1,
				'max'                => 5,
				'default'            => 1,
				'layout'             => 'inline',
				'responsive_options' => true,
			]
		);

		$options->add_option(
			'autoplay_delay',
			[
				'type'    => 'number',
				'title'   => __( 'Autoplay speed', 'zionbuilder' ),
				'min'     => 1,
				'max'     => 15000,
				'default' => 3000,
				'layout'  => 'inline',
			]
		);
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'zion-builder-slider' );

		// Enqueue responsive devices
		CommonJS::enqueue_responsive_devices( 'zion-builder-slider' );

		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/ImageSlider/editor.js' ) );
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
		wp_enqueue_style( 'swiper' );
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		$autoplay = $options->get_value( 'autoplay' );

		$config = [
			'arrows'           => $options->get_value( 'arrows' ),
			'pagination'       => $options->get_value( 'dots' ),
			'slides_to_show'   => $options->get_value( 'slides_to_show' ),
			'slides_to_scroll' => $options->get_value( 'slides_to_scroll' ),
			'rawConfig'        => [
				'loop'     => $options->get_value( 'infinite' ),
				'autoplay' => $autoplay,
			],
		];

		if ( $autoplay ) {
			$config['rawConfig']['autoplay'] = [
				'delay' => $options->get_value( 'autoplay_delay' ),
			];
		}

		$this->render_attributes->add( 'wrapper', 'data-zion-slider-config', wp_json_encode( $config ) );
		$this->render_attributes->add( 'wrapper', 'class', 'swiper-container' );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$images     = $options->get_value( 'images' );
		$pagination = $options->get_value( 'dots' );
		$arrows     = $options->get_value( 'arrows' ); ?>
		<div class="swiper-wrapper">
			<?php
			foreach ( $images as $image ) {
				printf( '<img src="%s" class="swiper-slide"/>', esc_attr( $image['image'] ) );
			}
			?>

		</div>

		<!-- Add Pagination -->
		<?php if ( $pagination ) : ?>
			<div class="swiper-pagination"></div>
		<?php endif; ?>

		<!-- Arrows -->
		<?php if ( $arrows ) : ?>
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
		<?php endif; ?>
		<?php
	}
}
