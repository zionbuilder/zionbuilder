<template>
	<div
		v-bind="getWrapperAttributes"
	>

		<slot name="start" />

		<div
			class="zb-el-gallery-item"
			v-for="(image, index) in getImages"
			:key="index"
			v-bind="getImageWrapperAttrs(image)"
			:class="api.getStyleClasses('image_wrapper_styles')"
		>
			<img
				:src="image.image"
			/>
		</div>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'gallery',
	props: ['options', 'data', 'api'],
	computed: {
		getImages () {
			return this.options.images
		},
		getWrapperAttributes () {
			if (this.options.use_modal) {
				return {
					'data-zion-lightbox': JSON.stringify({
						selector: ''
					})
				}
			}

			return {}
		}
	},
	methods: {
		getImageWrapperAttrs (image) {
			if (this.options.use_modal) {
				return {
					'data-src': image.image
				}
			}

			return {}
		}
	}
}
</script>
