<?php

namespace ZionBuilder\Upgrade;

use ZionBuilder\Settings;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Updater
 *
 * @since 1.0.1
 *
 * @package ZionBuilder
 */
class Upgrades {
	/**
	 * Updates the editor status meta key. This resolves compatibility with legacy ZionBuilder pages
	 *
	 * @return void
	 */
	public static function upgrade_v_1_0_1_update_editor_status_meta() {
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
			"
		);
	}

	/**
	 * Updates the editor status meta key. This resolves compatibility with legacy ZionBuilder pages
	 *
	 * @return void
	 */
	public static function upgrade_v_1_0_1_update_editor_elements_meta() {
		$saved_settings = Settings::get_all_values();
		$new_values     = [];

		if ( isset( $saved_settings['local_gradients'] ) && is_array( $saved_settings['global_gradients'] ) ) {
			foreach ( $saved_settings['local_gradients'] as $key => $value ) {
				$preset_name  = sprintf( 'gradientPreset%s', $key );
				$new_values[] = [
					'id'     => $preset_name,
					'name'   => $preset_name,
					'config' => $value,
				];
			}
		}

		if ( ! empty( $new_values ) ) {
			$saved_settings['local_gradients'] = $new_values;
			Settings::save_settings( $saved_settings );
		}
	}

	/**
	 * Updates local gradients
	 *
	 * @return void
	 */
	public static function upgrade_v_1_0_1_update_local_gradients() {
	}
}
