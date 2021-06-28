<template>
	<div class="znpb-element-category-listWrapper">
		<h3
			class="znpb-element-category-listTitle"
			v-if="elements.length"
		>{{this.category}}</h3>

		<TransitionGroup
			v-if="elements.length"
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
		element: Object,
		category: {
			type: String
		}
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
	padding: 0 0 40px;

	// padding: 15px 30px;

	grid-gap: 20px 18px;
	grid-template-columns: 1fr 1fr 1fr;

	// &Wrapper {
	// 	margin-bottom: 40px;
	// }

	&Title {
		padding: 0;
		margin-top: 0;
		margin-bottom: 10px;
		color: var(--zb-surface-text-muted-color);
		font-family: var(--zb-font-stack);
		font-size: 11px;
		font-weight: 700;
		line-height: 1;
		text-transform: uppercase;
	}
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
