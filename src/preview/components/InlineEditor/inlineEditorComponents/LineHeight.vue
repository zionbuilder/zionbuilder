<template>
	<div class="zion-inline-editor-slider-area">
		<InputRangeDynamic
			ref="inputRangeDynamicRef"
			:modelValue="sliderValue"
			:options="options"
			class="zion-inline-editor-slider-area--slider"
			@update:modelValue="onHeightChange"
		/>
	</div>
</template>

<script lang="ts" setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';

const editor = inject('ZionInlineEditor');

const sliderValue = ref('');
const inputRangeDynamicRef = ref(null);
let changeTimeout = null;
let isCurrentChange = false;

const options = [
	{
		unit: 'px',
		min: 1,
		max: 400,
		step: 1,
		shiftStep: 5,
	},

	{
		unit: 'em',
		min: 1,
		max: 100,
		step: 1,
		shiftStep: 5,
	},
	{
		unit: '%',
		min: 1,
		max: 100,
		step: 1,
		shiftStep: 5,
	},
	{
		unit: 'normal',
		min: null,
		max: null,
		step: null,
		shiftStep: null,
	},
];

function onHeightChange(newValue: string) {
	editor.editor.formatter.apply('lineHeight', { value: newValue });

	sliderValue.value = newValue;

	clearTimeout(changeTimeout);
	changeTimeout = setTimeout(() => {
		isCurrentChange = false;
	}, 100);

	isCurrentChange = true;
}
function onNodeChange() {
	if (!isCurrentChange) {
		getLineHeight();
	}
}

function getLineHeight() {
	// command not supported
	sliderValue.value = window.getComputedStyle(editor.editor.selection.getNode()).getPropertyValue('line-height');
}

onMounted(() => {
	// Set default line height
	getLineHeight();
	editor.editor.on('SelectionChange', onNodeChange);
});

onBeforeUnmount(() => {
	editor.editor.off('SelectionChange', onNodeChange);
});
</script>

<style lang="scss">
.zion-inline-editor-slider-area {
	width: 100%;
	padding: 16px;
	font-size: 13px;
}
</style>
