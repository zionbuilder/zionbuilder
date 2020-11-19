<template>
	<div>
		<slot name="start" />

		<a
			v-if="hasLink"
			v-bind="api.getAttributesForTag('link', extraAttributes)"
		>
			<img
				v-bind="api.getAttributesForTag('image')"
				:src="imageSrc"
			/>
		</a>

		<img
			v-else
			v-bind="api.getAttributesForTag('image', extraAttributes)"
			:src="imageSrc"
		/>
		<div
			class="zb-el-zionImage-caption"
			v-if="options.show_caption"
		>
			{{options.caption_text}}
		</div>

		<slot name="end" />
	</div>
</template>

<script>
import { getLinkAttributes } from '@zionbuilder/utils'

export default {
	name: 'zion_image',
	props: ['element', 'options', 'api'],
	computed: {
		imageSrc () {
			return (this.options.image || {}).image
		},
		hasLink () {
			return this.options.link && this.options.link.link
		},
		extraAttributes () {
			const attributes = getLinkAttributes(this.options.link)

			if (this.options.use_modal) {
				attributes.href = this.imageSrc
				attributes['data-zion-lightbox'] = true
			}

			return attributes
		}
	}
}
</script>
