<template>
	<div>
		<slot name="start" />

		<span
			v-if="options.plan_featured==='featured'"
			class="zb-el-pricingBox-featured"
			:class="api.getStyleClasses('featured_label_styles')"
		>{{$translate('featured')}}
		</span>
		<div class="zb-el-pricingBox-content">
			<div class="zb-el-pricingBox-heading">
				<h3
					class="zb-el-pricingBox-title"
					:class="api.getStyleClasses('title_styles')"
				>
					<RenderValue option="plan_title" />
				</h3>
				<p class="zb-el-pricingBox-description">
					<RenderValue option="plan_description" />
				</p>
			</div>
			<div class="zb-el-pricingBox-plan-price">
				<span class="zb-el-pricingBox-price">
					<span
						class="zb-el-pricingBox-price-price"
						:class="api.getStyleClasses('price_styles')"
					>
						{{pricingPrice || '$999'}}<span class="zb-el-pricingBox-price-dot">{{priceFloat ? '.' : ''}}</span>
					</span>
					<span class="zb-el-pricingBox-price-float">{{options.price && options.price.split('.').length > 1 ? priceFloat : null}}</span>
				</span>
				<span class="zb-el-pricingBox-period">
					<RenderValue option="period" />
				</span>
			</div>
			<div
				class="zb-el-pricingBox-plan-features"
				v-if="options.plan_details"
				v-html="options.plan_details"
				:class="api.getStyleClasses('features_styles')"
			>
			</div>
			<a
				v-if="options.button_link && options.button_link.link"
				:href="options.button_link.link"
				:title="options.button_link.title"
				:target="options.button_link.target"
				:class="api.getStyleClasses('button_styles')"
			>
				<BaseButton
					class="zb-el-pricingBox-action"
					type="primary"
				>
					<RenderValue option="button_text" />
				</BaseButton>
			</a>
			<BaseButton
				v-else
				class="zb-el-pricingBox-action"
				type="secondary"
				:class="api.getStyleClasses('button_styles')"
			>
				<RenderValue option="button_text" />
			</BaseButton>
		</div>

		<slot name="end" />
	</div>
</template>
<script>
export default {
	name: 'pricing_box',
	props: ['options', 'data', 'api'],
	computed: {
		pricingPrice () {
			return this.options.price ? this.options.price.split('.')[0] : null
		},
		priceFloat () {
			let floatValue = this.options.price ? this.options.price.split('.')[1] : null
			return floatValue
		}

	}
}
</script>
