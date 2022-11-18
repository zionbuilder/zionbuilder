<?php

namespace ZionBuilder\Upgrade;

use ZionBuilder\Assets;

use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Upgrader
 *
 * @since 1.0.1
 *
 * @package ZionBuilder
 */
class Upgrader {
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'check_for_updates' ), 5 );
		add_action( 'zionbuilder_run_update_callback', [ __CLASS__, 'run_update_callback' ] );
	}

	/**
	 * Will check for updates and apply them if necessary
	 *
	 * @return void
	 */
	public static function check_for_updates() {
		if ( self::requires_db_update() ) {
			self::update();
			update_option( Assets::REGENERATE_CACHE_FLAG, true, true );
		}
	}


	/**
	 * Runs the upgrade method with provided arguments
	 *
	 * @param callable $callback The arguments passed from self::get_update_config()
	 *
	 * @return void
	 */
	public static function run_update_callback( $callback ) {
		if ( is_callable( $callback ) ) {
			call_user_func_array( $callback, [] );
		}
	}

	/**
	 * Will check if we need to apply an update
	 *
	 * @return boolean
	 */
	public static function requires_db_update() {
		$old_version = self::get_db_version();
		$new_version = Plugin::instance()->get_version();
		return version_compare( $new_version, $old_version, '>' );
	}


	/**
	 * Returns the update configuration
	 *
	 * @return array<string, array<int, array{callback: string|array<mixed, mixed>, args?:array<mixed>}>> The version configuration and their callbacks
	 */
	public static function get_update_config() {
		$reflection            = new \ReflectionClass( 'ZionBuilder\Upgrade\Upgrades' );
		$upgrade_method_prefix = 'upgrade_v_';
		$config                = [];

		foreach ( $reflection->getMethods() as $method ) {
			$callback_name = $method->getName();

			if ( false === strpos( $callback_name, $upgrade_method_prefix ) ) {
				continue;
			}

			if ( ! preg_match( "/$upgrade_method_prefix(\d+_\d+_\d+)/", $callback_name, $matches ) ) {
				continue;
			}

			// Convert to valid version number
			$callback_version = (string) str_replace( '_', '.', $matches[1] );

			if ( ! isset( $config[$callback_version] ) ) {
				$config[$callback_version] = [];
			}

			$config[$callback_version][] = [
				'callback' => [ $method->class, $method->name ],
			];
		}

		// Add update DB version after all callbacks
		foreach ( array_keys( $config ) as $version ) {
			$config[$version][] = [
				'callback' => [ get_class(), 'update_db_version' ],
				'args'     => [ $version ],
			];
		}

		// Sort by version number
		ksort( $config );

		return $config;
	}

	/**
	 * Perform plugin DB updates
	 *
	 * @return void
	 */
	public static function update() {
		$current_db_version = self::get_db_version();
		$loop               = 0;
		$version_upodates   = [];

		// Check to see if an upgrade is already in progress
		$next_timestamp = \as_next_scheduled_action( 'zionbuilder_run_update_callback', null, 'zionbuilder-db-updates' );

		if ( $next_timestamp ) {
			return;
		}

		foreach ( self::get_update_config() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					\as_schedule_single_action(
						time() + $loop,
						'zionbuilder_run_update_callback',
						$update_callback,
						'zionbuilder-db-updates'
					);
					$version_upodates[] = $update_callback;
					$loop++;
				}
			}
		}

		if ( empty( $version_upodates ) ) {
			self::update_db_version();
		}
	}

	/**
	 * Will return the current builder version
	 *
	 * @return string
	 */
	public static function get_db_version() {
		$saved_version = get_option( 'zionbuilder_db_version' );
		$saved_version = ! empty( $saved_version ) ? $saved_version : '0.0.0';

		return $saved_version;
	}

	/**
	 * Will save the builder version to DB
	 *
	 * @param string $version The version string that will be added to DB
	 *
	 * @return boolean True if the option was successfully saved
	 */
	public static function update_db_version( $version = null ) {
		delete_option( 'zionbuilder_db_version' );
		return add_option( 'zionbuilder_db_version', is_null( $version ) ? Plugin::instance()->get_version() : $version );
	}
}
