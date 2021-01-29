<template>
	<Sortable
		v-model="element.content"
		@start="onSortableStart"
		@end="onSortableEnd"
		@drop="onSortableDrop"
		:group="groupInfo"
		:disabled="isPreviewMode"
		:allow-duplicate="true"
		:duplicate-callback="onSortableDuplicate"
		v-bind="$attrs"
		:class="{
			[`znpb__sortable-container--${getSortableAxis}`]: isDragging
		}"
		:axis="getSortableAxis"
	>
		<Element
			v-for="childElement in element.content"
			:key="childElement.uid"
			:element="childElement"
			:zion-element-uid="childElement.uid"
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
				:element="element"
				v-if="element.content.length === 0 && allowElementsAdd && !isPreviewMode"
			/>

			<slot name="end" />
		</template>
	</Sortable>
</template>

<script>
import { computed, ref } from 'vue'
import { useElements, useAddElementsPopup, usePreviewMode, useIsDragging, useElementActions, useHistory, useElementTypes } from '@zb/editor'
import { translate } from '@zb/i18n'

// Utils
import { getOptionValue } from '@zb/utils'

// Components
import Element from './Element.vue'

export default {
	name: 'SortableContent',
	inheritAttrs: false,
	components: {
		Element
	},
	props: {
		element: Object,

		group: {
			type: Object,
			required: false
		},
		allowElementsAdd: {
			type: Boolean,
			default: true
		},
		emptyPlaceholderText: {
			type: String
		}
	},
	setup (props) {
		const { addToHistory } = useHistory()
		const { getElementType } = useElementTypes()

		const defaultSortableGroup = {
			name: 'elements'
		}

		const showColumnTemplates = ref(false)
		const addElementsPopupButton = ref(null)
		const { getElement } = useElements()

		const showAddElementsPopup = computed(() => props.element.content.length > 0 && showColumnTemplates.value)
		const groupInfo = computed(() => props.group || defaultSortableGroup)
		const getSortableAxis = computed(() => {
			let orientation = 'horizontal'

			if (props.element.element_type === 'contentRoot') {
				return 'vertical'
			}

			const elementType = getElementType(props.element.element_type)

			if (elementType) {
				orientation = elementType.content_orientation
			}

			// Check columns and section direction
			if (props.element.options.inner_content_layout) {
				orientation = props.element.options.inner_content_layout
			}

			// Check media settings
			const mediaOrientation = getOptionValue(props.element.options, '_styles.wrapper.styles.default.default.flex-direction')

			if (mediaOrientation) {
				orientation = mediaOrientation === 'row' ? 'horizontal' : 'vertical'
			}

			return orientation
		})

		const toggleAddElementsPopup = () => {
			const { showAddElementsPopup } = useAddElementsPopup()
			showAddElementsPopup(props.element, addElementsPopupButton)
		}

		const { isPreviewMode } = usePreviewMode()
		const { isDragging, setDraggingState } = useIsDragging()
		const { copyElement } = useElementActions()

		function onSortableDrop (event) {
			const droppedElementUid = event.data.item.getAttribute('zion-element-uid')
			const element = getElement(droppedElementUid)
			const translateText = translate('moved')
			addToHistory(`${translateText} ${element.name}`)
		}

		function onSortableDuplicate (item) {
			return item.getClone()
		}

		function onSortableStart () {
			const { hideAddElementsPopup } = useAddElementsPopup()
			hideAddElementsPopup()
			setDraggingState(true)
		}

		function onSortableEnd () {
			setDraggingState(false)
		}

		return {
			isPreviewMode,
			groupInfo,
			getSortableAxis,
			showAddElementsPopup,
			toggleAddElementsPopup,
			addElementsPopupButton,
			showColumnTemplates,
			isDragging,
			setDraggingState,
			copyElement,
			onSortableDrop,
			onSortableDuplicate,
			onSortableStart,
			onSortableEnd
		}
	}
}
</script>

<style lang="scss">
.znpb-add-elemenets-tooltip-placeholder {
	position: absolute;
	bottom: 0;
	width: 100%;
}
</style>
