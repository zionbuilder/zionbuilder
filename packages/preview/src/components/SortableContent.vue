<template>
	<Sortable
		v-model="element.content"
		@start="onSortableStart"
		@end="onSortableEnd"
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
import { useElements, useAddElementsPopup, usePreviewMode, useIsDragging, useElementActions } from '@zb/editor'

// Utils
import { getOptionValue } from '@zb/utils'

// Components
import Element from './Element.vue'

const sharedStateGlobal = {
	controlPressed: null,
	draggedItemData: null
}

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
		const defaultSortableGroup = {
			name: 'elements'
		}

		const showColumnTemplates = ref(false)
		const addElementsPopupButton = ref(null)
		const { getElement } = useElements()

		const draggedItemData = ref(null)
		const positionRect = ref({
			left: 0,
			top: 0
		})
		const addColumnsRef = ref({
			getBoundingClientRect: () => {
				return this.positionRect
			}
		})

		const sharedState = ref(sharedStateGlobal)
		const showAddElementsPopup = computed(() => props.element.content.length > 0 && showColumnTemplates.value)
		const groupInfo = computed(() => props.group || defaultSortableGroup)
		const getSortableAxis = computed(() => {
			if (props.element.element_type === 'contentRoot') {
				return 'vertical'
			}

			let orientation = props.element.element_type === 'zion_column' ? 'vertical' : 'horizontal'

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

		return {
			isPreviewMode,
			groupInfo,
			getSortableAxis,
			showAddElementsPopup,
			sharedState,
			toggleAddElementsPopup,
			addElementsPopupButton,
			showColumnTemplates,
			isDragging,
			setDraggingState,
			copyElement
		}
	},

	methods: {
		onSortableDuplicate (item) {
			return item.getClone()
		},
		onSortableStart (event) {
			const { hideAddElementsPopup } = useAddElementsPopup()
			hideAddElementsPopup()
			this.setDraggingState(true)
		},
		onSortableEnd (event) {
			this.setDraggingState(false)
		}
	},
	watch: {
		'sharedState.controlPressed' (newValue, oldValue) {
			const draggedItem = this.sharedState.draggedItemData.item
			if (newValue) {
				draggedItem.style.display = null
				draggedItem.style.opacity = 0.2
				if (!this.isDragging.value) {
					draggedItem.style.opacity = null
					draggedItem.style.display = null
				}
			} else {
				if (this.isDragging.value) {
					draggedItem.style.opacity = null
					draggedItem.style.display = 'none'
				}
			}
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
