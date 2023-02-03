<?php

namespace ZionBuilder\Elements\Video;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Video
 *
 * @package ZionBuilder\Elements
 */
class Video extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_video';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Video', 'zionbuilder' );
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
		return [ 'video', 'media', 'youtube', 'vimeo', 'embed', 'mp4' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-media';
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
			'video_config',
			[
				'type'            => 'video',
				'default'         => [
					'videoSource' => 'youtube',
					'youtubeURL'  => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
				],
				'exclude_options' => [ 'controlsPosition' ],
			]
		);

		$options->add_option(
			'use_image_overlay',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Use image overlay?', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'image',
			[
				'type'       => 'image',
				'id'         => 'image',
				'title'      => esc_html__( 'Image overlay', 'zionbuilder' ),
				'show_size'  => true,
				'default'    => [
					'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
				],
				'dependency' => [
					[
						'option' => 'use_image_overlay',
						'value'  => [ true ],
					],
				],
			]
		);

		$options->add_option(
			'show_play_icon',
			[
				'type'       => 'checkbox_switch',
				'default'    => false,
				'layout'     => 'inline',
				'title'      => esc_html__( 'Show play icon', 'zionbuilder' ),
				'dependency' => [
					[
						'option' => 'use_image_overlay',
						'value'  => [ true ],
					],
				],
			]
		);

		// $options->add_option(
		//  'use_modal',
		//  [
		//      'type'       => 'checkbox_switch',
		//      'default'    => false,
		//      'layout'     => 'inline',
		//      'title'      => esc_html__( 'Show video in modal', 'zionbuilder' ),
		//      'dependency' => [
		//          [
		//              'option' => 'use_image_overlay',
		//              'value'  => [ true ],
		//          ],
		//      ],
		//  ]
		// );
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		// data-zion-options
		$video_options = array_merge(
			$options->get_value( 'video_config', [] ),
			[
				'use_image_overlay' => $options->get_value( 'use_image_overlay' ),
				'use_modal'         => $options->get_value( 'use_modal' ),
			]
		);
		$this->render_attributes->add( 'wrapper', 'data-zion-video', wp_json_encode( $video_options ) );
	}

	/**
	 * Get video source
	 *
	 * Returns the video source from config
	 *
	 * @param array $video_config The video config received from options
	 *
	 * @return string The video source type
	 */
	private function get_video_source( $video_config ) {
		return isset( $video_config['videoSource'] ) ? $video_config['videoSource'] : 'local';
	}

	/**
	 * Get video url
	 *
	 * Returns the vide url from config
	 *
	 * @param array $video_config The video config received from options
	 *
	 * @return string|boolean The video url
	 */
	private function get_video_url( $video_config ) {
		$source = $this->get_video_source( $video_config );

		if ( 'local' === $source && ! empty( $video_config['mp4'] ) ) {
			return $video_config['mp4'];
		} else {
			if ( 'youtube' === $source && ! empty( $video_config['youtubeURL'] ) ) {
				return $video_config['youtubeURL'];
			} else {
				if ( 'vimeo' === $source && ! empty( $video_config['vimeoURL'] ) ) {
					return $video_config['vimeoURL'];
				}
			}
		}

		return false;
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'zb-video' );

		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Video/editor', 'js' ) );
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
		$this->enqueue_element_style( Plugin::instance()->scripts->get_script_url( 'elements/Video/frontend', 'css' ) );
		$this->enqueue_editor_style( Plugin::instance()->scripts->get_script_url( 'elements/Video/editor', 'css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$video_config      = $options->get_value( 'video_config' );
		$use_image_overlay = $options->get_value( 'use_image_overlay' );
		$image             = $options->get_value( 'image' );
		$show_play_icon    = $options->get_value( 'show_play_icon' );
		$use_modal         = $options->get_value( 'use_modal' );
		$video_url         = $this->get_video_url( $video_config );
		$video_source      = $this->get_video_source( $video_config );

		// Show the overlay
		if ( $use_image_overlay ) {
			$play_button = '';
			$html_tag    = 'div';

			// Add play icon
			if ( $show_play_icon ) {
				$play_button = '<span class="zb-el-zionVideo-play-button zion-play-filled"><svg class="zb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M55.3 32 8.7 5.1v53.8L55.3 32z"/></svg></span>';
			}

			// Add background image
			if ( isset( $image['image'] ) ) {
				$this->render_attributes->add( 'image_overlay', 'style', sprintf( 'background-image: url( %s );', $image['image'] ) );
			}

			// Attach modal functionality
			if ( $use_modal && ! empty( $video_url ) ) {
				$html_tag = 'a';
				if ( 'local' === $video_source ) {
					$this->render_attributes->add( 'image_overlay', 'href', $video_url );
					$this->render_attributes->add( 'image_overlay', 'data-iframe', 'true' );
					$this->render_attributes->add( 'image_overlay', 'data-zion-lightbox', 'true' );
				} else {
					$this->render_attributes->add( 'image_overlay', 'href', $video_url );
					$this->render_attributes->add( 'image_overlay', 'data-zion-lightbox', true );
				}
			}

			// render image overlay
			$this->render_tag(
				$html_tag,
				'image_overlay',
				$play_button,
				[
					'class' => 'zb-el-zionVideo-overlay zb-el-zionVideo-overlay--inline',
				]
			);
		}
	}
}
