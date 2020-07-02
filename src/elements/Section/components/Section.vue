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
			<div class="znpb-mask">
				<BaseIcon
					:icon="shapeType"
					class="znpb-shape-divider-icon"
				/>
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
export default {
	name: 'zion_section',
	props: ['options', 'data', 'api'],
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
