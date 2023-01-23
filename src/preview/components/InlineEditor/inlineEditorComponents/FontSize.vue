<template>
	<div class="zion-inline-editor-slider-area">
		<InputRangeDynamic
			ref="inputRangeDynamicRef"
			:modelValue="sliderValue"
			:options="options"
			class="zion-inline-editor-slider-area--slider"
			@update:modelValue="onFontChange"
		/>
	</div>
</template>

<script lang="ts" setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';

const emit = defineEmits(['started-dragging']);

const editor = inject('ZionInlineEditor');
const sliderValue = ref(null);
const inputRangeDynamicRef = ref(null);
let changeTimeout: ReturnType<typeof setTimeout> | null = null;
let isCurrentChange = false;

const options = [
	{
		unit: 'px',
		min: 6,
		max: 300,
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
		unit: 'rem',
		min: 1,
		max: 6,
		step: 1,
		shiftStep: 1,
	},
	{
		unit: 'pt',
		min: 1,
		max: 60,
		step: 1,
		shiftStep: 1,
	},
	{
		unit: 'vh',
		min: 1,
		max: 100,
		step: 5,
		shiftStep: 1,
	},
];

function onFontChange(newValue) {
	editor.editor.formatter.apply('fontsize', { value: newValue });
	sliderValue.value = newValue;

	emit('started-dragging');

	clearTimeout(changeTimeout);
	changeTimeout = setTimeout(() => {
		isCurrentChange = false;
	}, 100);

	isCurrentChange = true;
}

function onNodeChange() {
	if (!isCurrentChange) {
		getFontSize();
	}
}

function getFontSize() {
	sliderValue.value = editor.editor.queryCommandValue('FontSize');
}

onMounted(() => {
	getFontSize();
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
