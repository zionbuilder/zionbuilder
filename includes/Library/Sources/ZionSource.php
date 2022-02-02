<?php

namespace ZionBuilder\Library\Sources;

use ZionBuilder\Plugin;

class ZionSource extends BaseSource {
	const SOURCE_URL = 'https://library.zionbuilder.io/wp-json/zionbuilder-library/v2/library';
	// const SOURCE_URL = 'http://zionbuilder.local/wp-json/zionbuilder-library/v2/library';

	/**
	 * True if you want to use the browser cache to cache the template lis
	 *
	 * @var boolean
	 */
	public $use_cache = true;

	public function get_type() {
		return self::TYPE_ZION;
	}

	public function on_init( $source_config ) {
		$this->url = self::SOURCE_URL . '/items-and-categories';
	}

	public function insert_item( $item_id ) {
		$url      = self::SOURCE_URL . '/get-template-archive';
		$args     = apply_filters(
			'zionbuilder/library/zion_library/remote_arguments',
			[
				'template_id' => $item_id,
			]
		);
		$url      = add_query_arg( $args, $url );
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$respone_body = wp_remote_retrieve_body( $response );
		$respone_body = json_decode( $respone_body, true );

		if ( empty( $respone_body['download_url'] ) ) {
			return new \WP_Error( 'template_data_not_valid', __( 'The template archive could not be found!', 'zionbuilder' ) );
		}

		$archive_url = $respone_body['download_url'];

		$template_file_download = download_url( $archive_url );

		if ( is_wp_error( $template_file_download ) ) {
			$template_file_download->add_data( [ 'status' => 500 ] );
			return $template_file_download;
		}

		$response = Plugin::instance()->import_export->insert_template_package( $template_file_download );

		// prepare the template data
		if ( is_wp_error( $response ) ) {
			$response->add_data( [ 'status' => 500 ] );
			return $response;
		}

		if ( empty( $response['template_data'] ) ) {
			return new \WP_Error( 'template_data_not_valid', __( 'The uploaded template is not valid!', 'zionbuilder' ) );
		}

		// Send back the response
		return $response['template_data'];
	}
}
