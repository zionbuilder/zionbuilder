<template>
	<Sortable
		v-model="templateItems"
		tag="ul"
		class="znpb-wireframe-view-wrapper"
		group="pagebuilder-wireframe-elements"
		:class="{
			[`znpb__sortable-container--${getSortableAxis}`]: UIStore.isElementDragging,
		}"
		:axis="getSortableAxis"
		:allow-duplicate="true"
		:duplicate-callback="onSortableDuplicate"
		@start="onSortableStart"
		@end="onSortableEnd"
	>
		<WireframeListItem v-for="element in templateItems" :key="element.uid" :element="element" />

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

import { useTreeViewList } from '../useTreeViewList';
import { useUIStore } from '/@/editor/store';

// Utils
import { get } from 'lodash-es';

const props = defineProps<{
	element: ZionElement;
	showAdd: boolean;
}>();

const UIStore = useUIStore();
const { templateItems } = useTreeViewList(props);

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

function onSortableDuplicate(item) {
	return item.getClone();
}

function onSortableStart(event) {
	UIStore.setElementDragging(true);
}

function onSortableEnd(event) {
	UIStore.setElementDragging(false);
}
</script>
