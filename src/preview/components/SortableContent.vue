<template>
	<Sortable
		v-model="children"
		:group="groupInfo"
		:disabled="UIStore.isPreviewMode"
		:allow-duplicate="true"
		:duplicate-callback="onSortableDuplicate"
		v-bind="$attrs"
		:class="{
			[`znpb__sortable-container--${getSortableAxis}`]: isDragging,
		}"
		:axis="getSortableAxis"
		@start="onSortableStart"
		@end="onSortableEnd"
		@drop="onSortableDrop"
	>
		<Element
			v-for="childElement in children"
			:key="childElement.uid"
			:element="childElement"
			:zion-element-uid="childElement.uid"
			:on-element-setup="onElementSetup"
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
import { translate } from '/@/common/modules/i18n';

// Utils
import { get } from 'lodash-es';

// Components
import Element from './Element.vue';
import EmptySortablePlaceholder from '/@/editor/common/EmptySortablePlaceholder.vue';
import SortablePlaceholder from '/@/editor/common/SortablePlaceholder.vue';
import SortableHelper from '/@/editor/common/SortableHelper.vue';

// Stores
import { useUIStore } from '../../editor/store';

const props = withDefaults(
	defineProps<{
		element: ZionElement;
		group?: Record<string, unknown>;
		allowElementsAdd?: boolean;
		emptyPlaceholderText?: string;
		onElementSetup?: Function;
	}>(),
	{
		group: () => {
			return {};
		},
		allowElementsAdd: true,
		emptyPlaceholderText: '',
		onElementSetup: () => {
			return {};
		},
	},
);

const children = computed({
	get: () => props.element.content.map(child => contentStore.getElement(child)),
	set: newValue =>
		contentStore.updateElement(
			props.element.uid,
			'content',
			newValue.map(element => element.uid),
		),
});

// const { addToHistory } = window.zb.editor.useHistory();
const elementsDefinitionsStore = window.parent.zb.editor.useElementDefinitionsStore();

const defaultSortableGroup = {
	name: 'elements',
};

const groupInfo = computed(() => props.group || defaultSortableGroup);
const getSortableAxis = computed(() => {
	let orientation = 'horizontal';

	if (props.element.element_type === 'contentRoot') {
		return 'vertical';
	}

	const elementType = elementsDefinitionsStore.getElementDefinition(props.element.element_type);

	if (elementType) {
		orientation = elementType.content_orientation;
	}

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

const UIStore = useUIStore();
const contentStore = window.parent.zb.editor.useContentStore();
const { isDragging, setDraggingState } = window.parent.zb.editor.useIsDragging();

function onSortableDrop(event) {
	const droppedElementUid = event.data.item.getAttribute('zion-element-uid');
	const element = contentStore.getElement(droppedElementUid);
	const translateText = translate('moved');
	addToHistory(`${translateText} ${element.name}`);
}

function onSortableDuplicate(item) {
	return item.getClone();
}

function onSortableStart() {
	const { hideAddElementsPopup } = window.parent.zb.editor.useAddElementsPopup();
	hideAddElementsPopup();
	setDraggingState(true);
}

function onSortableEnd() {
	setDraggingState(false);
}
</script>

<style lang="scss">
.znpb-add-elemenets-tooltip-placeholder {
	position: absolute;
	bottom: 0;
	width: 100%;
}
</style>
