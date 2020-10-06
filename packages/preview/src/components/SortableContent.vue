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
			v-for="childElementUid in contentModel"
			:key="childElementUid"
			:uid="childElementUid"
			:data="getPageContent[childElementUid]"
			:parentUid="data.uid"
		/>

		<slot
			name="start"
			slot="start"
		/>

		<EmptySortablePlaceholder
			slot="empty-placeholder"
			v-if="allowElementsAdd && !isPreviewMode"
			:parentUid="data.uid"
			:data="data"
		/>
		<div
			v-else-if="emptyPlaceholderText && !isPreviewMode"
			slot="empty-placeholder"
			class="znpb-empty-placeholder-text-wrapper"
		>
			{{emptyPlaceholderText}}
		</div>

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

						:parentUid="data.uid"
						:insertIndex="insertIndex"
						@close-popper="showColumnTemplates=false"
						:data="data"
						:empty-sortable="false"
					/>
				</template>
			</Tooltip>
		</template>
		<SortableHelper #helper />

		<SortablePlaceholder #placeholder />

		<slot
			name="end"
			slot="end"
		/>

	</Sortable>
</template>

<script>
// Utils
import { mapActions, mapGetters } from 'vuex'
import { Sortable, Tooltip } from '@zb/components'
// TODO: implement this
// import eventMarshall from '@/editor/common/eventMarshall'
import { getOptionValue } from '@zb/utils'

// Components
import Element from './Element.vue'
import SortableHelper from '../../../editor/src/common/SortableHelper.vue'
import SortablePlaceholder from '../../../editor/src/common/SortablePlaceholder.vue'
import EmptySortablePlaceholder from '../../../editor/src/common/EmptySortablePlaceholder.vue'
import ColumnTemplates from '../../../editor/src/common/ColumnTemplates.vue'

const sharedState = {
	controlPressed: null,
	draggedItemData: null
}

export default {
	name: 'SortableContent',
	inheritAttrs: false,
	props: {
		data: {
			type: Object,
			required: true
		},
		level: {
			type: Number,
			required: false,
			default: 3
		},
		content: {
			type: Array,
			required: false,
			default () {
				return []
			}
		},
		group: {
			type: Object,
			required: false
		},
		accepts: {
			type: Array,
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
	data () {
		return {
			draggedItemData: null,
			showColumnTemplates: false,
			positionRect: {
				left: 0,
				top: 0
			},
			addColumnsRef: {
				getBoundingClientRect: () => {
					return this.positionRect
				}
			},
			defaultSortableGroup: {
				name: 'elements'
			},
			sharedState: sharedState
		}
	},
	computed: {
		...mapGetters([
			'isPreviewMode',
			'getActiveShowElementsPopup',
			'isDragging',
			'getElementData',
			'getPageContent'
		]),
		getSortableAxis () {
			if (this.data.element_type === 'root') {
				return 'vertical'
			}
			let orientation = this.data.element_type === 'zion_column' ? 'vertical' : 'horizontal'

			// Check columns and section direction
			if (this.data.options.inner_content_layout) {
				orientation = this.data.options.inner_content_layout
			}

			// Check media settings
			const mediaOrientation = getOptionValue(this.data.options, '_styles.wrapper.default.default.flex-direction')
			if (mediaOrientation) {
				orientation = mediaOrientation === 'row' ? 'horizontal' : 'vertical'
			}

			return orientation
		},
		showAddElementsPopup () {
			return this.contentModel.length > 0 && this.showColumnTemplates
		},
		contentModel: {
			get () {
				return this.content
			},
			set (newValue) {
				if (!this.sharedState.controlPressed) {
					this.saveElementsOrder({
						newOrder: newValue,
						content: this.content
					})
				}
			}
		},
		insertIndex () {
			return this.contentModel.length
		},
		groupInfo () {
			return this.group || this.defaultSortableGroup
		}
	},

	components: {
		Sortable,
		SortableHelper,
		SortablePlaceholder,
		EmptySortablePlaceholder,
		ColumnTemplates,
		Tooltip,
		Element
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
			if (this.data && this.getActiveShowElementsPopup === this.data.uid) {
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
			this.showColumnTemplates = this.data && newValue === this.data.uid
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
