<template>
	<div class="zb-el-imageBox">
		<slot name="start" />

		<div v-if="imageSrc" class="zb-el-imageBox-imageWrapper">
			<img
				class="zb-el-imageBox-image"
				:src="imageSrc"
				v-bind="api.getAttributesForTag('image_styles')"
				:class="api.getStyleClasses('image_styles')"
			/>
		</div>
		<span class="zb-el-imageBox-spacer"></span>
		<div
			class="zb-el-imageBox-text"
			:style="{
				'text-align': options.align,
			}"
		>
			<component
				:is="titleTag"
				v-if="options.title"
				class="zb-el-imageBox-title"
				:class="api.getStyleClasses('title_styles')"
				v-bind="api.getAttributesForTag('title_styles')"
				v-html="options.title"
			>
			</component>
			<div
				v-if="options.description"
				class="zb-el-imageBox-description"
				:class="api.getStyleClasses('description_styles')"
				v-bind="api.getAttributesForTag('description_styles')"
			>
				<RenderValue option="description" />
			</div>
		</div>

		<slot name="start" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		image: {
			image: string;
		};
		title: string;
		description: string;
		align: string;
		link: {
			link: string;
		};
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const imageSrc = computed(() => {
	return (props.options.image || {}).image;
});

const titleTag = computed(() => {
	return props.options.link && props.options.link.link;
});
</script>
