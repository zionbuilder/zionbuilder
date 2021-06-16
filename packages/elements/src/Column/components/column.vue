<template>
	<SortableContent
		class="zb-column"
		:element="element"
		:tag="htmlTag"
		v-bind="extraAttributes"
	>

		<template #start>
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
		</template>

		<template #end>
			<slot name="end" />
		</template>
	</SortableContent>
</template>

<script>
import { getLinkAttributes } from '@zb/utils'

export default {
	name: 'zion_column',
	props: ['options', 'api', 'element'],
	computed: {
		htmlTag () {
			if (this.options.link && this.options.link.link) {
				return 'a'
			}
			return this.options.tag || 'div'
		},
		extraAttributes () {
			return getLinkAttributes(this.options.link)
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
