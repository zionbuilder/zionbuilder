<template>
	<SortableContent
		class="zb-column"
		:content="data.content"
		:data="data"
		:tag="htmlTag"
	>
		<slot
			name="start"
			slot="start"
		/>
		<template
			slot="start"
			name="start"
		>
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
			slot="start"
		/>
	</SortableContent>
</template>

<script>
import { SvgMask } from '@zb/components/forms'

export default {
	name: 'zion_column',
	components: {
		SvgMask
	},
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
