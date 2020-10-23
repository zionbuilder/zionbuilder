<template>
	<div
		class="znpb-element-options__vertical-breadcrumbs-item"
		:class="{'znpb-element-options__vertical-breadcrumbs-item--active': activeElement === parents.element}"
		@mouseenter.capture="parents.element.highlight"
		@mouseleave="parents.element.unHighlight"
		@click.stop="editElement(parents.element)"
	>

		<span>{{parents.element.name}}</span>

		<template v-if="parents.children.length > 0">
			<component
				:is="breadcrumbsComponent"
				class="znpb-element-options__vertical-breadcrumbs-wrapper--inner"
				v-for="child in parents.children"
				:parents="child"
			/>
		</template>
	</div>
</template>

<script>
import { resolveComponent } from 'vue'
import { usePanels, useEditElement } from '@data'

export default {
	name: 'BreadcrumbsItem',
	props: {
		parents: {
			type: Object,
			required: false
		}
	},
	setup (props) {
		const { openPanel } = usePanels()
		const { editElement, element: activeElement } = useEditElement()
		const breadcrumbsComponent = require('./Breadcrumbs.vue').default

		return {
			openPanel,
			editElement,
			activeElement,
			breadcrumbsComponent
		}
	}
}
</script>
