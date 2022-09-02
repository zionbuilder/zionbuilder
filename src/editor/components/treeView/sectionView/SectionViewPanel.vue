<template>
	<div id="znpb-section-view" class="znpb-tree-view-container znpb-fancy-scrollbar znpb-panel-view-wrapper">
		<Sortable
			:modelValue="children"
			class="znpb-section-view-wrapper"
			tag="ul"
			group="pagebuilder-sectionview-elements"
			:data-zion-element-uid="element.uid"
			@start="sortableStart"
			@end="sortableEnd"
			@drop="onSortableDrop"
		>
			<ElementSectionView
				v-for="childElement in children"
				:key="childElement.uid"
				:element="childElement"
				:data-zion-element-uid="childElement.uid"
			/>

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
import ElementSectionView from './ElementSectionView.vue';
import SortableHelper from '/@/editor/common/SortableHelper.vue';
import SortablePlaceholder from '/@/editor/common/SortablePlaceholder.vue';
import { useTreeViewList } from '../useTreeViewList';

const props = defineProps<{
	element: ZionElement;
}>();

const { sortableStart, sortableEnd, onSortableDrop, children } = useTreeViewList(props.element);
</script>
<style lang="scss">
.znpb-tree-view-container {
	flex-grow: 1;
	overflow-y: auto;
	padding: 0 20px 0 20px;
}
</style>
