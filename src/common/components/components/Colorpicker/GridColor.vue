<template>
	<div class="znpb-form-library-grid__panel-content znpb-fancy-scrollbar">
		<Icon
			icon="plus"
			class="znpb-colorpicker-circle znpb-colorpicker-add-color"
			@mousedown.stop="$emit('add-new-color')"
		/>
		<slot></slot>
	</div>
</template>

<script lang="ts" setup>
import { Icon } from '../Icon';

defineEmits(['add-new-color']);
</script>

<style lang="scss">
.znpb-form-library-grid__panel-content {
	display: grid;
	padding: 0 8px 0 20px;
	margin-right: 8px;
	margin-bottom: 10px;

	grid-gap: 5px;
	grid-row-gap: 10px;
	grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
	justify-items: center;

	&--no-pd {
		padding: 0;
		margin: 0;
	}

	.znpb-colorpicker-add-color {
		box-sizing: content-box;
		width: 26px;
		height: 26px;
		font-size: 10px;
	}

	.znpb-colorpicker-circle-color {
		width: 30px;
		height: 30px;
		box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1);
	}
}
</style>
