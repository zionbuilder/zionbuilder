<?php

namespace ZionBuilder\Elements\Testimonial;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Testimonial
 *
 * @package ZionBuilder\Elements
 */
class Testimonial extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'testimonial';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Testimonial', 'zionbuilder' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'content';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'testimonial', 'media', 'feedback', 'partners', 'review', 'testament', 'recommendation' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-testimonial';
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'content',
			[
				'type'    => 'editor',
				'title'   => esc_html__( 'Testimonial content', 'zionbuilder' ),
				'default' => __( 'Edit the element options to change it', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'name',
			[
				'type'        => 'text',
				'title'       => __( 'User name', 'zionbuilder' ),
				'description' => __( 'Set the desired user name.', 'zionbuilder' ),
				'placeholder' => __( 'User name', 'zionbuilder' ),
				'default'     => esc_html__( 'John Dow', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'description',
			[
				'type'        => 'text',
				'title'       => __( 'Client description', 'zionbuilder' ),
				'description' => __( 'Set the client description.', 'zionbuilder' ),
				'placeholder' => __( 'description', 'zionbuilder' ),
				'default'     => __( "I'm Awesome", 'zionbuilder' ),
			]
		);

		$options->add_option(
			'image',
			[
				'type'        => 'image',
				'description' => 'Choose the desired user photo.',
				'title'       => esc_html__( 'User Photo', 'zionbuilder' ),
				'show_size'   => false,
				'default'     => $this->get_url( 'empty-profile-photo.svg' ),
			]
		);

		$options->add_option(
			'position',
			[
				'type'             => 'select',
				'default'          => 'left',
				'title'            => esc_html__( 'Image position', 'zionbuilder' ),
				'description'      => esc_html__( 'Set where the image should be', 'zionbuilder' ),
				'options'          => [
					[
						'name' => esc_html__( 'Image on top', 'zionbuilder' ),
						'id'   => 'top',
					],
					[
						'name' => esc_html__( 'Image to the left', 'zionbuilder' ),
						'id'   => 'left',
					],
					[
						'name' => esc_html__( 'Image to the right', 'zionbuilder' ),
						'id'   => 'right',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => '-position--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'alignment',
			[
				'type'             => 'select',
				'default'          => 'left',
				'title'            => esc_html__( 'Content alignment', 'zionbuilder' ),
				'description'      => esc_html__( 'Set how elements to be aligned', 'zionbuilder' ),
				'options'          => [
					[
						'name' => esc_html__( 'Left', 'zionbuilder' ),
						'id'   => 'left',
					],
					[
						'name' => esc_html__( 'Center', 'zionbuilder' ),
						'id'   => 'center',
					],
					[
						'name' => esc_html__( 'Right', 'zionbuilder' ),
						'id'   => 'right',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => '-align--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'stars',
			[
				'type'    => 'select',
				'title'   => __( 'Stars', 'zionbuilder' ),
				'default' => 'no_stars',
				'options' => [
					[
						'name' => __( 'Do not show stars', 'zionbuilder' ),
						'id'   => 'no_stars',
					],
					[
						'name' => __( '1 star', 'zionbuilder' ),
						'id'   => 1,
					],
					[
						'name' => __( '2 stars', 'zionbuilder' ),
						'id'   => 2,
					],
					[
						'name' => __( '3 stars', 'zionbuilder' ),
						'id'   => 3,
					],
					[
						'name' => __( '4 stars', 'zionbuilder' ),
						'id'   => 4,
					],
					[
						'name' => __( '5 stars', 'zionbuilder' ),
						'id'   => 5,
					],
				],
			]
		);
	}

	/**
	 * Get style elements
	 *
	 * Returns a list of elements/tags that for which you
	 * want to show style options
	 *
	 * @return void
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'inner_content_styles_misc',
			[
				'title'    => esc_html__( 'Testimonial styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-testimonial-content',
			]
		);
		$this->register_style_options_element(
			'inner_content_styles_user',
			[
				'title'    => esc_html__( 'Name styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-testimonial__userInfo-name',
			]
		);
		$this->register_style_options_element(
			'inner_content_styles_description',
			[
				'title'    => esc_html__( 'Description styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-testimonial__userInfo-description',
			]
		);
		$this->register_style_options_element(
			'inner_content_styles_image',
			[
				'title'    => esc_html__( 'Image styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-testimonial__userImage',
			]
		);
		$this->register_style_options_element(
			'inner_content_styles_stars',
			[
				'title'    => esc_html__( 'Stars styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-testimonial__stars',
			]
		);
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Testimonial/editor', 'js' ) );
	}

	/**
	 * Enqueue element styles for both frontend and editor
	 *
	 * If you want to use the ZionBuilder cache system you must use
	 * the enqueue_editor_style(), enqueue_element_style() functions
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		// Using helper methods will go through caching policy
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Testimonial/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$content     = $options->get_value( 'content' );
		$name        = $options->get_value( 'name' );
		$description = $options->get_value( 'description' );
		$image       = $options->get_value( 'image' );
		$position    = $options->get_value( 'position' );
		$stars       = $options->get_value( 'stars' );

		$stars_full = [
			'family'  => 'Font Awesome 5 Free Solid',
			'name'    => 'star',
			'unicode' => 'uf005',
		];

		$stars_empty = [
			'family'  => 'Font Awesome 5 Free Regular',
			'name'    => 'star',
			'unicode' => 'uf005',
		];

		if ( $image && ( $position === 'top' ) ) : ?>
			<img
				src="<?php echo esc_attr( $image ); ?>"
					<?php echo $this->render_attributes->get_attributes_as_string( 'inner_content_styles_image', [ 'class' => 'zb-el-testimonial__userImage' ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

			/>
		<?php endif; ?>
		<?php if ( ! empty( $content ) ) : ?>
			<div <?php echo $this->render_attributes->get_attributes_as_string( 'inner_content_styles_misc', [ 'class' => 'zb-el-testimonial-content' ] );// phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php echo wp_kses_post( $content ); ?>
			</div>
		<?php endif; ?>

		<div class="zb-el-testimonial__user">

			<?php if ( $image && ( $position !== 'top' ) ) : ?>
				<img
					src="<?php echo esc_attr( $image ); ?>"
					<?php echo $this->render_attributes->get_attributes_as_string( 'inner_content_styles_image', [ 'class' => 'zb-el-testimonial__userImage' ] );// phpcs:ignore WordPress.Security.EscapeOutput ?>
				/>
			<?php endif; ?>

			<div class="zb-el-testimonial__userInfo">

				<?php if ( ! empty( $name ) ) : ?>
					<div <?php echo $this->render_attributes->get_attributes_as_string( 'inner_content_styles_user', [ 'class' => 'zb-el-testimonial__userInfo-name' ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php echo wp_kses_post( $name ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
					<div <?php echo $this->render_attributes->get_attributes_as_string( 'inner_content_styles_description', [ 'class' => 'zb-el-testimonial__userInfo-description' ] );// phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ( $stars !== 'no_stars' ) ) : ?>
					<div <?php echo $this->render_attributes->get_attributes_as_string( 'inner_content_styles_stars', [ 'class' => 'zb-el-testimonial__stars' ] );// phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php
						for ( $x = 1; $x <= $stars; $x ++ ) {
							$this->attach_icon_attributes( 'icon', $stars_full );
							$this->render_tag( 'span', 'icon', '', [ 'class' => 'zb-el-testimonial__stars--full' ] );
						}
						?>
						<?php
						for ( $x = 1; $x <= ( 5 - $stars ); $x ++ ) {
							$this->attach_icon_attributes( 'icon', $stars_empty );
							$this->render_tag( 'span', 'icon' );
						}
						?>
					</div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}
