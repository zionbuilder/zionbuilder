<template>
	<Sortable
		tag="ul"
		class="znpb-tree-view-wrapper"
		v-model="templateItems"
		group="pagebuilder-treview-elements"
		@start="sortableStart"
		@end="sortableEnd"
		handle=".znpb-tree-view__itemIcon"
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
				v-if="templateItems.length === 0 && element.isWrapper"
			>
				<AddElementIcon
					:element="element"
					placement="inside"
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
			position: relative;
			overflow: hidden;
			z-index: 1;
			padding: 0;
			margin: 0;

			& .znpb-tree-view__item {
				position: relative;
				padding-left: 20px;
				width: 100%;
				margin: 0;

				&::before {
					content: "";
					position: absolute;
					bottom: calc(100% + -24px);
					left: 3px;
					width: 100%;
					height: 5000px;
					border: 2px solid var(--zb-surface-border-color);
					border-top: 0;
					border-right: 0;
					border-radius: 8px;
					border-top-left-radius: 0;
					border-bottom-right-radius: 0;
					margin-top: 11px;
					z-index: -1;
				}
			}
		}
	}

	&__view__ListAddButtonInside {
		position: relative;
		height: 40px;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-bottom: 5px;

		.znpb-element-toolbox__add-element-button {
			--button-size: 24px;
			--font-size: 12px;
			position: relative;
			top: auto;
			left: auto;
			margin: 0;
		}
	}
}
</style>
