<template>
	<div class="znpb-element-category-listWrapper">
		<h3 v-if="elements.length" class="znpb-element-category-listTitle">{{ category }}</h3>

		<ul v-if="elements.length" class="znpb-element-category-list">
			<!-- list of elements -->
			<ElementListItem
				v-for="childElement in elements"
				:key="childElement.element_type"
				:item="childElement"
				@click="emit('add-element', childElement)"
			/>
		</ul>
	</div>
</template>

<script lang="ts" setup>
import ElementListItem from './ElementListItem.vue';

defineProps<{
	elements: Array;
	element: ZionElement;
	category: string;
}>();

const emit = defineEmits(['add-element']);
</script>

<style lang="scss">
/* vars */

.znpb-element-category-list {
	display: grid;
	padding: 0 0 30px;
	// padding: 15px 30px;

	grid-gap: 15px;
	grid-template-columns: 1fr 1fr 1fr 1fr;

	// &Wrapper {
	// 	margin-bottom: 40px;
	// }

	&Title {
		padding: 0;
		margin-top: 0;
		margin-bottom: 10px;
		color: var(--zb-surface-text-color);
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
