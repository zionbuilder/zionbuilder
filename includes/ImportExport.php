<?php

namespace ZionBuilder;

use ZionBuilder\Templates;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}


/**
 * Class ImportExport
 * This class provides methods for import and export elements or templates
 *
 * @package ZionBuilder
 */
class ImportExport {

	/**
	 * Holds the uploads placeholder used for replacing the uploads urls
	 *
	 * @var string
	 */
	private $url_placeholder = 'ZION_URL_PLACEHOLDER';

	/**
	 * Holds the allowed file types on export
	 *
	 * @var array<string>
	 */
	private $allowed_file_types = [ 'jpg', 'png', 'gif', 'svg', 'jpeg', 'txt', 'mp4', 'm4v', 'mov', 'wmv', 'avi', 'mpg', 'ogv', '3gp', '3g2' ];

	/**
	 * Holds a reference for the uploaded images
	 *
	 * @var array<string>
	 */
	private $uploaded_media = [];

	/**
	 * Holds a reference to the export file name containing template data
	 */
	const TEMPLATE_CONFIG_FILENAME = 'template_config.txt';

	/**
	 * Holds the reference to the instance of the ZipArchive class
	 *
	 * @var null|\ZipArchive
	 */
	public $zip = null;


	/**
	 * Holds a reference to WP uploads directory
	 *
	 * @var array
	 */
	public $upload_dir = null;

	/**
	 * Holds a reference to WP uploads directory url
	 *
	 * @var string
	 */
	public $upload_dir_url = null;

	/**
	 * Holds a reference to WP uploads directory basedir
	 *
	 * @var string
	 */
	public $upload_dir_path = null;

	/**
	 * Holds a reference to WP uploads directory url without www subdomain
	 *
	 * @var string
	 */
	public $upload_dir_url_without_www = null;

	/**
	 * Holds a reference to the temporary file location for the extracted archive
	 *
	 * @var string
	 */
	private $temp_location = null;

	/**
	 * Main class constructor
	 */
	public function __construct() {
		$this->upload_dir                 = wp_upload_dir();
		$this->upload_dir_url             = $this->upload_dir['baseurl'];
		$this->upload_dir_path            = $this->upload_dir['basedir'];
		$this->upload_dir_url_without_www = str_replace( 'www . ', '', $this->upload_dir_url );
	}

	/**
	 * Handles the template export
	 * Receives the template_data, custom_css and custom js, template name
	 *
	 * @param mixed $template_name
	 * @param mixed $template_config
	 *
	 * @return \WP_Error|string String on success
	 */
	public function export_template( $template_name, $template_config ) {

		// Don't do anything if the template data is empty or invalid
		if ( empty( $template_name ) ) {
			return new \WP_Error( 'Invalid template data', __( 'The template data is empty or invalid. Export aborted.', 'zionbuilder' ) );
		}

		// if the zip was created return the zip link
		return $this->create_zip( $template_name, $template_config );
	}

	public function get_export_directory_config( $template_name ) {
		$file_name  = sanitize_file_name( $template_name );
		$temp_paths = FileSystem::get_temp_upload_dir( $file_name );

		$temp_paths['file_path'] = trailingslashit( $temp_paths['basedir'] ) . $template_name . '.zip';

		return $temp_paths;
	}


	/**
	 * This function will create the template zip and insert its contents
	 *
	 * @param array $data_config   [
	 *                             string template name
	 *                             object template data
	 *                             string custom_css
	 *                             string custom_js
	 *                             ]
	 * @param mixed $template_name
	 *
	 * @return \WP_Error|string String on success
	 */
	public function create_zip( $template_name, $data_config ) {
		// check if zipArchive class exists
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new \WP_Error( 'Export failed', esc_html__( 'ZipArchive class is not installed on your server.', 'zionbuilder' ) );
		}

		$this->zip = new \ZipArchive();

		// get export path
		$export_dir_config = $this->get_export_directory_config( $template_name );
		$export_path       = $export_dir_config['file_path'];

		// create the zip
		$open_zip = $this->zip->open( $export_path, \ZIPARCHIVE::CREATE | \ZipArchive::OVERWRITE );

		// throw an error if the export path is invalid
		if ( true !== $open_zip ) {
			return new \WP_Error(
				'Export failed',
				/* translators: %s: The export path for templates */
				sprintf( esc_html__( 'Could not create the export file in %s', 'zionbuilder' ), $export_path )
			);
		}

