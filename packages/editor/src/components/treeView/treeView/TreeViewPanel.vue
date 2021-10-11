<template>
	<div class="znpb-tree-viewWrapper">
		<div class="znpb-tree-viewExpandContainer">
			<a
				href="#"
				@click="expandAll(element)"
			>
				{{$translate('expand_all')}}

				<Icon
					icon="select"
					:size="14"
				/>

			</a>
			<a
				href="#"
				@click="collapseAll(element)"
			>
				{{$translate('collapse_all')}}
				<Icon
					icon="select"
					:size="14"
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
