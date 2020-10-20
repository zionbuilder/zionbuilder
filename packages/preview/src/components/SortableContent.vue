<template>
	<Sortable
		v-model="contentModel"
		@start="onSortableStart"
		@drop="onSortableDrop"
		@end="onSortableEnd"
		:group="groupInfo"
		:disabled="isPreviewMode"
		v-bind="$attrs"
		:class="{
			[`znpb__sortable-container--${getSortableAxis}`]: isDragging
		}"
		:axis="getSortableAxis"
	>

		<Element
			v-for="childElement in contentModel"
			:uid="childElement.uid"
			:data="childElement"
			:parentUid="element.uid"
		/>

		<template #start>
			<slot
				name="start"
			/>
		</template>

		<template #content>
			<Tooltip
				ref="addElementsPopup"
				tooltip-class="hg-popper--big-arrows"
				placement='bottom'
				append-to="body"
				trigger="click"
				v-if="showAddElementsPopup"
				:show="showAddElementsPopup"
				:close-on-outside-click="true"
				:close-on-escape="true"
				@hide="onAddElementsHide"
				@show="onAddElementsShow"
				key="addElements"
				class="znpb-add-elemenets-tooltip-placeholder"
			>
				<template #content>
					<ColumnTemplates

						:parentUid="element.uid"
						:insertIndex="insertIndex"
						@close-popper="showColumnTemplates=false"
						:data="data"
						:empty-sortable="false"
					/>
				</template>
			</Tooltip>
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
				v-if="allowElementsAdd && !isPreviewMode"
			/>

			<div
				v-else-if="emptyPlaceholderText && !isPreviewMode"
				class="znpb-empty-placeholder-text-wrapper"
			>
				{{emptyPlaceholderText}}
			</div>

			<slot
				name="end"
			/>
		</template>
	</Sortable>
</template>

<script>
import { computed, ref } from 'vue'

// Utils
import { mapActions, mapGetters } from 'vuex'

// TODO: implement this
// import eventMarshall from '@/editor/common/eventMarshall'
import { getOptionValue } from '@zb/utils'

// Components
import Element from './Element.vue'
import SortableHelper from '../../../editor/src/common/SortableHelper.vue'
import SortablePlaceholder from '../../../editor/src/common/SortablePlaceholder.vue'
import EmptySortablePlaceholder from '../../../editor/src/common/EmptySortablePlaceholder.vue'
import ColumnTemplates from '../../../editor/src/common/ColumnTemplates.vue'
import { useElements } from '@zb/editor'

const sharedStateGlobal = {
	controlPressed: null,
	draggedItemData: null
}

