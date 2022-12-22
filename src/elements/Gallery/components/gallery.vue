<template>
	<div v-bind="getWrapperAttributes">
		<slot name="start" />

		<div
			v-for="(image, index) in getImages"
			:key="index"
			class="zb-el-gallery-item"
			:class="api.getStyleClasses('image_wrapper_styles')"
			v-bind="api.getAttributesForTag('image_wrapper_styles', getImageWrapperAttrs(image))"
		>
			<img :src="image.image" />
		</div>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		images: {
			image: string;
		}[];
		use_modal: boolean;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const getImages = computed(() => {
	return props.options.images;
});

const getWrapperAttributes = computed(() => {
	if (props.options.use_modal) {
		return {
			'data-zion-lightbox': JSON.stringify({
				selector: '',
			}),
		};
	}

	return {};
});

function getImageWrapperAttrs(image: { image: string }) {
	if (props.options.use_modal) {
		return {
			'data-src': image.image,
		};
	}

	return {};
}
</script>
