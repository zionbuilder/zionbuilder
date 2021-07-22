<template>
	<Icon
		icon="plus"
		:bgSize="40"
		:rounded="true"
		color="#fff"
	></Icon>
</template>

<script>
export default {
	name: 'SortableHelper',
	data () {
		return {
		}
	}
}
</script>

<style lang="scss">
.zion-editor__sortable-helper {
	width: 40px;
	height: 40px;
	font-size: 14px;
	background-color: var(--zb-element-color);
	border-radius: 50%;
	cursor: none;
}
.zb-column + .zion-editor__sortable-helper {
	background-color: var(--zb-column-color);
}
.zb-section + .zion-editor__sortable-helper {
	background-color: var(--zb-section-color);
}
</style>
