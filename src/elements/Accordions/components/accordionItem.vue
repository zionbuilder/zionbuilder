<template>
	<div
		v-if="accordionApi"
		class="zb-el-accordions-accordionWrapper"
		:class="{ 'zb-el-accordions--active': activeByDefault }"
	>
		<slot name="start" />

		<component
			:is="titleTag"
			class="zb-el-accordions-accordionTitle"
			:class="accordionApi.getStyleClasses('inner_content_styles_title')"
			v-bind="accordionApi.getAttributesForTag('inner_content_styles_title')"
		>
			{{ options.title }}
			<span class="zb-el-accordions-accordionIcon" />
		</component>
		<div
			class="zb-el-accordions-accordionContent"
			:class="accordionApi.getStyleClasses('inner_content_styles_content')"
			v-bind="accordionApi.getAttributesForTag('inner_content_styles_content')"
		>
			<!-- eslint-disable-next-line vue/no-v-html -->
			<div class="zb-el-accordions-accordionContent__inner" v-html="renderedContent"></div>
		</div>
		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue';

const props = defineProps<{
	options: {
		title: string;
		content: string;
		active_by_default: boolean;
		title_tag: string;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const renderedContent = computed(() => {
	return props.options.content ? props.options.content : 'accordion content';
});
const activeByDefault = computed(() => {
	return props.options.active_by_default ? props.options.active_by_default : false;
});

const accordionApi = inject('accordionsApi', null);
const titleTag = computed(() => {
	const parentAccordionTitle = accordionApi ? accordionApi.options.value.title_tag : 'div';
	return props.options.title_tag || parentAccordionTitle || 'div';
});
</script>
