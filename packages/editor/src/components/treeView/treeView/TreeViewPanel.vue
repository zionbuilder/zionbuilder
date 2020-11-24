<template>
	<div
		class="znpb-tree-view znpb-fancy-scrollbar znpb-panel-view-wrapper"
		ref="treeViewPanel"
		id="znpb-tree-view-panel"
	>
		<TreeViewList
			:element="element"
		/>
	</div>
</template>
<script lang="ts">
export default {
	name: 'TreeViewPanel',
	props: {
		element: {
			type: Object,
			required: true
		}
	}
}
</script>
<style lang="scss">
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
