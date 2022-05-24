<template>
	<div
		class="znpb-element-options__vertical-breadcrumbs-item"
		:class="{ 'znpb-element-options__vertical-breadcrumbs-item--active': activeElement === parents.element }"
		@mouseenter.capture="parents.element.highlight"
		@mouseleave="parents.element.unHighlight"
		@click.stop="editElement(parents.element)"
	>
		<span>{{ parents.element.name }}</span>

		<template v-if="parents.children.length > 0">
			<component
				:is="Breadcrumbs"
				v-for="child in parents.children"
				:key="child.element.uid"
				class="znpb-element-options__vertical-breadcrumbs-wrapper--inner"
				:parents="child"
			/>
		</template>
	</div>
</template>

<script lang="ts" setup>
import { useEditElement } from '../../composables';
import Breadcrumbs from './Breadcrumbs.vue';

defineProps<{
	parents?: Record<string, unknown>;
}>();

const { editElement, element: activeElement } = useEditElement();
</script>
