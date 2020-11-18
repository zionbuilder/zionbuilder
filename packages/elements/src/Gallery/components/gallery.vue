<template>
	<div
		v-bind="getWrapperAttributes"
	>

		<slot name="start" />

		<div
			class="zb-el-gallery-item"
			v-for="(image, index) in getImages"
			:key="index"
			v-bind="api.getAttributesForTag('image_wrapper_styles', getImageWrapperAttrs(image))"
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
	props: ['options', 'element', 'api'],
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