export default {
	name: 'SortableContent',
	inheritAttrs: false,
	components: {
		SortableHelper,
		SortablePlaceholder,
		EmptySortablePlaceholder,
		ColumnTemplates,
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
	setup(props) {
		const defaultSortableGroup = {
			name: 'elements'
		}

		const { getElement } = useElements()

		const draggedItemData = ref(null)
		const showColumnTemplates = ref(false)
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

		const contentModel = computed({
			get () {
				return props.element.content.map(elementUID => {
					return getElement(elementUID)
				})
			},
			set (value) {
				props.element.content = value.map(element => element.uid)
			}
		})
		const showAddElementsPopup = computed(() => contentModel.length > 0 && showColumnTemplates.value)
		const insertIndex = computed(() => contentModel.length)
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
			const mediaOrientation = getOptionValue(props.element.options, '_styles.wrapper.default.default.flex-direction')
			if (mediaOrientation) {
				orientation = mediaOrientation === 'row' ? 'horizontal' : 'vertical'
			}

			return orientation
		})

		return {
			contentModel,
			groupInfo,
			getSortableAxis,
			insertIndex,
			showAddElementsPopup,
			sharedState
		}
	},
	computed: {
		...mapGetters([
			'isPreviewMode',
			'getActiveShowElementsPopup',
			'isDragging',
			'getElementData',
			'getPageContent'
		])
	},

	methods: {
		...mapActions([
			'saveElementsOrder',
			'setActiveShowElementsPopup',
			'setDraggingState',
			'addElement',
			'copyElement'
		]),
		onKeyup (event) {
			if (event.keyCode === 17) {
				this.sharedState.controlPressed = false
			}
		},
		onKeydown (event) {
			this.sharedState.controlPressed = event.ctrlKey
		},
		addNewItemToList (toVm, modifiedNewIndex, draggedItemId) {
			this.addElement({
				parentUid: toVm.$parent.data.uid,
				index: modifiedNewIndex,
				data: this.getElementData(draggedItemId)
			})
		},
		onAddElementsHide () {
			this.showColumnTemplates = false
			this.resetAddElementsPopup()

			// remove active element popup
			if (this.element && this.getActiveShowElementsPopup === this.element.uid) {
				this.setActiveShowElementsPopup(null)
			}
		},
		onAddElementsShow () {
			this.showColumnTemplates = true

			if (eventMarshall.getActiveTooltip) {
				eventMarshall.getActiveTooltip.showColumnTemplates = false
			}

			eventMarshall.addActiveTooltip(this)
		},
		resetAddElementsPopup () {
			if (eventMarshall.getActiveTooltip && eventMarshall.getActiveTooltip === this) {
				eventMarshall.reset()
			}
		},
		onSortableDrop (event) {
			const { placeBefore, newIndex, toVm, item } = event.data

			if (this.sharedState.controlPressed) {
				const modifiedNewIndex = placeBefore ? newIndex : newIndex + 1
				this.copyElement({
					elementUid: item.id,
					insertParent: toVm.$parent.data.uid
				})

				item.style.display = null
				item.style.opacity = null
			}
		},
		onSortableStart (event) {
			this.sharedState.controlPressed = false
			this.sharedState.draggedItemData = event.data
			this.setDraggingState(true)
			document.addEventListener('keydown', this.onKeydown)
			document.addEventListener('keyup', this.onKeyup)
		},
		onSortableEnd (event) {
			this.setDraggingState(false)
			document.removeEventListener('keydown', this.onKeydown)
			document.removeEventListener('keyup', this.onKeyup)
		}
	},
	watch: {
		getActiveShowElementsPopup (newValue, oldValue) {
			this.showColumnTemplates = this.element && newValue === this.element.uid
		},
		showAddElementsPopup (newValue) {
			if (newValue) {
				const elementRect = this.$el.getBoundingClientRect()
				this.positionRect = elementRect
				this.$refs.addElementsPopup.scheduleUpdate()
			}
		},
		'sharedState.controlPressed' (newValue, oldValue) {
			const draggedItem = this.sharedState.draggedItemData.item
			if (newValue) {
				draggedItem.style.display = null
				draggedItem.style.opacity = 0.2
				if (!this.isDragging) {
					draggedItem.style.opacity = null
					draggedItem.style.display = null
				}
			} else {
				if (this.isDragging) {
					draggedItem.style.opacity = null
					draggedItem.style.display = 'none'
				}
			}
		}
	},
	beforeUnmount () {
		this.resetAddElementsPopup()
	}
}
</script>

<style lang="scss">
.znpb-add-elemenets-tooltip-placeholder {
	position: absolute;
	bottom: 0;
	width: 100%;
}

.znpb-empty-placeholder-text-wrapper {
	position: relative;
	padding: 30px;
	color: rgba($black, .5);
	font-family: Roboto, sans-serif;
	font-size: 14px;
	text-align: center;

	&:before {
		content: "";
		position: absolute;
		top: 50%;
		right: 0;
		bottom: 0;
		left: 0;
		width: calc(100% - 20px);
		height: calc(100% - 20px);
		margin: 0 auto;
		text-align: center;
		background-color: rgba($elements-toolbox-color, .2);
		transform: translateY(-50%);
	}
}
</style>
