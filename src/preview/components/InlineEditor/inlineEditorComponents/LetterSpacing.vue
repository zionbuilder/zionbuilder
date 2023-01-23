<template>
	<div class="zion-inline-editor-slider-area">
		<InputRangeDynamic
			ref="inputRangeDynamicRef"
			:modelValue="sliderValue"
			:options="options"
			class="zion-inline-editor-slider-area--slider"
			@update:modelValue="onLetterChange"
		/>
	</div>
</template>

<script lang="ts" setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';

const emit = defineEmits(['started-dragging']);

const editor = inject('ZionInlineEditor');
const sliderValue = ref(null);
const inputRangeDynamicRef = ref(null);
let isCurrentChange = false;
let changeTimeout = null;

const options = [
	{
		unit: 'px',
		min: 0,
		max: 300,
		step: 1,
		shiftStep: 5,
	},
	{
		unit: 'rem',
		min: 1,
		max: 10,
		step: 1,
		shiftStep: 1,
	},
	{
		unit: 'normal',
		min: null,
		max: null,
		step: null,
		shiftStep: null,
	},
];

function onLetterChange(newValue) {
	editor.editor.formatter.apply('letterSpacing', { value: newValue });
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
		getLetterSpacing();
	}
}

function getLetterSpacing() {
	const letterSpacing = window.getComputedStyle(editor.editor.selection.getNode()).getPropertyValue('letter-spacing');
	sliderValue.value = letterSpacing;
}

onMounted(() => {
	editor.editor.formatter.register('letterSpacing', {
		selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
		styles: { 'letter-spacing': '%value' },
	});

	getLetterSpacing();
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
