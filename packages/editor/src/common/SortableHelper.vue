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
	display: flex;
	align-items: center;
	justify-content: center;
	width: 28px;
	height: 28px;
	font-size: 14px;
	background-color: var(--zb-secondary-color);
	border-radius: 50%;
	cursor: none;
}
</style>
