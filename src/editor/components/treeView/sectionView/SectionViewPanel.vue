<template>
	<div
		id="znpb-section-view"
		class="znpb-tree-view-container znpb-fancy-scrollbar znpb-panel-view-wrapper"
	>
		<Sortable
			class="znpb-section-view-wrapper"
			v-model="templateItems"
			tag="ul"
			group="pagebuilder-sectionview-elements"
			@start="sortableStart"
			@end="sortableEnd"
		>
			<ElementSectionView
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

			<template #end>
				<div
					class="znpb-tree-view__view__ListAddButtonInside"
					v-if="templateItems.length === 0"
				>
					<AddElementIcon
						:element="element"
						placement="inside"
					/>
				</div>
			</template>

		</Sortable>
	</div>
</template>
<script>
import ElementSectionView from './ElementSectionView.vue'
import SortableHelper from '../../../common/SortableHelper.vue'
import SortablePlaceholder from '../../../common/SortablePlaceholder.vue'
import { useTreeViewList } from '../useTreeViewList'

export default {
	name: 'section-view',
	components: {
		ElementSectionView,
		SortableHelper,
		SortablePlaceholder
	},
	props: {
		element: {
			type: Object,
			required: true
		}
	},
	setup (props) {
		const {
			templateItems,
			sortableStart,
			sortableEnd
		} = useTreeViewList(props)

		return {
			templateItems,
			sortableStart,
			sortableEnd
		}

	}
}
</script>
<style lang="scss">
.znpb-tree-view-container {
	flex-grow: 1;
	overflow-y: auto;
	padding: 0 20px 0 20px;
}
</style>
