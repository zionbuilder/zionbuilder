<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class WPMedia
 *
 * @package ZionBuilder
 */
class WPMedia {
	/**
	 * WPMedia constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_get-attachment', [ $this, 'get_attachment' ], -1 );
		add_action( 'delete_attachment', [ $this, 'on_delete_image' ] );
		add_filter( 'image_downsize', [ $this, 'on_image_downsize' ], 10, 3 );

		// Add src set for custom sizes
		add_filter( 'wp_calculate_image_srcset_meta', [ $this, 'on_wp_calculate_image_srcset_meta' ] );
	}


	/**
	 * Returns all registered image sizes for a given image config
	 *
	 * @param array<string, mixed> $image_config The image config for which you want to retrieve the image sizes
	 *
	 * @return \WP_Error|array<string, string> The image sizes list or WP_Error on failure
	 */
	public static function get_image_sizes( $image_config ) {
		if ( empty( $image_config['image'] ) ) {
			return new \WP_Error( __( 'image url missing', 'zionbuilder' ) );
		}

		// Get the image id
		$attachment_id = attachment_url_to_postid( $image_config['image'] );

		if ( empty( $attachment_id ) ) {
			return new \WP_Error( __( 'Could not retrieve the image id', 'zionbuilder' ) );
		}

		$image_sizes = [];

		// Check for custom size
		if ( ! empty( $image_config['image_size'] ) && $image_config['image_size'] === 'custom' && ! empty( $image_config['custom_size'] ) ) {
			$custom_size = $image_config['custom_size'];
			$width       = ! empty( $custom_size['width'] ) ? $custom_size['width'] : 0;
			$height      = ! empty( $custom_size['height'] ) ? $custom_size['height'] : 0;
			$size_name   = self::generate_size_name( $width, $height );

			// Add custom image size
			add_image_size( $size_name, $width, $height, true );
		}

		foreach ( get_intermediate_image_sizes() as $size ) {
			$image_details = wp_get_attachment_image_src( $attachment_id, $size );

			if ( ! $image_details ) {
				return new \WP_Error( __( 'Could not retrieve the image src for given image id', 'zionbuilder' ) );
			}

			$image_sizes[$size] = $image_details[0];
		}

		return $image_sizes;
	}


	/**
	 * Generates image size name based on width and height
	 *
	 * @param int $width  The image width
	 * @param int $height The image height
	 *
	 * @return string The image size name
	 */
	public static function generate_size_name( $width, $height ) {
		return sprintf( 'zion_custom_%sx%s', $width, $height );
	}


	/**
	 * Add resized image srcset value
	 *
	 * @param array<string, mixed> $image_meta
	 *
	 * @return array<string, mixed>
	 */
	public function on_wp_calculate_image_srcset_meta( $image_meta ) {
		if ( ! empty( $image_meta['image_meta']['zion_resized_images'] ) ) {
			foreach ( $image_meta['image_meta']['zion_resized_images'] as $custom_size ) {
				$size_name                       = self::generate_size_name( $custom_size['width'], $custom_size['height'] );
				$image_meta['sizes'][$size_name] = $custom_size;
			}
		}

		return $image_meta;
	}

	/**
	 * Hooks into image_downsize filter in order to check if the requested image size should be handled by this class
	 *
	 * @param bool|array<int, string> $out  Whether to short-circuit the image downsize
	 * @param int                     $id   The image id
	 * @param string                  $size The image size as string
	 *
	 * @return bool|array<int, string> The image downside list
	 */
	public function on_image_downsize( $out, $id, $size ) {
		// Check to see if the size is our own
		if ( is_string( $size ) && strpos( $size, 'zion_custom_' ) === 0 ) {
			preg_match( '#zion_custom_(\d*)x(\d*)#', $size, $matches );
			$width  = $matches[1];
			$height = $matches[2];
			$url    = $this->get_resized_image_url( $id, (int) $width, (int) $height );

			return [
				$url,
				$width,
				$height,
			];
		}

		return $out;
	}

