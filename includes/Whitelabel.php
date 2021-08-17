<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;
use ZionBuilder\Utils;


// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Whitelabel
 * @package ZionBuilder
 */
class Whitelabel {

	/**
	 * Holds a reference to the plugin name
	 *
	 * @var string
	 */
	public $plugin_title = 'Zion Builder';

	/**
	 * Holds a reference to the plugin slug. This is used for admin urls
	 *
	 * @var string
	 */
	public $plugin_slug = 'zionbuilder';

	/**
	 * Holds a reference to the plugin logo URL
	 *
	 * @var string
	 */
	public $plugin_logo = '';

	/**
	 * Holds a reference to the plugin Help URL
	 *
	 * @var string
	 */
	public $plugin_help_url = '';

	/**
	 * Holds a reference to the plugin loader URL
	 *
	 * @var string
	 */
	public $plugin_loader_logo = '';

	/**
	 * Whitelabel constructor.
	 */
	public function __construct() {
		$white_label_data         = apply_filters(
			'zionbuilder/whitelabel/data',
			[
				'plugin_title'       => 'Zion Builder',
				'plugin_slug'        => 'zionbuilder',
				'plugin_logo'        => Utils::get_logo_url(),
				'plugin_help_url'    => 'https://zionbuilder.io/help-center/',
				'plugin_loader_logo' => Utils::get_loader_url(),
			]
		);
		$this->plugin_title       = $white_label_data['plugin_title'];
		$this->plugin_logo        = $white_label_data['plugin_logo'];
		$this->plugin_help_url    = $white_label_data['plugin_help_url'];
		$this->plugin_loader_logo = $white_label_data['plugin_loader_logo'];
		$this->plugin_slug        = $white_label_data['plugin_slug'];
	}

	/**
	 * @return string
	 */
	public static function get_logo_url() {
		return Plugin::instance()->whitelabel->plugin_logo;
	}

	/**
	 * @return string
	 */
	public static function get_title() {
		return Plugin::instance()->whitelabel->plugin_title;
	}

	/**
	 * Returns the plugin id/slug
	 *
	 * @return string
	 */
	public static function get_id() {
		return Plugin::instance()->whitelabel->plugin_slug;
	}

	/**
	 * @return string
	 */
	public static function get_help_url() {
		return Plugin::instance()->whitelabel->plugin_help_url;
	}

	/**
	 * @return string
	 */
	public static function get_loader_url() {
		return Plugin::instance()->whitelabel->plugin_loader_logo;
	}
}
