<template>
	<div id="znpb-section-view" class="znpb-tree-view-container znpb-fancy-scrollbar znpb-panel-view-wrapper">
		<Sortable
			v-model="templateItems"
			class="znpb-section-view-wrapper"
			tag="ul"
			group="pagebuilder-sectionview-elements"
			@start="sortableStart"
			@end="sortableEnd"
		>
			<ElementSectionView v-for="element in templateItems" :key="element.uid" :element="element" />

			<template #helper>
				<SortableHelper />
			</template>

			<template #placeholder>
				<SortablePlaceholder />
			</template>

			<template #end>
				<div v-if="templateItems.length === 0" class="znpb-tree-view__view__ListAddButtonInside">
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
const { templateItems, sortableStart, sortableEnd } = useTreeViewList(props);
</script>
<style lang="scss">
.znpb-tree-view-container {
	flex-grow: 1;
	overflow-y: auto;
	padding: 0 20px 0 20px;
}
</style>