		// change default images url and add template images inside the zip
		if ( ! empty( $data_config ) ) {
			$this->add_template_images( $data_config, 'export' );
		}

		// add the template data in zip in json format
		$this->zip->addFromString( self::TEMPLATE_CONFIG_FILENAME, wp_json_encode( $data_config ) );

		// close the zip
		$this->zip->close();
		return esc_html__( 'Archive created successfully.', 'zionbuilder' );
	}

	/**
	 * This function will search for each element values inside the data config and return updated $data_config with the replaced urls
	 *
	 * @param mixed  $data_config
	 * @param string $method      That is used
	 *
	 * @return array
	 */
	public function add_template_images( &$data_config, $method ) {
		if ( empty( $data_config ) ) {
			return $data_config;
		}

		// search for all items values inside the array
		if ( ! is_array( $data_config ) ) {
			return $this->search_images_url( $data_config, $method );
		}

		foreach ( $data_config as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			if ( is_array( $value ) ) {
				$data_config[$key] = $this->add_template_images( $value, $method );
			} else {
				// check if it's a valid image url and replace it with placeholder
				$data_config[$key] = $this->search_images_url( $value, $method );
			}
		}

		return $data_config;
	}

	/**
	 * This function will search for images url inside the data config
	 *
	 * @param string|array $value
	 * @param mixed        $method
	 *
	 * @return string|array
	 */
	public function search_images_url( $value, $method ) {
		// Only process strings
		if ( ! is_string( $value ) ) {
			return $value;
		}

		$callbackfunction = ( 'export' === $method ) ? 'change_export_images_url' : 'change_import_images_url';
		$file_types       = implode( '|', $this->allowed_file_types );
		$pattern          = ( 'export' === $method ) ? "#https?://[^/\s]+/\S+\.($file_types)#" : "#{$this->url_placeholder}\/\S+\.($file_types)#";

		$value = preg_replace_callback(
			$pattern,
			[
				$this,
				$callbackfunction,
			],
			$value
		);

		return $value;
	}

	/**
	 * This function will add the media files inside the zip and replace the old urls with placeholder
	 *
	 * @param array $file
	 *
	 * @return string the modified url
	 */
	public function change_export_images_url( $file = [] ) {
		// check if the file exists
		if ( false !== strpos( $file[0], $this->upload_dir_url ) || false !== strpos( $file[0], $this->upload_dir_url_without_www ) ) {
			$path = str_replace( [ $this->upload_dir_url, $this->upload_dir_url_without_www ], '', $file[0] );
			$file = $this->upload_dir_path . $path;

			// upload media
			if ( is_file( $file ) ) {
				$this->zip->addFile( $file, 'images' . $path );
			}

			// will return the modified path
			return $this->url_placeholder . $path;
		}
		return $file[0];
	}

	/**
	 * This function will upload the media files from the zip and replace the placeholder urls
	 *
	 * @param array $file
	 *
	 * @return string the modified url
	 */
	public function change_import_images_url( $file = [] ) {

		// get the paths from uploaded folder
		$path = str_replace( $this->url_placeholder, '', $file[0] );

		// Upload media file
		$this->upload_media( $path );

		return $this->upload_dir_url . $path;
	}

	/**
	 * This function will upload media files from a specific path
	 *
	 * @param string $path
	 *
	 * @return null|\WP_Error null on success
	 */
	public function upload_media( $path ) {

		// check if the image was already uploaded
		if ( in_array( $path, $this->uploaded_media, true ) ) {
			return null;
		}

		$file_name   = basename( $path );
		$upload_path = $this->upload_dir_path . str_replace( $file_name, '', $path );
		$save_path   = $this->upload_dir_path . $path;
		$temp_path   = $this->temp_location . '/images' . $path;

		// create file path if it doesn't exist
		if ( ! file_exists( $upload_path ) ) {
			wp_mkdir_p( $upload_path );
		}

		// check if the images already exists
		if ( is_file( $save_path ) ) {
			return null;
		}

		// copy the new images to the file path
		copy( $temp_path, $save_path );

		// Check the type of file.
		$filetype = wp_check_filetype( basename( $save_path ), null );

		// Prepare an array of post data for the attachment.
		$attachment = [
			'guid'           => $this->upload_dir_url . '/' . basename( $save_path ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $save_path ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		];

		// will return a wp_error on failure
		$attachment_id = wp_insert_attachment( $attachment, $save_path, $parent = 0, $wp_error = true );

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $attachment_id, $save_path );

		wp_update_attachment_metadata( $attachment_id, $attach_data );
		$this->uploaded_media[] = $path;
		return null;
	}

	/**
	 * Handles the template import
	 *
	 * @param mixed $file
	 *
	 * @return int|\WP_Error
	 */
	public function import_template( $file ) {
		FileSystem::get_file_system();

		// check if is a valid import resource
		if ( empty( $file ) ) {
			return new \WP_Error( 'Import failed', __( 'Empty import resource', 'zionbuilder' ) );
		}

		// check if is a valid file
		switch ( $file['file']['error'] ) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				return new \WP_Error( 'no_file_sent', __( 'No file was uploaded', 'zionbuilder' ) );
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return new \WP_Error( 'maximum_size_exceeded', __( 'The uploaded file exceeds the MAX_FILE_SIZE', 'zionbuilder' ) );
			default:
				break;
		}

		$content_data = $this->insert_template_package( $file['file']['tmp_name'] );

		if ( is_wp_error( $content_data ) ) {
			return $content_data;
		}

		// create a new template and update post meta
		$template_name = ! empty( $content_data['template_name'] ) ? $content_data['template_name'] : esc_html__( 'My Template', 'zionbuilder' );
		$template_id   = Templates::create_template( $template_name, $content_data );

		// set the metafields for the newly created template
		if ( ! $template_id ) {
			return new \WP_Error( 'Import failed', __( 'The newly template was not created.', 'zionbuilder' ) );
		}

		return $template_id;
	}

	public function insert_template_package( $file_path ) {
		// create the temp folder if it doesn't exist
		$temp_location = $this->upload_dir_path . '/zionbuilder/temp';
		$file_info     = pathinfo( $file_path );
		$newfilename   = wp_unique_filename( $temp_location, $file_info['filename'] );
		$temp_location = sprintf( '%s/%s', $temp_location, $newfilename );

		$this->temp_location = $temp_location;

		if ( ! file_exists( $temp_location ) ) {
			FileSystem::get_file_system()->mkdir( $temp_location, 0777, true );
		}

		// extract the zip to a temp location
		$unzip = unzip_file( $file_path, $temp_location );

		// throw error if the zip files cannot be extracted
		if ( is_wp_error( $unzip ) ) {
			// remove the temp directory
			FileSystem::get_file_system()->delete( $temp_location, true );
			// send a wp error
			return $unzip;
		}

		// reads the template data file and check if it has content
		$temp_location = trailingslashit( $temp_location );
		$content       = ( FileSystem::get_file_system()->exists( $temp_location . self::TEMPLATE_CONFIG_FILENAME ) ) ? FileSystem::get_file_system()->get_contents( $temp_location . self::TEMPLATE_CONFIG_FILENAME ) : false;

		// throw error if the zip configuration file is missing
		if ( false === $content ) {
			// remove the temp directory
			FileSystem::get_file_system()->delete( $temp_location, true );
			// send a wp error
			return new \WP_Error( 'Import failed', __( 'No configuration file found inside uploaded zip.', 'zionbuilder' ) );
		}

		// check for template data
		$content_data = json_decode( $content, true );

		if ( empty( $content_data ) ) {
			// remove the temp directory
			FileSystem::get_file_system()->delete( $temp_location, true );
			// send error
			return new \WP_Error( 'Import failed', __( 'The template configuration file is empty. There are no elements found.', 'zionbuilder' ) );
		}

		// replace dummy url with the new site uploads directory & uploads all assets from zip file
		$this->add_template_images( $content_data, 'import' );

		// Cleanup temp
		FileSystem::get_file_system()->delete( $temp_location, true );

		return $content_data;
	}

	/**
	 * This function will provide the download functionality for the export archive and exit the process
	 *
	 * @param mixed $template_name
	 *
	 * @return \WP_Error
	 */
	public function download_template( $template_name ) {
		$export_dir_config = $this->get_export_directory_config( $template_name );
		$export_path       = $export_dir_config['file_path'];

		if ( empty( $export_path ) || ! FileSystem::get_file_system()->is_file( $export_path ) ) {
			return new \WP_Error( 'download_failed', 'Invalid file path!' );
		}

		$archive_file_name = basename( $export_path );

		header( 'Content-type: application/zip' );
		header( "Content-Disposition: attachment; filename=$archive_file_name" );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		echo FileSystem::get_file_system()->get_contents( $export_path ); // phpcs:ignore WordPress.Security.EscapeOutput
		FileSystem::get_file_system()->delete( $export_dir_config['basedir'], true );
		exit();
	}
}
