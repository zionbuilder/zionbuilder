<template>
	<div class="zion-inline-editor-panel-color">
		<div class="zion-inline-editor-button">
			<InputColorPicker
				:modelValue="color"
				:show-library="false"
				type="simple"
				@update:modelValue="onColorChange"
				@open="$emit('open-color-picker', true)"
				@close="$emit('close-color-picker', false)"
			/>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';

const editor = inject('ZionInlineEditor');
const color = ref(null);

let justChangeColor = false;
let changeTimeout = null;

function onColorChange(newValue) {
	color.value = newValue;
	editor.editor.formatter.apply('forecolor', { value: newValue });

	clearTimeout(changeTimeout);
	changeTimeout = setTimeout(() => {
		justChangeColor = false;
	}, 500);

	justChangeColor = true;
}

function onNodeChange() {
	if (!justChangeColor) {
		getActiveColor();
	}
}

function getActiveColor() {
	// set a flag so we don't recursively update the color
	color.value = editor.editor.queryCommandValue('forecolor');
}

onMounted(() => {
	getActiveColor();
	editor.editor.on('NodeChange', onNodeChange);
});

onBeforeUnmount(() => {
	editor.editor.off('NodeChange', onNodeChange);
});
</script>

<style lang="scss">
.vc-chrome {
	width: 100% !important;
}
.vc-chrome * {
	user-select: none !important;
}

.zion-inline-editor-button {
	position: relative;
}

.zion-inline-editor-panel-color {
	& > .zion-inline-editor-button {
		padding: 11px 11px;
	}

	.znpb-form-colorpicker {
		padding: 0;
		span:first-child {
			width: 15px;
			height: 15px;
			background-position: 0 0, 3px 3px;
			background-size: 6px 6px;
		}

		.znpb-colorpicker-circle {
			width: 15px;
			height: 15px;
			box-shadow: 0 0 0 2px rgb(56, 56, 56);
			&.znpb-colorpicker-circle--no-color:before {
				width: 15px;
			}
			&--trigger {
				box-shadow: none;
			}
		}
	}
}
.zion-inline-editor-color-picker-button {
	content: ' ';
	position: absolute;
	top: 50%;
	left: 50%;
	width: 20px;
	height: 20px;
	background: #fff;
	border-radius: 50%;
	transform: translate(-50%, -50%);
}
</style>
