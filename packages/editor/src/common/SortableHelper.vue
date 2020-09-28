<template>
	<BaseIcon
		icon="plus"
		:bgSize="40"
		:rounded="true"
		color="#fff"
	></BaseIcon>
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
	background-color: $secondary;
	border-radius: 50%;
	cursor: none;
}
.zb-column + .zion-editor__sortable-helper {
	background-color: $column-color;
}
.zb-section + .zion-editor__sortable-helper {
	background-color: $section-color;
}

</style>
