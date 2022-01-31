<?php

namespace ZionBuilder\Library\Sources;

class ZionSource extends BaseSource {
	const SOURCE_URL = 'https://library.zionbuilder.io/wp-json/zionbuilder-library/v1/items-and-categories';

	public function get_type() {
		return self::TYPE_ZION;
	}

	public function on_init( $source_config ) {
		$this->url = self::SOURCE_URL;
	}

	public function get_item_builder_data( $item_id ) {

	}
}
