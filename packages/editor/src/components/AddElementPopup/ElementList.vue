<template>
	<div>
		<TransitionGroup
			name="pb_element"
			tag="ul"
			class="znpb-element-category-list"
		>
			<!-- list of elements -->
			<ElementListItem
				v-for="element in elements"
				:item="element"
				:key="element.element_type"
				@click="$emit('add-element', element)"
			/>
		</TransitionGroup>
	</div>
</template>

<script>
import ElementListItem from './ElementListItem.vue'

export default {
	name: 'ElementList',
	components: {
		ElementListItem
	},
	props: {
		elements: {
			type: Array,
			required: true
		},
		element: Object
	},
	setup () {
		return {

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
