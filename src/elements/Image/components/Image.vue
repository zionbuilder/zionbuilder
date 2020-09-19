<template>
	<div>
		<slot name="start" />

		<RenderTag
			tag-id="link"
			v-if="hasLink"
		>
			<a
				v-bind="extraAttributes"
			>
				<RenderTag
					tag-id="image"
				>
					<img
						:src="imageSrc"
					/>

				</RenderTag>
			</a>

		</RenderTag>

		<RenderTag
			tag-id="image"
			v-else
		>
			<img
				:src="imageSrc"
			/>

		</RenderTag>

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
export default {
	name: 'zion_image',
	props: ['data', 'options', 'api'],
	computed: {
		imageSrc () {
			return (this.options.image || {}).image
		},
		hasLink () {
			return this.options.link && this.options.link.link
		},
		extraAttributes () {
			const attributes = window.zb.utils.getLinkAttributes(this.options.link)

			if (this.options.use_modal) {
				attributes.href = this.imageSrc
				attributes['data-zion-lightbox'] = true
			}

			return attributes
		}
	}
}
</script>
