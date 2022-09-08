<template>
	<Sortable
		:modelValue="children"
		tag="ul"
		class="znpb-tree-view-wrapper"
		group="pagebuilder-treview-elements"
		handle=".znpb-tree-view__itemIcon"
		:data-zion-element-uid="element.uid"
		@start="sortableStart"
		@end="sortableEnd"
		@drop="onSortableDrop"
	>
		<TreeViewListItem
			v-for="childElement in children"
			:key="childElement.uid"
			:element="childElement"
			:data-zion-element-uid="childElement.uid"
		/>

		<template #end>
			<div v-if="children.length === 0 && element.isWrapper" class="znpb-tree-view__view__ListAddButtonInside">
				<AddElementIcon :element="element" placement="inside" :index="-1" />
			</div>
		</template>

		<template #helper>
			<SortableHelper />
		</template>

		<template #placeholder>
			<SortablePlaceholder />
		</template>
	</Sortable>
</template>
<script lang="ts" setup>
import { computed } from 'vue';
import SortableHelper from '/@/editor/common/SortableHelper.vue';
import SortablePlaceholder from '/@/editor/common/SortablePlaceholder.vue';
import TreeViewListItem from './TreeViewListItem.vue';

import { useTreeViewList } from '../useTreeViewList';
import { useContentStore } from '/@/editor/store';

const props = defineProps<{
	element: ZionElement;
}>();

const contentStore = useContentStore();
const children = computed(() => {
	return props.element.content.map(childUID => contentStore.getElement(childUID));
});

// Stores
const { sortableStart, sortableEnd, onSortableDrop } = useTreeViewList();
</script>

<style lang="scss">
.znpb-tree-view {
	padding-bottom: 25px;

	& .znpb-tree-view-wrapper {
		padding: 0 20px;

		& .znpb-tree-view-wrapper {
			position: relative;
			overflow: hidden;
			z-index: 1;
			padding: 0;
			margin: 0;

			& .znpb-tree-view__item {
				position: relative;
				padding-left: 20px;
				width: 100%;
				margin: 0;

				&::before {
					content: '';
					position: absolute;
					bottom: calc(100% + -24px);
					left: 0;
					width: 100%;
					height: 5000px;
					/* border: 2px solid var(--zb-surface-border-color); */
					box-shadow: inset 2px -2px 0 0 var(--zb-surface-border-color);
					border-top: 0;
					border-right: 0;
					border-radius: 8px;
					border-top-left-radius: 0;
					border-bottom-right-radius: 0;
					margin-top: 11px;
					z-index: -1;
				}
			}
		}
	}

	&__view__ListAddButtonInside {
		position: relative;
		height: 40px;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-bottom: 5px;

		.znpb-element-toolbox__add-element-button {
			--button-size: 24px;
			--font-size: 12px;
			position: relative;
			top: auto;
			left: auto;
			margin: 0;
		}
	}
}
</style>
