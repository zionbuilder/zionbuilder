<?php

namespace ZionBuilder\Upgrade;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Updater
 *
 * @since 1.0.1
 * @package ZionBuilder
 */
class Upgrades {
	/**
	 * Updates the editor status meta key. This resolves compatibility with legacy ZionBuilder pages
	 *
	 * @return void
	 */
	public static function upgrade_v_1_0_1_uadate_editor_status_meta() {
		global $wpdb;

		$wpdb->query(
			"
				UPDATE {$wpdb->postmeta} AS meta1
				RIGHT JOIN {$wpdb->postmeta} AS meta2
					ON meta1.post_id = meta2.post_id
				SET
					meta1.meta_key = '_zionbuilder_page_status'
				WHERE meta1.meta_key = '_zn_zion_builder_status'
				AND meta2.meta_key = '_zn_page_builder_els'
				AND JSON_VALID(meta2.meta_value) = 1
			"
		);
	}

	/**
	 * Updates the editor status meta key. This resolves compatibility with legacy ZionBuilder pages
	 *
	 * @return void
	 */
	public static function upgrade_v_1_0_1_uadate_editor_elements_meta() {
		global $wpdb;

		$wpdb->query(
			"
				UPDATE {$wpdb->postmeta} AS meta1
				RIGHT JOIN {$wpdb->postmeta} AS meta2
					ON meta1.post_id = meta2.post_id
				SET
					meta1.meta_key = '_zionbuilder_page_elements'
				WHERE meta1.meta_key = '_zn_page_builder_els'
				AND JSON_VALID(meta2.meta_value) = 1
			"
		);
	}
}
