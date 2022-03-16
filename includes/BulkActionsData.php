<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Cache
 *
 * @package ZionBuilder
 */
class BulkActionsData {
	public function __construct() {
		add_filter( 'zionbuilder/api/bulk_actions/get_input_select_options/get_all_posts', [ __CLASS__, 'get_all_posts' ], 10, 2 );
		add_filter( 'zionbuilder/api/bulk_actions/get_input_select_options/get_post_taxonomies', [ __CLASS__, 'get_post_taxonomies' ], 10, 2 );
	}

	public static function get_all_posts( $items, $config ) {
		$defaults = [
			'page'           => 1,
			'search_keyword' => '',
			'include'        => '',
		];

		$config = wp_parse_args( $config, $defaults );

		$posts_query = new \WP_Query(
			[
				'post_type'      => 'any',
				'post_status'    => 'publish',
				'posts_per_page' => 25,
				'paged'          => $config['page'],
				's'              => $config['searchKeyword'],
				'include'        => $config['include'],
			]
		);

		return array_map(
			function( $post ) {
				$post_type = get_post_type_object( get_post_type( $post ) );

				return [
					'id'   => $post->ID,
					'name' => sprintf( '%s (%s)', $post->post_title, $post_type->labels->singular_name ),
				];
			},
			$posts_query->posts
		);
	}

	public static function get_post_taxonomies( $items, $config ) {
		global $post;
		$options    = [];
		$taxonomies = get_post_taxonomies( $post->ID );

		if ( empty( $taxonomies ) ) {
			return $options;
		}
		foreach ( $taxonomies as $k ) {
			$options[] = [
				// Nicely format the name
				'name' => ucwords( str_replace( [ '_', '-' ], ' ', $k ) ),
				'id'   => $k,
			];
		}
		return $options;
	}
}
