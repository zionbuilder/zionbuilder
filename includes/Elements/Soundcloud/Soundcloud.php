<?php

namespace ZionBuilder\Elements\Soundcloud;

use ZionBuilder\Elements\Element;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class Soundcloud extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'soundcloud';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Soundcloud', 'zionbuilder' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'external';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'soundcloud', 'audio', 'sound', 'cloud', 'music', 'player', 'spotify', 'embed', 'media', 'mp3' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-soundcloud';
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
			'soundcloud_url',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Audio URL', 'zionbuilder' ),
				'placeholder' => 'https://soundcloud.com/gennaro-di-cello-1/sets/jazz',
				'default'     => 'https://soundcloud.com/gennaro-di-cello-1/sets/jazz',
			]
		);

		$options->add_option(
			'visual',
			[
				'type'        => 'select',
				'title'       => esc_html__( 'Player type', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'options'     => [
					[
						'name' => esc_html__( 'Simple' ),
						'id'   => false,
					],
					[
						'name' => esc_html__( 'Artistic graphic' ),
						'id'   => true,
					],
				],
			]
		);

		$options->add_option(
			'auto_play',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Autoplay audio?', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'show_artwork',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Show artwork?', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'columns'     => 2,
				'title_width' => 50,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => false,
					],
				],
				'dependency'  => [
					[
						'option' => 'visual',
						'type'   => 'not_in',
						'value'  => [ true ],
					],
				],
			]
		);

		$options->add_option(
			'show_playcount',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Show play count?', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => false,
					],
				],
				'dependency'  => [
					[
						'option' => 'visual',
						'type'   => 'not_in',
						'value'  => [ true ],
					],
				],
			]
		);

		$options->add_option(
			'hide_related',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Show related', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => false,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => true,
					],
				],
			]
		);

		$options->add_option(
			'show_user',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Show user?', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'liking',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Show Like button?', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'buying',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Show buy button?', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'sharing',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Show share button?', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'download',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Show download button?', 'zionbuilder' ),
				'layout'      => 'inline',
				'default'     => true,
				'title_width' => 50,
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Yes', 'zionbuilder' ),
						'id'   => true,
					],
					[
						'name' => __( 'No', 'zionbuilder' ),
						'id'   => false,
					],
				],
			]
		);

		$options->add_option(
			'color',
			[
				'type'   => 'colorpicker',
				'title'  => __( 'Buttons color', 'zionbuilder' ),
				'layout' => 'inline',
			]
		);
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$url         = $options->get_value( 'soundcloud_url', 'https://soundcloud.com/gennaro-di-cello-1/sets/jazz' );
		$params_list = [
			'auto_play'      => true,
			'show_artwork'   => true,
			'show_playcount' => true,
			'hide_related'   => true,
			'show_user'      => true,
			'liking'         => true,
			'buying'         => true,
			'sharing'        => true,
			'download'       => true,
		];

		foreach ( $params_list as $option_key => $default_value ) {
			$option_value        = $options->get_value( $option_key, $default_value );
			$actual_option_value = $option_value ? 'true' : 'false';
			$url                 = add_query_arg( $option_key, $actual_option_value, $url );
		}

		// Add color
		$color = $options->get_value( 'color', false );
		if ( $color ) {
			$url = add_query_arg( 'color', $color, $url );
		}

		$player_html = wp_oembed_get( $url );

		// Check if visual is set to false
		$visual = $options->get_value( 'visual', true );
		if ( $visual === false ) {
			$player_html = str_replace( 'visual=true', 'visual=false', $player_html );
			$player_html = str_replace( 'height="400"', 'height="200"', $player_html );
		}

		// Ignore the escape output as the HTML comes from wp_oembed_get()
		echo $player_html; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
