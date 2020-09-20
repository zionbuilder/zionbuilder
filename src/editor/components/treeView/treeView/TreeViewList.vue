<template>
	<Sortable
		tag="ul"
		class="znpb-tree-view-wrapper"
		v-model="templateItems"
		group="pagebuilder-treview-elements"
		@start="sortableStart"
		@end="sortableEnd"
	>

		<TreeViewListItem
			v-for="childElementUid in templateItems"
			@on:delete-element="deleteElement"
			:parentUid="elementUid || parentUid"
			:elementUid="childElementUid"
			:parentsToExpand="parentsToExpand"
			:key="'tree-view-element-' + childElementUid"
			@scroll-to-item="onScrollToItem"
		/>
		<SortableHelper slot="helper"></SortableHelper>
		<SortablePlaceholder slot="placeholder"></SortablePlaceholder>
		<div
			class="znpb-tree-view__item-add-element-button"
			v-if="addButton"
			@click="toggleAddElementsPopup"
			slot="end"
		>
			<BaseIcon
				:bgColor="addButtonColor"
				icon="plus"
				:size="11"
				:bgSize="25"
				:rounded="true"
				color="#fff"
				class="znpb-tree-view__item-add-element-button-icon"
			></BaseIcon>
		</div>
	</Sortable>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { Sortable } from '@zb/components'
import SortableHelper from '@/editor/common/SortableHelper.vue'
import SortablePlaceholder from '@/editor/common/SortablePlaceholder.vue'

export default {
	name: 'TreeViewList',
	components: {
		Sortable,
		SortableHelper,
		SortablePlaceholder
	},
	data () {
		return {
			// hovered: false
		}
	},
	beforeCreate: function () {
		// Use this method to fix circular dependency + sortable functionality
		this.$options.components.TreeViewListItem = require('./TreeViewListItem.vue').default
	},
	props: {
		content: {
			type: Array,
			required: false
		},
		elementUid: {
			type: String
		},
		addButton: {
			type: Boolean,
			required: false
		},
		addButtonColor: {
			type: String,
			required: false
		},
		parentUid: {
			type: String
		},
		parentsToExpand: {
			type: Array,
			default () {
				return []
			}
		}
	},
	computed: {
		...mapGetters(['getActiveShowElementsPopup']),
		templateItems: {
			get () {
				return this.content || []
			},
			set (value) {
				this.saveElementsOrder({
					newOrder: value,
					content: this.content
				})
			}
		}

	},
	methods: {
		...mapActions([
			'deleteElement',
			'saveElementsOrder',
			'setDraggingState',
			'setActiveShowElementsPopup'
		]),
		onScrollToItem (event) {
			this.$emit('scroll-to-item', event)
		},
		toggleAddElementsPopup () {
			this.getActiveShowElementsPopup === this.elementUid ? this.setActiveShowElementsPopup(null) : this.setActiveShowElementsPopup(this.elementUid)
		},
		sortableStart () {
			this.setDraggingState(true)
		},
		sortableEnd () {
			this.setDraggingState(false)
		}
	}
}
</script>

<style lang="scss">
.znpb-tree-view__item-add-element-button {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	padding: 7.5px 0;
	margin: 0 auto;
	margin-bottom: 5px;
	border: 1px dashed $border-color;
	border-radius: 3px;
	cursor: pointer;
}
.znpb-tree-view {
	& .znpb-tree-view-wrapper {
		padding: 0 20px;

		& .znpb-tree-view-wrapper {
			padding-right: 0;
			padding-left: 10px;
		}
	}
}
</style>
