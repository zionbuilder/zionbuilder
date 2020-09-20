<template>
	<component
		:is="tag"
		:sort="false"
		:group="{
			name: 'elements',
			pull: 'clone',
			put: false
		}"
	>
		<TransitionGroup
			name="pb_element"
			tag="ul"
			class="znpb-element-category-list"
		>

			{{elements}}
			<!-- list of elements -->
			<PagebuilderElement
				v-for="( item, itemId ) in computedElements"
				:key="itemId"
				:item="item"
			/>
		</TransitionGroup>

		<SortablePlaceholder
			slot="placeholder"
			v-if="tag === 'Sortable'"
		/>
	</component>
</template>

<script>
import PagebuilderElement from './PagebuilderElement.vue'
import { Sortable } from '@zb/components'
import SortablePlaceholder from '@/editor/common/SortablePlaceholder.vue'

export default {
	name: 'ElementList',
	props: {
		elements: {
			type: Array,
			required: true
		},
		tag: {
			type: String,
			required: false,
			default: 'Sortable'
		}
	},
	components: {
		PagebuilderElement,
		Sortable,
		SortablePlaceholder
	},
	computed: {
		computedElements () {
			return this.elements.filter(function (element) {
				return element.show_in_ui
			})
		}
	}
}
</script>

<style lang="scss">
/* vars */

.znpb-element-category-list {
	display: grid;
	padding: 0;

// padding: 15px 30px;

	grid-gap: 20px 18px;
	grid-template-columns: 1fr 1fr 1fr;
}

.pb_element {
	position: relative;
	z-index: 1;
	display: inline-flex;
	flex-direction: column;
	margin: 10px;
	cursor: pointer;
}
</style>
