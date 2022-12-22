<template>
	<div>
		<slot name="start" />

		<a
			v-if="hasLink"
			v-bind="api.getAttributesForTag('link_styles', extraAttributes)"
			:class="api.getStyleClasses('link_styles')"
		>
			<img
				v-bind="api.getAttributesForTag('image_styles')"
				:src="imageSrc"
				:class="api.getStyleClasses('image_styles')"
			/>
		</a>

		<img
			v-else-if="imageSrc"
			v-bind="api.getAttributesForTag('image_styles', extraAttributes)"
			:src="imageSrc"
			:class="api.getStyleClasses('image_styles')"
		/>
		<div v-if="options.show_caption" class="zb-el-zionImage-caption">
			{{ options.caption_text }}
		</div>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		image: {
			image: string;
		};
		link: {
			link: string;
		};
		use_modal: boolean;
		show_caption: boolean;
		caption_text: string;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const imageSrc = computed(() => {
	return (props.options.image || {}).image;
});

const hasLink = computed(() => {
	return props.options.link && props.options.link.link;
});

const extraAttributes = computed(() => {
	const attributes = window.zb.utils.getLinkAttributes(props.options.link);

	if (props.options.use_modal) {
		attributes.href = imageSrc.value;
		attributes['data-zion-lightbox'] = true;
	}

	return attributes;
});
</script>
