<template>
	<div class="zb-el-accordions-accordionWrapper" :class="{ 'zb-el-accordions--active': activeByDefault }">
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
			<div class="zb-el-accordions-accordionContent__inner" v-html="renderedContent"></div>
		</div>
		<slot name="end" />
	</div>
</template>

<script>
import { computed, inject } from 'vue';

export default {
	name: 'AccordionItem',
	props: ['options', 'element', 'api'],
	setup(props) {
		let renderedContent = computed(() => {
			return props.options.content ? props.options.content : 'accordion content';
		});
		let activeByDefault = computed(() => {
			return props.options.active_by_default ? props.options.active_by_default : false;
		});

		const accordionApi = inject('accordionsApi');
		const titleTag = computed(() => {
			return props.options.title_tag || accordionApi.options.value.title_tag || 'div';
		});

		return {
			titleTag,
			renderedContent,
			activeByDefault,
			accordionApi,
		};
	},
};
</script>
