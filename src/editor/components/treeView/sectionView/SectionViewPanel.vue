<template>
	<div id="znpb-section-view" class="znpb-tree-view-container znpb-fancy-scrollbar znpb-panel-view-wrapper">
		<Sortable
			v-model="children"
			class="znpb-section-view-wrapper"
			tag="ul"
			group="pagebuilder-sectionview-elements"
			@start="sortableStart"
			@end="sortableEnd"
		>
			<ElementSectionView v-for="childElement in children" :key="childElement.uid" :element="childElement" />

			<template #helper>
				<SortableHelper />
			</template>

			<template #placeholder>
				<SortablePlaceholder />
			</template>

			<template #end>
				<div v-if="children.length === 0" class="znpb-tree-view__view__ListAddButtonInside">
					<AddElementIcon :element="element" placement="inside" />
				</div>
			</template>
		</Sortable>
	</div>
</template>
<script lang="ts" setup>
import { computed } from 'vue';
import ElementSectionView from './ElementSectionView.vue';
import SortableHelper from '/@/editor/common/SortableHelper.vue';
import SortablePlaceholder from '/@/editor/common/SortablePlaceholder.vue';
import { useTreeViewList } from '../useTreeViewList';
import { useContentStore } from '/@/editor/store';

const props = defineProps<{
	element: ZionElement;
}>();

const contentStore = useContentStore();

const children = computed({
	get: () => props.element.content.map(child => contentStore.getElement(child)),
	set: newValue =>
		contentStore.updateElement(
			props.element.uid,
			'content',
			newValue.map(element => element.uid),
		),
});

const { sortableStart, sortableEnd } = useTreeViewList(props);
</script>
<style lang="scss">
.znpb-tree-view-container {
	flex-grow: 1;
	overflow-y: auto;
	padding: 0 20px 0 20px;
}
</style>
