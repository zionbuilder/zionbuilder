<template>
	<Sortable
		v-model="element.content"
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
			v-for="childElement in element.content"
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
				v-if="element.content.length === 0 && allowElementsAdd && !isPreviewMode"
				:element="element"
			/>

			<slot name="end" />
		</template>
	</Sortable>
</template>

<script>
import { computed, ref } from 'vue';
import { translate } from '@/common/modules/i18n';

// Utils
import { get } from 'lodash-es';

// Components
import Element from './Element.vue';

// Stores
import { useUIStore } from '../../editor/store';

export default {
	name: 'SortableContent',
	components: {
		Element,
	},
	inheritAttrs: false,
	props: {
		element: Object,

		group: {
			type: Object,
			required: false,
		},
		allowElementsAdd: {
			type: Boolean,
			default: true,
		},
		emptyPlaceholderText: {
			type: String,
		},
		onElementSetup: {
			type: Function,
		},
	},
	setup(props) {
		const { addToHistory } = window.zb.editor.useHistory();
		const { getElementType } = window.zb.editor.useElementTypes();

		const defaultSortableGroup = {
			name: 'elements',
		};

		const showColumnTemplates = ref(false);
		const addElementsPopupButton = ref(null);
		const { getElement } = window.zb.editor.useElements();

		const showAddElementsPopup = computed(() => props.element.content.length > 0 && showColumnTemplates.value);
		const groupInfo = computed(() => props.group || defaultSortableGroup);
		const getSortableAxis = computed(() => {
			let orientation = 'horizontal';

			if (props.element.element_type === 'contentRoot') {
				return 'vertical';
			}

			const elementType = getElementType(props.element.element_type);

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
		const { isDragging, setDraggingState } = window.zb.editor.useIsDragging();
		const { copyElement } = window.zb.editor.useElementActions();

		function onSortableDrop(event) {
			const droppedElementUid = event.data.item.getAttribute('zion-element-uid');
			const element = getElement(droppedElementUid);
			const translateText = translate('moved');
			addToHistory(`${translateText} ${element.name}`);
		}

		function onSortableDuplicate(item) {
			return item.getClone();
		}

		function onSortableStart() {
			const { hideAddElementsPopup } = window.zb.editor.useAddElementsPopup();
			hideAddElementsPopup();
			setDraggingState(true);
		}

		function onSortableEnd() {
			setDraggingState(false);
		}

		return {
			UIStore,
			groupInfo,
			getSortableAxis,
			showAddElementsPopup,
			addElementsPopupButton,
			showColumnTemplates,
			isDragging,
			setDraggingState,
			copyElement,
			onSortableDrop,
			onSortableDuplicate,
			onSortableStart,
			onSortableEnd,
		};
	},
};
</script>

<style lang="scss">
.znpb-add-elemenets-tooltip-placeholder {
	position: absolute;
	bottom: 0;
	width: 100%;
}
</style>
