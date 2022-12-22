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

<script>
export default {
	name: 'Gallery',
	props: ['options', 'element', 'api'],
	computed: {
		getImages() {
			return this.options.images;
		},
		getWrapperAttributes() {
			if (this.options.use_modal) {
				return {
					'data-zion-lightbox': JSON.stringify({
						selector: '',
					}),
				};
			}

			return {};
		},
	},
	methods: {
		getImageWrapperAttrs(image) {
			if (this.options.use_modal) {
				return {
					'data-src': image.image,
				};
			}

			return {};
		},
	},
};
</script>