	/**
	 * Get image
	 *
	 * Will return the image tag from provided image configuration
	 *
	 * @param string|array $image_config
	 * @param string|array $attributes   {
	 * src: string,
	 * class: string,
	 * alt: string,
	 * srcset: string,
	 * sizes: string
	 * }
	 *
	 * @return string The HTML image tag
	 */
	public static function get_imge( $image_config, $attributes = [] ) {
		// Don't proceed if we have not image data
		if ( ! empty( $image_config ) ) {
			if ( is_array( $image_config ) ) {
				if ( ! isset( $image_config['image'] ) ) {
					return '';
				}

				$image_url   = isset( $image_config['image_source'] ) ? $image_config['image_source'] : $image_config['image'];
				$size        = isset( $image_config['image_size'] ) ? $image_config['image_size'] : 'full';
				$custom_size = isset( $image_config['custom_size'] ) ? $image_config['custom_size'] : [];

				// Check for custom size
				if ( $size === 'custom' ) {
					$width  = ! empty( $custom_size['width'] ) ? $custom_size['width'] : 0;
					$height = ! empty( $custom_size['height'] ) ? $custom_size['height'] : 0;

					if ( $width || $height ) {
						$size = self::generate_size_name( $width, $height );
					}
				}

				$attachment_id = attachment_url_to_postid( $image_url );
			} else {
				$size          = 'full';
				$attachment_id = attachment_url_to_postid( $image_config );
			}

			if ( $attachment_id ) {
				$image_tag = wp_get_attachment_image( $attachment_id, $size, false, $attributes );

				if ( $image_tag ) {
					return $image_tag;
				}
			}
		}

		return self::get_default_image( $image_config, $attributes );
	}


	/**
	 * Get default image
	 *
	 * Try to get the image tag in case the get_image could not provide the actual image
	 *
	 * @param mixed $image_config The image config
	 * @param mixed $attributes
	 *
	 * @return string The default image with width and height parameters
	 */
	public static function get_default_image( $image_config, $attributes = [] ) {
		if ( ! $image_config ) {
			return '';
		}

		$attributes        = array_map( 'esc_attr', $attributes );
		$attributes_string = '';

		foreach ( $attributes as $name => $value ) {
			$attributes_string .= " $name=" . '"' . $value . '"';
		}

		if ( is_array( $image_config ) && isset( $image_config['image'] ) ) {
			$width  = isset( $image_config['width'] ) ? $image_config['width'] : '';
			$height = isset( $image_config['height'] ) ? $image_config['height'] : '';

			return sprintf( '<img src="%s" width="%s" height="%s" %s />', $image_config['image'], $width, $height, $attributes_string );
		} else {
			return sprintf( '<img src="%s" %s />', $image_config, $attributes_string );
		}
	}


	/**
	 * Retrieves the image url for a specific size
	 *
	 * @param int     $attachment_id The attachment id for which you want to retrieve the size
	 * @param int     $width         The desired image width
	 * @param int     $height        The desired image height
	 * @param boolean $crop          True if you want to crop the image. Defaults to true
	 *
	 * @return string|\WP_Error The image URL
	 */
	public static function get_resized_image_url( $attachment_id, $width = 0, $height = 0, $crop = true ) {
		// Check to see if the image already exists for the requested size
		$image_path        = get_attached_file( $attachment_id );
		$image_url         = wp_get_attachment_url( $attachment_id );
		$file_info         = pathinfo( $image_path );
		$file_extension    = $file_info['extension'];
		$file_name         = $file_info['filename'];
		$crop_suffix       = $crop ? '_c' : '';
		$image_suffix      = $width . 'x' . $height . 'x' . $crop_suffix . '_' . filesize( $image_path ) . '_' . filemtime( $image_path );
		$resized_file_name = $file_name . '-' . $image_suffix . '.' . $file_extension;

		$dir = pathinfo( $image_path, PATHINFO_DIRNAME );
		$url = pathinfo( $image_url, PATHINFO_DIRNAME );

		$resized_file_path = trailingslashit( trailingslashit( $dir ) ) . $resized_file_name;
		$resized_file_url  = trailingslashit( trailingslashit( $url ) ) . $resized_file_name;

		if ( ! file_exists( $resized_file_path ) ) {
			// Generate the file
			$resized_image = self::resize_image( $attachment_id, $width, $height, true, $resized_file_path );

			if ( is_wp_error( $resized_image ) ) {
				return $resized_image;
			}
		}

		return $resized_file_url;
	}

