<template>
	<component
		class="zb-section"
		:is="htmlTag"
	>
		<slot name="start" />
		<RenderTag
			tag-id="div"
			v-if="topMask!==undefined && Object.keys(topMask).length"
		>
			<SvgMask
				:shapePath="topMask['shape']"
				:color="topMask['color']"
				:height="topMask['height']"
				position="top"
			/>

		</RenderTag>
		<RenderTag
			tag-id="div"
			v-if="bottomMask!==undefined && Object.keys(bottomMask).length"
		>
			<SvgMask
				:shapePath="bottomMask['shape']"
				:color="bottomMask['color']"
				:height="bottomMask['height']"
				position="bottom"
			/>

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
import SvgMask from '@/common/components/forms/elements/shape-dividers/SvgMask.vue'
export default {
	name: 'zion_section',
	props: ['options', 'data', 'api'],
	components: {
		SvgMask
	},
	computed: {
		topMask () {
			return this.shapes['top']
		},
		bottomMask () {
			return this.shapes['bottom']
		},
		htmlTag () {
			return this.options.tag || 'section'
		},
		shapes () {
			return this.options.shapes || {}
		}
	}
}

</script>
<style lang="scss">
</style>
