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
				ref="addElementsPopupButton"
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
import { computed, ref, isReadonly } from 'vue'
import { mapActions, mapGetters } from 'vuex'
import SortableHelper from '../../../common/SortableHelper.vue'
import SortablePlaceholder from '../../../common/SortablePlaceholder.vue'
import { useElements, useAddElementsPopup } from '@data'

export default {
	name: 'TreeViewList',
	components: {
		SortableHelper,
		SortablePlaceholder
	},
	props: {
		element: {
			type: Object,
			required: false
		}
	},
	setup (props, context) {
		// Add elements button DOM element will be populated after mount
		const $root = ref(null)
		const addElementsPopupButton = ref(null)
		const { getElement } = useElements()

		const templateItems = computed({
			get () {

				return props.element.content.map(elementUID => {
					return getElement(elementUID)
				})
			},
			set (value) {
				console.log({value})
			}
		})

		function toggleAddElementsPopup () {
			const { showAddElementsPopup } = useAddElementsPopup()

			showAddElementsPopup(props.element, addElementsPopupButton)
		}

		function sortableStart () {
			this.setDraggingState(true)
		}

		function sortableEnd () {
			this.setDraggingState(false)
		}

		return {
			addElementsPopupButton,
			templateItems,
			toggleAddElementsPopup,
			sortableStart,
			sortableEnd
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