	/**
	 * Resize image
	 *
	 * Will resize an image based on width and height values
	 *
	 * @param mixed $attachment_id
	 * @param mixed $width
	 * @param mixed $height
	 * @param mixed $crop
	 * @param mixed $resized_file_name
	 *
	 * @return \WP_Error|string The resized image data or WP_Error in case of fail
	 */
	public static function resize_image( $attachment_id, $width, $height, $crop = true, $resized_file_name = false ) {

		// Get image file from url
		$image_file = get_attached_file( $attachment_id );
		$editor     = wp_get_image_editor( $image_file );

		if ( is_wp_error( $editor ) ) {
			return $editor;
		}

		if ( $crop === true ) {
			$size = is_user_logged_in() ? getimagesize( $image_file ) : getimagesize( $image_file );
			// Set original width and height
			list( $orig_width, $orig_height ) = $size;

			// Generate width or height if not provided
			if ( $width && ! $height ) {
				$height = floor( $orig_height * ( $width / $orig_width ) );
			} else {
				if ( $height && ! $width ) {
					$width = floor( $orig_width * ( $height / $orig_height ) );
				} else {
					if ( $width === 0 && $height === 0 ) {
						$image_url = wp_get_attachment_url( $attachment_id );
						if ( false === $image_url ) {
							return new \WP_Error( 'image_id_not_valid', esc_html__( 'The image could not be retrieved', 'zionbuilder' ) );
						}

						return $image_url;
					}
				}
			}

			$src_x = 0;
			$src_y = 0;
			$src_w = $orig_width;
			$src_h = $orig_height;

			$cmp_x = $orig_width / $width;
			$cmp_y = $orig_height / $height;

			// Calculate x or y coordinate and width or height of source
			if ( $cmp_x > $cmp_y ) {
				$src_w = round( $orig_width / $cmp_x * $cmp_y );
				$src_x = round( ( $orig_width - ( $orig_width / $cmp_x * $cmp_y ) ) / 2 );
			} else {
				if ( $cmp_y > $cmp_x ) {
					$src_h = round( $orig_height / $cmp_y * $cmp_x );
					$src_y = round( ( $orig_height - ( $orig_height / $cmp_y * $cmp_x ) ) / 2 );
				}
			}

			// Crop image
			$resized = $editor->crop( $src_x, $src_y, $src_w, $src_h, $width, $height );
		} else {
			$resized = $editor->resize( $width, $height );
		}

		if ( is_wp_error( $resized ) ) {
			return $resized;
		}

		$saved_image = $editor->save( $resized_file_name );

		if ( is_array( $saved_image ) ) {
			$metadata = wp_get_attachment_metadata( $attachment_id );
			if ( isset( $metadata['image_meta'] ) ) {
				$metadata['image_meta']['zion_resized_images'][] = $saved_image;
				wp_update_attachment_metadata( $attachment_id, $metadata );
			}
		}

		return $saved_image;
	}

	/**
	 * Action that runs when an image is deleted
	 * It will delete all registered image sizes
	 *
	 * @param integer $attachment_id
	 *
	 * @return void
	 */
	public function on_delete_image( $attachment_id ) {
		$metadata          = wp_get_attachment_metadata( $attachment_id );
		$parent_image_file = get_attached_file( $attachment_id );

		// Return if no metadata is found
		if ( ! $metadata ) {
			return;
		}

		// Return if we don't have the proper metadata
		if ( ! is_array( $metadata['image_meta']['zion_resized_images'] ) ) {
			return;
		}

		// Delete the resized image (if it has not yet been deleted)
		foreach ( $metadata['image_meta']['zion_resized_images'] as $size_meta ) {
			$file_to_delete = str_replace( basename( $parent_image_file ), $size_meta['file'], $parent_image_file );
			wp_delete_file( $file_to_delete );
		}
	}

	/**
	 * Will retrieve the image for use in ajax calls
	 *
	 * @return void
	 */
	public function get_attachment() {
		if ( isset( $_POST['is_media_image'] ) && $_POST['is_media_image'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( ! current_user_can( 'upload_files' ) ) {
				wp_send_json_error();
			}

			// Check to see if we received an image url or id
			$attachment_url = wp_strip_all_tags(
				stripslashes(
					filter_var( $_POST['image_url'], FILTER_VALIDATE_URL ) // phpcs:ignore WordPress.Security.NonceVerification
				)
			);

			$attachment_id = attachment_url_to_postid( $attachment_url );

			if ( ! $attachment_id ) {
				wp_send_json_error();
			}

			$attachment = wp_prepare_attachment_for_js( $attachment_id );

			if ( ! $attachment ) {
				wp_send_json_error();
			}

			wp_send_json_success( $attachment );
		}
	}
}
