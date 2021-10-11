<template>
	<div class="znpb-tree-viewWrapper">
		<div class="znpb-tree-viewExpandContainer">
			<a
				href="#"
				@click="expandAll(element)"
			>
				{{$translate('expand_all')}}

				<Icon
					icon="long-arrow-down"
					:size="10"
				/>

			</a>
			<a
				href="#"
				@click="collapseAll(element)"
			>
				{{$translate('collapse_all')}}
				<Icon
					icon="long-arrow-up"
					:size="10"
				/>
			</a>
		</div>

		<div class="znpb-tree-view znpb-fancy-scrollbar znpb-panel-view-wrapper">
			<TreeViewList :element="element" />
		</div>
	</div>

</template>
<script lang="ts">
export default {
	name: "TreeViewPanel",
	props: {
		element: {
			type: Object,
			required: true,
		},
	},
	setup(props) {
		function expandAll(element) {
			if (element.content.length > 0) {
				element.content.forEach((child) => {
					child.treeViewItemExpanded = true;
					expandAll(child);
				});
			}
		}

		function collapseAll(element) {
			if (element.content.length > 0) {
				element.content.forEach((child) => {
					child.treeViewItemExpanded = false;
					collapseAll(child);
				});
			}
		}

		return {
			expandAll,
			collapseAll,
		};
	},
};
</script>
<style lang="scss">
.znpb-tree-viewExpandContainer {
	padding: 0 20px;
	display: flex;
	justify-content: flex-end;
	margin-bottom: 12px;

	a {
		display: flex;
		align-items: center;
		margin-left: 12px;
		color: var(--zb-surface-text-color);
		font-size: 11px;
		font-weight: 700;
		transition: color 0.15s;

		&:hover {
			color: var(--zb-surface-text-hover-color);
		}
	}

	.znpb-editor-icon-wrapper {
		position: relative;
		top: -1px;
		margin-left: 3px;
	}
}

.znpb-tree-viewWrapper {
	display: flex;
	flex-direction: column;
	max-height: 100%;
}

.znpb-tree-view--no_content {
	padding: 0 30px;
	text-align: center;
}
.znpb-tree-view {
	flex-grow: 1;
	overflow-x: hidden;
	overflow-y: auto;
}
</style>
