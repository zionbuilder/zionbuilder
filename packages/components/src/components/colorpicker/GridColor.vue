<template>
	<div class="znpb-form-library-grid__panel-content znpb-fancy-scrollbar">
		<BaseIcon
			icon="plus"
			@click.stop.native="$emit('add-new-color')"
			class="znpb-colorpicker-circle znpb-colorpicker-add-color "
		/>
		<slot></slot>
	</div>
</template>
<script>
import BaseIcon from '../BaseIcon.vue'

export default {
	name: 'GridColor',
	components: {
		BaseIcon
	}
}
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
	.znpb-colorpicker-add-color {
		box-sizing: content-box;
		width: 26px;
		height: 26px;
		font-size: 10px;
	}

	.znpb-colorpicker-circle-color {
		width: 30px;
		height: 30px;
		box-shadow: inset 0 0 0 2px rgba(0, 0, 0, .1);
	}
}
</style>
