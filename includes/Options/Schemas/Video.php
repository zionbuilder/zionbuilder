<?php

namespace ZionBuilder\Options\Schemas;

use ZionBuilder\Options\Options;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Typography
 *
 * @package ZionBuilder\Options\Schemas
 */
class Video extends BaseSchema {
	/**
	 * Returns the schema for the Video option type
	 *
	 * @return array
	 */
	public static function get_schema() {
		$options = [
			'videoSource'      => [
				'type'      => 'select',
				'title'     => esc_html__( 'Video source', 'zionbuilder' ),
				'default'   => 'local',
				'css_class' => 'znpb-input-background-video__source',
				'options'   => [
					[
						'name' => esc_html__( 'Local', 'zionbuilder' ),
						'id'   => 'local',
					],
					[
						'name' => esc_html__( 'Vimeo', 'zionbuilder' ),
						'id'   => 'vimeo',
					],
					[
						'name' => esc_html__( 'Youtube', 'zionbuilder' ),
						'id'   => 'youtube',
					],
				],
			],
			'mp4'              => [
				'type'        => 'media',
				'title'       => esc_html__( 'Local source', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Select the video to use.', 'zionbuilder' ),
				'media_type'  => 'video',
				'dependency'  => [
					[
						'option' => 'videoSource',
						'value'  => [ 'local' ],
					],
				],
			],
			'vimeoURL'         => [
				'type'        => 'text',
				'title'       => esc_html__( 'Vimeo video url', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Enter Vimeo url.', 'zionbuilder' ),
				'dependency'  => [
					[
						'option' => 'videoSource',
						'value'  => [ 'vimeo' ],
					],
				],
			],
			'youtubeURL'       => [
				'type'        => 'text',
				'title'       => esc_html__( 'Youtube video url', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Enter the Youtube video url.', 'zionbuilder' ),
				'dependency'  => [
					[
						'option' => 'videoSource',
						'value'  => [ 'youtube' ],
					],
				],
			],
			'bgType'           => [
				'type'       => 'custom_selector',
				'columns'    => 2,
				'title'      => esc_html__( 'Background type', 'zionbuilder' ),
				'default'    => 'cover',
				'options'    => [
					[
						'id'   => 'contain',
						'name' => 'Contain',
					],
					[
						'id'   => 'cover',
						'name' => 'Cover',
					],
				],
				'dependency' => [
					[
						'option' => 'videoSource',
						'value'  => [ 'local' ],
					],
				],
			],
			'controls'         => [
				'type'       => 'checkbox_switch',
				'default'    => true,
				'title'      => esc_html__( 'Show controls', 'zionbuilder' ),
				'layout'     => 'inline',
				'dependency' => [
					[
						'option' => 'videoSource',
						'value'  => [ 'local', 'youtube', 'vimeo' ],
					],
				],
			],
			'controlsPosition' => [
				'type'       => 'custom_selector',
				'columns'    => 3,
				'title'      => esc_html__( 'Controls position', 'zionbuilder' ),
				'options'    => [
					[
						'id'   => 'left',
						'icon' => 'align--left',
					],
					[
						'id'   => 'center',
						'icon' => 'align--center',
					],
					[
						'id'   => 'right',
						'icon' => 'align--right',
					],
				],
				'dependency' => [
					[
						'option' => 'controls',
						'value'  => [ true ],
					],
				],
			],
			'autoplay'         => [
				'type'    => 'checkbox_switch',
				'default' => true,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Autoplay video?', 'zionbuilder' ),

			],
			'muted'            => [
				'type'    => 'checkbox_switch',
				'default' => true,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Start muted?', 'zionbuilder' ),
			],
		];

		$video_options = new Options( 'zionbuilder/schema/video_options', $options );

		return $video_options->get_schema();
	}
}
