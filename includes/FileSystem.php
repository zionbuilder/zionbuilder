<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class FileSystem
 *
 * @package ZionBuilder
 */
class FileSystem {
	/**
	 * Holds a reference to the WP upload dir config
	 *
	 * @var array{
	 *             path: string,
	 *             url: string,
	 *             subdir: string,
	 *             basedir: string,
	 *             baseurl: string,
	 *             error: string|false
	 *             }
	 */
	private static $wp_upload_dir = null;

	/**
	 * This is a wrapper for wp_file_system
	 *
	 * @return mixed
	 */
	public static function get_file_system() {
		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	public static function unzip_archive( $from, $to, $allowed_extensions = [] ) {
		$file_system      = self::get_file_system();
		$temp_folder_name = trailingslashit( uniqid() );
		$temp_folder_dir  = self::get_temp_upload_dir( $temp_folder_name );
		$extracted        = unzip_file( $from, $temp_folder_dir['basedir'] );

		// Create the to folder if not exists
		self::create_folder( $to );

		if ( is_wp_error( $extracted ) ) {
			return new \WP_Error( 'extract_failed', __( 'The zip file could not be extracted!', 'zionbuilder' ) );
		} else {
			// We need to remove any unnecessary files and move the allowed file types one folder up
			$elements_files_obj = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $temp_folder_dir['basedir'], \RecursiveIteratorIterator::CHILD_FIRST ) );
			$elements_files_obj->setMaxDepth( 2 );
			foreach ( $elements_files_obj as $filename => $fileobject ) {
				if ( ! empty( $allowed_extensions ) && ! in_array( pathinfo( $fileobject->getFilename(), PATHINFO_EXTENSION ), $allowed_extensions, true ) ) {
					continue;
				}

				copy( $filename, $to . $fileobject->getFilename() );
			}

			$file_system->rmdir( $temp_folder_dir['basedir'], true );
		}

		return true;
	}

	public static function bulk_rename( $directory, $name ) {
		$directory  = trailingslashit( $directory );
		$extensions = [ 'eot', 'svg', 'ttf', 'woff' ];
		foreach ( glob( $directory . '*' ) as $file ) {
			$path_parts = pathinfo( $file );
			if ( in_array( $path_parts['extension'], $extensions, true ) ) {
				rename( $file, trailingslashit( $path_parts['dirname'] ) . $name . '.' . $path_parts['extension'] );
			}
		}
	}

	public static function find_file( $folder, $extension ) {
		$files = scandir( $folder );

		foreach ( $files as $file ) {
			if ( strpos( strtolower( $file ), $extension ) !== false && $file[0] !== '.' ) {
				return $file;
			}
		}

		return false;
	}

	public static function create_folder( $folder, $addindex = true ) {
		if ( is_dir( $folder ) && $addindex === false ) {
			return true;
		}

		$created = wp_mkdir_p( $folder );

		if ( $addindex === false ) {
			return $created;
		}

		// Add an index.php file
		$index_file = trailingslashit( $folder ) . 'index.php';
		if ( is_file( $index_file ) ) {
			return $created;
		}

		$fs = self::get_file_system();
		$fs->put_contents( $index_file, "<?php\r\necho 'Directory browsing is not allowed!';\r\n" );

		return $created;
	}

	public static function get_wp_upload_dir() {
		if ( null === self::$wp_upload_dir ) {
			self::$wp_upload_dir = wp_upload_dir();
		}

		return self::$wp_upload_dir;
	}

	public static function get_zionbuilder_upload_dir( $path = '' ) {
		$wp_uploads_dir     = self::get_wp_upload_dir();
		$basedir            = trailingslashit( $wp_uploads_dir['basedir'] );
		$baseurl            = trailingslashit( $wp_uploads_dir['baseurl'] );
		$zionbuilder_folder = trailingslashit( 'zionbuilder' );

		// Create the folder if it doesn't exists
		self::create_folder( $basedir . $zionbuilder_folder . $path );

		return [
			'basedir' => $basedir . $zionbuilder_folder . $path,
			'baseurl' => esc_url( $baseurl . $zionbuilder_folder . $path ),
		];
	}

	public static function get_temp_upload_dir( $path = '' ) {
		$temp_folder        = trailingslashit( 'temp' );
		$zionbuilder_folder = self::get_zionbuilder_upload_dir( $temp_folder . $path );

		return $zionbuilder_folder;
	}
}
