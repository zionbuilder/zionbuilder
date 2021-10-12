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
			:key="element.uid"
			:element="element"
			@expand-panel="$emit('expand-panel')"
		/>

		<template #end>
			<div
				class="znpb-tree-view__view__ListAddButtonInside"
				v-if="templateItems.length === 0"
			>
				<AddElementIcon
					:element="element"
					class="znpb-tree-view__ListAddButton"
				/>
			</div>

		</template>

		<template #helper>
			<SortableHelper />
		</template>

		<template #placeholder>
			<SortablePlaceholder />
		</template>
	</Sortable>
</template>
<script>
import SortableHelper from '../../../common/SortableHelper.vue'
import SortablePlaceholder from '../../../common/SortablePlaceholder.vue'
import { useTreeViewList } from '../useTreeViewList'

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
		const {
			templateItems,
			sortableStart,
			sortableEnd
		} = useTreeViewList(props)

		function onChildActive () {
			expanded.value = true;
		}

		return {
			templateItems,
			onChildActive,
			sortableStart,
			sortableEnd
		}
	}
}
</script>

<style lang="scss">
.znpb-tree-view {
	padding-bottom: 25px;

	& .znpb-tree-view-wrapper {
		padding: 0 20px;

		& .znpb-tree-view-wrapper {
			padding-right: 0;
			padding-left: 10px;
		}
	}
}

.znpb-tree-view__view__ListAddButtonInside {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	padding: 7.5px 0;
	margin: 0 auto;
	margin-bottom: 5px;
	border: 1px dashed var(--zb-surface-border-color);
	border-radius: 3px;
	cursor: pointer;
}
</style>
