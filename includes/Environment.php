<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Settings
 *
 * @package ZionBuilder
 *
 * @throws \LogicException If manifest file is not found.
 */
class Environment {
	const MANIFEST_FILENAME = 'manifest.json';

	private static $config = null;

	public static function get_config() {
		if ( null === self::$config ) {
			if ( ! file_exists( Utils::get_file_path( self::MANIFEST_FILENAME ) ) ) {
				throw new \LogicException( sprintf( 'Manifest %s does not exist.', Utils::get_file_path( self::MANIFEST_FILENAME ) ) );
			}

			self::$config = \json_decode( FileSystem::get_file_system()->get_contents( Utils::get_file_path( self::MANIFEST_FILENAME ) ), true );
		}

		return self::$config;
	}


	/**
	 * Returns a value from the manifest file
	 *
	 * @param string $id The id for the requested value
	 *
	 * @return mixed The value for the requested id
	 */
	public static function get_value( $id, $default = null ) {
		$config = self::get_config();

		if ( isset( $config[$id] ) ) {
			return $config[$id];
		}

		return $default;
	}

	/**
	 * Returns the debug state for the project
	 *
	 * In debug mode, scripts will be loaded from Webpack dev server
	 * and assets will be recompiled automatically
	 *
	 * @return boolean
	 */
	public static function is_debug() {
		return self::get_value( 'debug' );
	}
}
