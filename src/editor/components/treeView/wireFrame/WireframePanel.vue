<template>
	<div
		id="znpb-wireframe-panel"
		class="znpb-tree-view-bar znpb-wireframe-container znpb-fancy-scrollbar znpb-panel-view-wrapper"
	>
		<!-- content -->
		<WireframeList :element="element" :show-add="false" />
	</div>
</template>
<script>
import WireframeList from './WireframeList.vue';

export default {
	name: 'WireframePanel',
	components: {
		WireframeList,
	},
	props: {
		element: {
			type: Object,
			required: true,
		},
	},
};
</script>
<style lang="scss">
// style panel when wireframe active
.znpb-editor-panel__container.znpb-editor-panel__container--wireframe {
	.znpb-tree-view__type_wrapper {
		overflow-y: auto;
	}
}

.znpb-wireframe-container {
	width: 100%;
	height: 100%;
	padding: 0 30px;
	margin: 0 auto;
}
</style>
