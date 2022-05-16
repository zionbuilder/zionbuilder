<template>
	<Sortable
		v-model="templateItems"
		tag="ul"
		class="znpb-wireframe-view-wrapper"
		group="pagebuilder-wireframe-elements"
		:class="{
			[`znpb__sortable-container--${getSortableAxis}`]: isDragging,
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

<script>
import { computed } from 'vue';

import { useTreeViewList } from '../useTreeViewList';
import { useIsDragging } from '@composables';

// Utils
import { get } from 'lodash-es';

export default {
	name: 'WireframeList',
	props: {
		element: {
			type: Object,
			required: true,
		},
		showAdd: {
			type: Boolean,
			default: true,
		},
	},
	setup(props) {
		const { addElementsPopupButton, templateItems, addButtonBgColor, sortableStart, sortableEnd } =
			useTreeViewList(props);
		const { isDragging, setDraggingState } = useIsDragging();

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
			console.log({ mediaOrientation });
			if (mediaOrientation) {
				orientation = mediaOrientation === 'row' ? 'horizontal' : 'vertical';
			}

			return orientation;
		});

		function onSortableDuplicate(item) {
			return item.getClone();
		}

		function onSortableStart(event) {
			setDraggingState(true);
		}

		function onSortableEnd(event) {
			setDraggingState(false);
		}

		return {
			addElementsPopupButton,
			templateItems,
			sortableStart,
			sortableEnd,
			addButtonBgColor,
			getSortableAxis,
			isDragging,

			// Methods
			onSortableDuplicate,
			onSortableStart,
			onSortableEnd,
		};
	},
};
</script>
