<template>
	<div class="znpb-element-options__breadcrumbs znpb-fancy-scrollbar">
		<Breadcrumbs v-if="parents.children.length > 0" :parents="parents" />
		<span v-else>This element has no children</span>
	</div>
</template>

<script>
import { computed } from 'vue';
import Breadcrumbs from './Breadcrumbs.vue';
import { useEditElement, useElements } from '../../composables';

export default {
	name: 'BreadcrumbsWrapper',
	components: {
		Breadcrumbs,
	},
	props: {
		element: Object,
	},
	setup(props) {
		const getChildren = function (element) {
			const { element: activeElement } = useEditElement();
			const children = {
				element: element,
				children: [],
				active: activeElement.value === element,
			};

			if (element.content) {
				element.content.forEach(childElement => {
					children.children.push(getChildren(childElement));
				});
			}

			return children;
		};

		const parents = computed(() => {
			const { element: activeElement } = useEditElement();
			let parentStructure = getChildren(props.element);
			let element = props.element;

			while (element.parent && element.parent.element_type !== 'contentRoot') {
				parentStructure = {
					element: element.parent,
					children: [parentStructure],
					active: activeElement.value === element.parent,
				};

				element = element.parent;
			}

			return parentStructure;
		});

		return {
			parents,
		};
	},
};
</script>
