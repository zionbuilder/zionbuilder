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
			<div
				class="znpb-mask"
				v-html="getSvgIcon"
			>

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
import Masks from '@/common/components/forms/elements/shape-dividers/Masks.vue'
export default {
	name: 'zion_section',
	props: ['options', 'data', 'api'],
	mixins: [Masks],
	computed: {
		htmlTag () {
			return this.options.tag || 'section'
		},
		shapeType () {
			return this.options.shape_type || ''
		}
	}

}

</script>
<style lang="scss">
</style>
