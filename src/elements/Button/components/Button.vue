<template>
	<div>
		<slot name="start" />

		<component
			:is="getTag"
			v-bind="api.getAttributesForTag('button_styles', getButtonAttributes)"
			ref="button"
			class="zb-el-button"
			:class="[api.getStyleClasses('button_styles'), { 'zb-el-button--has-icon': options.icon }]"
		>
			<ElementIcon
				v-if="options.icon"
				class="zb-el-button__icon"
				v-bind="api.getAttributesForTag('icon_styles')"
				:icon-config="iconConfig"
				:class="api.getStyleClasses('icon_styles')"
			/>

			<span v-if="options.button_text" class="zb-el-button__text">
				{{ options.button_text }}
			</span>
		</component>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		button_text: string;
		icon: {
			icon: string;
			size: string;
			color: string;
		};
		link: {
			link: string;
			target: string;
			title: string;
		};
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const iconConfig = computed(() => {
	return props.options.icon;
});

const getTag = computed(() => {
	// Check if has link else span
	return props.options.link && props.options.link.link ? 'a' : 'div';
});

const getButtonAttributes = computed(() => {
	const attrs: Record<string, string> = {};
	// Link
	if (props.options.link && props.options.link.link) {
		attrs.href = props.options.link.link;
		attrs.target = props.options.link.target;
		attrs.title = props.options.link.title;
	}

	return attrs;
});
</script>
