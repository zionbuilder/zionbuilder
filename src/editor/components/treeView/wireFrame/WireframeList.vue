<template>
	<Sortable
		:modelValue="children"
		tag="ul"
		class="znpb-wireframe-view-wrapper"
		group="pagebuilder-wireframe-elements"
		:class="{
			[`znpb__sortable-container--${getSortableAxis}`]: UIStore.isElementDragging,
		}"
		:axis="getSortableAxis"
		:allow-duplicate="true"
		:data-zion-element-uid="element.uid"
		@start="sortableStart"
		@end="sortableEnd"
		@drop="onSortableDrop"
	>
		<WireframeListItem
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
			<EmptySortablePlaceholder v-if="!element.content.length && element.isWrapper" :element="element" />

			<AddElementIcon :element="element" class="znpb-tree-view__ListAddButton" placement="next" />
		</template>
	</Sortable>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

// Components
import EmptySortablePlaceholder from '/@/editor/common/EmptySortablePlaceholder.vue';
import SortableHelper from '/@/editor/common/SortableHelper.vue';
import SortablePlaceholder from '/@/editor/common/SortablePlaceholder.vue';
import { useUIStore } from '/@/editor/store';
import { useTreeViewList } from '../useTreeViewList';

// Utils
import { get } from 'lodash-es';
import WireframeListItem from './WireframeListItem.vue';

const props = withDefaults(
	defineProps<{
		element: ZionElement;
		showAdd?: boolean;
	}>(),
	{
		showAdd: true,
	},
);

const UIStore = useUIStore();
const { sortableStart, sortableEnd, onSortableDrop, children } = useTreeViewList(props.element);

const getSortableAxis = computed(() => {
	if (props.element.element_type === 'contentRoot') {
		return 'vertical';
	}

	let orientation = props.element.element_type === 'zion_column' ? 'vertical' : 'horizontal';

	// Check columns and section direction
	if (props.element.options.inner_content_layout) {
		orientation = props.element.options.inner_content_layout;
	}

	// Check media settings
	const mediaOrientation = get(props.element.options, '_styles.wrapper.styles.default.default.flex-direction');

	if (mediaOrientation) {
		orientation = mediaOrientation === 'row' ? 'horizontal' : 'vertical';
	}

	return orientation;
});
</script>
