<template>
	<div class="znpb-element-options__breadcrumbs znpb-fancy-scrollbar">
		<Breadcrumbs v-if="breadcrumbsItem.children.length > 0" :item="breadcrumbsItem" />
		<span v-else>This element has no children</span>
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import Breadcrumbs from './Breadcrumbs.vue';
import { useContentStore } from '/@/editor/store';

const props = defineProps<{
	element: ZionElement;
}>();

const contentStore = useContentStore();

const getChildren = function (element: ZionElement) {
	const children = {
		element: element,
		children: [],
		active: props.element.uid === element.uid,
	};

	if (element.content) {
		element.content.forEach(childElementUID => {
			const childElement = contentStore.getElement(childElementUID);
			children.children.push(getChildren(childElement));
		});
	}

	return children;
};

const breadcrumbsItem = computed(() => {
	let parentStructure = getChildren(props.element);
	let element = props.element;

	while (element.parent && element.parent.elementDefinition.element_type !== 'contentRoot') {
		parentStructure = {
			element: element.parent,
			children: [parentStructure],
			active: props.element === element.parent,
		};

		element = element.parent;
	}

	return parentStructure;
});
</script>
