<template>
	<component
		class="zb-section"
		:is="htmlTag"
	>
		<slot name="start" />
		<RenderTag
			tag-id="div"
			v-if="shapeType.length"
		>
			<div class="znpb-mask znpb-shape-divider-icon">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					xmlns:xlink="http://www.w3.org/1999/xlink"
					preserveAspectRatio="none"
				>

					<!-- <BaseIcon
					:icon="shapeType"
					class="znpb-mask znpb-shape-divider-icon"
					preserveAspectRatio="none"
				/> -->
					<use
						:xlink:href="`${svgUrl}#${shapeType}`"
						:href="`${svgUrl}#${shapeType}`"
					>
					</use>
				</svg>
			</div>
		</RenderTag>
		<RenderTag tag-id="inner_content">
			<SortableContent
				:content="data.content"
				:data="data"
				class="zb-section__innerWrapper"
			></SortableContent>
		</RenderTag>

		<slot name="end" />
	</component>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
	name: 'zion_section',
	props: ['options', 'data', 'api'],
	computed: {
		htmlTag () {
			return this.options.tag || 'section'
		},
		shapeType () {
			return this.options.shape_type || ''
		},
		...mapGetters([
			'getAssetsUrl'
		]),
		svgUrl () {
			return this.getAssetsUrl + '/masks/masks.svg'
		},
		getStyle () {
			let style = {
				// backgroundImage: `url("${this.svgUrl}#${this.shapeType})")`
				// background: `url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="none"><use xlink:href="${this.svgUrl}#${this.shapeType}" href="${this.svgUrl}#${this.shapeType}"></use></svg>')`
			}
			return style
		}
	}
}
</script>
<style lang="scss">
</style>
