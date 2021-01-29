<template>
	<div class="znpb-element-category-listWrapper">
		<h3 class="znpb-element-category-listTitle">{{this.category}}</h3>

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

		<div v-else>{{$translate('no_elements_found')}}</div>
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
	padding: 0;

// padding: 15px 30px;

	grid-gap: 20px 18px;
	grid-template-columns: 1fr 1fr 1fr;

	&Wrapper {
		margin-bottom: 20px;
	}

	&Title {
		padding: 0;
		margin-top: 0;
		margin-bottom: 10px;
		color: $font-color;
		font-family: $font-stack;
		font-size: 13px;
		font-weight: 500;
		line-height: 14px;
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
