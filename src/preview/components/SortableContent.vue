<template>
	<Sortable
		:model-value="children"
		:group="groupInfo"
		:disabled="isDisabled"
		:allow-duplicate="true"
		v-bind="$attrs"
		:class="{
			[`znpb__sortable-container--${getSortableAxis}`]: UIStore.isElementDragging,
			[`znpb__sortable-container--disabled`]: isDisabled,
		}"
		:axis="getSortableAxis"
		:data-zion-element-uid="element.uid"
		@start="onSortableStart"
		@end="onSortableEnd"
		@drop="onSortableDrop"
	>
		<Element
			v-for="childElement in children"
			:key="childElement.uid"
			:element="childElement"
			:data-zion-element-uid="childElement.uid"
		/>

		<template #start>
			<slot name="start" />
		</template>

		<template #helper>
			<SortableHelper />
		</template>

		<template #placeholder>
			<SortablePlaceholder />
		</template>

		<template #end>
			<EmptySortablePlaceholder
				v-if="element.content.length === 0 && allowElementsAdd && !UIStore.isPreviewMode"
				:element="element"
			/>

			<slot name="end" />
		</template>
	</Sortable>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { useContentStore, useUIStore, useUserStore } from '/@/editor/store';

// Utils
import { get } from 'lodash-es';

// Components
import Element from './Element.vue';
import EmptySortablePlaceholder from '/@/editor/common/EmptySortablePlaceholder.vue';
import SortablePlaceholder from '/@/editor/common/SortablePlaceholder.vue';
import SortableHelper from '/@/editor/common/SortableHelper.vue';

const props = withDefaults(
	defineProps<{
		element: ZionElement;
		group?: Record<string, unknown>;
		allowElementsAdd?: boolean;
		emptyPlaceholderText?: string;
		disabled?: boolean;
	}>(),
	{
		group: () => {
			return {};
		},
		allowElementsAdd: true,
		emptyPlaceholderText: '',
		disabled: false,
	},
);

// Stores
const UIStore = useUIStore();
const contentStore = useContentStore();
const UserStore = useUserStore();

const children = computed(() => props.element.content.map(childUID => contentStore.getElement(childUID)));
const defaultSortableGroup = {
	name: 'elements',
};

const isDisabled = computed(() => {
	return UIStore.isPreviewMode || props.disabled || !UserStore.userCanEditContent;
});

const groupInfo = computed(() => props.group || defaultSortableGroup);
const getSortableAxis = computed(() => {
	let orientation = 'horizontal';

	if (props.element.element_type === 'contentRoot') {
		return 'vertical';
	}

	orientation = props.element.elementDefinition.content_orientation;

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

function onSortableStart() {
	UIStore.hideAddElementsPopup();
	UIStore.setElementDragging(true);
}

function onSortableEnd() {
	UIStore.setElementDragging(false);
}

function onSortableDrop(event) {
	const { item, to, newIndex, duplicateItem, placeBefore } = event.data;

	const movedElement = contentStore.getElement(item.dataset.zionElementUid);
	if (duplicateItem) {
		const elementForInsert = movedElement.getClone();
		window.zb.run('editor/elements/add', {
			parentUID: to.dataset.zionElementUid,
			element: elementForInsert,
			index: placeBefore ? newIndex : newIndex + 1,
		});
	} else {
		window.zb.run('editor/elements/move', {
			newParent: contentStore.getElement(to.dataset.zionElementUid),
			element: contentStore.getElement(item.dataset.zionElementUid),
			index: newIndex,
		});
	}
}
</script>

<style lang="scss">
.znpb-add-elements-tooltip-placeholder {
	position: absolute;
	bottom: 0;
	width: 100%;
}
</style>
