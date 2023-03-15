<?php

namespace ZionBuilder\Elements\PricingBox;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class PricingBox extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'pricing_box';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name\
	 */
	public function get_name() {
		return __( 'Pricing Box', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'pricing', 'box', 'prices', 'pricelist', 'packages', 'lists', 'offers', 'special', 'rates', 'comparison', 'cost', 'table', 'pricing table', 'cards', 'plans', 'billing', 'saas' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-pricing-table';
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
			'plan_title',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Plan title', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Basic', 'zionbuilder' ),
				'default'     => esc_html__( 'Basic', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'plan_description',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Plan description', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Pricing box description', 'zionbuilder' ),
				'default'     => esc_html__( 'Price box description', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'heading_color',
			[
				'type'      => 'colorpicker',
				'width'     => 50,
				'title'     => __( 'Heading color', 'zionbuilder' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-pricingBox-heading',
						'value'    => 'color: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .zb-el-pricingBox-title',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'heading_background_color',
			[
				'type'      => 'colorpicker',
				'width'     => 50,
				'title'     => __( 'Heading background', 'zionbuilder' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-pricingBox-heading',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'price',
			[
				'type'        => 'text',
				'width'       => 50,
				'title'       => esc_html__( 'Price', 'zionbuilder' ),
				'description' => esc_html__( 'Choose price', 'zionbuilder' ),
				'default'     => esc_html__( '$999', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'period',
			[
				'type'        => 'text',
				'width'       => 50,
				'title'       => esc_html__( 'Period', 'zionbuilder' ),
				'placeholder' => esc_html__( '/month', 'zionbuilder' ),
				'default'     => esc_html__( '/month', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'plan_details',
			[
				'type'        => 'editor',
				'title'       => esc_html__( 'Features', 'zionbuilder' ),
				'description' => __( 'Please add each feature on a new line', 'zionbuilder' ),
				'default'     => __( '<p>20+ Proactively</p><p>facilitate 150+ Alternative</p><p>Vectors quality</p><p>Strategic themes</p><p>Brand</p><p>Intellectual capital</p><p>Proactive applications</p>', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'button_text',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Button text', 'zionbuilder' ),
				'description' => esc_html__( 'Set the desired text for the button.', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Button text', 'zionbuilder' ),
				'default'     => esc_html__( 'Select this plan', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'button_link',
			[
				'type'        => 'link',
				'description' => esc_html__( 'Add a link for the Button', 'zionbuilder' ),
				'title'       => esc_html__( 'Button link', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'plan_featured',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Plan featured?', 'zionbuilder' ),
				'layout'      => 'inline',
				'description' => __( "Select 'YES' if you want this Plan to have a featured badge", 'zionbuilder' ),
				'default'     => 'featured',
				'columns'     => 2,
				'options'     => [
					[
						'name' => 'Yes',
						'id'   => 'featured',
					],
					[
						'name' => 'No',
						'id'   => 'not_featured',
					],
				],
			]
		);

		$options->add_option(
			'borders_color',
			[
				'type'      => 'colorpicker',
				'title'     => __( 'Borders color', 'zionbuilder' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-pricingBox-content',
						'value'    => 'border-color: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .zb-el-pricingBox-heading',
						'value'    => 'border-color: {{VALUE}}',
					],
					[
						'selector' => '{{ELEMENT}} .zb-el-pricingBox-plan-price',
						'value'    => 'border-color: {{VALUE}}',
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
			'featured_label_styles',
			[
				'title'    => esc_html__( 'Featured Label styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-pricingBox-featured',
			]
		);

		$this->register_style_options_element(
			'title_styles',
			[
				'title'    => esc_html__( 'Title styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-pricingBox-title',
			]
		);

		$this->register_style_options_element(
			'price_styles',
			[
				'title'    => esc_html__( 'Price styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-pricingBox-price-price',
			]
		);

		$this->register_style_options_element(
			'features_styles',
			[
				'title'    => esc_html__( 'Features styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-pricingBox-plan-features',
			]
		);

		$this->register_style_options_element(
			'button_styles',
			[
				'title'      => esc_html__( 'Button styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-pricingBox-action',
				'render_tag' => 'link',
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/PricingBox/editor', 'js' ) );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/PricingBox/frontend.css' ) );
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Button/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$plan_title       = $options->get_value( 'plan_title' );
		$plan_description = $options->get_value( 'plan_description' );
		$heading_colors   = $options->get_value( 'heading_colors' );
		$price            = $options->get_value( 'price' );
		$period           = $options->get_value( 'period' );
		$plan_details     = $options->get_value( 'plan_details' );
		$button_text      = $options->get_value( 'button_text' );
		$button_link      = $options->get_value( 'button_link' );
		$plan_featured    = $options->get_value( 'plan_featured' );

		$compiled_price = explode( '.', $price );

		// Custom css classes

		if ( $plan_featured === 'featured' ) {?>
			<span <?php echo $this->render_attributes->get_attributes_as_string( 'featured_label_styles', [ 'class' => 'zb-el-pricingBox-featured' ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?> ><?php echo wp_kses_post( $plan_featured ); ?></span>
		<?php } ?>

			<div class="zb-el-pricingBox-content">
				<div class="zb-el-pricingBox-heading">
				<?php if ( ! empty( $plan_title ) ) : ?>
					<h3  <?php echo $this->render_attributes->get_attributes_as_string( 'title_styles', [ 'class' => 'zb-el-pricingBox-title' ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php echo wp_kses_post( $plan_title ); ?>
					</h3>
				<?php endif; ?>
				<?php if ( ! empty( $plan_description ) ) : ?>
					<p class="zb-el-pricingBox-description">
						<?php echo wp_kses_post( $plan_description ); ?>
					</p>
				<?php endif; ?>
				</div>
				<?php if ( ! empty( $compiled_price[0] ) ) : ?>
				<div class="zb-el-pricingBox-plan-price">
					<span class="zb-el-pricingBox-price">
						<span  <?php echo $this->render_attributes->get_attributes_as_string( 'price_styles', [ 'class' => 'zb-el-pricingBox-price-price' ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
							<?php echo wp_kses_post( $compiled_price[0] ); ?>
							<?php if ( isset( $compiled_price[1] ) ) : ?>
								<span class="zb-el-pricingBox-price-dot">.</span>
							<?php endif; ?>
						</span>
						<?php if ( isset( $compiled_price[1] ) ) : ?>
							<span class="zb-el-pricingBox-price-float"><?php echo wp_kses_post( $compiled_price[1] ); ?></span>
						<?php endif; ?>

					</span>
					<?php if ( ! empty( $period ) ) : ?>
					<span class="zb-el-pricingBox-period">
						<?php echo wp_kses_post( $period ); ?>
					</span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $plan_details ) ) : ?>
					<div  <?php echo $this->render_attributes->get_attributes_as_string( 'features_styles', [ 'class' => 'zb-el-pricingBox-plan-features' ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php echo wp_kses_post( $plan_details ); ?>
					</div>
				<?php endif; ?>
				<?php
				if ( ! empty( $button_text ) ) {
					$combined_button_attr = $this->render_attributes->get_combined_attributes( 'button_styles', [ 'class' => 'zb-el-pricingBox-action zb-el-button' ] );

					$html_tag = 'div';

					if ( ! empty( $button_link['link'] ) ) {
						$this->attach_link_attributes( 'link', $button_link );
						$html_tag = 'a';
					}

					$this->render_tag(
						$html_tag,
						'link',
						$button_text,
						$combined_button_attr
					);
				}
				?>
			</div>

		<?php
	}
}
