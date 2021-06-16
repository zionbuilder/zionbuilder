<template>
	<component
		class="zb-section"
		:is="htmlTag"
	>
		<slot name="start" />

		<SvgMask
			v-if="topMask!==undefined && topMask.shape"
			:shapePath="topMask['shape']"
			:color="topMask['color']"
			:flip="topMask['flip']"
			position="top"
		/>

		<SvgMask
			v-if="bottomMask!==undefined && bottomMask.shape"
			:shapePath="bottomMask['shape']"
			:color="bottomMask['color']"
			:flip="bottomMask['flip']"
			position="bottom"
		/>

		<SortableContent
			v-bind="api.getAttributesForTag('inner_content_styles')"
			:element="element"
			class="zb-section__innerWrapper"
			:class="api.getStyleClasses('inner_content_styles')"
		></SortableContent>

		<slot name="end" />

	</component>
</template>

<script>
export default {
	name: 'zion_section',
	props: ['options', 'api', 'element'],
	computed: {
		topMask () {
			return this.shapes['top']
		},
		bottomMask () {
			return this.shapes['bottom']
		},
		htmlTag () {
			return /^[a-z0-9]+$/i.test(this.options.tag) ? this.options.tag : 'section'
		},
		shapes () {
			return this.options.shapes || {}
		}
	}
}

</script>
<style lang="scss">
</style>
