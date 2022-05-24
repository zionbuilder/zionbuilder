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
				:is="breadcrumbsComponent"
				v-for="child in parents.children"
				:key="child.element.uid"
				class="znpb-element-options__vertical-breadcrumbs-wrapper--inner"
				:parents="child"
			/>
		</template>
	</div>
</template>

<script>
import { useEditElement } from '../../composables';
import { useUIStore } from '../../store';

export default {
	name: 'BreadcrumbsItem',
	props: {
		parents: {
			type: Object,
			required: false,
		},
	},
	setup(props) {
		const { openPanel } = useUIStore();
		const { editElement, element: activeElement } = useEditElement();
		const breadcrumbsComponent = require('./Breadcrumbs.vue').default;

		return {
			openPanel,
			editElement,
			activeElement,
			breadcrumbsComponent,
		};
	},
};
</script>
