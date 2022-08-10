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

<script>
export default {
	name: 'ZionImage',
	props: ['element', 'options', 'api'],
	computed: {
		imageSrc() {
			return (this.options.image || {}).image;
		},
		hasLink() {
			return this.options.link && this.options.link.link;
		},
		extraAttributes() {
			const attributes = window.zb.utils.getLinkAttributes(this.options.link);

			if (this.options.use_modal) {
				attributes.href = this.imageSrc;
				attributes['data-zion-lightbox'] = true;
			}

			return attributes;
		},
	},
};
</script>
