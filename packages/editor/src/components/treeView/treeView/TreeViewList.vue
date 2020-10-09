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
			v-for="element in templateItems"
			:element="element"
			:key="'tree-view-element-' + element.uid"
		/>
		<template #helper>
			<SortableHelper/>
		</template>

		<template #placeholder>
			<SortablePlaceholder/>
		</template>

		<template #end>
			<div
				class="znpb-tree-view__item-add-element-button"
				@click="toggleAddElementsPopup"
			>
				<Icon
					icon="plus"
					:size="11"
					:bgSize="25"
					:rounded="true"
					color="#fff"
					class="znpb-tree-view__item-add-element-button-icon"
				></Icon>
			</div>
		</template>
	</Sortable>
</template>
<script>
import { defineAsyncComponent } from 'vue'
import { mapActions, mapGetters } from 'vuex'
import SortableHelper from '../../../common/SortableHelper.vue'
import SortablePlaceholder from '../../../common/SortablePlaceholder.vue'
import TreeViewListItem from './TreeViewListItem.vue'

export default {
	name: 'TreeViewList',
	components: {
		SortableHelper,
		SortablePlaceholder,
		TreeViewListItem: () => import('./TreeViewListItem.vue')
	},
	data () {
		return {
			// hovered: false
		}
	},
	props: {
		content: {
			type: Array,
			required: false
		}
	},
	computed: {
		...mapGetters(['getActiveShowElementsPopup']),
		templateItems: {
			get () {
				return this.content
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
