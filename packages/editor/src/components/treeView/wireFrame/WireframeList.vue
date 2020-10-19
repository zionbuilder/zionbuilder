<template>
	<Sortable
		tag="ul"
		class="znpb-wireframe-view-wrapper"
		v-model="templateItems"
		group="pagebuilder-wireframe-elements"
	>
		<WireframeListItem
			v-for="element in templateItems"
			:element="element"
			:key="element.uid"
		/>

		<template #helper>
			<SortableHelper />
		</template>

		<template #placeholder>
			<SortablePlaceholder />
		</template>

		<template
			v-if="showAdd"
			#end
		>
			<div
				class="znpb-element-toolbox__add-element-button"
				@click="toggleAddElementsPopup"
				ref="addElementsPopupButton"
			>
				<Icon
					icon="plus"
					:rounded="true"
				/>
			</div>
		</template>


	</Sortable>
</template>

<script lang="ts">
import SortablePlaceholder from '../../../common/SortablePlaceholder.vue'
import SortableHelper from '../../../common/SortableHelper.vue'
import ElementWireframeView from './ElementWireframeView.vue'
import { useTreeViewList } from '../useTreeViewList'

export default {
	name: 'WireframeList',
	components: {
		SortablePlaceholder,
		SortableHelper
	},
	props: {
		element: {
			type: Object,
			required: true
		},
		showAdd: {
			type: Boolean,
			default: true
		}
	},
	setup (props) {
		const {
			addElementsPopupButton,
			templateItems,
			addButtonBgColor,
			toggleAddElementsPopup,
			sortableStart,
			sortableEnd
		} = useTreeViewList(props)

		return {
			addElementsPopupButton,
			templateItems,
			toggleAddElementsPopup,
			sortableStart,
			sortableEnd,
			addButtonBgColor
		}
	}
}
</script>