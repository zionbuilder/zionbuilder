<template>
	<SortableContent
		class="zb-column"
		:element="data"
		:tag="htmlTag"
	>

		<template #start>
			<slot name="start" />
			<SvgMask
				v-if="topMask!==undefined && topMask.shape"
				:shapePath="topMask['shape']"
				:color="topMask['color']"
				:height="topMask['height']"
				:flip="topMask['flip']"
				position="top"
			/>

			<SvgMask
				v-if="bottomMask!==undefined && bottomMask.shape"
				:shapePath="bottomMask['shape']"
				:color="bottomMask['color']"
				:height="bottomMask['height']"
				:flip="bottomMask['flip']"
				position="bottom"
			/>
		</template>

		<slot
			name="end"
			slot="end"
		/>
	</SortableContent>
</template>

<script>
export default {
	name: 'zion_column',
	props: ['options', 'data', 'api'],
	computed: {
		htmlTag () {
			return this.options.tag || 'div'
		},
		topMask () {
			return this.shapes['top']
		},
		bottomMask () {
			return this.shapes['bottom']
		},
		shapes () {
			return this.options.shapes || {}
		}
	}
}
</script>
