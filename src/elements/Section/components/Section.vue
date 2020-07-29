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
import { mapGetters } from 'vuex'
import Masks from '@/common/components/forms/elements/shape-dividers/Masks.vue'
export default {
	name: 'zion_section',
	props: ['options', 'data', 'api'],
	mixins: [Masks],
	computed: {
		...mapGetters([
			'getMasks'
		]),
		htmlTag () {
			return this.options.tag || 'section'
		},
		shapeType: {
			get () {
				return this.options.shape_type || ''
			},
			set (newValue) {
				this.getFile(this.shapePath)
			}
		},
		shapePath () {
			return this.getMasks[this.shapeType]
		}
	}

}

</script>
<style lang="scss">
</style>
