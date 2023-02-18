<template>
	<div>
		<slot name="start" />

		<span
			v-if="options.plan_featured === 'featured'"
			class="zb-el-pricingBox-featured"
			:class="api.getStyleClasses('featured_label_styles')"
			v-bind="api.getAttributesForTag('featured_label_styles')"
			>{{ $translate('featured') }}
		</span>
		<div class="zb-el-pricingBox-content">
			<div class="zb-el-pricingBox-heading">
				<h3
					class="zb-el-pricingBox-title"
					:class="api.getStyleClasses('title_styles')"
					v-bind="api.getAttributesForTag('title_styles')"
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
						v-bind="api.getAttributesForTag('price_styles')"
					>
						{{ pricingPrice || '$999' }}<span class="zb-el-pricingBox-price-dot">{{ priceFloat ? '.' : '' }}</span>
					</span>
					<span class="zb-el-pricingBox-price-float">{{
						options.price && options.price.split('.').length > 1 ? priceFloat : null
					}}</span>
				</span>
				<span class="zb-el-pricingBox-period">
					<RenderValue option="period" />
				</span>
			</div>
			<div
				v-if="options.plan_details"
				class="zb-el-pricingBox-plan-features"
				:class="api.getStyleClasses('features_styles')"
				v-bind="api.getAttributesForTag('features_styles')"
				v-html="options.plan_details"
			></div>
			<a
				v-if="options.button_link && options.button_link.link"
				:href="options.button_link.link"
				:title="options.button_link.title"
				:target="options.button_link.target"
				v-bind="api.getAttributesForTag('button_styles')"
				class="zb-el-pricingBox-action zb-el-button"
				:class="api.getStyleClasses('button_styles')"
			>
				<RenderValue option="button_text" />
			</a>
			<div
				v-else
				class="zb-el-pricingBox-action zb-el-button"
				:class="api.getStyleClasses('button_styles')"
				v-bind="api.getAttributesForTag('button_styles')"
			>
				<RenderValue option="button_text" />
			</div>
		</div>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		plan_title: string;
		plan_description: string;
		plan_featured: string;
		price: string;
		period: string;
		plan_details: string;
		button_text: string;
		button_link: {
			link: string;
			title: string;
			target: string;
		};
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const pricingPrice = computed(() => {
	return props.options.price ? props.options.price.split('.')[0] : null;
});

const priceFloat = computed(() => {
	return props.options.price ? props.options.price.split('.')[1] : null;
});
</script>
